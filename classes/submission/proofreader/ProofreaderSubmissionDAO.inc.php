<?php

/**
 * @file ProofreaderSubmissionDAO.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package submission.proofreader
 * @class ProofreaderSubmissionDAO
 *
 * Class for ProofreaderSubmission DAO.
 * Operations for retrieving and modifying ProofreaderSubmission objects.
 *
 * $Id$
 */

import('submission.proofreader.ProofreaderSubmission');

class ProofreaderSubmissionDAO extends DAO {
	/** Helper DAOs */
	var $articleDao;
	var $articleCommentDao;
	var $editAssignmentDao;
	var $proofAssignmentDao;
	var $layoutAssignmentDao;
	var $galleyDao;
	var $suppFileDao;

	/**
	 * Constructor.
	 */
	function ProofreaderSubmissionDAO() {
		parent::DAO();

		$this->articleDao = &DAORegistry::getDAO('ArticleDAO');
		$this->articleCommentDao = &DAORegistry::getDAO('ArticleCommentDAO');
		$this->proofAssignmentDao = &DAORegistry::getDAO('ProofAssignmentDAO');
		$this->editAssignmentDao = &DAORegistry::getDAO('EditAssignmentDAO');
		$this->layoutAssignmentDao = &DAORegistry::getDAO('LayoutAssignmentDAO');
		$this->galleyDao = &DAORegistry::getDAO('ArticleGalleyDAO');
		$this->suppFileDao = &DAORegistry::getDAO('SuppFileDAO');
	}

	/**
	 * Retrieve a proofreader submission by article ID.
	 * @param $articleId int
	 * @return ProofreaderSubmission
	 */
	function &getSubmission($articleId, $journalId = null) {
		$primaryLocale = Locale::getPrimaryLocale();
		$locale = Locale::getLocale();

		$params = array(
			'title',
			$primaryLocale,
			'title',
			$locale,
			'abbrev',
			$primaryLocale,
			'abbrev',
			$locale,
			$articleId
		);
		if ($journalId) $params[] = $journalId;

		$result = &$this->retrieve(
			'SELECT	a.*,
				COALESCE(stl.setting_value, stpl.setting_value) AS section_title,
				COALESCE(sal.setting_value, sapl.setting_value) AS section_abbrev
			FROM articles a
				LEFT JOIN sections s ON s.section_id = a.section_id
				LEFT JOIN section_settings stpl ON (s.section_id = stpl.section_id AND stpl.setting_name = ? AND stpl.locale = ?)
				LEFT JOIN section_settings stl ON (s.section_id = stl.section_id AND stl.setting_name = ? AND stl.locale = ?)
				LEFT JOIN section_settings sapl ON (s.section_id = sapl.section_id AND sapl.setting_name = ? AND sapl.locale = ?)
				LEFT JOIN section_settings sal ON (s.section_id = sal.section_id AND sal.setting_name = ? AND sal.locale = ?)
			WHERE	article_id = ?' .
				($journalId?' AND a.journal_id = ?':''),
			$params
		);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = &$this->_returnSubmissionFromRow($result->GetRowAssoc(false));
		}

		$result->Close();
		unset($result);

