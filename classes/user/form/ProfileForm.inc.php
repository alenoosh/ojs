<?php

/**
 * @file ProfileForm.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package user.form
 * @class ProfileForm
 *
 * Form to edit user profile.
 *
 * $Id$
 */

import('form.Form');

class ProfileForm extends Form {
	/**
	 * Constructor.
	 */
	function ProfileForm() {
		parent::Form('user/profile.tpl');

		$user = &Request::getUser();

		$site = &Request::getSite();

		// Validation checks for this form
		// Opatan Inc. : FormValidator for firstName and lastName is repalced with FormValidatorLocale
		$this->addCheck(new FormValidatorLocale($this, 'firstName', 'required', 'user.profile.form.firstNameRequired'));
		$this->addCheck(new FormValidatorLocale($this, 'lastName', 'required', 'user.profile.form.lastNameRequired'));
		$this->addCheck(new FormValidatorUrl($this, 'userUrl', 'optional', 'user.profile.form.urlInvalid'));
		// Opatan Inc. : FormValidatorEmail and FormValidatorCustom for email are removed
		$this->addCheck(new FormValidatorPost($this));
	}

	/**
	 * Display the form.
	 */
	function display() {
		$user = &Request::getUser();

		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('username', $user->getUsername());

		$site = &Request::getSite();
		$templateMgr->assign('availableLocales', $site->getSupportedLocaleNames());

		$roleDao = &DAORegistry::getDAO('RoleDAO');
		$journalDao = &DAORegistry::getDAO('JournalDAO');
		$notificationStatusDao = &DAORegistry::getDAO('NotificationStatusDAO');
		$userSettingsDao = &DAORegistry::getDAO('UserSettingsDAO');

		$journals = &$journalDao->getJournals();
		$journals = &$journals->toArray();

		foreach ($journals as $thisJournal) {
			if ($thisJournal->getSetting('enableSubscriptions') == true && $thisJournal->getSetting('enableOpenAccessNotification') == true) {
				$templateMgr->assign('displayOpenAccessNotification', true);
				$templateMgr->assign_by_ref('user', $user);
				break;
			}
		}

		$journals = &$journalDao->getJournals();
		$journals = &$journals->toArray();
		$journalNotifications = &$notificationStatusDao->getJournalNotifications($user->getUserId());

		$countryDao =& DAORegistry::getDAO('CountryDAO');
		$countries =& $countryDao->getCountries();

		$disciplineDao =& DAORegistry::getDAO('DisciplineDAO');
		$disciplines =& $disciplineDao->getDisciplines();

		$templateMgr->assign_by_ref('journals', $journals);
		$templateMgr->assign_by_ref('countries', $countries);
		$templateMgr->assign_by_ref('disciplines', $disciplines);
		$templateMgr->assign_by_ref('journalNotifications', $journalNotifications);
		$templateMgr->assign('helpTopicId', 'user.registerAndProfile');

		$journal =& Request::getJournal();
		if ($journal) {
			$roleDao =& DAORegistry::getDAO('RoleDAO');
			$roles =& $roleDao->getRolesByUserId($user->getUserId(), $journal->getJournalId());
			$roleNames = array();
			foreach ($roles as $role) $roleNames[$role->getRolePath()] = $role->getRoleName();
			$templateMgr->assign('allowRegReviewer', $journal->getSetting('allowRegReviewer'));
			$templateMgr->assign('allowRegAuthor', $journal->getSetting('allowRegAuthor'));
			$templateMgr->assign('allowRegReader', $journal->getSetting('allowRegReader'));
			$templateMgr->assign('roles', $roleNames);
		}

		parent::display();
	}

	function getLocaleFieldNames() {
		$userDao =& DAORegistry::getDAO('UserDAO');
		return $userDao->getLocaleFieldNames();
	}

	/**
	 * Initialize form data from current settings.
	 */
	function initData() {
		$user = &Request::getUser();

		$this->_data = array(
			'salutation' => $user->getSalutation(null), // Opatan Inc. : Localized
			'firstName' => $user->getFirstName(null), // Opatan Inc. : Localized
			'middleName' => $user->getMiddleName(null), // Opatan Inc. : Localized
			'initials' => $user->getInitials(),
			'lastName' => $user->getLastName(null), // Opatan Inc. : Localized
			'gender' => $user->getGender(),
			'discipline' => $user->getDiscipline(),
			'affiliation' => $user->getAffiliation(null), // Opatan Inc. : Localized
			'signature' => $user->getSignature(null), // Localized
			// Opatan Inc. : 'email' => $user->getEmail(), is removed
			'userUrl' => $user->getUrl(),
			'phone' => $user->getPhone(),
			'fax' => $user->getFax(),
			'mailingAddress' => $user->getMailingAddress(),
			'country' => $user->getCountry(),
			'biography' => $user->getBiography(null), // Localized
			'interests' => $user->getInterests(null), // Localized
			'userLocales' => $user->getLocales(),
			'isAuthor' => Validation::isAuthor(),
			'isReader' => Validation::isReader(),
			'isReviewer' => Validation::isReviewer()
		);
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array(
			'salutation',
			'firstName',
			'middleName',
			'lastName',
			'gender',
			'discipline',
			'initials',
			'affiliation',
			'signature',
			// Opatan Inc. : 'email' is removed
			'userUrl',
			'phone',
			'fax',
			'mailingAddress',
			'country',
			'biography',
			'interests',
			'userLocales',
			'readerRole',
			'authorRole',
			'reviewerRole'
		));

