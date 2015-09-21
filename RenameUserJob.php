<?php
/**
 * Custom job to perform updates on tables in busier environments
 *
 * Job parameters include:
 *   - table     : DB table to update
 *   - column    : The *_user_text column to update
 *   - oldname   : The old user name
 *   - newname   : The new user name
 *   - count     : The expected number of rows to update in this batch
 *
 * Additionally, one of the following groups of parameters must be set:
 * a) The timestamp based rename paramaters:
 *   - timestampColumn : The *_timestamp column
 *   - minTimestamp    : The minimum bound of the timestamp column range for this batch
 *   - maxTimestamp    : The maximum bound of the timestamp column range for this batch
 *   - uniqueKey       : A column that is unique (preferrably the PRIMARY KEY) [optional]
 * b) The unique key based rename paramaters:
 *   - uniqueKey : A column that is unique (preferrably the PRIMARY KEY)
 *   - keyId     : A list of values for this column to determine rows to update for this batch
 *
 * To avoid some race conditions, the following parameters should be set:
 *   - userID    : The ID of the user to update
 *   - uidColumn : The *_user_id column
 */
class RenameUserJob extends Job {
	public function __construct( Title $title, $params = array(), $id = 0 ) {
		parent::__construct( 'renameUser', $title, $params, $id );
	}

	public function run() {
		global $wgUpdateRowsPerQuery;

		$table = $this->params['table'];
		$column = $this->params['column'];
		$oldname = $this->params['oldname'];
		$newname = $this->params['newname'];
		$count = $this->params['count'];
		if ( isset( $this->params['userID'] ) ) {
			$userID = $this->params['userID'];
			$uidColumn = $this->params['uidColumn'];
		} else {
			$userID = null;
			$uidColumn = null;
		}
		if ( isset( $this->params['timestampColumn'] ) ) {
			$timestampColumn = $this->params['timestampColumn'];
			$minTimestamp = $this->params['minTimestamp'];
			$maxTimestamp = $this->params['maxTimestamp'];
		} else {
			$timestampColumn = null;
			$minTimestamp = null;
			$maxTimestamp = null;
		}
		$uniqueKey = isset( $this->params['uniqueKey'] ) ? $this->params['uniqueKey'] : null;
		$keyId = isset( $this->params['keyId'] ) ? $this->params['keyId'] : null;

		# Conditions like "*_user_text = 'x'
		$conds = array( $column => $oldname );
		# If user ID given, add that to condition to avoid rename collisions
		if ( $userID !== null ) {
			$conds[$uidColumn] = $userID;
		}
		# Bound by timestamp if given
		if ( $timestampColumn !== null ) {
			$conds[] = "$timestampColumn >= '$minTimestamp'";
			$conds[] = "$timestampColumn <= '$maxTimestamp'";
		# Bound by unique key if given (B/C)
		} elseif ( $uniqueKey !== null && $keyId !== null ) {
			$conds[$uniqueKey] = $keyId;
		} else {
			throw new InvalidArgumentException( "Expected ID batch or time range" );
		}

		$dbw = wfGetDB( DB_MASTER );

		$affectedCount = 0;
		# Actually update the rows for this job...
		if ( $uniqueKey !== null ) {
			# Select the rows to update by PRIMARY KEY
			$ids = $dbw->selectFieldValues( $table, $uniqueKey, $conds, __METHOD__ );
			# Update these rows by PRIMARY KEY to avoid slave lag
			foreach ( array_chunk( $ids, $wgUpdateRowsPerQuery ) as $batch ) {
				$dbw->commit( __METHOD__, 'flush' );

				$dbw->update( $table,
					array( $column => $newname ),
					array( $column => $oldname, $uniqueKey => $batch ),
					__METHOD__
				);
				$affectedCount += $dbw->affectedRows();
			}
		} else {
			# Update the chuck of rows directly
			$dbw->update( $table,
				array( $column => $newname ),
				$conds,
				__METHOD__
			);
			$affectedCount += $dbw->affectedRows();
		}

		# Special case: revisions may be deleted while renaming...
		if ( $affectedCount < $count && $table == 'revision' && $timestampColumn !== null ) {
			# If some revisions were not renamed, they may have been deleted.
			# Do a pass on the archive table to get these straglers...
			$ids = $dbw->selectFieldValues(
				'archive',
				'ar_id',
				array(
					'ar_user_text' => $oldname,
					'ar_user' => $userID,
					// No user,rev_id index, so use timestamp to bound
					// the rows. This can use the user,timestamp index.
					"ar_timestamp >= '$minTimestamp'",
					"ar_timestamp <= '$maxTimestamp'"
				),
				__METHOD__
			);
			if ( $ids ) {
				$dbw->update(
					'archive',
					array( 'ar_user_text' => $newname ),
					array( 'ar_user_text' => $oldname, 'ar_id' => $ids ),
					__METHOD__
				);
			}
		}
		# Special case: revisions may be restored while renaming...
		if ( $affectedCount < $count && $table == 'archive' && $timestampColumn !== null ) {
			# If some revisions were not renamed, they may have been restored.
			# Do a pass on the revision table to get these straglers...
			$ids = $dbw->selectFieldValues(
				'revision',
				'rev_id',
				array(
					'rev_user_text' => $oldname,
					'rev_user' => $userID,
					// No user,rev_id index, so use timestamp to bound
					// the rows. This can use the user,timestamp index.
					"rev_timestamp >= '$minTimestamp'",
					"rev_timestamp <= '$maxTimestamp'"
				),
				__METHOD__
			);
			if ( $ids ) {
				$dbw->update(
					'revision',
					array( 'rev_user_text' => $newname ),
					array( 'rev_user_text' => $oldname, 'rev_id' => $ids ),
					__METHOD__
				);
			}
		}

		return true;
	}
}