		return $returner;
	}

	/**
	 * Internal function to return a ProofreaderSubmission object from a row.
	 * @param $row array
	 * @return ProofreaderSubmission
	 */
	function &_returnSubmissionFromRow(&$row) {
		$submission = &new ProofreaderSubmission();
		$this->articleDao->_articleFromRow($submission, $row);
		$submission->setMostRecentProofreadComment($this->articleCommentDao->getMostRecentArticleComment($row['article_id'], COMMENT_TYPE_PROOFREAD, $row['article_id']));
		$submission->setProofAssignment($this->proofAssignmentDao->getProofAssignmentByArticleId($row['article_id']));

		// Editor Assignment
		$editAssignments =& $this->editAssignmentDao->getEditAssignmentsByArticleId($row['article_id']);
		$submission->setEditAssignments($editAssignments->toArray());

		// Layout reference information
		$submission->setLayoutAssignment($this->layoutAssignmentDao->getLayoutAssignmentByArticleId($row['article_id']));

		$submission->setGalleys($this->galleyDao->getGalleysByArticle($row['article_id']));

		$submission->setSuppFiles($this->suppFileDao->getSuppFilesByArticle($row['article_id']));

		$submission->setMostRecentLayoutComment($this->articleCommentDao->getMostRecentArticleComment($row['article_id'], COMMENT_TYPE_LAYOUT, $row['article_id']));

		HookRegistry::call('ProofreaderSubmissionDAO::_returnProofreaderSubmissionFromRow', array(&$submission, &$row));

		return $submission;
	}

	/**
	 * Update an existing proofreader submission.
	 * @param $submission ProofreaderSubmission
	 */
	function updateSubmission(&$submission) {
		// Only update proofread-specific data
		$proofreadAssignment =& $submission->getProofAssignment();
		$this->proofAssignmentDao->updateProofAssignment($proofAssignment);
	}

	/**
	 * Get set of proofreader assignments assigned to the specified proofreader.
	 * @param $proofreaderId int
	 * @param $journalId int optional
	 * @param $searchField int SUBMISSION_FIELD_... constant
	 * @param $searchMatch String 'is' or 'contains'
	 * @param $search String Search string
	 * @param $dateField int SUBMISSION_FIELD_DATE_... constant
	 * @param $dateFrom int Search from timestamp
	 * @param $dateTo int Search to timestamp
	 * @param $active boolean true to select active assignments, false to select completed assignments
	 * @return array ProofreaderSubmission
	 */
	function &getSubmissions($proofreaderId, $journalId = null, $searchField = null, $searchMatch = null, $search = null, $dateField = null, $dateFrom = null, $dateTo = null, $active = true, $rangeInfo = null) {
		$primaryLocale = Locale::getPrimaryLocale();
		$locale = Locale::getLocale();

		$params = array(
			'title',
			$primaryLocale,
			'title',
			$locale,
			'abbrev',
			$primaryLocale,
			'abbrev',
			$locale,
			'title',
			'firstName', // Opatan Inc.
			$locale, // Opatan Inc.
			'lastName', // Opatan Inc.
			$locale, // Opatan Inc.
			'middleName', // Opatan Inc.
			$locale, // Opatan Inc.
			'firstName', // Opatan Inc.
			$locale, // Opatan Inc.
			'lastName', // Opatan Inc.
			$locale, // Opatan Inc.
			'middleName', // Opatan Inc.
			$locale,
			$proofreaderId
		);
		if (isset($journalId)) $params[] = $journalId;

		$searchSql = '';

		if (!empty($search)) switch ($searchField) {
			case SUBMISSION_FIELD_TITLE:
				if ($searchMatch === 'is') {
					$searchSql = ' AND LOWER(atl.setting_value) = LOWER(?)';
				} else {
					$searchSql = ' AND LOWER(atl.setting_value) LIKE LOWER(?)';
					$search = '%' . $search . '%';
				}
				$params[] = $search;
				break;
			case SUBMISSION_FIELD_AUTHOR:
				$first_last = $this->_dataSource->Concat('aaf.setting_value', '\' \'', 'aal.setting_value');
				$first_middle_last = $this->_dataSource->Concat('aaf.setting_value', '\' \'', 'aam.setting_value', '\' \'', 'aal.setting_value');
				$last_comma_first = $this->_dataSource->Concat('aal.setting_value', '\', \'', 'aaf.setting_value');
				$last_comma_first_middle = $this->_dataSource->Concat('aal.setting_value', '\', \'', 'aaf.setting_value', '\' \'', 'aam.setting_value');

				if ($searchMatch === 'is') {
					$searchSql = " AND (LOWER(aal.setting_value) = LOWER(?) OR LOWER($first_last) = LOWER(?) OR LOWER($first_middle_last) = LOWER(?) OR LOWER($last_comma_first) = LOWER(?) OR LOWER($last_comma_first_middle) = LOWER(?))";
				} else {
					$searchSql = " AND (LOWER(aal.setting_value) LIKE LOWER(?) OR LOWER($first_last) LIKE LOWER(?) OR LOWER($first_middle_last) LIKE LOWER(?) OR LOWER($last_comma_first) LIKE LOWER(?) OR LOWER($last_comma_first_middle) LIKE LOWER(?))";
					$search = '%' . $search . '%';
				}
				$params[] = $params[] = $params[] = $params[] = $params[] = $search;
				break;
			case SUBMISSION_FIELD_EDITOR:
				$first_last = $this->_dataSource->Concat('edsf.setting_value', '\' \'', 'edsl.setting_value');
				$first_middle_last = $this->_dataSource->Concat('edsf.setting_value', '\' \'', 'edsm.setting_value', '\' \'', 'edsl.setting_value');
				$last_comma_first = $this->_dataSource->Concat('edsl.setting_value', '\', \'', 'edsf.setting_value');
				$last_comma_first_middle = $this->_dataSource->Concat('edsl.setting_value', '\', \'', 'edsf.setting_value', '\' \'', 'edsm.setting_value');
				if ($searchMatch === 'is') {
					$searchSql = " AND (LOWER(edsl.setting_value) = LOWER(?) OR LOWER($first_last) = LOWER(?) OR LOWER($first_middle_last) = LOWER(?) OR LOWER($last_comma_first) = LOWER(?) OR LOWER($last_comma_first_middle) = LOWER(?))";
				} else {
					$searchSql = " AND (LOWER(edsl.setting_value) LIKE LOWER(?) OR LOWER($first_last) LIKE LOWER(?) OR LOWER($first_middle_last) LIKE LOWER(?) OR LOWER($last_comma_first) LIKE LOWER(?) OR LOWER($last_comma_first_middle) LIKE LOWER(?))";
					$search = '%' . $search . '%';
				}
				$params[] = $params[] = $params[] = $params[] = $params[] = $search;
				break;
		}

		if (!empty($dateFrom) || !empty($dateTo)) switch($dateField) {
			case SUBMISSION_FIELD_DATE_SUBMITTED:
				if (!empty($dateFrom)) {
					$searchSql .= ' AND a.date_submitted >= ' . $this->datetimeToDB($dateFrom);
				}
				if (!empty($dateTo)) {
					$searchSql .= ' AND a.date_submitted <= ' . $this->datetimeToDB($dateTo);
				}
				break;
			case SUBMISSION_FIELD_DATE_COPYEDIT_COMPLETE:
				if (!empty($dateFrom)) {
					$searchSql .= ' AND c.date_final_completed >= ' . $this->datetimeToDB($dateFrom);
				}
				if (!empty($dateTo)) {
					$searchSql .= ' AND c.date_final_completed <= ' . $this->datetimeToDB($dateTo);
				}
				break;
			case SUBMISSION_FIELD_DATE_LAYOUT_COMPLETE:
				if (!empty($dateFrom)) {
					$searchSql .= ' AND l.date_completed >= ' . $this->datetimeToDB($dateFrom);
				}
				if (!empty($dateTo)) {
					$searchSql .= ' AND l.date_completed <= ' . $this->datetimeToDB($dateTo);
				}
				break;
			case SUBMISSION_FIELD_DATE_PROOFREADING_COMPLETE:
				if (!empty($dateFrom)) {
					$searchSql .= ' AND p.date_proofreader_completed >= ' . $this->datetimeToDB($dateFrom);
				}
				if (!empty($dateTo)) {
					$searchSql .= 'AND p.date_proofreader_completed <= ' . $this->datetimeToDB($dateTo);
				}
				break;
		}
		$sql = 'SELECT DISTINCT
				a.*,
				COALESCE(stl.setting_value, stpl.setting_value) AS section_title,
				COALESCE(sal.setting_value, sapl.setting_value) AS section_abbrev
			FROM
				articles a
				INNER JOIN article_authors aa ON (aa.article_id = a.article_id)
				INNER JOIN proof_assignments p ON (p.article_id = a.article_id)
				LEFT JOIN sections s ON s.section_id = a.section_id
				LEFT JOIN copyed_assignments c ON (c.article_id = a.article_id)
				LEFT JOIN edit_assignments e ON (e.article_id = a.article_id)
				LEFT JOIN users ed ON (e.editor_id = ed.user_id)
				LEFT JOIN layouted_assignments l ON (l.article_id = a.article_id)
				LEFT JOIN section_settings stpl ON (s.section_id = stpl.section_id AND stpl.setting_name = ? AND stpl.locale = ?)
				LEFT JOIN section_settings stl ON (s.section_id = stl.section_id AND stl.setting_name = ? AND stl.locale = ?)
				LEFT JOIN section_settings sapl ON (s.section_id = sapl.section_id AND sapl.setting_name = ? AND sapl.locale = ?)
				LEFT JOIN section_settings sal ON (s.section_id = sal.section_id AND sal.setting_name = ? AND sal.locale = ?)
				LEFT JOIN article_settings atl ON (a.article_id = atl.article_id AND atl.setting_name = ?)
				LEFT JOIN user_settings edsf ON (ed.user_id = edsf.user_id AND edsf.setting_name = ? AND edsf.locale = ?)
				LEFT JOIN user_settings edsl ON (ed.user_id = edsl.user_id AND edsl.setting_name = ? AND edsl.locale = ?)
				LEFT JOIN user_settings edsm ON (ed.user_id = edsm.user_id AND edsm.setting_name = ? AND edsm.locale = ?)

				LEFT JOIN article_author_settings aaf ON (aa.author_id = aaf.author_id AND aaf.setting_name = ? AND aaf.locale = ?)
				LEFT JOIN article_author_settings aal ON (aa.author_id = aal.author_id AND aal.setting_name = ? AND aal.locale = ?)			
				LEFT JOIN article_author_settings aam ON (aa.author_id = aam.author_id AND aam.setting_name = ? AND aam.locale = ?)			
			WHERE
				p.proofreader_id = ? AND
				' . (isset($journalId)?'a.journal_id = ? AND':'') . '
				p.date_proofreader_notified IS NOT NULL';

		if ($active) {
			$sql .= ' AND p.date_proofreader_completed IS NULL';
		} else {
			$sql .= ' AND p.date_proofreader_completed IS NOT NULL';		
		}

		$result = &$this->retrieveRange($sql . ' ' . $searchSql, $params, $rangeInfo);

		$returner = &new DAOResultFactory ($result, $this, '_returnSubmissionFromRow');
		return $returner;
	}

	/**
	 * Get count of active and complete assignments
	 * @param proofreaderId int
	 * @param journalId int
	 */
	function getSubmissionsCount($proofreaderId, $journalId) {
		$submissionsCount = array();
		$submissionsCount[0] = 0;
		$submissionsCount[1] = 0;

		$sql = 'SELECT p.date_proofreader_completed FROM articles a INNER JOIN proof_assignments p ON (p.article_id = a.article_id) LEFT JOIN sections s ON s.section_id = a.section_id WHERE p.proofreader_id = ? AND a.journal_id = ? AND p.date_proofreader_notified IS NOT NULL';

		$result = &$this->retrieve($sql, array($proofreaderId, $journalId));

		while (!$result->EOF) {
			if ($result->fields['date_proofreader_completed'] == null) {
				$submissionsCount[0] += 1;
			} else {
				$submissionsCount[1] += 1;
			}
			$result->moveNext();
		}

		return $submissionsCount;
	}
}

?>
