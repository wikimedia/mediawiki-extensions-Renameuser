<?php
if ( !defined( 'MEDIAWIKI' ) ) die();
/**
 * A Special Page extension to rename users, runnable by users with renameuser
 * rights
 *
 * @file
 * @ingroup Extensions
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgAvailableRights[] = 'renameuser';
$wgGroupPermissions['bureaucrat']['renameuser'] = true;

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'Renameuser',
	'author'         => array( 'Ævar Arnfjörð Bjarmason', 'Aaron Schulz' ),
	'url'            => 'https://www.mediawiki.org/wiki/Extension:Renameuser',
	'descriptionmsg' => 'renameuser-desc',
);

# Internationalisation files
$wgExtensionMessagesFiles['Renameuser'] = __DIR__ . '/Renameuser.i18n.php';
$wgExtensionMessagesFiles['RenameuserAliases'] = __DIR__ . '/Renameuser.alias.php';

/**
 * Users with more than this number of edits will have their rename operation
 * deferred via the job queue.
 */
define( 'RENAMEUSER_CONTRIBJOB', 5000 );

# Add a new log type
$wgLogTypes[] = 'renameuser';
$wgLogActionsHandlers['renameuser/renameuser'] = 'RenameuserLogFormatter';
$wgAutoloadClasses['SpecialRenameuser'] = __DIR__ . '/Renameuser_body.php';
$wgAutoloadClasses['RenameuserLogFormatter'] = __DIR__ . '/Renameuser_body.php';
$wgAutoloadClasses['RenameUserJob'] = __DIR__ . '/RenameUserJob.php';
$wgSpecialPages['Renameuser'] = 'SpecialRenameuser';
$wgSpecialPageGroups['Renameuser'] = 'users';
$wgJobClasses['renameUser'] = 'RenameUserJob';

$wgHooks['ShowMissingArticle'][] = 'wfRenameUserShowLog';
$wgHooks['ContributionsToolLinks'][] = 'wfRenameuserOnContribsLink';

/**
 * Show a log if the user has been renamed and point to the new username.
 * Don't show the log if the $oldUserName exists as a user.
 *
 * @param $article Article
 * @return bool
 */
function wfRenameUserShowLog( $article ) {
	global $wgOut;
	$title = $article->getTitle();
	$oldUser = User::newFromName( $title->getBaseText() );
	if ( ($title->getNamespace() == NS_USER || $title->getNamespace() == NS_USER_TALK ) && ($oldUser && $oldUser->isAnon() )) {
		// Get the title for the base userpage
		$page = Title::makeTitle( NS_USER, str_replace( ' ', '_', $title->getBaseText() ) )->getPrefixedDBkey();
		LogEventsList::showLogExtract( $wgOut, 'renameuser', $page, '', array( 'lim' => 10, 'showIfEmpty' => false,
			'msgKey' => array( 'renameuser-renamed-notice', $title->getBaseText() ) ) );
	}
	return true;
}

/**
 * @param $id
 * @param $nt Title
 * @param $tools
 * @return bool
 */
function wfRenameuserOnContribsLink( $id, $nt, &$tools ) {
	global $wgUser;

	if ( $wgUser->isAllowed( 'renameuser' ) && $id ) {
		$tools[] = Linker::link(
			SpecialPage::getTitleFor( 'Renameuser' ),
			wfMessage( 'renameuser-linkoncontribs' )->text(),
			array( 'title' => wfMessage( 'renameuser-linkoncontribs-text' )->parse() ),
			array( 'oldusername' => $nt->getText() )
		);
	}
	return true;
}
