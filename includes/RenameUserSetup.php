<?php

namespace MediaWiki\Extension\Renameuser;

use \MediaWiki\RenameUser as Core;
use \MediaWiki\RenameUser\Hook as CoreHook;
use \MediaWiki\Extension\Renameuser as Ext;
use \MediaWiki\Extension\Renameuser\Hook as ExtHook;

class RenameUserSetup {
	public static function setup() {
		// This extension has moved to core. Define b/c class aliases.
		class_alias( CoreHook\RenameUserAbortHook::class, ExtHook\RenameUserAbortHook::class );
		class_alias( CoreHook\RenameUserCompleteHook::class, ExtHook\RenameUserCompleteHook::class );
		class_alias( CoreHook\RenameUserPreRenameHook::class, ExtHook\RenameUserPreRenameHook::class );
		class_alias( CoreHook\RenameUserSQLHook::class, ExtHook\RenameUserSQLHook::class );
		class_alias( CoreHook\RenameUserWarningHook::class, ExtHook\RenameUserWarningHook::class );
		class_alias( Core\RenameuserSQL::class, Ext\RenameuserSQL::class );
	}
}
