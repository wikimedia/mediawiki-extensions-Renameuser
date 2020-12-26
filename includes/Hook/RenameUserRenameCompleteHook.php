<?php

/**
 * This is a hook handler interface, see docs/Hooks.md.
 * Use the hook name "RenameUserRenameComplete" to register handlers implementing this interface.
 *
 * @stable to implement
 * @ingroup Hooks
 * @since 1.36
 */
interface RenameUserRenameCompleteHook {

	/**
	 * @param int $uid The user ID
	 * @param string $old The old username
	 * @param string $new The new username
	 */
	public function onRenameUserRenameCompleteHook( int $uid, string $old, string $new ) : void;

}
