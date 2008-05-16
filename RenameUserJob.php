<?php

/**
 * Custom job to perform updates on tables in busier environments
 */
class RenameUserJob extends Job {

	/**
	 * Constructor
	 *
	 * @param Title $title Associated title
	 * @param array $params Job parameters
	 */
	public function __construct( $title, $params ) {
		parent::__construct( 'renameUser', $title, $params );
	}

	/**
	 * Execute the job
	 *
	 * @return bool
	 */
	public function run() {
		$dbw = wfGetDB( DB_MASTER );
		extract( $this->params );
		# Conditions like "*_user_text = 'x', *_id = y"
		$conds = array( $column => $oldname, $uniqueKey => $keyId );
		# If user ID given, add that too to be safe. This avoids 
		# possible rename collisions.
		if( isset($userID) ) {
			$conds[$uidColumn] = $userID;
		}
		# Update a chuck of rows!
		$dbw->update( $table,
			array( $column => $newname ),
			$conds,
			__METHOD__
		);
		# Special case: revisions may be deleted while renaming...
		if( $table == 'revision' && isset($userID) ) {
			$expected = count($keyId);
			$actual = $dbw->affectedRows();
			# If some revisions were not renamed, they may have been deleted.
			# Do a pass on the archive table to get these straglers...
			if( $actual < $expected ) {
				$dbw->update( 'archive',
					array( 'ar_user_text' => $newname ),
					array( 'ar_user_text' => $oldname,
						'ar_rev_id' => $keyId,
						'ar_user' => $userID,
						// No user,rev_id index, so use timestamp to bound
						// the rows. This can use the user,timestamp index.
						"ar_timestamp >= $minTimestamp",
						"ar_timestamp <= $maxTimestamp"),
					__METHOD__
				);
			}
		}
		return true;
	}

}