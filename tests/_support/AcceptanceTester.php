<?php
/**
 * Comment Guestbook specific Acceptance testing actions
 *
 * @phpcs:disable Squiz.Commenting
 * @phpcs:disable WordPress.Files.FileName
 * @phpcs:disable WordPress.NamingConventions
 * @phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
 */

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor {

	use _generated\AcceptanceTesterActions;


	/**
	 * Define custom actions here
	 */
	public function createGuestbookPage( bool $commentStatus = true, array $cliOptions = array() ): int {
		$I      = $this;
		$ret    = $I->cliToString(
			array_merge(
				array(
					'post',
					'create',
					'--post_type=page',
					'--post_title=Guestbook',
					'--post_content=<p>before the shortcode</p>[comment-guestbook]<p>after the shortcode</p>',
					'--post_status=publish',
					'--comment_status=' . ( $commentStatus ? 'open' : 'closed' ),
					'--porcelain',
				),
				$cliOptions
			)
		);
		$pageId = intval( $ret );
		return $pageId;
	}


	public function logout() {
		$I = $this;
		$I->amOnAdminPage( '/' );
		$I->tryToClick( 'Log Out', '#wpadminbar' );
	}


	public function amOnGuestbookPage( int $pageId ) {
		$I = $this;
		$I->amOnPage( '/?page_id=' . $pageId );
	}


	public function amOnGuestbookOptions( string $section ) {
		$I = $this;
		$I->loginAsAdmin();
		$I->amOnAdminPage( 'options-general.php?page=cgb_admin_options&tab=' . $section );
		$I->see( 'Comment Guestbook Settings', 'h2' );
	}


	public function allowGuestbookComments( string $pageId ) {
		$I = $this;
		// set all options to allow comments on the guestbook page
		$I->setPageCommentStatus( $pageId, true );
		$I->updateWPOption( 'comment_registration', '' );
		$I->updateWPOption( 'comment_moderation', '' );
		$I->updateWPOption( 'comment_whitelist', '' );
	}


	public function changeGuestbookOption( string $section, string $input_type, string $option, string $newValue ) {
		$I = $this;
		$I->amOnGuestbookOptions( $section );
		$verify = null;
		switch ( $input_type ) {
			case 'checkbox':
				$selector = '#' === substr( $option, 0, 1 ) ? $option : '#' . $option;
				if ( empty( $newValue ) ) {
					$I->uncheckOption( $selector );
					$verify = function () use ( &$I, $selector, $newValue ) {
						$I->dontSeeCheckboxIsChecked( $selector );
					};
				} else {
					$I->checkOption( $selector );
					$verify = function() use ( &$I, $selector, $newValue ) {
						$I->seeCheckboxIsChecked( $selector );
					};
				}
				break;
			case 'radio':
				$selector = 'input[name=' . $option . ']';
				$I->selectOption( $selector, $newValue );
				$verify = function() use ( &$I, $selector, $newValue ) {
					$I->seeOptionIsSelected( $selector, $newValue );
				};
				break;
			case 'text':
				$selector = '#' === substr( $option, 0, 1 ) ? $option : '#' . $option;
				$I->fillField( $selector, $newValue );
				$verify = function() use ( &$I, $selector, $newValue ) {
					$I->seeInField( $selector, $newValue );
				};
		}
		$I->click( 'form[name=cgb-' . $section . '-settings] input[type=submit]' );
		$I->see( 'Settings saved.', '#setting-error-settings_updated' );
		$verify();
	}


	public function setPageCommentStatus( int $pageId, $status ) {
		$I = $this;
		$I->cli(
			array(
				'post',
				'update',
				$pageId,
				'--comment_status=' . ( $status ? 'open' : 'closed' ),
			)
		);
	}


	public function addGuestbookComment( int $pageId, string $comment = 'hello guestbook page', string $author = '', string $email = '', string $url = '', bool $setCookie = false ) {
		$I = $this;
		$I->amOnGuestbookPage( $pageId );
		$I->seeCommentForm( $pageId );
		$I->fillField( '#comment', $comment );
		'' !== $author ? $I->fillField( '#author', $author ) : null;
		'' !== $email ? $I->fillField( '#email', $email ) : null;
		'' !== $url ? $I->fillField( '#url', $url ) : null;
		$setCookie ? $I->checkOption( '#wp-comment-cookies-consent' ) : null;
		$I->click( 'form#commentform input[type=submit]' );
	}


	public function createGuestbookComment( int $pageId, string $comment, string $author = '', string $email = '', string $url = '', array $cliOptions = array() ): int {
		$I         = $this;
		$ret       = $I->cliToString(
			array_merge(
				array(
					'comment',
					'create',
					'--comment_post_ID=' . $pageId,
					'--comment_content=' . $comment,
					'--comment_author=' . $author,
					'--comment_author_email=' . $email,
					'--comment_author_url=' . $url,
					'--porcelain',
				),
				$cliOptions
			)
		);
		$commentId = intval( $ret );
		return $commentId;
	}


	public function createGuestbookComments( int $pageId, int $numComments, string $author = '', string $email = '', string $url = '' ): array {
		$I        = $this;
		$comments = array_fill( 1, $numComments, null );
		foreach ( $comments as $n => &$comment ) {
			$comment['content'] = 'Comment ' . $n . ' ' . uniqid();
			$comment['id']      = $I->createGuestbookComment( $pageId, $comment['content'], $author, $email, $url, array( '--comment_date=' . gmdate( 'Y-m-d H:i:s', time() - 86400 * ( $numComments - $n ) ) ) );
		}
		unset( $comment );
		return $comments;
	}


	public function deleteGuestbookComment( string $comment_content ) {
		$I = $this;
		$I->cli( array( 'db', 'query', 'DELETE FROM wp_comments WHERE comment_content="' . addslashes( $comment_content ) . '"' ) );
	}


	public function deleteAllComments() {
		$I   = $this;
		$ids = $I->cliToString( array( 'comment', 'list', '--format=ids' ) );
		$I->cli( array( 'comment', 'delete', $ids ) );
	}


	public function updateWPOption( string $option_name, string $option_value ) {
		$I = $this;
		$I->cli( array( 'option', 'update', $option_name, $option_value ) );
	}


	public function seeCommentForm( int $pageId ) {
		$I = $this;
		$I->amOnGuestbookPage( $pageId );
		$I->seeElement( 'form#commentform' );
	}


	public function seeCommentFormInPageArea( int $pageId ) {
		$I = $this;
		$I->amOnGuestbookPage( $pageId );
		$I->seeElement( 'div.entry-content form#commentform' );
	}


	public function dontSeeCommentFormInPageArea( int $pageId ) {
		$I = $this;
		$I->amOnGuestbookPage( $pageId );
		$I->dontSeeElement( 'div.entry-content form#commentform' );
	}


	public function seeCommentInPage( string $comment, int $pageId = null ) {
		$I = $this;
		if ( $pageId ) {
			$I->amOnGuestbookPage( $pageId );
		}
		$I->see( $comment, '#comments .comment-content' );
	}


	public function dontSeeCommentInPage( string $comment, int $pageId = null ) {
		$I = $this;
		if ( $pageId ) {
			$I->amOnGuestbookPage( $pageId );
		}
		$I->dontSee( $comment, '#comments .comment-content' );
	}


	public function seeCommentInAdminArea( string $comment, string $status ) {
		$I = $this;
		$I->loginAsAdmin();
		$I->amOnAdminPage( 'edit-comments.php' . ( '' === $status ? '' : '?comment_status=' . $status ) );
		$I->see( $comment, '.comment' );
	}

}
