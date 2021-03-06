<?php

/**
 * @file Author.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package article
 * @class Author
 *
 * Article author metadata class.
 *
 * $Id$
 */

class Author extends DataObject {

	/**
	 * Constructor.
	 */
	function Author() {
		parent::DataObject();
		$this->setAuthorId(0);
	}

	/**
	 * Get the author's complete name.
	 * Includes first name, middle name (if applicable), and last name.
	 * @return string
	 */
	function getFullName() {
		$locale = Locale::getLocale();
		// Opatan Inc. : gets localized author firstName, middleName and lastName
		return $this->getData('firstName', $locale) . ' ' . ($this->getData('middleName', $locale) != '' ? $this->getData('middleName', $locale) . ' ' : '') . $this->getData('lastName', $locale);
	}

	//
	// Get/set methods
	//

	/**
	 * Get ID of author.
	 * @return int
	 */
	function getAuthorId() {
		return $this->getData('authorId');
	}

	/**
	 * Set ID of author.
	 * @param $authorId int
	 */
	function setAuthorId($authorId) {
		return $this->setData('authorId', $authorId);
	}

	/**
	 * Get ID of article.
	 * @return int
	 */
	function getArticleId() {
		return $this->getData('articleId');
	}

	/**
	 * Set ID of article.
	 * @param $articleId int
	 */
	function setArticleId($articleId) {
		return $this->setData('articleId', $articleId);
	}

	/**
	 * Opatan Inc. : 
	 * Get localized author firstName
	 */
	 function getAuthorFirstName() {
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
	 * Get localized author middleName
	 */
	 function getAuthorMiddleName() {
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
	 * Get localized author lastName
	 */
	 function getAuthorLastName() {
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
	 * Get localized author affiliation
	 */
	 function getAuthorAffiliation() {
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
	 * Get country code
	 * @return string
	 */
	function getCountry() {
		return $this->getData('country');
	}

	/**
	 * Get localized country
	 * @return string
	 */
	function getCountryLocalized() {
		$countryDao =& DAORegistry::getDAO('CountryDAO');
		$country = $this->getCountry();
		if ($country) {
			return $countryDao->getCountry($country);
		}
		return null;
	}

	/**
	 * Set country code.
	 * @param $country string
	 */
	function setCountry($country) {
		return $this->setData('country', $country);
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
	 * Get URL.
	 * @return string
	 */
	function getUrl() {
		return $this->getData('url');
	}

	/**
	 * Set URL.
	 * @param $url string
	 */
	function setUrl($url) {
		return $this->setData('url', $url);
	}

	/**
	 * Get the localized biography for this author
	 */
	function getAuthorCompetingInterests() {
		return $this->getLocalizedData('competingInterests');
	}

	/**
	 * Get author competing interests.
	 * @param $locale string
	 * @return string
	 */
	function getCompetingInterests($locale) {
		return $this->getData('competingInterests', $locale);
	}

	/**
	 * Set author competing interests.
	 * @param $biography string
	 * @param $locale string
	 */
	function setCompetingInterests($competingInterests, $locale) {
		return $this->setData('competingInterests', $competingInterests, $locale);
	}

	/**
	 * Get the localized biography for this author
	 */
	function getAuthorBiography() {
		return $this->getLocalizedData('biography');
	}

	/**
	 * Get author biography.
	 * @param $locale string
	 * @return string
	 */
	function getBiography($locale) {
		return $this->getData('biography', $locale);
	}

	/**
	 * Set author biography.
	 * @param $biography string
	 * @param $locale string
	 */
	function setBiography($biography, $locale) {
		return $this->setData('biography', $biography, $locale);
	}

	/**
	 * Get primary contact.
	 * @return boolean
	 */
	function getPrimaryContact() {
		return $this->getData('primaryContact');
	}

	/**
	 * Set primary contact.
	 * @param $primaryContact boolean
	 */
	function setPrimaryContact($primaryContact) {
		return $this->setData('primaryContact', $primaryContact);
	}

	/**
	 * Get sequence of author in article's author list.
	 * @return float
	 */
	function getSequence() {
		return $this->getData('sequence');
	}

	/**
	 * Set sequence of author in article's author list.
	 * @param $sequence float
	 */
	function setSequence($sequence) {
		return $this->setData('sequence', $sequence);
	}

}

?>
