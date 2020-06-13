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
				'cgb_form_require_no_name_mail' => '',
				'cgb_form_remove_mail'          => '',
				'cgb_form_remove_website'       => '',
				'cgb_form_comment_label'        => 'default',
				'cgb_form_title'                => 'default',
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
		$comment = 'guestbook comment ' . uniqid();
		$I->createGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		$I->dontSeeElement( '.commentlist + #respond' );
		// Check when enabled
		$I->changeGuestbookOption( 'comment_form', 'checkbox', 'cgb_form_below_comments', '1' );
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
		$comment = 'guestbook comment ' . uniqid();
		$I->createGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		$I->dontSeeElement( '#respond + .commentlist' );
		// Check when enabled
		$I->changeGuestbookOption( 'comment_form', 'checkbox', 'cgb_form_above_comments', '1' );
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
		$comment = 'guestbook comment ' . uniqid();
		$I->createGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
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


	public function FormRequireNoNameMail( AcceptanceTester $I ) {
		$I->wantTo( 'test "Form comment author name/email requirement" (cgb_form_require_no_name_mail)' );
		$gbPageId     = $I->createGuestbookPage();
		$samplePageId = 2;
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		$I->setPageCommentStatus( $samplePageId, true );
		// Check when disabled (default)
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( 'input#author[required=required]' );
		$I->seeElement( 'input#email[required=required]' );
		$comment = 'guestbook comment ' . uniqid();
		$I->createGuestbookComment( $gbPageId, $comment );
		$I->dontSeeCommentInPage( $comment );
		// Check when enabled
		$I->changeGuestbookOption( 'comment_form', 'checkbox', 'cgb_form_require_no_name_mail', '1' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->dontSeeElement( 'input#author[required=required]' );
		$I->dontSeeElement( 'input#email[required=required]' );
		$comment = 'guestbook comment ' . uniqid();
		$I->createGuestbookComment( $gbPageId, $comment );
		$I->seeCommentInPage( $comment );
		// Check other page
		$I->amOnGuestbookPage( $samplePageId );
		$I->seeElement( 'input#author[required=required]' );
		$I->seeElement( 'input#email[required=required]' );
		$comment = 'sample page comment (' . uniqid() . ')';
		$I->createGuestbookComment( $samplePageId, $comment );
		$I->dontSeeCommentInPage( $comment );
	}


	public function FormRemoveEmail( AcceptanceTester $I ) {
		$I->wantTo( 'test "Remove email field" (cgb_form_remove_mail)' );
		$gbPageId     = $I->createGuestbookPage();
		$samplePageId = 2;
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		$I->setPageCommentStatus( $samplePageId, true );
		// Check when disabled (default)
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( 'input#email[required=required]' );
		$comment = 'guestbook comment ' . uniqid();
		$I->createGuestbookComment( $gbPageId, $comment, 'testuser' );
		$I->dontSeeCommentInPage( $comment );
		// Check when enabled
		$I->changeGuestbookOption( 'comment_form', 'checkbox', 'cgb_form_remove_mail', '1' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->dontSeeElement( 'input#email' );
		$comment = 'guestbook comment ' . uniqid();
		$I->createGuestbookComment( $gbPageId, $comment, 'testuser' );
		$I->seeCommentInPage( $comment );
		// Check other page
		$I->amOnGuestbookPage( $samplePageId );
		$I->seeElement( 'input#email[required=required]' );
		$comment = 'sample page comment (' . uniqid() . ')';
		$I->createGuestbookComment( $samplePageId, $comment, 'testuser' );
		$I->dontSeeCommentInPage( $comment );
	}


	public function FormRemoveWebsite( AcceptanceTester $I ) {
		$I->wantTo( 'test "Remove website field" (cgb_form_remove_website)' );
		$gbPageId     = $I->createGuestbookPage();
		$samplePageId = 2;
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		$I->setPageCommentStatus( $samplePageId, true );
		// Check when disabled (default)
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( 'input#url' );
		// Check when enabled
		$I->changeGuestbookOption( 'comment_form', 'checkbox', 'cgb_form_remove_website', '1' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->dontSeeElement( 'input#url' );
		// Check other page
		$I->amOnGuestbookPage( $samplePageId );
		$I->seeElement( 'input#url' );
	}


	public function FormCommentLabel( AcceptanceTester $I ) {
		$I->wantTo( 'test "Label for comment field" (cgb_form_comment_label)' );
		$gbPageId     = $I->createGuestbookPage();
		$samplePageId = 2;
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		$I->setPageCommentStatus( $samplePageId, true );
		// Check when enabled
		$label = 'Custom Label ' . uniqid();
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_comment_label', $label );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $label, 'label[for=comment]' );
		// Check other page
		$I->amOnGuestbookPage( $samplePageId );
		$I->dontSee( $label, 'label[for=comment]' );
	}


	public function FormTitle( AcceptanceTester $I ) {
		$I->wantTo( 'test "Comment form title" (cgb_form_title)' );
		$gbPageId     = $I->createGuestbookPage();
		$samplePageId = 2;
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		$I->setPageCommentStatus( $samplePageId, true );
		// Check when enabled
		$label = 'Custom title ' . uniqid();
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_title', $label );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $label, '#reply-title' );
		// Check other page
		$I->amOnGuestbookPage( $samplePageId );
		$I->dontSee( $label, '#reply-title' );
	}


	public function FormTitleReply( AcceptanceTester $I ) {
		$I->wantTo( 'test "Comment form title reply" (cgb_form_title_reply_to)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		$comment = 'guestbook comment ' . uniqid();
		$I->createGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		// Check when enabled
		$label = 'Custom title ' . uniqid();
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_title_reply_to', $label );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->dontSee( $label, '#reply-title' );
		$I->click( '.reply > .comment-reply-link' );
		$I->see( $label, '#reply-title' );
	}


	public function FormNotes( AcceptanceTester $I ) {
		$I->wantTo( 'test "Notes before and after the form fields" (cgb_form_notes_before, cgb_form_notes_after)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		// Check when enabled
		$before = 'Notes before form fields ' . uniqid();
		$after  = 'Notes after form fields ' . uniqid();
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_notes_before', '<div>' . $before . '</div>' );
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_notes_after', '<div>' . $after . '</div>' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $before, '#commentform > div' );
		$I->see( $after, '#commentform > div' );
	}


	public function FormLabelSubmit( AcceptanceTester $I ) {
		$I->wantTo( 'test "Label of submit button" (cgb_form_label_submit)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		// Check when enabled
		$label = 'Submit button label ' . uniqid();
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_label_submit', $label );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( 'form#commentform input#submit[value="' . $label . '"]' );
	}


	public function FormLabelCancelReply( AcceptanceTester $I ) {
		$I->wantTo( 'test "Label for cancel reply link" (cgb_form_cancel_reply)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		$comment = 'guestbook comment ' . uniqid();
		$I->createGuestbookComment( $gbPageId, $comment, 'testuser', 'user@test.at' );
		$I->seeCommentInPage( $comment );
		// Check when enabled
		$label = 'Cancel reply label ' . uniqid();
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_cancel_reply', $label );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->click( '.reply > .comment-reply-link' );
		$I->see( $label, '#cancel-comment-reply-link' );
	}


	public function FormMustLoginMessage( AcceptanceTester $I ) {
		$I->wantTo( 'test "Must login message" (cgb_form_must_login_message)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		$I->updateGuestbookOption( 'cgb_ignore_comment_registration', '' );
		$I->updateGuestbookOption( 'comment_registration', '1' );
		// Check when enabled
		$message = 'Must login message ' . uniqid();
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_must_login_message', '<div>' . $message . '</div>' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $message, '#respond div' );
	}


	public function FormStyles( AcceptanceTester $I ) {
		$I->wantTo( 'test "Comment form styles" (cgb_form_styles)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		// Check when enabled
		$styles = '#commentform { color:aquamarine; font-family:' . uniqid() . '; }';
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_styles', $styles );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $styles, '.entry-content > style' );
	}


	public function FormArgs( AcceptanceTester $I ) {
		$I->wantTo( 'test "Comment form args" (cgb_form_args)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		// Check when enabled
		$formId = 'form-id-' . uniqid();
		$I->changeGuestbookOption( 'comment_form', 'text', 'cgb_form_args', 'array("id_form" => "' . $formId . '")' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->dontSeeElement( '#commentform' );
		$I->seeElement( '#' . $formId );
	}

}
