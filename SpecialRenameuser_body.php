<?php

class Renameuser extends SpecialPage {
	function Renameuser() {
		SpecialPage::SpecialPage('Renameuser', 'renameuser');
	}
	
	function execute() {
		global $wgOut, $wgUser, $wgTitle, $wgRequest, $wgContLang, $wgLang;
		global $wgVersion, $wgMaxNameChars;

		$fname = 'Renameuser::execute';

		$this->setHeaders();

		if ( !$wgUser->isAllowed( 'renameuser' ) ) {
			$wgOut->permissionRequired( 'renameuser' );
			return;
		}

		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}

		if ( version_compare( $wgVersion, '1.7.0', '<' ) ) {
			$wgOut->versionRequired( '1.7.0' );
			return;
		}
		
		$oldusername = Title::newFromText( $wgRequest->getText( 'oldusername' ) );
		$newusername = Title::newFromText( $wgRequest->getText( 'newusername' ) );

		$action = $wgTitle->escapeLocalUrl();
		$renameuserold = wfMsgHtml( 'renameuserold' );
		$renameusernew = wfMsgHtml( 'renameusernew' );
		$movepages = wfMsgHtml( 'renameusermove' );
		$oun = is_object( $oldusername ) ? $oldusername->getText() : '';
		$nun = is_object( $newusername ) ? $newusername->getText() : '';
		$submit = wfMsgHtml( 'renameusersubmit' );
		$token = $wgUser->editToken();

