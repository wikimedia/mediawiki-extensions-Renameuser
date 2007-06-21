<?php

class RenameUserJob extends Job {
	function __construct($title,$params) {
		parent::__construct('renameUser', $title, $params);
	}

	function run() {
		$dbw = wfGetDB( DB_MASTER );
		// Our keyId param will be an array of ids
		$dbw->update( $this->params['table'],
			array( $this->params['column'] => $this->params['newname'] ),
			array( $this->params['column'] => $this->params['oldname'], 
				$this->params['uniqueKey'] => $this->params['keyId'] )
			#,array( $dbw->lowPriorityOption() )
		);
	}
}

?>
