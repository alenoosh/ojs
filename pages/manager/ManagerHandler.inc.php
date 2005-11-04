<?php

/**
 * ManagerHandler.inc.php
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package pages.manager
 *
 * Handle requests for journal management functions. 
 *
 * $Id$
 */

class ManagerHandler extends Handler {

	/**
	 * Display journal management index page.
	 */
	function index() {
		ManagerHandler::validate();
		ManagerHandler::setupTemplate();

		$journal = &Request::getJournal();
		$journalSettingsDao = &DAORegistry::getDAO('JournalSettingsDAO');
		$subscriptionsEnabled = $journalSettingsDao->getSetting($journal->getJournalId(), 'enableSubscriptions'); 

		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('subscriptionsEnabled', $subscriptionsEnabled);
		$templateMgr->assign('helpTopicId','journal.index');
		$templateMgr->display('manager/index.tpl');
	}
	
	/**
	 * Validate that user has permissions to manage the selected journal.
	 * Redirects to user index page if not properly authenticated.
	 */
	function validate() {
		parent::validate();
		if (!Validation::isJournalManager()) {
			Validation::redirectLogin();
		}
	}
	
	/**
	 * Setup common template variables.
	 * @param $subclass boolean set to true if caller is below this handler in the hierarchy
	 */
	function setupTemplate($subclass = false) {
		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('pageHierarchy',
			$subclass ? array(array('user', 'navigation.user'), array('manager', 'manager.journalManagement'))
				: array(array('user', 'navigation.user'))
		);
		$templateMgr->assign('pagePath', '/user/manager');
	}
	
	
	//
	// Setup
	//

	function setup($args) {
		import('pages.manager.SetupHandler');
		SetupHandler::setup($args);
	}

	function saveSetup($args) {
		import('pages.manager.SetupHandler');
		SetupHandler::saveSetup($args);
	}
	
	
	//
	// People Management
	//

	function people($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::people($args);
	}
	
	function enrollSearch($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::enrollSearch($args);
	}
	
	function enroll($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::enroll($args);
	}
	
	function unEnroll($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::unEnroll($args);
	}
	
	function enrollSyncSelect($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::enrollSyncSelect($args);
	}
	
	function enrollSync($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::enrollSync($args);
	}
	
	function createUser() {
		import('pages.manager.PeopleHandler');
		PeopleHandler::createUser();
	}
	
	function disableUser($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::disableUser($args);
	}
	
	function enableUser($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::enableUser($args);
	}
	
	function removeUser($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::removeUser($args);
	}
	
	function editUser($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::editUser($args);
	}
	
	function updateUser() {
		import('pages.manager.PeopleHandler');
		PeopleHandler::updateUser();
	}
	
	function userProfile($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::userProfile($args);
	}
	
	function signInAsUser($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::signInAsUser($args);
	}
	
	function signOutAsUser() {
		import('pages.manager.PeopleHandler');
		PeopleHandler::signOutAsUser();
	}
	
	
	//
	// Section Management
	//
	
	function sections() {
		import('pages.manager.SectionHandler');
		SectionHandler::sections();
	}
	
	function createSection() {
		import('pages.manager.SectionHandler');
		SectionHandler::createSection();
	}
	
	function editSection($args) {
		import('pages.manager.SectionHandler');
		SectionHandler::editSection($args);
	}
	
	function updateSection() {
		import('pages.manager.SectionHandler');
		SectionHandler::updateSection();
	}
	
	function deleteSection($args) {
		import('pages.manager.SectionHandler');
		SectionHandler::deleteSection($args);
	}
	
	function moveSection() {
		import('pages.manager.SectionHandler');
		SectionHandler::moveSection();
	}
	
	
	//
	// E-mail Management
	//
	
	function emails() {
		import('pages.manager.EmailHandler');
		EmailHandler::emails();
	}
	
	function createEmail($args) {
		import('pages.manager.EmailHandler');
		EmailHandler::createEmail($args);
	}
	
	function editEmail($args) {
		import('pages.manager.EmailHandler');
		EmailHandler::editEmail($args);
	}
	
	function updateEmail() {
		import('pages.manager.EmailHandler');
		EmailHandler::updateEmail();
	}
	
	function deleteCustomEmail($args) {
		import('pages.manager.EmailHandler');
		EmailHandler::deleteCustomEmail($args);
	}
	
	function resetEmail($args) {
		import('pages.manager.EmailHandler');
		EmailHandler::resetEmail($args);
	}
	
	function disableEmail($args) {
		import('pages.manager.EmailHandler');
		EmailHandler::disableEmail($args);
	}
	
	function enableEmail($args) {
		import('pages.manager.EmailHandler');
		EmailHandler::enableEmail($args);
	}
	
	function resetAllEmails() {
		import('pages.manager.EmailHandler');
		EmailHandler::resetAllEmails();
	}
	
	function email($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::email($args);
	}
	
	function selectTemplate($args) {
		import('pages.manager.PeopleHandler');
		PeopleHandler::selectTemplate($args);
	}
	
	
	//
	// Languages
	//
	
	function languages() {
		import('pages.manager.JournalLanguagesHandler');
		JournalLanguagesHandler::languages();
	}
	
	function saveLanguageSettings() {
		import('pages.manager.JournalLanguagesHandler');
		JournalLanguagesHandler::saveLanguageSettings();
	}
	
	
	//
	// Files Browser
	//
	
	function files($args) {
		import('pages.manager.FilesHandler');
		FilesHandler::files($args);
	}
	
	function fileUpload($args) {
		import('pages.manager.FilesHandler');
		FilesHandler::fileUpload($args);
	}
	
	function fileMakeDir($args) {
		import('pages.manager.FilesHandler');
		FilesHandler::fileMakeDir($args);
	}
	
	function fileDelete($args) {
		import('pages.manager.FilesHandler');
		FilesHandler::fileDelete($args);
	}


	//
	// Subscription Types
	//

	function subscriptionTypes() {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::subscriptionTypes();
	}

	function deleteSubscriptionType($args) {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::deleteSubscriptionType($args);
	}

	function createSubscriptionType() {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::createSubscriptionType();
	}

	function selectSubscriber($args) {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::selectSubscriber($args);
	}

	function editSubscriptionType($args) {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::editSubscriptionType($args);
	}

	function updateSubscriptionType($args) {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::updateSubscriptionType($args);
	}

	function moveSubscriptionType($args) {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::moveSubscriptionType($args);
	}

	//
	// Subscriptions
	//

	function subscriptions() {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::subscriptions();
	}

	function deleteSubscription($args) {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::deleteSubscription($args);
	}

	function createSubscription() {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::createSubscription();
	}

	function editSubscription($args) {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::editSubscription($args);
	}

	function updateSubscription($args) {
		import('pages.manager.SubscriptionHandler');
		SubscriptionHandler::updateSubscription($args);
	}

	//
	// Import/Export
	//

	function importexport($args) {
		import('pages.manager.ImportExportHandler');
		ImportExportHandler::importExport($args);
	}

	//
	// Plugin Management
	//

	function plugins($args) {
		import('pages.manager.PluginHandler');
		PluginHandler::plugins($args);
	}

	function plugin($args) {
		import('pages.manager.PluginHandler');
		PluginHandler::plugin($args);
	}
}

?>
