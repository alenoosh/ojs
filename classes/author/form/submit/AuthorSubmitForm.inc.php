<?php

/**
 * AuthorSubmitForm.inc.php
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package author.form.submit
 *
 * Base class for journal author submit forms.
 *
 * $Id$
 */

import('form.Form');

class AuthorSubmitForm extends Form {

	/** @var int the ID of the article */
	var $articleId;
	
	/** @var Article current article */
	var $article;

	/** @var int the current step */
	var $step;
	
	/**
	 * Constructor.
	 * @param $articleId int
	 * @param $step int
	 */
	function AuthorSubmitForm($articleId, $step) {
		parent::Form(sprintf('author/submit/step%d.tpl', $step));
		$this->step = $step;
		if (isset($articleId) && !empty($articleId)) {
			$articleDao = &DAORegistry::getDAO('ArticleDAO');
			$this->article = &$articleDao->getArticle($articleId);
			if (isset($this->article)) {
				$this->articleId = $this->article->getArticleId();
			}
		}
	}
	
	/**
	 * Display the form.
	 */
	function display() {
		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('sidebarTemplate', 'author/submit/submitSidebar.tpl');
		$templateMgr->assign('articleId', $this->articleId);
		$templateMgr->assign('submitStep', $this->step);
		
		if (isset($this->article)) {
			$templateMgr->assign('submissionProgress', $this->article->getSubmissionProgress());
		}
		
		switch($this->step) {
			case '2':
				$helpTopicId = 'submission.indexingAndMetadata';
				break;
			case '4':
				$helpTopicId = 'submission.supplementaryFiles';
				break;
			default:
				$helpTopicId = 'submission.index';
		}
		$templateMgr->assign('helpTopicId', $helpTopicId);

		$journal = &Request::getJournal();
		$settingsDao = &DAORegistry::getDAO('JournalSettingsDAO');
		$templateMgr->assign_by_ref('journalSettings', $settingsDao->getJournalSettings($journal->getJournalId()));
		
		parent::display();
	}
	
}

?>
