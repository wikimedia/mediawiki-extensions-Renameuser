<?php

/**
 * Special page that allows authorised users to rename
 * user accounts
 */
class SpecialRenameuser extends SpecialPage {
	public function __construct() {
		parent::__construct( 'Renameuser', 'renameuser' );
	}

	public function doesWrites() {
		return true;
	}

	/**
	 * Show the special page
	 *
	 * @param mixed $par Parameter passed to the page
	 * @throws PermissionsError
	 * @throws ReadOnlyError
	 * @throws UserBlockedError
	 */
	public function execute( $par ) {
		global $wgContLang, $wgCapitalLinks;

		$this->setHeaders();
		$this->addHelpLink( 'Help:Renameuser' );

		$out = $this->getOutput();
		$out->addWikiMsg( 'renameuser-summary' );

		$user = $this->getUser();
		if ( !$user->isAllowed( 'renameuser' ) ) {
			throw new PermissionsError( 'renameuser' );
		}

		if ( wfReadOnly() ) {
			throw new ReadOnlyError;
		}

		if ( $user->isBlocked() ) {
			throw new UserBlockedError( $this->getUser()->mBlock );
		}

		$this->useTransactionalTimeLimit();

		$request = $this->getRequest();
		$showBlockLog = $request->getBool( 'submit-showBlockLog' );
		$usernames = explode( '/', $par, 2 ); // this works as "/" is not valid in usernames
		$oldnamePar = trim( str_replace( '_', ' ', $request->getText( 'oldusername', $usernames[0] ) ) );
		$oldusername = Title::makeTitle( NS_USER, $oldnamePar );
		$newnamePar = isset( $usernames[1] ) ? $usernames[1] : null;
		$newnamePar = trim( str_replace( '_', ' ', $request->getText( 'newusername', $newnamePar ) ) );
		// Force uppercase of newusername, otherwise wikis
		// with wgCapitalLinks=false can create lc usernames
		$newusername = Title::makeTitleSafe( NS_USER, $wgContLang->ucfirst( $newnamePar ) );
		$oun = is_object( $oldusername ) ? $oldusername->getText() : '';
		$nun = is_object( $newusername ) ? $newusername->getText() : '';
		$token = $user->getEditToken();
		$reason = $request->getText( 'reason' );

		$move_checked = $request->getBool( 'movepages', !$request->wasPosted() );
		$suppress_checked = $request->getCheck( 'suppressredirect' );

		$warnings = [];
		if ( $oun && $nun && !$request->getCheck( 'confirmaction' ) ) {
			Hooks::run( 'RenameUserWarning', [ $oun, $nun, &$warnings ] );
		}

		$formDescriptor['oldusername'] = [
			'type' => 'user',
			'name' => 'oldusername',
			'id' => 'mw-input',
			'label' => $this->msg( 'renameuserold' )->text(),
			'size' => 20,
			'default' => $oun,
			'tabindex' => '1',
		];
		$formDescriptor['newusername'] = [
			'type' => 'text',
			'name' => 'newusername',
			'id' => 'mw-input',
			'label' => $this->msg( 'renameusernew' )->text(),
			'size' => 20,
			'default' => $nun,
			'tabindex' => '2',
		];
		$formDescriptor['reason'] = [
			'type' => 'text',
			'name' => 'reason',
			'id' => 'mw-input',
			'label' => $this->msg( 'renameuserreason' )->text(),
			'size' => 40,
			'default' => $reason,
			'tabindex' => '3',
			'maxlength' => 255,
		];

		if ( $user->isAllowed( 'move' ) ) {
			$formDescriptor['movepages'] = [
				'type' => 'check',
				'name' => 'movepages',
				'id' => 'movepages',
				'label-message' => 'renameusermove',
				'default' => $move_checked,
				'tabindex' => '4',
			];

			if ( $user->isAllowed( 'suppressredirect' ) ) {
				$formDescriptor['suppressredirect'] = [
					'type' => 'check',
					'name' => 'suppressredirect',
					'id' => 'suppressredirect',
					'label-message' => 'renameusersuppress',
					'default' => $suppress_checked,
					'tabindex' => '5',
				];
			}
		}

		if ( $warnings ) {
			$warningsHtml = [];
			foreach ( $warnings as $warning ) {
				$warningsHtml[] = is_array( $warning ) ?
					$this->msg( $warning[0] )->rawParams( array_slice( $warning, 1 ) )->escaped() :
					$this->msg( $warning )->escaped();
			}

			$out->addHTML( "
				<tr>
					<td class='mw-label'>" . $this->msg( 'renameuserwarnings' )->escaped() . "
					</td>
					<td class='mw-input'>" .
				'<ul class="error"><li>' .
				implode( '</li><li>', $warningsHtml ) . '</li></ul>' .
				'</td>
				</tr>'
			);
			$formDescriptor['confirmaction'] = [
				'type' => 'check',
				'name' => 'confirmaction',
				'id' => 'confirmaction',
				'label-message' => 'renameuserconfirm',
				'default' => false,
				'tabindex' => '6',
			];
		}

		$formDescriptor['submit-showBlockLog'] = [
			'type' => 'submit',
			'name' => 'submit-showBlockLog',
			'default' => $this->msg( 'renameuser-submit-blocklog' )->text(),
			'id' => 'submit-showBlockLog',
			'flags' => [ 'primary' ],
		];

		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$htmlForm
			->addHiddenField( 'token', $token )
			->setMethod( 'post' )
			->setAction( $this->getPageTitle()->getLocalURL() )
			->setId( 'renameuser' )
			->setSubmitTextMsg( 'renameusersubmit' )
			->setSubmitID( 'submit' )
			->setSubmitName( 'submit' )
			->setWrapperLegendMsg( 'renameuser' )
			->prepareForm()
			->displayForm( false );

		// Show block log if requested
		if ( $showBlockLog && is_object( $oldusername ) ) {
			$this->showLogExtract( $oldusername, 'block', $out );

			return;
		}

		if ( $request->getText( 'token' ) === '' ) {
			# They probably haven't even submitted the form, so don't go further.
			return;
		} elseif ( $warnings ) {
			# Let user read warnings
			return;
		} elseif ( !$request->wasPosted() || !$user->matchEditToken( $request->getVal( 'token' ) ) ) {
			$out->wrapWikiMsg( "<div class=\"errorbox\">$1</div>", 'renameuser-error-request' );

			return;
		} elseif ( !is_object( $oldusername ) ) {
			$out->wrapWikiMsg( "<div class=\"errorbox\">$1</div>",
				[ 'renameusererrorinvalid', $request->getText( 'oldusername' ) ] );

			return;
		} elseif ( !is_object( $newusername ) ) {
			$out->wrapWikiMsg( "<div class=\"errorbox\">$1</div>",
				[ 'renameusererrorinvalid', $request->getText( 'newusername' ) ] );

			return;
		} elseif ( $oldusername->getText() === $newusername->getText() ) {
			$out->wrapWikiMsg( "<div class=\"errorbox\">$1</div>", 'renameuser-error-same-user' );

			return;
		}

		// Suppress username validation of old username
		$olduser = User::newFromName( $oldusername->getText(), false );
		$newuser = User::newFromName( $newusername->getText(), 'creatable' );

		// It won't be an object if for instance "|" is supplied as a value
		if ( !is_object( $olduser ) ) {
			$out->wrapWikiMsg( "<div class=\"errorbox\">$1</div>",
				[ 'renameusererrorinvalid', $oldusername->getText() ] );

			return;
		}
		if ( !is_object( $newuser ) || !User::isCreatableName( $newuser->getName() ) ) {
			$out->wrapWikiMsg( "<div class=\"errorbox\">$1</div>",
				[ 'renameusererrorinvalid', $newusername->getText() ] );

			return;
		}

		// Check for the existence of lowercase oldusername in database.
		// Until r19631 it was possible to rename a user to a name with first character as lowercase
		if ( $oldusername->getText() !== $wgContLang->ucfirst( $oldusername->getText() ) ) {
			// oldusername was entered as lowercase -> check for existence in table 'user'
			$dbr = wfGetDB( DB_REPLICA );
			$uid = $dbr->selectField( 'user', 'user_id',
				[ 'user_name' => $oldusername->getText() ],
				__METHOD__ );
			if ( $uid === false ) {
				if ( !$wgCapitalLinks ) {
					$uid = 0; // We are on a lowercase wiki but lowercase username does not exists
				} else {
					// We are on a standard uppercase wiki, use normal
					$uid = $olduser->idForName();
					$oldusername = Title::makeTitleSafe( NS_USER, $olduser->getName() );
				}
			}
		} else {
			// oldusername was entered as upperase -> standard procedure
			$uid = $olduser->idForName();
		}

		if ( $uid === 0 ) {
			$out->wrapWikiMsg( "<div class=\"errorbox\">$1</div>",
				[ 'renameusererrordoesnotexist', $oldusername->getText() ] );

			return;
		}

		if ( $newuser->idForName() !== 0 ) {
			$out->wrapWikiMsg( "<div class=\"errorbox\">$1</div>",
				[ 'renameusererrorexists', $newusername->getText() ] );

			return;
		}

		// Give other affected extensions a chance to validate or abort
		if ( !Hooks::run(
			'RenameUserAbort',
			[ $uid, $oldusername->getText(), $newusername->getText() ]
		) ) {
			return;
		}

		// Do the heavy lifting...
		$rename = new RenameuserSQL(
			$oldusername->getText(),
			$newusername->getText(),
			$uid,
			$this->getUser(),
			[ 'reason' => $reason ]
		);
		if ( !$rename->rename() ) {
			return;
		}

		// If this user is renaming his/herself, make sure that Title::moveTo()
		// doesn't make a bunch of null move edits under the old name!
		if ( $user->getId() === $uid ) {
			$user->setName( $newusername->getText() );
		}

		// Move any user pages
		if ( $request->getCheck( 'movepages' ) && $user->isAllowed( 'move' ) ) {
			$dbr = wfGetDB( DB_REPLICA );

			$pages = $dbr->select(
				'page',
				[ 'page_namespace', 'page_title' ],
				[
					'page_namespace' => [ NS_USER, NS_USER_TALK ],
					$dbr->makeList( [
						'page_title ' . $dbr->buildLike( $oldusername->getDBkey() . '/', $dbr->anyString() ),
						'page_title = ' . $dbr->addQuotes( $oldusername->getDBkey() ),
					], LIST_OR ),
				],
				__METHOD__
			);

			$suppressRedirect = false;

			if ( $request->getCheck( 'suppressredirect' ) && $user->isAllowed( 'suppressredirect' ) ) {
				$suppressRedirect = true;
			}

			$output = '';
			$linkRenderer = $this->getLinkRenderer();
			foreach ( $pages as $row ) {
				$oldPage = Title::makeTitleSafe( $row->page_namespace, $row->page_title );
				$newPage = Title::makeTitleSafe( $row->page_namespace,
					preg_replace( '!^[^/]+!', $newusername->getDBkey(), $row->page_title ) );
				# Do not autodelete or anything, title must not exist
				if ( $newPage->exists() && !$oldPage->isValidMoveTarget( $newPage ) ) {
					$link = $linkRenderer->makeKnownLink( $newPage );
					$output .= Html::rawElement(
						'li',
						[ 'class' => 'mw-renameuser-pe' ],
						$this->msg( 'renameuser-page-exists' )->rawParams( $link )->escaped()
					);
				} else {
					$success = $oldPage->moveTo(
						$newPage,
						false,
						$this->msg(
							'renameuser-move-log',
							$oldusername->getText(),
							$newusername->getText() )->inContentLanguage()->text(),
						!$suppressRedirect
					);
					if ( $success === true ) {
						# oldPage is not known in case of redirect suppression
						$oldLink = $linkRenderer->makeLink( $oldPage, null, [], [ 'redirect' => 'no' ] );

						# newPage is always known because the move was successful
						$newLink = $linkRenderer->makeKnownLink( $newPage );

						$output .= Html::rawElement(
							'li',
							[ 'class' => 'mw-renameuser-pm' ],
							$this->msg( 'renameuser-page-moved' )->rawParams( $oldLink, $newLink )->escaped()
						);
					} else {
						$oldLink = $linkRenderer->makeKnownLink( $oldPage );
						$newLink = $linkRenderer->makeLink( $newPage );
						$output .= Html::rawElement(
							'li', [ 'class' => 'mw-renameuser-pu' ],
							$this->msg( 'renameuser-page-unmoved' )->rawParams( $oldLink, $newLink )->escaped()
						);
					}
				}
			}
			if ( $output ) {
				$out->addHTML( Html::rawElement( 'ul', [], $output ) );
			}
		}

		// Output success message stuff :)
		$out->wrapWikiMsg( "<div class=\"successbox\">$1</div><br style=\"clear:both\" />",
			[ 'renameusersuccess', $oldusername->getText(), $newusername->getText() ] );
	}

	/**
	 * @param Title $username
	 * @param string $type
	 * @param OutputPage &$out
	 */
	protected function showLogExtract( $username, $type, &$out ) {
		# Show relevant lines from the logs:
		$logPage = new LogPage( $type );
		$out->addHTML( Xml::element( 'h2', null, $logPage->getName()->text() ) . "\n" );
		LogEventsList::showLogExtract( $out, $type, $username->getPrefixedText() );
	}

	/**
	 * Return an array of subpages beginning with $search that this special page will accept.
	 *
	 * @param string $search Prefix to search for
	 * @param int $limit Maximum number of results to return (usually 10)
	 * @param int $offset Number of results to skip (usually 0)
	 * @return string[] Matching subpages
	 */
	public function prefixSearchSubpages( $search, $limit, $offset ) {
		if ( !class_exists( 'UserNamePrefixSearch' ) ) { // check for version 1.27
			return [];
		}
		$user = User::newFromName( $search );
		if ( !$user ) {
			// No prefix suggestion for invalid user
			return [];
		}
		// Autocomplete subpage as user list - public to allow caching
		return UserNamePrefixSearch::search( 'public', $search, $limit, $offset );
	}

	protected function getGroupName() {
		return 'users';
	}
}
