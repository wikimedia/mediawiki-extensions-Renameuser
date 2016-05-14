<?php

/**
 * Special page that allows authorised users to rename
 * user accounts
 */
class SpecialRenameuser extends FormSpecialPage {
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
	 */
	public function execute( $par ) {
		$this->setHeaders();
		$this->checkPermissions();
		$this->checkReadOnly();
		$this->useTransactionalTimeLimit();

		parent::execute( $par );
	}

	protected function getFormFields() {
		$user = $this->getUser();

		$formDescriptor = [
			'oldusername' => [
				'type' => 'user',
				'name' => 'oldusername',
				'label-message' => 'renameuserold',
			],

			'newusername' => [
				'type' => 'text',
				'name' => 'newusername',
				'label-message' => 'renameusernew',
			],

			'reason' => [
				'type' => 'text',
				'name' => 'reason',
				'label-message' => 'renameuserreason',
				'maxlength' => 255,
			],
		];

		if ( $user->isAllowed( 'move' ) ) {
			$formDescriptor['movepages'] = [
				'type' => 'check',
				'name' => 'movepages',
				'label-message' => 'renameusermove',
			];
		}

		if ( $user->isAllowed( 'suppressredirect' ) ) {
			$formDescriptor['suppressredirect'] = [
				'type' => 'check',
				'name' => 'suppressredirect',
				'label-message' => 'renameusersuppress',
			];
		}

		$formDescriptor['confirm'] = [
			'type' => 'check',
			'name' => 'confirm',
			'label-message' => 'renameuserconfirm',
		];

		return $formDescriptor;
	}

	public function alterForm( HTMLForm $form ) {
		$form->setSubmitTextMsg( 'renameusersubmit' );
	}

	public function onSubmit( array $data ) {
		$status = Status::newGood();

		$oldUserName = $data['oldusername'];
		$newUserName = $data['newusername'];
		$oldUserObj = User::newFromName( $oldUserName, false );
		$newUserObj = User::newFromName( $newUserName, 'creatable' );

		if ( $oldUserObj === false ) {
			$status->fatal( 'renameusererrorinvalid', $oldUserName );
		} elseif ( $oldUserObj->getId() === 0 ) {
			$status->fatal( 'renameusererrordoesnotexist', $oldUserName );
		} elseif ( $oldUserObj->isBlocked() &&
			$oldUserObj->getBlock()->getExpiry() == 'infinity' ) {
				$status->fatal( 'renameusererrorblocked', $oldUserName );
		}

		if ( $newUserObj === false || User::isIP( $newUserName ) ||
			!User::isValidUserName( $newUserName )
		) {
			$status->fatal( 'renameusererrorinvalid', $newUserName );
		} elseif ( $newUserObj->getId() !== 0 ) {
			if ( $oldUserObj !== false && $oldUserObj->getName() == $newUserObj->getName() ) {
				$status->fatal( 'renameuser-error-same-user' );
			} else {
				$status->fatal( 'renameusererrorexists', $newUserName );
			}
		}

		if ( $oldUserName && $newUserName ) {
			$warnings = [];
			Hooks::run( 'RenameUserWarning', [ $oldUserName, $newUserName, &$warnings ] );

			foreach ( $warnings as $warning ) {
				call_user_func_array( [ $status, 'fatal' ], $warning );
			}
		}

		if ( !$data['confirm'] ) {
			$status->fatal( 'renameusererrorconfirm' );
		}

		if ( !$status->isGood() ) {
			return $status;
		}

		$user = $this->getUser();
		$userID = $oldUserObj->getId();
		$output = '';

		// Give other affected extensions a chance to validate or abort
		$reason = 'hookaborted';
		if ( !Hooks::run( 'RenameUserAbort',
			[ $userID, $oldUserName, $newUserName, &$reason ] )
		) {
			return Status::newFatal( $reason );
		}

		// Do the heavy lifting...
		$rename = new RenameuserSQL(
			$oldUserName,
			$newUserName,
			$userID,
			$user,
			[ 'reason' => $reason ]
		);
		if ( !$rename->rename() ) {
			return Status::newFatal( 'renameuser-error-request' );
		}

		// If this user is renaming his/herself, make sure that Title::moveTo()
		// doesn't make a bunch of null move edits under the old name!
		if ( $user->getId() === $userID ) {
			$user->setName( $newUserName );
		}

		if ( $data['movepages'] && $user->isAllowed( 'move' ) ) {
			$dbr = wfGetDB( DB_SLAVE );
			$oldUserTitle = $oldUserObj->getUserPage();
			$newUserTitle = $newUserObj->getUserPage();

			$pages = $dbr->select(
				'page',
				[ 'page_namespace', 'page_title' ],
				[
					'page_namespace IN (' . NS_USER . ',' . NS_USER_TALK . ')',
					'(page_title ' . $dbr->buildLike( $oldUserTitle->getDBkey() . '/', $dbr->anyString() ) .
					' OR page_title = ' . $dbr->addQuotes( $oldUserTitle->getDBkey() ) . ')'
				],
				__METHOD__
			);

			$suppressRedirect = $data['suppressredirect'] && $user->isAllowed( 'suppressredirect' );

			foreach ( $pages as $row ) {
				$oldPage = Title::makeTitleSafe( $row->page_namespace, $row->page_title );
				$newPage = Title::makeTitleSafe( $row->page_namespace,
					preg_replace( '!^[^/]+!', $newUserTitle->getDBkey(), $row->page_title ) );

				# Do not autodelete or anything, title must not exist
				if ( $newPage->exists() && !$oldPage->isValidMoveTarget( $newPage ) ) {
					$link = Linker::linkKnown( $newPage );
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
							$oldUserName,
							$newUserName )->inContentLanguage()->text(),
						!$suppressRedirect
					);
					if ( $success === true ) {
						# oldPage is not known in case of redirect suppression
						$oldLink = Linker::link( $oldPage, null, [], [ 'redirect' => 'no' ] );

						# newPage is always known because the move was successful
						$newLink = Linker::linkKnown( $newPage );

						$output .= Html::rawElement(
							'li',
							[ 'class' => 'mw-renameuser-pm' ],
							$this->msg( 'renameuser-page-moved' )->rawParams( $oldLink, $newLink )->escaped()
						);
					} else {
						$oldLink = Linker::linkKnown( $oldPage );
						$newLink = Linker::link( $newPage );
						$output .= Html::rawElement(
							'li', [ 'class' => 'mw-renameuser-pu' ],
							$this->msg( 'renameuser-page-unmoved' )->rawParams( $oldLink, $newLink )->escaped()
						);
					}
				}
			}
		}

		$out = $this->getOutput();
		$out->addBacklinkSubtitle( $this->getPageTitle() );

		// Output success message stuff :)
		$out->wrapWikiMsg( "<div class=\"successbox\">$1</div><br style=\"clear:both\" />",
			[ 'renameusersuccess', $oldUserName, $newUserName ] );

		if ( $output !== '' ) {
			$out->addHTML( Html::rawElement( 'ul', [], $output ) );
		}

		return Status::newGood();
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

	protected function getDisplayFormat() {
		return 'ooui';
	}

	protected function getGroupName() {
		return 'users';
	}
}
