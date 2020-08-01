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
				'cgb_clist_threaded'         => 'default',
				'cgb_clist_order'            => 'default',
				'cgb_clist_child_order_desc' => '',
				'cgb_clist_default_page'     => 'default',
				'cgb_clist_pagination'       => 'default',
				'cgb_clist_per_page'         => '0',
				'cgb_clist_show_all'         => '',
				'cgb_clist_num_pagination'   => '',
				'cgb_clist_title'            => '',
				'cgb_clist_in_page_content'  => '',
				'cgb_comment_callback'       => '',  // empty value seem to work here
				'cgb_clist_styles'           => '',
				'cgb_clist_args'             => '',
			)
		);
	}


	public function CListThreaded( AcceptanceTester $I ) {
		$I->wantTo( 'test "Theaded comment list" (cgb_clist_threaded)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateWPOption( 'cgb_adjust_output', '1' );
		$parentComment   = 'Parent comment ' . uniqid();
		$childComment    = 'Child comment ' . uniqid();
		$parentCommentId = $I->createGuestbookComment( $gbPageId, $parentComment, 'testuser', 'user@test.at' );
		$childCommentId  = $I->createGuestbookComment( $gbPageId, $childComment, 'testuser', 'user@test.at', '', array( '--comment_parent=' . $parentCommentId ) );
		// Check default option with WordPress setting enabled
		$I->updateWPOption( 'thread_comments', '1' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $parentComment, '.comment' );
		$I->see( $childComment, '.comment .children .comment' );
		// Check default option with WordPress setting disabled
		$I->updateWPOption( 'thread_comments', '0' );
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
		$I->updateWPOption( 'thread_comments', '1' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( $childComment, '.comment' );
		$I->dontSee( $childComment, '.comment .children .comment' );
	}


	public function CListOrder( AcceptanceTester $I ) {
		$I->wantTo( 'test "Comment list order" (cgb_clist_order)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateWPOption( 'cgb_adjust_output', '1' );
		$firstComment    = 'First comment ' . uniqid();
		$secondComment   = 'Second comment ' . uniqid();
		$firstCommentId  = $I->createGuestbookComment( $gbPageId, $firstComment, 'testuser', 'user@test.at', '', array( '--comment_date=' . gmdate( 'Y-m-d H:i:s', time() - 86400 ) ) );
		$secondCommentId = $I->createGuestbookComment( $gbPageId, $secondComment, 'testuser', 'user@test.at' );
		// Check default option with WordPress setting oldest first
		$I->updateWPOption( 'comment_order', 'asc' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $firstCommentId . '~#li-comment-' . $secondCommentId );
		// Check default option with WordPress setting newest first
		$I->updateWPOption( 'comment_order', 'desc' );
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $secondCommentId . '~#li-comment-' . $firstCommentId );
		// Check with oldest first
		$I->changeGuestbookOption( 'comment_list', 'radio', 'cgb_clist_order', 'asc' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $firstCommentId . '~#li-comment-' . $secondCommentId );
		// Check with newest first
		$I->changeGuestbookOption( 'comment_list', 'radio', 'cgb_clist_order', 'desc' );
		$I->updateWPOption( 'comment_order', 'asc' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $secondCommentId . '~#li-comment-' . $firstCommentId );
	}


	public function CListChildOrderDesc( AcceptanceTester $I ) {
		$I->wantTo( 'test "Comment list child order" (cgb_clist_child_order_desc)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateWPOption( 'cgb_adjust_output', '1' );
		$parentComment   = 'Parent comment ' . uniqid();
		$firstComment    = 'First comment ' . uniqid();
		$secondComment   = 'Second comment ' . uniqid();
		$parentCommentId = $I->createGuestbookComment( $gbPageId, $parentComment, 'testuser', 'user@test.at' );
		$firstCommentId  = $I->createGuestbookComment( $gbPageId, $firstComment, 'testuser', 'user@test.at', '', array( '--comment_parent=' . $parentCommentId, '--comment_date=' . gmdate( 'Y-m-d H:i:s', time() - 86400 ) ) );
		$secondCommentId = $I->createGuestbookComment( $gbPageId, $secondComment, 'testuser', 'user@test.at', '', array( '--comment_parent=' . $parentCommentId ) );
		// Check with option disabled (oldest first)
		$I->changeGuestbookOption( 'comment_list', 'checkbox', 'cgb_clist_child_order_desc', '' );
		$I->logout();
		// Check with comment list order default
		$I->updateWPOption( 'cgb_clist_order', 'default' );
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $firstCommentId . '~#li-comment-' . $secondCommentId );
		// Check with comment list order asc
		$I->updateWPOption( 'cgb_clist_order', 'asc' );
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $firstCommentId . '~#li-comment-' . $secondCommentId );
		// Check with comment list order desc
		$I->updateWPOption( 'cgb_clist_order', 'desc' );
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $firstCommentId . '~#li-comment-' . $secondCommentId );

		// Check with option enabled (newest first)
		$I->changeGuestbookOption( 'comment_list', 'checkbox', 'cgb_clist_child_order_desc', '1' );
		$I->logout();
		// Check with comment list order default
		$I->updateWPOption( 'cgb_clist_order', 'default' );
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $secondCommentId . '~#li-comment-' . $firstCommentId );
		// Check with comment list order asc
		$I->updateWPOption( 'cgb_clist_order', 'asc' );
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $secondCommentId . '~#li-comment-' . $firstCommentId );
		// Check with comment list order desc
		$I->updateWPOption( 'cgb_clist_order', 'desc' );
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $secondCommentId . '~#li-comment-' . $firstCommentId );
	}


	public function CListPage( AcceptanceTester $I ) {
		$I->wantTo( 'test "Comment list page options" (cgb_clist_default_page, cgb_clist_pagination, cgb_clist_per_page, cgb_clist_show_all, cgb_clist_num_pagination)' );
		$gbPageId = $I->createGuestbookPage();
		$I->allowGuestbookComments( $gbPageId );
		$I->updateWPOption( 'cgb_adjust_output', '1' );
		$numComments = 21;
		$comments    = $I->createGuestbookComments( $gbPageId, $numComments, 'testuser', 'user@test.at' );
		// Check standard plugin and standard WP settings
		$I->updateWPOption( 'default_comments_page', 'newest' );
		$I->updateWPOption( 'page_comments', '' );
		$I->updateWPOption( 'comments_per_page', 10 ); // not the default WP setting, but better for testing
		$I->updateWPOption( 'comment_order', 'asc' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		// Check that all comments are visible and ordered from old to new
		$I->seeElement( '#li-comment-' . $comments[1]['id'] . '~#li-comment-' . $comments[21]['id'] );
		// Check WP paged setting
		$I->updateWPOption( 'page_comments', '1' );
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $comments[21]['id'] );
		$I->dontSeeElement( '#li-comment-' . $comments[1]['id'] . ',#li-comment-' . $comments[20]['id'] );
		// Check WP default page setting newest
		$I->updateWPOption( 'default_comments_page', 'oldest' );
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $comments[1]['id'] . '~#li-comment-' . $comments[10]['id'] );
		$I->dontSeeElement( '#li-comment-' . $comments[21]['id'] . ',#li-comment-' . $comments[11]['id'] );
		// Check WP comment order
		$I->updateWPOption( 'comment_order', 'desc' );
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $comments[10]['id'] . '~#li-comment-' . $comments[1]['id'] );
		$I->dontSeeElement( '#li-comment-' . $comments[21]['id'] . ',#li-comment-' . $comments[11]['id'] );

		// Check cgb_clist_default_page
		$I->changeGuestbookOption( 'comment_list', 'radio', 'cgb_clist_default_page', 'last' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $comments[21]['id'] );
		$I->dontSeeElement( '#li-comment-' . $comments[1]['id'] . ',#li-comment-' . $comments[20]['id'] );
		$I->changeGuestbookOption( 'comment_list', 'radio', 'cgb_clist_default_page', 'first' );
		$I->updateWPOption( 'default_comments_page', 'newest' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $comments[10]['id'] . '~#li-comment-' . $comments[1]['id'] );
		$I->dontSeeElement( '#li-comment-' . $comments[21]['id'] . ',#li-comment-' . $comments[11]['id'] );

		// Check cgb_clist_pagination and cgb_clist_per_page
		$I->changeGuestbookOption( 'comment_list', 'radio', 'cgb_clist_pagination', 'false' );
		$I->changeGuestbookOption( 'comment_list', 'text', 'cgb_clist_per_page', '15' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $comments[21]['id'] . '~#li-comment-' . $comments[1]['id'] );
		$I->changeGuestbookOption( 'comment_list', 'radio', 'cgb_clist_pagination', 'true' );
		$I->updateWPOption( 'page_comments', '' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->seeElement( '#li-comment-' . $comments[15]['id'] . '~#li-comment-' . $comments[1]['id'] );
		$I->dontSeeElement( '#li-comment-' . $comments[16]['id'] . ',#li-comment-' . $comments[21]['id'] );

		// Check cgb_clist_num_pagination
		$I->changeGuestbookOption( 'comment_list', 'checkbox', 'cgb_clist_num_pagination', '' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( 'Newer comments', '.nav-next > a' );
		$I->dontSee( '1', '.page-numbers' );
		$I->changeGuestbookOption( 'comment_list', 'checkbox', 'cgb_clist_num_pagination', '1' );
		$I->logout();
		$I->amOnGuestbookPage( $gbPageId );
		$I->see( 'Newer comments', '.pagination > .next.page-numbers' );
		$I->see( '1', '.pagination > .page-numbers.current' );
		$I->see( '2', '.pagination > .page-numbers' );
		$I->dontSee( '3', '.page-numbers' );
	}

}
