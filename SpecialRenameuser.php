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
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

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
define( 'RENAMEUSER_CONTRIBLIMIT', 5000 );

function wfSpecialRenameuser() {
	global $IP, $wgMessageCache, $wgHooks;
	
	$wgMessageCache->addMessages(
		array(
			'renameuser' => 'Rename user',
			'renameuserold' => 'Current username: ',
			'renameusernew' => 'New username: ',
			'renameusersubmit' => 'Submit',
			
			'renameusererrordoesnotexist' => 'The username "<nowiki>$1</nowiki>" does not exist',
			'renameusererrorexists' => 'The username "<nowiki>$1</nowiki>" already exits',
			'renameusererrorinvalid' => 'The username "<nowiki>$1</nowiki>" is invalid',
			'renameusererrortoomany' => 'The user "<nowiki>$1</nowiki>" has $2 contributions, renaming a user with more ' .
							'than $3 contributions could adversely affect site performance',
			'renameusersuccess' => 'The user "<nowiki>$1</nowiki>" has been renamed to "<nowiki>$2</nowiki>"',
			
			'renameuserlogpage' => 'User rename log',
			'renameuserlogpagetext' => 'This is a log of changes to user names',
			'renameuserlog' => 'Renamed the user "[[User:$1|$1]]" (which had $3 edits) to "[[User:$2|$2]]"',
		)
	);

	$wgHooks['LogPageValidTypes'][] = 'wfSpecialRenameuserAddLogType';
	$wgHooks['LogPageLogName'][] = 'wfSpecialRenameuserAddLogName';
	$wgHooks['LogPageLogHeader'][] = 'wfSpecialRenameuserAddLogHeader';

	require_once( "$IP/includes/SpecialPage.php" );
	class Renameuser extends SpecialPage {
		function Renameuser() {
			SpecialPage::SpecialPage('Renameuser', 'renameuser');
		}
		
		function execute() {
			global $wgOut, $wgUser, $wgTitle, $wgRequest, $wgContLang, $wgLang;
			global $wgVersion, $wgMaxNameChars;

			$fname = 'Renameuser::execute';

			$this->setHeaders();

			if ( ! $wgUser->isAllowed( 'renameuser' ) ) {
				$wgOut->permissionRequired( 'renameuser' );
				return;
			}

			if ( wfReadOnly() ) {
				$wgOut->readOnlyPage();
				return;
			}

			if ( version_compare( $wgVersion, '1.5beta2', '<' ) ) {
				$wgOut->versionRequired( '1.5beta2' );
				return;
			}
			
			$oldusername = $wgContLang->ucfirst( strtr( trim( $wgRequest->getText( 'oldusername' ) ), '_', ' ' ) );
			$newusername = $wgContLang->ucfirst( strtr( trim( $wgRequest->getText( 'newusername' ) ), '_', ' ' ) );

			$action = $wgTitle->escapeLocalUrl();
			$renameuserold = wfMsgHtml( 'renameuserold' );
			$renameusernew = wfMsgHtml( 'renameusernew' );
			$submit = wfMsgHtml( 'renameusersubmit' );
			$token = $wgUser->editToken();

			$wgOut->addHTML( "
<form id='renameuser' method='post' action=\"$action\">
	<table>
		<tr>
			<td align='right'>$renameuserold</td>
			<td align='left'><input tabindex='1' type='text' size='20' name='oldusername' value=\"" . htmlspecialchars($oldusername) . "\" /></td>
		</tr>
		<tr>
			<td align='right'>$renameusernew</td>
			<td align='left'><input tabindex='1' type='text' size='20' name='newusername' value=\"" . htmlspecialchars($newusername) . "\"/></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td align='right'><input type='submit' name='submit' value=\"$submit\" /></td>
		</tr>
	</table>
	<input type='hidden' name='token' value='$token' />
</form>");
			// Sanity checks
			if ( !$wgRequest->wasPosted() || !$wgUser->matchEditToken( $wgRequest->getVal( 'token' ) ) ) 
				return;

			if ($oldusername == '' || $newusername == '' || $oldusername == $newusername)
				return;
			
			$wgOut->addHTML( '<hr />' );
			
			$olduser = User::newFromName( $oldusername );
			$newuser = User::newFromName( $newusername );

			// It won't be an object if for instance "|" is supplied as a value
			if ( !is_object( $newuser ) || !is_object( $newuser ) ) {
				$wgOut->addWikiText( wfMsg( 'renameusererrorinvalid', $newusername ) );
				return;
			}
			
			$uid = $olduser->idForName();
			if ($uid == 0) {
				$wgOut->addWikiText( wfMsg( 'renameusererrordoesnotexist', $oldusername ) );
				return;
			}
			
			if ($newuser->idForName() != 0) {
				$wgOut->addWikiText( wfMsg( 'renameusererrorexists', $newusername ) );
				return;
			}

			// Check edit count
			$contribs = User::edits( $uid );
			if ( RENAMEUSER_CONTRIBLIMIT != 0 && $contribs > RENAMEUSER_CONTRIBLIMIT ) {
				$wgOut->addWikiText(
					wfMsg( 'renameusererrortoomany',
						$oldusername,
						$wgLang->formatNum( $contribs ),
						$wgLang->formatNum( RENAMEUSER_CONTRIBLIMIT )
					)
				);
				return;
			}

			$rename = new RenameuserSQL( $oldusername, $newusername, $uid );
			$rename->rename();
			
			$log = new LogPage( 'renameuser' );
			$log->addEntry( 'renameuser', $wgTitle, wfMsg( 'renameuserlog', $oldusername, $newusername, $contribs ) );
			
			$wgOut->addWikiText( wfMsg( 'renameusersuccess', $oldusername, $newusername ) );
		}
	}

	class RenameuserSQL {
		
		/**
		  * The old username
		  *
		  * @var string 
		  * @access private
		  */
		var $old;
		
		/**
		  * The new username
		  *
		  * @var string
		  * @access private
		  */
		var $new;
		
		/**
		  * The user ID
		  *
		  * @var integer
		  * @access private
		  */
		var $uid;

		/**
		  * The the tables => fields to be updated
		  *
		  * @var array
		  * @access private
		  */
		var $tables;

		/**
		 * Constructor
		 *
		 * @param string $old The old username
		 * @param string $new The new username
		 */
		function RenameuserSQL($old, $new, $uid) {
			$this->old = $old;
			$this->new = $new;
			$this->uid = $uid;

			$this->tables = array(
				// 1.5 schema
				'user' => 'user_name',
				'revision' => 'rev_user_text',
				'archive' => 'ar_user_text',
				'image' => 'img_user_text',
				'oldimage' => 'oi_user_text',
				'recentchanges' => 'rc_user_text'
			);
		}

		/**
		 * Do the rename operation
		 */
		function rename() {
			global $wgMemc, $wgDBname;
			
			$fname = 'RenameuserSQL::rename';
			
			wfProfileIn( $fname );
			
			$dbw =& wfGetDB( DB_MASTER );

			foreach ($this->tables as $table => $field) {
				$dbw->update( $table,
					array( $field => $this->new ),
					array( $field => $this->old ),
					$fname,
					array( $dbw->lowPriorityOption() )
				);
			}

			# Update user_touched and clear user cache
			$dbw->update( 'user', 
				array( 'user_touched' => $dbw->timestamp() ), 
				array( 'user_name' => $this->new ),
				$fname
		  	);
			
			$wgMemc->delete( "$wgDBname:user:id:{$this->uid}" );

			wfProfileOut( $fname );
		}
	}
	SpecialPage::addPage( new Renameuser );
}

function wfSpecialRenameuserAddLogType( &$types ) {
	if ( !in_array( 'renameuser', $types ) )
		$types[] = 'renameuser';
}

function wfSpecialRenameuserAddLogName( &$names ) {
	$names['renameuser'] = 'renameuserlogpage';
}

function wfSpecialRenameuserAddLogHeader( &$headers ) {
	$headers['renameuser'] = 'renameuserlogpagetext';
}
