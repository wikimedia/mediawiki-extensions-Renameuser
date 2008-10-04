<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A Special Page extension to rename users, runnable by users with renameuser
 * righs
 *
 * @addtogroup Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgAvailableRights[] = 'renameuser';
$wgGroupPermissions['bureaucrat']['renameuser'] = true;

$wgAvailableRights[] = 'renameuser-moverootuserpage';
$wgGroupPermissions['bureaucrat']['renameuser-moverootuserpage'] = true;

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Renameuser',
	'author'         => array( 'Ævar Arnfjörð Bjarmason', 'Aaron Schulz' ),
	'url'            => 'http://www.mediawiki.org/wiki/Extension:Renameuser',
	'description'    => 'Rename a user (need \'\'renameuser\'\' right)',
	'descriptionmsg' => 'renameuser-desc',
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$Rev$',
);

# Internationalisation file
$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['Renameuser'] = $dir . 'SpecialRenameuser.i18n.php';
$wgExtensionAliasesFiles['Renameuser'] = $dir . 'SpecialRenameuser.alias.php';

/**
 * The maximum number of edits a user can have and still be allowed renaming,
 * set it to 0 to disable the limit.
 */
define( 'RENAMEUSER_CONTRIBLIMIT', 2000000 );
define( 'RENAMEUSER_CONTRIBJOB', 10000 );
/**
 * If you do not want to disallow the move of root userpages, set this to false in your LocalSettings.php
 */
$wgRenameUserCheckRootUserpageMoves = true;

# Add a new log type
global $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
$wgLogTypes[]                          = 'renameuser';
$wgLogNames['renameuser']              = 'renameuserlogpage';
$wgLogHeaders['renameuser']            = 'renameuserlogpagetext';
#$wgLogActions['renameuser/renameuser'] = 'renameuserlogentry';
$wgLogActionsHandlers['renameuser/renameuser'] = 'wfRenameUserLogActionText'; // deal with old breakage

function wfRenameUserLogActionText( $type, $action, $title = NULL, $skin = NULL, $params = array(), $filterWikilinks=false ) {
	if( !$title || $title->getNamespace() !== NS_USER ) {
		$rv = ''; // handled in comment, the old way
	} else {
		$titleLink = $skin ? 
			$skin->makeLinkObj( $title, htmlspecialchars( $title->getPrefixedText() ) ) : $title->getText();
		# Add title to params
		array_unshift( $params, $titleLink );
		$rv = wfMsgReal( 'renameuserlogentry', $params );
	}
	return $rv;
}

$wgAutoloadClasses['SpecialRenameuser'] = dirname( __FILE__ ) . '/SpecialRenameuser_body.php';
$wgAutoloadClasses['RenameuserHooks'] = dirname( __FILE__ ) . '/SpecialRenameuser_body.php';
$wgAutoloadClasses['RenameUserJob'] = dirname(__FILE__) . '/RenameUserJob.php';

function wfRenameuserSetup () {
	global $wgRenameUserCheckRootUserpageMoves, $wgHooks;
	if ( $wgRenameUserCheckRootUserpageMoves )
		$wgHooks['AbortMove'][] = 'RenameuserHooks::isValidMove';
}

$wgExtensionFunctions[] = 'wfRenameuserSetup';

$wgSpecialPages['Renameuser'] = 'SpecialRenameuser';
$wgSpecialPageGroups['Renameuser'] = 'users';
$wgJobClasses['renameUser'] = 'RenameUserJob';

