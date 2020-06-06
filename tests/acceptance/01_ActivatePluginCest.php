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
class AcitvatePluginCest {

	private $pluginName = 'comment-guestbook';


	public function _before( AcceptanceTester $I ) {
	}


	public function activatePlugin( AcceptanceTester $I ) {
		$I->wantTo( 'activate the plugin' );
		$I->loginAsAdmin();
		$I->amOnPluginsPage();
		$I->seePluginInstalled( $this->pluginName );
		$I->activatePlugin( $this->pluginName );
		$I->seePluginActivated( $this->pluginName );
	}

}
