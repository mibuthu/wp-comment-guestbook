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
class CommentListSettingsCest {


	public function _before( AcceptanceTester $I ) {
	}


	public function DefaultValues( AcceptanceTester $I ) {
		$I->wantTo( 'check default values of the comment form options' );
		$I->amOnGuestbookOptions( 'comment_list' );
		$I->seeInFormFields(
			'form[name=cgb-comment_list-settings]',
			array(
				'cgb_clist_threaded'        => 'default',
				'cgb_clist_order'           => 'default',
				'cgb_clist_child_order'     => 'default',
				'cgb_clist_default_page'    => 'default',
				'cgb_clist_pagination'      => 'default',
				'cgb_clist_per_page'        => '0',
				'cgb_clist_show_all'        => '',
				'cgb_clist_num_pagination'  => '',
				'cgb_clist_title'           => '',
				'cgb_clist_in_page_content' => '',
				'cgb_comment_callback'      => '',  // empty value seem to work here
				'cgb_clist_styles'          => '',
				'cgb_clist_args'            => '',
			)
		);
	}


	public function CListThreaded( AcceptanceTester $I ) {
		$I->wantTo( 'test "Theaded comment list" (cgb_clist_threaded)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateGuestbookOption( 'cgb_adjust_output', '1' );
		$parentComment   = 'Parent comment ' . uniqid();
		$childComment    = 'Child comment ' . uniqid();
		$parentCommentId = $I->createGuestbookComment( $gbPageId, $parentComment, 'testuser', 'user@test.at' );
		$childCommentId  = $I->createGuestbookComment( $gbPageId, $childComment, 'testuser', 'user@test.at', '', array( '--comment_parent=' . $parentCommentId ) );
		// Check default option with WordPress setting enabled
		$I->cli( array( 'option', 'update', 'thread_comments', '1' ) );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $parentComment, '.comment' );
		$I->see( $childComment, '.comment .children .comment' );
		// Check default option with WordPress setting disabled
		$I->cli( array( 'option', 'update', 'thread_comments', '0' ) );
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $childComment, '.comment' );
		$I->dontSee( $childComment, '.comment .children .comment' );
		// Check when enabled
		$I->changeGuestbookOption( 'comment_list', 'radio', 'cgb_clist_threaded', 'enabled' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $childComment, '.comment .children .comment' );
		// Check when disabled
		$I->changeGuestbookOption( 'comment_list', 'radio', 'cgb_clist_threaded', 'disabled' );
		$I->cli( array( 'option', 'update', 'thread_comments', '1' ) );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $childComment, '.comment' );
		$I->dontSee( $childComment, '.comment .children .comment' );
	}

}
