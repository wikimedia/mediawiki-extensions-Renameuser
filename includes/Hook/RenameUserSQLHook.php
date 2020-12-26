<?php

/**
 * This is a hook handler interface, see docs/Hooks.md.
 * Use the hook name "RenameUserSQL" to register handlers implementing this interface.
 *
 * @stable to implement
 * @ingroup Hooks
 * @since 1.36
 */
interface RenameUserSQLHook {

	public function onRenameUserSQL( RenameuserSQL $renameUserSql ) : void;

}
