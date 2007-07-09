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
		$dbw->update(
			$table,
			array( $column => $newname ),
			array(
				$column => $oldname,
				$uniqueKey => $keyId,
			),
			__METHOD__
		);
		return true;
	}

}