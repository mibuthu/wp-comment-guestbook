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
class CommentFormSettingsCest {


	public function _before( AcceptanceTester $I ) {
	}


	public function DefaultValues( AcceptanceTester $I ) {
		$I->wantTo( 'check default values of the comment form options' );
		$I->amOnGuestbookOptions( 'comment_form' );
		$I->seeInFormFields(
			'form[name=cgb-comment_form-settings]',
			array(
				'cgb_form_below_comments'       => '',
				'cgb_form_above_comments'       => '',
				'cgb_form_in_page'              => '1',
				'cgb_form_expand_type'          => 'false',
				'cgb_form_expand_link_text'     => 'Add a new guestbook entry',  // without translation due to english by default.
				'cgb_add_cmessage'              => '',
				'cgb_form_require_no_name_mail' => '',
				'cgb_form_remove_mail'          => '',
				'cgb_form_remove_website'       => '',
				'cgb_form_comment_label'        => 'default',
				'cgb_form_title_reply'          => 'default',
				'cgb_form_title_reply_to'       => 'default',
				'cgb_form_notes_before'         => 'default',
				'cgb_form_notes_after'          => 'default',
				'cgb_form_label_submit'         => 'default',
				'cgb_form_cancel_reply'         => 'default',
				'cgb_form_must_login_message'   => 'default',
				'cgb_form_styles'               => '',
				'cgb_form_args'                 => '',
			)
		);
	}


	public function FormBelowComments( AcceptanceTester $I ) {
		$I->wantTo( 'test "Form below comments" (cgb_form_below_comments)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		// Check when disabled (default)
		$I->logout();
		$comment = 'guestbook comment (' . uniqid() . ')';
		$I->addGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		$I->dontSeeElement( '.commentlist + #respond' );
		// Change to enabled
		$I->changeGuestbookOption( 'comment_form', 'checkbox', 'cgb_form_below_comments', '1' );
		// Check when enabled
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeCommentInPage( $comment );
		$I->seeElement( '.commentlist + #respond' );
	}


	public function FormAboveComments( AcceptanceTester $I ) {
		$I->wantTo( 'test "Form above comments" (cgb_form_above_comments)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		// Check when disabled (default)
		$I->logout();
		$comment = 'guestbook comment (' . uniqid() . ')';
		$I->addGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		$I->dontSeeElement( '#respond + .commentlist' );
		// Change to enabled
		$I->changeGuestbookOption( 'comment_form', 'checkbox', 'cgb_form_above_comments', '1' );
		// Check when enabled
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeCommentInPage( $comment );
		$I->seeElement( '#respond + .commentlist' );
	}


	public function FormInPage( AcceptanceTester $I ) {
		$I->wantTo( 'test "Form in page" (cgb_form_in_page)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		// Check when enabled (default)
		$I->logout();
		$comment = 'guestbook comment (' . uniqid() . ')';
		$I->addGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		$I->seeCommentFormInPageArea( $gbPageId );
		// Check when also form above comments is enabled -> no form inside page shall be displayed
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		$I->updateGuestbookOption( 'cgb_form_above_comments', '1' );
		$I->dontSeeCommentFormInPageArea( $gbPageId );
		// Change to enabled
		$I->changeGuestbookOption( 'comment_form', 'checkbox', 'cgb_form_in_page', '' );
		$I->updateGuestbookOption( 'cgb_adjust_output', '' );
		$I->updateGuestbookOption( 'cgb_form_above_comments', '' );
		// Check when enabled
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeCommentInPage( $comment );
		$I->dontSeeCommentFormInPageArea( $gbPageId );
	}


	public function FormExpand( AcceptanceTester $I ) {
		$I->wantTo( 'test "Form the form expand type and link text" (cgb_form_expand_type, cgb_form_expand_link_text)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		// Check not collapsed form (default)
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->dontSeeElement( '#show-form-1' );
		$I->dontSee( 'Add a new guestbook entry', 'a' );
		$I->dontSeeElement( '.entry-content > style' );
		// Change to 'static'
		$I->changeGuestbookOption( 'comment_form', 'radio', 'cgb_form_expand_type', 'static' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#show-form-1' );
		$I->seeLink( 'Add a new guestbook entry' );
		$I->see( 'div.form-wrapper { display:none; }', '.entry-content > style' );
		// Change to 'animated' and change link text
		$I->changeGuestbookOption( 'comment_form', 'radio', 'cgb_form_expand_type', 'animated' );
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_expand_link_text', 'Link to show the comment form' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#show-form-1' );
		$I->seeLink( 'Link to show the comment form' );
		$I->see( 'transition:transform', '.entry-content > style' );
	}

}
