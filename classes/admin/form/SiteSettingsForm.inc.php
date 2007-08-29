<?php

/**
 * @file SiteSettingsForm.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package admin.form
 * @class SiteSettingsForm
 *
 * Form to edit site settings.
 *
 * $Id$
 */

define('SITE_MIN_PASSWORD_LENGTH', 4);
import('form.Form');

class SiteSettingsForm extends Form {
	
	/**
	 * Constructor.
	 */
	function SiteSettingsForm() {
		parent::Form('admin/settings.tpl');
		
		// Validation checks for this form
		$this->addCheck(new FormValidatorLocale($this, 'title', 'required', 'admin.settings.form.titleRequired'));
		$this->addCheck(new FormValidatorLocale($this, 'contactName', 'required', 'admin.settings.form.contactNameRequired'));
		$this->addCheck(new FormValidatorLocaleEmail($this, 'contactEmail', 'required', 'admin.settings.form.contactEmailRequired'));
		$this->addCheck(new FormValidatorCustom($this, 'minPasswordLength', 'required', 'admin.settings.form.minPasswordLengthRequired', create_function('$l', sprintf('return $l >= %d;', SITE_MIN_PASSWORD_LENGTH))));
		$this->addCheck(new FormValidatorPost($this));
	}
	
	function getLocaleFieldNames() {
		$siteDao =& DAORegistry::getDAO('SiteDAO');
		return $siteDao->getLocaleFieldNames();
	}

	/**
	 * Display the form.
	 */
	function display() {
		$site =& Request::getSite();
		$publicFileManager =& new PublicFileManager();
		$siteStyleFilename = $publicFileManager->getSiteFilesPath() . '/' . $site->getSiteStyleFilename();
		$journalDao = &DAORegistry::getDAO('JournalDAO');
		$journals = &$journalDao->getJournalTitles();
		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('redirectOptions', $journals);
		$templateMgr->assign('originalStyleFilename', $site->getOriginalStyleFilename());
		$templateMgr->assign('styleFilename', $site->getSiteStyleFilename());
		$templateMgr->assign('publicFilesDir', Request::getBasePath() . '/' . $publicFileManager->getSiteFilesPath());
		$templateMgr->assign('dateStyleFileUploaded', file_exists($siteStyleFilename)?filemtime($siteStyleFilename):null);
		$templateMgr->assign('siteStyleFileExists', file_exists($siteStyleFilename));
		$templateMgr->assign('helpTopicId', 'site.siteManagement');
		parent::display();
	}
	
	/**
	 * Initialize form data from current settings.
	 */
	function initData() {
		$siteDao = &DAORegistry::getDAO('SiteDAO');
		$site = &$siteDao->getSite();
		
		$this->_data = array(
			'title' => $site->getTitle(null), // Localized
			'intro' => $site->getIntro(null), // Localized
			'redirect' => $site->getJournalRedirect(),
			'about' => $site->getAbout(null), // Localized
			'contactName' => $site->getContactName(null), // Localized
			'contactEmail' => $site->getContactEmail(null), // Localized
			'minPasswordLength' => $site->getMinPasswordLength()
		);
	}
	
	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(
			array('title', 'intro', 'about', 'redirect', 'contactName', 'contactEmail', 'minPasswordLength')
		);
	}
	
	/**
	 * Save site settings.
	 */
	function execute() {
		$siteDao = &DAORegistry::getDAO('SiteDAO');
		$site = &$siteDao->getSite();
		
		$site->setTitle($this->getData('title'), null); // Localized
		$site->setIntro($this->getData('intro'), null); // Localized
		$site->setAbout($this->getData('about'), null); // Localized
		$site->setJournalRedirect($this->getData('redirect'));
		$site->setContactName($this->getData('contactName'), null); // Localized
		$site->setContactEmail($this->getData('contactEmail'), null); // Localized
		$site->setMinPasswordLength($this->getData('minPasswordLength'));
		
		$siteDao->updateSite($site);
	}
	
	/**
	 * Uploads custom site stylesheet.
	 */
	function uploadSiteStyleSheet() {
		import('file.PublicFileManager');
		$fileManager = &new PublicFileManager();
		$site =& Request::getSite();
		if ($fileManager->uploadedFileExists('siteStyleSheet')) {
			$type = $fileManager->getUploadedFileType('siteStyleSheet');
			if ($type != 'text/plain' && $type != 'text/css') {
				return false;
			}

			$uploadName = $site->getSiteStyleFilename();
			if($fileManager->uploadSiteFile('siteStyleSheet', $uploadName)) {
				$siteDao =& DAORegistry::getDAO('SiteDAO');
				$site->setOriginalStyleFilename($fileManager->getUploadedFileName('siteStyleSheet'));
				$siteDao->updateSite($site);
			}
		}
		
		return true;
	}
}

?>