		if ($this->getData('userLocales') == null || !is_array($this->getData('userLocales'))) {
			$this->setData('userLocales', array());
		}
	}

	/**
	 * Save profile settings.
	 */
	function execute() {
		$user = &Request::getUser();

		$user->setSalutation($this->getData('salutation'), null); // Opatan Inc. : Localized
		$user->setFirstName($this->getData('firstName'), null); // Opatan Inc. : Localized
		$user->setMiddleName($this->getData('middleName'), null); // Opatan Inc. : Localized
		$user->setLastName($this->getData('lastName'), null); // Opatan Inc. : Localized
		$user->setGender($this->getData('gender'));
		$user->setDiscipline($this->getData('discipline'));
		$user->setInitials($this->getData('initials'));
		$user->setAffiliation($this->getData('affiliation'), null); // Opatan Inc. : Localized
		$user->setSignature($this->getData('signature'), null); // Localized
		// Opatan Inc. : $this->getData('email') changed to $user->getUsername()
		$user->setEmail($user->getUsername()); 
		$user->setUrl($this->getData('userUrl'));
		$user->setPhone($this->getData('phone'));
		$user->setFax($this->getData('fax'));
		$user->setMailingAddress($this->getData('mailingAddress'));
		$user->setCountry($this->getData('country'));
		$user->setBiography($this->getData('biography'), null); // Localized
		$user->setInterests($this->getData('interests'), null); // Localized

		$site = &Request::getSite();
		$availableLocales = $site->getSupportedLocales();

		$locales = array();
		foreach ($this->getData('userLocales') as $locale) {
			if (Locale::isLocaleValid($locale) && in_array($locale, $availableLocales)) {
				array_push($locales, $locale);
			}
		}
		$user->setLocales($locales);

		$userDao = &DAORegistry::getDAO('UserDAO');
		$userDao->updateUser($user);

		$roleDao = &DAORegistry::getDAO('RoleDAO');
		$journalDao = &DAORegistry::getDAO('JournalDAO');
		$notificationStatusDao = &DAORegistry::getDAO('NotificationStatusDAO');

		// Roles
		$journal =& Request::getJournal();
		if ($journal) {
			$role =& new Role();
			$role->setUserId($user->getUserId());
			$role->setJournalId($journal->getJournalId());
			if ($journal->getSetting('allowRegReviewer')) {
				$role->setRoleId(ROLE_ID_REVIEWER);
				$hasRole = Validation::isReviewer();
				$wantsRole = Request::getUserVar('reviewerRole');
				if ($hasRole && !$wantsRole) $roleDao->deleteRole($role);
				if (!$hasRole && $wantsRole) $roleDao->insertRole($role);
			}
			if ($journal->getSetting('allowRegAuthor')) {
				$role->setRoleId(ROLE_ID_AUTHOR);
				$hasRole = Validation::isAuthor();
				$wantsRole = Request::getUserVar('authorRole');
				if ($hasRole && !$wantsRole) $roleDao->deleteRole($role);
				if (!$hasRole && $wantsRole) $roleDao->insertRole($role);
			}
			if ($journal->getSetting('allowRegReader')) {
				$role->setRoleId(ROLE_ID_READER);
				$hasRole = Validation::isReader();
				$wantsRole = Request::getUserVar('readerRole');
				if ($hasRole && !$wantsRole) $roleDao->deleteRole($role);
				if (!$hasRole && $wantsRole) $roleDao->insertRole($role);
			}
		}

		$journals = &$journalDao->getJournals();
		$journals = &$journals->toArray();
		$journalNotifications = $notificationStatusDao->getJournalNotifications($user->getUserId());

		$readerNotify = Request::getUserVar('journalNotify');

		foreach ($journals as $thisJournal) {
			$thisJournalId = $thisJournal->getJournalId();
			$currentlyReceives = !empty($journalNotifications[$thisJournalId]);
			$shouldReceive = !empty($readerNotify) && in_array($thisJournal->getJournalId(), $readerNotify);
			if ($currentlyReceives != $shouldReceive) {
				$notificationStatusDao->setJournalNotifications($thisJournalId, $user->getUserId(), $shouldReceive);
			}
		}

		$openAccessNotify = Request::getUserVar('openAccessNotify');

		$userSettingsDao = &DAORegistry::getDAO('UserSettingsDAO');
		$journals = &$journalDao->getJournals();
		$journals = &$journals->toArray();

		foreach ($journals as $thisJournal) {
			if (($thisJournal->getSetting('enableSubscriptions') == true) && ($thisJournal->getSetting('enableOpenAccessNotification') == true)) {
				$currentlyReceives = $user->getSetting('openAccessNotification', $thisJournal->getJournalId());
				$shouldReceive = !empty($openAccessNotify) && in_array($thisJournal->getJournalId(), $openAccessNotify);
				if ($currentlyReceives != $shouldReceive) {
					$userSettingsDao->updateSetting($user->getUserId(), 'openAccessNotification', $shouldReceive, 'bool', $thisJournal->getJournalId());
				}
			}
		}

		if ($user->getAuthId()) {
			$authDao = &DAORegistry::getDAO('AuthSourceDAO');
			$auth = &$authDao->getPlugin($user->getAuthId());
		}

		if (isset($auth)) {
			$auth->doSetUserInfo($user);
		}
	}
}

?>
