<?php
/**
 * Test file
 *
 * @phpcs:disable Squiz.Commenting
 * @phpcs:disable WordPress.Files.FileName
 * @phpcs:disable WordPress.NamingConventions
 * @phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
 */

// Class
class GuestbookPageCest {


	public function _before( AcceptanceTester $I ) {
	}


	public function CreateGuestbookPage( AcceptanceTester $I ) {
		$I->wantTo( 'create a guestbook page with [comment-guestbook] shortcode' );
		$pageId = $I->createGuestbookPage();
		$I->amOnGuestbookPage( $pageId );
		$I->see( 'Guestbook', 'h1' );
		$I->see( 'before the shortcode', '.post-inner' );
		$I->dontSee( '[comment-guestbook]' );
	}

}
