<?php

/**
 * @file ReviewerDAO.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package reviewer
 * @class ReviewerDAO
 *
 * Class for Reviewer DAO.
 * Operations for retrieving and modifying Reviewer objects.
 *
 * $Id$
 */

import('reviewer.Reviewer');

class ReviewerDAO extends DAO {
	/**
	 * Retrieve a reviewer by ID.
	 * @param $reviewerId int
	 * @return Reviewer
	 */
	function &getReviewer($reviewerId) {
		$locale = Locale::getLocale();
		$paramArray = array('firstName', $locale, 'lastName', $locale, 'middleName', $locale, 'affiliation', $locale, $reviewerId);
		$result = &$this->retrieve(
			'SELECT 
				r.*,
				rf.setting_value AS first_name, rl.setting_value AS last_name,
				rm.setting_value AS middle_name, ra.setting_value AS affiliation
			 FROM 
			 	reviewers r
				LEFT JOIN reviewer_settings rf ON (r.reviewer_id = rf.reviewer_id AND rf.setting_name = ? AND rf.locale = ?) 
				LEFT JOIN reviewer_settings rl ON (r.reviewer_id = rl.reviewer_id AND rl.setting_name = ? AND rl.locale = ?)
				LEFT JOIN reviewer_settings rm ON (r.reviewer_id = rm.reviewer_id AND rm.setting_name = ? AND rm.locale = ?)
				LEFT JOIN reviewer_settings ra ON (r.reviewer_id = ra.reviewer_id AND ra.setting_name = ? AND ra.locale = ?) 
			 WHERE r.reviewer_id = ?',
			 $paramArray 
		);

		$reviewer = null;
		if ($result->RecordCount() != 0) {
			$reviewer = &$this->_returnReviewerFromRow($result->GetRowAssoc(false));
		}
		$result->Close();
		unset($result);
		return $reviewer;
	}

	/**
	 * Retrieve a reviewer by Email.
	 * @param $email string
	 * @return Reviewer
	 */
	function &getReviewerByEmail($email) {
		$locale = Locale::getLocale();
		$paramArray = array('firstName', $locale, 'lastName', $locale, 'middleName', $locale, 'affiliation', $locale, $email);
		$result = &$this->retrieve(
			'SELECT 
				r.*,
				rf.setting_value AS first_name, rl.setting_value AS last_name,
				rm.setting_value AS middle_name, ra.setting_value AS affiliation
			 FROM 
			 	reviewers r
				LEFT JOIN reviewer_settings rf ON (r.reviewer_id = rf.reviewer_id AND rf.setting_name = ? AND rf.locale = ?) 
				LEFT JOIN reviewer_settings rl ON (r.reviewer_id = rl.reviewer_id AND rl.setting_name = ? AND rl.locale = ?)
				LEFT JOIN reviewer_settings rm ON (r.reviewer_id = rm.reviewer_id AND rm.setting_name = ? AND rm.locale = ?)
				LEFT JOIN reviewer_settings ra ON (r.reviewer_id = ra.reviewer_id AND ra.setting_name = ? AND ra.locale = ?) 
			 WHERE r.email = ?',
			 $paramArray 
		);

		$reviewer = null;
		if ($result->RecordCount() != 0) {
			$reviewer = &$this->_returnReviewerFromRow($result->GetRowAssoc(false));
		}
		$result->Close();
		unset($result);
		return $reviewer;
	}

	/**
	 * Insert a new reviewer.
	 * @param $reviewer Reviewer
	 * @param $articleId
	 */
	function insertReviewer(&$reviewer, $articleId) {
		$this->update(sprintf('INSERT INTO reviewers (email, article_id) VALUES (?, ?)'), array($reviewer->getEmail(), $articleId));

		$reviewer->setReviewerId($this->getInsertReviewerId());
		$this->updateLocaleFields($reviewer);
		return $reviewer->getReviewerId();
	}

	// Opatan Inc.
	function &_returnReviewerFromRow(&$row) {
		$locale = Locale::getLocale();
		$reviewer = &new Reviewer();
		$reviewer->setReviewerId($row['reviewer_id']);
		$reviewer->setArticleId($row['article_id']);
		$reviewer->setFirstName($row['first_name'], $locale);
		$reviewer->setMiddleName($row['middle_name'], $locale);
		$reviewer->setLastName($row['last_name'], $locale);
		$reviewer->setAffiliation($row['affiliation'], $locale);
		$reviewer->setEmail($row['email']);
		$this->getDataObjectSettings('reviewer_settings', 'reviewer_id', $row['reviewer_id'], $reviewer);

		HookRegistry::call('ReviewerDAO::_returnReviewerFromRow', array(&$reviewer, &$row));

		return $reviewer;
	}
	
	/**
	 * Delete reviewers by article id
	 * @param $articleId
	**/
	function deleteReviewersByArticle($articleId) {
		$result = &$this->retrieve('SELECT reviewer_id FROM reviewers WHERE article_id = ?', $articleId);
		if ($result->RecordCount() != 0) {
			while (!$result->EOF) {	
				$row = $result->GetRowAssoc(false);
				$reviewerId = $row['reviewer_id'];
				$this->update('DELETE FROM reviewer_settings WHERE reviewer_id = ?', $reviewerId);
				$result->MoveNext();			
			}				
		}

		$result->Close();
		unset($result);	

		$this->update('DELETE FROM reviewers WHERE article_id = ?', $articleId);
	}

	/**
	 * Get a list of field names for which data is localized.
	 * @return array
	 */
	function getLocaleFieldNames() {
		return array('firstName', 'lastName', 'middleName', 'affiliation');
	}

	/**
	 * Update the settings for this object
	 * @param $article object
	 */
	function updateLocaleFields(&$reviewer) {
		$this->updateDataObjectSettings('reviewer_settings', $reviewer, array(
			'reviewer_id' => $reviewer->getReviewerId()
		));
	}

	/**
	 * Get the ID of the last inserted reviewer.
	 * @return int
	 */
	function getInsertReviewerId() {
		return $this->getInsertId('reviewers', 'reviewer_id');
	}

	/**
	 * Update reviewer status
	 * @param $reviewer object
	 */
	function updateReviewerStatus(&$reviewer) {
		return $this->update(
			sprintf('UPDATE reviewers SET status = ? WHERE reviewer_id = ?'), 
			array($reviewer->getStatus(), $reviewer->getReviewerId()));
	}
}
?>
