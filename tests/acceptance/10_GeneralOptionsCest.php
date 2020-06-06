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
class GeneralOptionsCest {


	public function _before( AcceptanceTester $I ) {
	}


	public function DefaultValues( AcceptanceTester $I ) {
		$I->wantTo( 'check default values of the general options' );
		$I->amOnGuestbookOptions( 'general' );
		$I->seeInFormFields(
			'form[name=cgb-general-settings]',
			array(
				'cgb_ignore_comments_open'        => '1',
				'cgb_ignore_comment_registration' => '1',
				'cgb_ignore_comment_moderation'   => '',
				'cgb_adjust_output'               => '',
				'cgb_l10n_domain'                 => 'default',
			)
		);
	}


	public function GuestbookCommentStatus( AcceptanceTester $I ) {
		$I->wantTo( 'test "Guestbook comment status" (cgb_ignore_comments_open)' );
		$gbPageId     = $I->createGuestbookPage();
		$samplePageId = 2;
		$I->allowGuestbookComments( $gbPageId );
		$I->setPageCommentStatus( $gbPageId, false );
		$I->setPageCommentStatus( $samplePageId, false );
		$I->logout();
		// Check when enabled (default)
		// Comment on guestbook page shall be allowed
		$comment = 'test guestbook comment status (' . uniqid() . ')';
		$I->addGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		// Comments on other pages shall be forbidden
		$I->dontSeeCommentFormInPageArea( $samplePageId );
		// Change to disabled
		$I->setGuestbookOption( 'general', 'checkbox', '#cgb_ignore_comments_open', '' );
		// Check when disabled
		$I->dontSeeCommentFormInPageArea( $gbPageId );
	}


	public function GuestbookCommentRegistration( AcceptanceTester $I ) {
		$I->wantTo( 'test "Guestbook comment registration" (cgb_ignore_comment_registration)' );
		$gbPageId     = $I->createGuestbookPage();
		$samplePageId = 2;
		$I->allowGuestbookComments( $gbPageId );
		$I->setPageCommentStatus( $samplePageId, true );
		$I->cli( array( 'option', 'update', 'comment_registration', '1' ) );
		$I->logout();
		// Check when enabled (default)
		// Comment on guestbook page shall be possible
		$comment = 'test guestbook comment registration (' . uniqid() . ')';
		$I->addGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		// Comment on other pages shall not be allowed
		$I->dontSeeCommentFormInPageArea( $samplePageId );
		// Change to disabled
		$I->setGuestbookOption( 'general', 'checkbox', '#cgb_ignore_comment_registration', '' );
		$I->logout();
		// Check when disabled
		$I->dontSeeCommentFormInPageArea( $gbPageId );
		$I->seeElement( 'div.entry-content div#respond .must-log-in' );
	}


	public function GuestbookCommentModeration( AcceptanceTester $I ) {
		$I->wantTo( 'test "Guestbook comment moderation" (cgb_ignore_comment_moderation)' );
		$gbPageId     = $I->createGuestbookPage();
		$samplePageId = 2;
		$I->allowGuestbookComments( $gbPageId );
		$I->setPageCommentStatus( $samplePageId, true );
		$I->cli( array( 'option', 'update', 'comment_moderation', '1' ) );
		// Check when disabled (default)
		$I->logout();
		$comment = 'test guestbook comment moderation (' . uniqid() . ')';
		$I->addGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		$I->seeElement( '.comment-awaiting-moderation' );
		$I->seeCommentInAdminArea( $comment, 'moderated' );
		// Change to enabled
		$I->setGuestbookOption( 'general', 'checkbox', '#cgb_ignore_comment_moderation', '1' );
		// Delete created comment to avoid comment_flood error
		$I->cli( array( 'db', 'query', 'DELETE FROM wp_comments WHERE comment_approved=0' ) );
		// Check when enabled
		// Comment on other page shall require moderation
		$I->logout();
		$comment = 'test comment moderation on sample page (' . uniqid() . ')';
		$I->addGuestbookComment( $samplePageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		$I->seeElement( '.comment-awaiting-moderation' );
		$I->seeCommentInAdminArea( $comment, 'moderated' );
		// Delete created comment to avoid comment_flood error
		$I->cli( array( 'db', 'query', 'DELETE FROM wp_comments WHERE comment_approved=0' ) );
		// Comment on guestbook page shall be allowed without moderation
		$I->logout();
		$comment = 'a second test of guestbook comment moderation (' . uniqid() . ')';
		$I->addGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		$I->dontSeeElement( '.comment-awaiting-moderation' );
		$I->seeCommentInAdminArea( $comment, 'approved' );
	}


	// public function CommentsAdjustment( AcceptanceTester $I ) {
	// 	$I->wantTo( 'test "Comments adjustment" (cgb_adjust_output)' );
	// 	$I->see( 'This test is not implemented yet!' );
	// 	// TODO: implementation missing
	// }

	// public function DomainForTranslation( AcceptanceTester $I ) {
	// 	$I->wantTo( 'test "Domain for translation" (cgb_l10n_domain)' );
	// 	$I->see( 'This test is not implemented yet!' );
	// 	// TODO: implementation missing
	// }

}