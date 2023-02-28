<?php

namespace MediaWiki\Extension\Renameuser;

use MediaWiki\RenameUser as Core;
use MediaWiki\RenameUser\Hook as CoreHook;

class RenameUserSetup {
	public static function setup() {
		// This extension has moved to core. Define b/c class aliases.
		class_alias( CoreHook\RenameUserAbortHook::class,
			'MediaWiki\Extension\Renameuser\Hook\RenameUserAbortHook' );
		class_alias( CoreHook\RenameUserCompleteHook::class,
			'MediaWiki\Extension\Renameuser\Hook\RenameUserCompleteHook' );
		class_alias( CoreHook\RenameUserPreRenameHook::class,
			'MediaWiki\Extension\Renameuser\Hook\RenameUserPreRenameHook' );
		class_alias( CoreHook\RenameUserSQLHook::class,
			'MediaWiki\Extension\Renameuser\Hook\RenameUserSQLHook' );
		class_alias( CoreHook\RenameUserWarningHook::class,
			'MediaWiki\Extension\Renameuser\Hook\RenameUserWarningHook' );
		class_alias( Core\RenameuserSQL::class,
			'MediaWiki\Extension\Renameuser\RenameuserSQL' );
	}
}
