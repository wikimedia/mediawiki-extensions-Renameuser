<?php

/**
 * This is a hook handler interface, see docs/Hooks.md.
 * Use the hook name "RenameUserAbort" to register handlers implementing this interface.
 *
 * @stable to implement
 * @ingroup Hooks
 * @since 1.36
 */
interface RenameUserAbortHook {

	/**
	 * Allows the renaming to be aborted.
	 *
	 * @param int $uid
	 * @param string $old
	 * @param string $new
	 *
	 * @return bool|void
	 */
	public function onRenameUserAbort( int $uid, string $old, string $new );

}
