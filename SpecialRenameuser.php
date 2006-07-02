<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A Special Page extension to rename users, runnable by users with renameuser
 * righs
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgAvailableRights[] = 'renameuser';
$wgGroupPermissions['bureaucrat']['renameuser'] = true;

$wgExtensionFunctions[] = 'wfSpecialRenameuser';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Renameuser',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'url' => 'http://meta.wikimedia.org/wiki/Renameuser'
);
/**
 * The maximum number of edits a user can have and still be allowed renaming,
 * set it to 0 to disable the limit.
 */
//define( 'RENAMEUSER_CONTRIBLIMIT', 6800 );
define( 'RENAMEUSER_CONTRIBLIMIT', 20000 );

# Add a new log type
global $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
$wgLogTypes[]                          = 'renameuser';
$wgLogNames['renameuser']              = 'renameuserlogpage';
$wgLogHeaders['renameuser']            = 'renameuserlogpagetext';
$wgLogActions['renameuser/renameuser'] = 'renameuserlogentry';

# Register the special page
if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/SpecialRenameuser_body.php', 'Renameuser', 'Renameuser' );

function wfSpecialRenameuser() {
	global $wgMessageCache;

	$wgMessageCache->addMessages(
		array(
			'renameuser' => 'Rename user',
			'renameuserold' => 'Current username: ',
			'renameusernew' => 'New username: ',
			'renameusersubmit' => 'Submit',
			
			'renameusererrordoesnotexist' => 'The user "<nowiki>$1</nowiki>" does not exist',
			'renameusererrorexists' => 'The user "<nowiki>$1</nowiki>" already exits',
			'renameusererrorinvalid' => 'The username "<nowiki>$1</nowiki>" is invalid',
			'renameusererrortoomany' => 'The user "<nowiki>$1</nowiki>" has $2 contributions, renaming a user with more ' .
							'than $3 contributions could adversely affect site performance',
			'renameusersuccess' => 'The user "<nowiki>$1</nowiki>" has been renamed to "<nowiki>$2</nowiki>"',
			
			'renameuserlogpage' => 'User rename log',
			'renameuserlogpagetext' => 'This is a log of changes to user names',
			'renameuserlogentry' => '',
			'renameuserlog' => 'Renamed the user "[[User:$1|$1]]" (which had $3 edits) to "[[User:$2|$2]]"',
		)
	);
}
?>
