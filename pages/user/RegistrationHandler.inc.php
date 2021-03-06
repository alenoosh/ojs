<?php

/**
 * @file RegistrationHandler.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package pages.user
 * @class RegistrationHandler
 *
 * Handle requests for user registration. 
 *
 * $Id$
 */

class RegistrationHandler extends UserHandler {

	/**
	 * Display registration form for new users.
	 */
	function register() {
		RegistrationHandler::validate();
		parent::setupTemplate(true);

		$journal = &Request::getJournal();

		if ($journal != null) {
			import('user.form.RegistrationForm');

			$regForm = &new RegistrationForm();
			if ($regForm->isLocaleResubmit()) {
				$regForm->readInputData();
			} else {
				$regForm->initData();
			}
			$regForm->display();

		} else {
			$journalDao = &DAORegistry::getDAO('JournalDAO');
			$journals = &$journalDao->getEnabledJournals(); //Enabled added
			$templateMgr = &TemplateManager::getManager();
			$templateMgr->assign('source', Request::getUserVar('source'));
			$templateMgr->assign_by_ref('journals', $journals);
			$templateMgr->display('user/registerSite.tpl');
		}
	}

	/**
	 * Validate user registration information and register new user.
	 */
	function registerUser() {
		RegistrationHandler::validate();
		import('user.form.RegistrationForm');

		$regForm = &new RegistrationForm();
		$regForm->readInputData();

		if ($regForm->validate()) {
			$regForm->execute();
			if (Config::getVar('email', 'require_validation')) {
				// Send them home; they need to deal with the
				// registration email.
				Request::redirect(null, 'index');
			}
			Validation::login($regForm->getData('username'), $regForm->getData('password'), $reason);
			if ($reason !== null) {
				parent::setupTemplate(true);
				$templateMgr = &TemplateManager::getManager();
				$templateMgr->assign('pageTitle', 'user.login');
				$templateMgr->assign('errorMsg', $reason==''?'user.login.accountDisabled':'user.login.accountDisabledWithReason');
				$templateMgr->assign('errorParams', array('reason' => $reason));
				$templateMgr->assign('backLink', Request::url(null, null, 'login'));
				$templateMgr->assign('backLinkLabel', 'user.login');
				return $templateMgr->display('common/error.tpl');
			}
			// Opatan Inc.
			if (Request::getUserVar('isReviewer') == 1) {
				$reviewerId = Request::getUserVar('reviewerId');
				$source = Request::getUserVar('source');
				RegistrationHandler::enrollAsReviewer($reviewerId, $source);
			} else {
				if($source = Request::getUserVar('source'))
					Request::redirectUrl($source);	
				else Request::redirect(null, 'login');
			}
		} else {
			parent::setupTemplate(true);
			$regForm->display();
		}
	}

	/**
	 * Show error message if user registration is not allowed.
	 */
	function registrationDisabled() {
		parent::setupTemplate(true);
		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('pageTitle', 'user.register');
		$templateMgr->assign('errorMsg', 'user.register.registrationDisabled');
		$templateMgr->assign('backLink', Request::url(null, null, 'login'));
		$templateMgr->assign('backLinkLabel', 'user.login');
		$templateMgr->display('common/error.tpl');
	}

	/**
	 * Check credentials and activate a new user
	 * @author Marc Bria <marc.bria@uab.es>
	 */
	function activateUser($args) {
		$username = array_shift($args);
		$accessKeyCode = array_shift($args);

		$journal = &Request::getJournal();
		$userDao = &DAORegistry::getDAO('UserDAO');
		$user =& $userDao->getUserByUsername($username);
		if (!$user) Request::redirect(null, 'login');

		// Checks user & token
		import('security.AccessKeyManager');
		$accessKeyManager =& new AccessKeyManager();
		$accessKeyHash = AccessKeyManager::generateKeyHash($accessKeyCode);
		$accessKey =& $accessKeyManager->validateKey(
			'RegisterContext',
			$user->getUserId(),
			$accessKeyHash
		);

		if ($accessKey != null && $user->getDateValidated() === null) {
			// Activate user
			$user->setDisabled(false);
			$user->setDisabledReason('');
			$user->setDateValidated(Core::getCurrentDate());
			$userDao->updateUser($user);

			$templateMgr =& TemplateManager::getManager();
			$templateMgr->assign('message', 'user.login.activated');
			return $templateMgr->display('common/message.tpl');
		}
		Request::redirect(null, 'login');
	}

		
	/**
	 * Opatan Inc. :
	 * Activate reviewer if the activation url has been clicked.
	 */
	function activateReviewer($args) {
		$reviewerId = (int) $args[0];
		$reviewerDao = &DAORegistry::getDAO('ReviewerDAO');
		$reviewer = &$reviewerDao->getReviewer($reviewerId);

		// mark this reviewer as assigned
		$reviewer->setStatus(2);
		$reviewerDao->updateReviewerStatus($reviewer);
	
		// enroll as reviewer
		RegistrationHandler::validate();
		import('user.form.RegistrationForm');

		parent::setupTemplate(true);

		$regForm = &new RegistrationForm();
		$regForm->initData();
		$regForm->setData('isReviewer', 1);
		$regForm->setData('reviewerId', $reviewer->getReviewerId());
		$regForm->setData('firstName', $reviewer->getFirstName(null));
		$regForm->setData('lastName', $reviewer->getLastName(null));
		$regForm->setData('username', $reviewer->getEmail());
		if ($reviewer->getMiddleName(null)) {
			$regForm->setData('middleName', $reviewer->getMiddleName(null));
		}
		if ($reviewer->getAffiliation(null)) {
			$regForm->setData('affiliation', $reviewer->getAffiliation(null));
		}
		$regForm->display();
	}
	
	/**
	 * Opatan Inc.
	 * Assign reviewer.
	 */
	function enrollAsReviewer($arg1, $arg2) {
		$reviewerId = $arg1;
		$source = $arg2;
		$journal = Request::getJournal();

		$reviewerDao = &DAORegistry::getDAO('ReviewerDAO');
		$reviewer = &$reviewerDao->getReviewer($reviewerId);
		
		$userDao = &DAORegistry::getDAO('UserDAO');
		$user = &$userDao->getUserByUsername($reviewer->getEmail());
		$userId = $user->getUserId();
		
		// enroll as reviewer
		$roleDao = &DAORegistry::getDAO('RoleDAO');
		$roleName = 'reviewer';
		$roleId = $roleDao->getRoleIdFromPath($roleName);
		if ($roleId != null) {
			$role = &new Role();
			$role->setJournalId($journal->getJournalId());
			$role->setUserId($userId);
			$role->setRoleId($roleId);
			$roleDao->insertRole($role);
		}

		if ($source)
			Request::redirectUrl($source);
		else Request::redirect(null, 'login');
	}

	/**
	 * Validation check.
	 * Checks if journal allows user registration.
	 */	
	function validate() {
		parent::validate(false);
		$journal = Request::getJournal();
		if ($journal != null) {
			$journalSettingsDao = &DAORegistry::getDAO('JournalSettingsDAO');
			if ($journalSettingsDao->getSetting($journal->getJournalId(), 'disableUserReg')) {
				// Users cannot register themselves for this journal
				RegistrationHandler::registrationDisabled();
				exit;
			}
		}
	}

}

?>