		$wgOut->addHTML( "
<!-- Current contributions limit is " . RENAMEUSER_CONTRIBLIMIT . " -->
<form id='renameuser' method='post' action=\"$action\">
<table>
	<tr>
		<td align='right'>$renameuserold </td>
		<td align='left'><input tabindex='1' type='text' size='20' name='oldusername' value=\"$oun\" /></td>
	</tr>
	<tr>
		<td align='right'>$renameusernew </td>
		<td align='left'><input tabindex='2' type='text' size='20' name='newusername' value=\"$nun\"/></td>
	</tr>" );
		if ( $wgUser->isAllowed( 'move' ) && version_compare( $wgVersion, '1.9alpha', '>=' ) ) {
			$wgOut->addHTML( "
	<tr>
		<td>&nbsp;</td>
		<td>
			<input tabindex='3' type='checkbox' name='movepages' id='movepages' checked='checked' />
			<label for='movepages'>$movepages</label>
		</td>
	</tr>" );
		}

		$wgOut->addHTML( "
	<tr>
		<td>&nbsp;</td>
		<td><input type='submit' name='submit' value=\"$submit\" /></td>
	</tr>
</table>
<input type='hidden' name='token' value='$token' />
</form>");
		// Sanity checks
		if ( !$wgRequest->wasPosted() || !$wgUser->matchEditToken( $wgRequest->getVal( 'token' ) ) ) 
			return;

		if ( !is_object( $oldusername ) || !is_object( $newusername ) || $oldusername->getText() == $newusername->getText() )
			return;
		
		$wgOut->addHTML( '<hr />' );
		
		// Suppress username validation of old username
		$olduser = User::newFromName( $oldusername->getText(), false );
		$newuser = User::newFromName( $newusername->getText() );

		// It won't be an object if for instance "|" is supplied as a value
		if ( !is_object( $olduser ) ) {
			$wgOut->addWikiText( wfMsg( 'renameusererrorinvalid', $oldusername->getText() ) );
			return;
		}

		if ( !is_object( $newuser ) ) {
			$wgOut->addWikiText( wfMsg( 'renameusererrorinvalid', $newusername->getText() ) );
			return;
		}
		
		$uid = $olduser->idForName();
		if ($uid == 0) {
			$wgOut->addWikiText( wfMsg( 'renameusererrordoesnotexist', $oldusername->getText() ) );
			return;
		}
		
		if ($newuser->idForName() != 0) {
			$wgOut->addWikiText( wfMsg( 'renameusererrorexists', $newusername->getText() ) );
			return;
		}

		// Check edit count
		if ( !$wgUser->isAllowed( 'siteadmin' ) ) {
			$contribs = User::edits( $uid );
			if ( RENAMEUSER_CONTRIBLIMIT != 0 && $contribs > RENAMEUSER_CONTRIBLIMIT ) {
				$wgOut->addWikiText(
					wfMsg( 'renameusererrortoomany',
						$oldusername->getText(),
						$wgLang->formatNum( $contribs ),
						$wgLang->formatNum( RENAMEUSER_CONTRIBLIMIT )
					)
				);
				return;
			}
		}

		$rename = new RenameuserSQL( $oldusername->getText(), $newusername->getText(), $uid );
		$rename->rename();

		$log = new LogPage( 'renameuser' );
		$log->addEntry( 'renameuser', $oldusername, wfMsgForContent( 'renameuserlog', $oldusername->getText(), $newusername->getText(), $wgContLang->formatNum( $contribs ) ) );

		$wgOut->addWikiText( wfMsg( 'renameusersuccess', $oldusername->getText(), $newusername->getText() ) );

		if ( $wgRequest->getCheck( 'movepages' ) && $wgUser->isAllowed( 'move' ) && version_compare( $wgVersion, '1.9alpha', '>=' ) ) {
			$dbr =& wfGetDB( DB_SLAVE );
			$pages = $dbr->select(
				'page',
				array( 'page_namespace', 'page_title' ),
				array(
					'page_namespace IN (' . NS_USER . ',' . NS_USER_TALK . ')',
					'(page_title LIKE "' . $dbr->escapeLike( $oldusername->getDbKey() . '/' ) . '%" OR page_title = "' . $oldusername->getDbKey() . '")'
				),
				__METHOD__
			);

			$output = '';
			$skin =& $wgUser->getSkin();
			while ( $row = $dbr->fetchObject( $pages ) ) {
				$oldPage = Title::makeTitleSafe( $row->page_namespace, $row->page_title );
				$newPage = Title::makeTitleSafe( $row->page_namespace, preg_replace( '!^[^/]+!', $newusername->getDbKey(), $row->page_title ) );
				if ( $newPage->exists() && !$oldPage->isValidMoveTarget( $newPage ) ) {
					$link = $skin->makeKnownLinkObj( $oldPage );
					$output .= '<li>' . wfMsgHtml( 'renameuser-page-exists', $link ) . '</li>';
				} else {
					$success = $oldPage->moveTo( $newPage, false, wfMsg( 'renameuser-move-log', $oldusername->getText(), $newusername->getText() ) );
					if( $success === true ) {
						$oldLink = $skin->makeKnownLinkObj( $oldPage );
						$newLink = $skin->makeKnownLinkObj( $newPage );
						$output .= '<li>' . wfMsgHtml( 'renameuser-page-moved', $oldLink, $newLink ) . '</li>';
					} else {
						$oldLink = $skin->makeKnownLinkObj( $oldPage );
						$newLink = $skin->makeLinkObj( $newPage );
						$output .= '<li>' . wfMsgHtml( 'renameuser-page-unmoved', $oldLink, $newLink ) . '</li>';
					}
				}
			}
			if( $output )
				$wgOut->addHtml( '<ul>' . $output . '</ul>' );
		}
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
			'image' => 'img_user_text',
			'oldimage' => 'oi_user_text',

			// Very hot table, causes lag and deadlocks to update like this
			/*'recentchanges' => 'rc_user_text'*/
		);
		
		global $wgRenameUserQuick;
		if( !$wgRenameUserQuick )
			$this->tables['archive'] = 'ar_user_text';
		
	}

	/**
	 * Do the rename operation
	 */
	function rename() {
		global $wgMemc, $wgDBname, $wgAuth;
		
		$fname = 'RenameuserSQL::rename';
		
		wfProfileIn( $fname );
		
		$dbw =& wfGetDB( DB_MASTER );

		foreach ($this->tables as $table => $field) {
			$dbw->update( $table,
				array( $field => $this->new ),
				array( $field => $this->old ),
				$fname
				#,array( $dbw->lowPriorityOption() )
			);
		}

		$dbw->update( 'user', 
			array( 'user_touched' => $dbw->timestamp() ), 
			array( 'user_name' => $this->new ),
			$fname
		);
		
		// Clear the user cache
		$wgMemc->delete( "$wgDBname:user:id:{$this->uid}" );
		
		// Inform authentication plugin of the change
		$user = User::newFromId( $this->uid );
		$wgAuth->updateExternalDB( $user );

		wfProfileOut( $fname );
	}
}

?>
