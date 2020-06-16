<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}

require_once "$IP/maintenance/Maintenance.php";

/**
 * @ingroup Maintenance
 */
class CleanupArchiveUserText extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Update the archive table where users were ' .
			'previously renamed, but their archive contributions were not' );

		$this->requireExtension( 'Renameuser' );
	}

	public function execute() {
		$this->output( "archive.ar_user_text is no longer used.\n" );
	}

	public function getDbType() {
		return Maintenance::DB_ADMIN;
	}
}

$maintClass = CleanupArchiveUserText::class;
require_once RUN_MAINTENANCE_IF_MAIN;
