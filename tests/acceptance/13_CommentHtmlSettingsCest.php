<?php
/**
 * Test file
 *
 * @phpcs:disable Squiz.Commenting
 * @phpcs:disable WordPress.Files.FileName
 * @phpcs:disable WordPress.NamingConventions
 * @phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
 * @phpcs:disable Generic.Commenting.DocComment.MissingShort
 */

// Class
class CommentHtmlSettingsCest {


	public function _before( AcceptanceTester $I ) {
	}


	public function DefaultValues( AcceptanceTester $I ) {
		$I->wantTo( 'check default values of the comment list options' );
		$I->amOnGuestbookSettings( 'comment_html' );
		$I->seeInFormFields(
			'form[name=cgb-comment_html-settings]',
			[
				// TODO: add other settings
				'cgb_comment_callback' => '',  // empty value seem to work here
			]
		);
	}

}
