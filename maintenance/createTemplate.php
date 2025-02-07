<?php

namespace MediaWiki\Extension\DynamicPageList3\Maintenance;

use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\Maintenance\LoggedUpdateMaintenance
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

$IP ??= getenv( 'MW_INSTALL_PATH' ) ?: dirname( __DIR__, 3 );
require_once "$IP/maintenance/Maintenance.php";

class CreateTemplate extends LoggedUpdateMaintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Handle inserting DynamicPageList3\'s necessary template for content inclusion.' );
	}

	/**
	 * Get the unique update key for this logged update.
	 *
	 * @return string
	 */
	protected function getUpdateKey(): string {
		return 'dynamic-page-list-3-create-template';
	}

	/**
	 * Message to show that the update was done already and was just skipped
	 *
	 * @return string
	 */
	protected function updateSkippedMessage(): string {
		return 'Template already created.';
	}

	/**
	 * Handle inserting DynamicPageList3's necessary template for content inclusion.
	 *
	 * @return bool
	 */
	protected function doDBUpdates(): bool {
		$title = Title::newFromText( 'Template:Extension DPL' );

		// Make sure template does not already exist
		if ( !$title->exists() ) {
			$services = MediaWikiServices::getInstance();
			$wikiPageFactory = $services->getWikiPageFactory();
			$page = $wikiPageFactory->newFromTitle( $title );

			$updater = $page->newPageUpdater( User::newSystemUser( 'DynamicPageList3 extension' ) );
			$content = $page->getContentHandler()->makeContent( '<noinclude>This page was automatically created. It serves as an anchor page for all \'\'\'[[Special:WhatLinksHere/Template:Extension_DPL|invocations]]\'\'\' of [https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:DynamicPageList3 Extension:DynamicPageList3].</noinclude>', $title );
			$updater->setContent( SlotRecord::MAIN, $content );
			$comment = CommentStoreComment::newUnsavedComment( 'Autogenerated DynamicPageList3\'s necessary template for content inclusion.' );

			$updater->saveRevision(
				$comment,
				EDIT_NEW | EDIT_FORCE_BOT
			);
		}

		return true;
	}
}

$maintClass = CreateTemplate::class;
require_once RUN_MAINTENANCE_IF_MAIN;
