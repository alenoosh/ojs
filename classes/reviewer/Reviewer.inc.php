<?php

/**
 * @file Reviewer.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package reviewer
 * @class Reviewer
 *
 * Reviewer class.
 * Basic class describing reviewers recommended by the Author.
 *
 * $Id$
 */

class Reviewer extends DataObject {

	function Reviewer() {
		parent::DataObject();
	}

	//
	// Get/set methods
	//

	/**
	 * Get the ID of the reviewer.
	 * @return int
	 */
	function getReviewerId() {
		return $this->getData('reviewerId');
	}

	/**
	 * Set the ID of the reviewer.
	 * @param $reviewerId int
	 */
	function setReviewerId($reviewerId) {
		return $this->setData('reviewerId', $reviewerId);
	}

 	/**
	 * Opatan Inc. : 
	 * Get localized reviewer firstName.
	 */
	function getReviewerFirstName() {
		return $this->getLocalizedData('firstName');
	}

	/**
   	 * Opatan Inc. : @param $locale string added
	 * Get first name.
	 * @return string
	 */
	function getFirstName($locale) {
		return $this->getData('firstName', $locale);
	}

	/**
	 * Opatan Inc. : @param $locale string added
	 * Set first name.
	 * @param $firstName string
	 */
	function setFirstName($firstName, $locale)
	{
		return $this->setData('firstName', $firstName, $locale);
	}

 	/**
	 * Opatan Inc. : 
	 * Get localized reviewer middleName.
	 */
	function getReviewerMiddleName() {
		return $this->getLocalizedData('middleName');
	}

	/**
	 * Opatan Inc. : @param $locale string added
	 * Get middle name.
	 * @return string
	 */
	function getMiddleName($locale) {
		return $this->getData('middleName', $locale);
	}

	/**
	 * Opatan Inc. : @param $locale string added
	 * Set middle name.
	 * @param $middleName string
	 */
	function setMiddleName($middleName, $locale) {
		return $this->setData('middleName', $middleName, $locale);
	}

 	/**
	 * Opatan Inc. : 
	 * Get localized reviewer lastName.
	 */
	function getReviewerLastName() {
		return $this->getLocalizedData('lastName');
	}

	/**
	 * Opatan Inc. : @param $locale string added
	 * Get last name.
	 * @return string
	 */
	function getLastName($locale) {
		return $this->getData('lastName', $locale);
	}

	/**
	 * Opatan Inc. : @param $locale string added
	 * Set last name.
	 * @param $lastName string
	 */
	function setLastName($lastName, $locale) {
		return $this->setData('lastName', $lastName, $locale);
	}

 	/**
	 * Opatan Inc. :
	 * Get localized reviewer affiliation.
	 */
	function getReviewerAffiliation() {
		return $this->getLocalizedData('affiliation');
	}

	/**
	 * Opatan Inc. : @param $locale string added
	 * Get affiliation (position, institution, etc.).
	 * @return string
	 */
	function getAffiliation($locale) {
		return $this->getData('affiliation', $locale);
	}

	/**
	 * Opatan Inc. : @param $locale string added
	 * Set affiliation.
	 * @param $affiliation string
	 */
	function setAffiliation($affiliation, $locale) {
		return $this->setData('affiliation', $affiliation, $locale);
	}

	/**
	 * Get email address.
	 * @return string
	 */
	function getEmail() {
		return $this->getData('email');
	}

	/**
	 * Set email address.
	 * @param $email string
	 */
	function setEmail($email) {
		return $this->setData('email', $email);
	}

	/**
	 * Retrieve array of user settings.
	 * @param journalId int
	 * @return array
	 */
	/*
	function &getSettings($journalId = null) {
		$userSettingsDao = &DAORegistry::getDAO('UserSettingsDAO');
		$settings = &$userSettingsDao->getSettingsByJournal($this->getData('userId'), $journalId);
		return $settings;
	}
	*/

	/**
	 * Retrieve a user setting value.
	 * @param $name
	 * @param $journalId int
	 * @return mixed
	 */
	/*
	function &getSetting($name, $journalId = null) {
		$userSettingsDao = &DAORegistry::getDAO('UserSettingsDAO');
		$setting = &$userSettingsDao->getSetting($this->getData('userId'), $name, $journalId);
		return $setting;
	}
	*/

	/**
	 * Set a user setting value.
	 * @param $name string
	 * @param $value mixed
	 * @param $type string optional
	 */
	/*
	function updateSetting($name, $value, $type = null, $journalId = null) {
		$userSettingsDao = &DAORegistry::getDAO('UserSettingsDAO');
		return $userSettingsDao->updateSetting($this->getData('userId'), $name, $value, $type, $journalId);
	}
	*/

	/**
	 * Get the reviewer's complete name.
	 * Includes first name, middle name (if applicable), and last name.
	 * @param $lastFirst boolean return in "LastName, FirstName" format
	 * @return string
	 */
	function getFullName($lastFirst = false) {
		$locale = Locale::getLocale();
		if ($lastFirst) {
			return $this->getData('lastName', $locale) . ', ' . $this->getData('firstName', $locale) . ($this->getData('middleName', $locale) != '' ? ' ' . $this->getData('middleName', $locale) : ''); // Opatan Inc. : gets localized firstName, middleName and lastName

		} else {			
			return $this->getData('firstName', $locale) . ' ' . ($this->getData('middleName', $locale) != '' ? $this->getData('middleName', $locale) . ' ' : '') . $this->getData('lastName', $locale); // Opatan Inc. : gets localized firstName, middleName and lastName
		}
	}
}
?>
