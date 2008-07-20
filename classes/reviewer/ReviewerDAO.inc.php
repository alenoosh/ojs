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
}
?>
