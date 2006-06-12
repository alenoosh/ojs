<?php

/**
 * AuthorSubmitStep5Form.inc.php
 *
 * Copyright (c) 2003-2006 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package author.form.submit
 *
 * Form for Step 5 of author article submission.
 *
 * $Id$
 */

import("author.form.submit.AuthorSubmitForm");

class AuthorSubmitStep5Form extends AuthorSubmitForm {
	
	/**
	 * Constructor.
	 */
	function AuthorSubmitStep5Form($article) {
		parent::AuthorSubmitForm($article, 5);
	}
	
	/**
	 * Display the form.
	 */
	function display() {
		$templateMgr = &TemplateManager::getManager();
		
		// Get article file for this article
		$articleFileDao = &DAORegistry::getDAO('ArticleFileDAO');
		$articleFiles =& $articleFileDao->getArticleFilesByArticle($this->articleId);

		$templateMgr->assign_by_ref('files', $articleFiles);
		$templateMgr->assign_by_ref('journal', Request::getJournal());

		parent::display();
	}
	
	/**
	 * Save changes to article.
	 */
	function execute() {
		$articleDao = &DAORegistry::getDAO('ArticleDAO');

		$journal = Request::getJournal();

		// Update article
		$article = &$this->article;
		$article->setDateSubmitted(Core::getCurrentDate());
		$article->setSubmissionProgress(0);
		$article->stampStatusModified();
		$articleDao->updateArticle($article);

		// Designate this as the review version by default.
		$authorSubmissionDao =& DAORegistry::getDAO('AuthorSubmissionDAO');
		$authorSubmission =& $authorSubmissionDao->getAuthorSubmission($article->getArticleId());
		AuthorAction::designateReviewVersion($authorSubmission, true);
		unset($authorSubmission);

		// Create additional submission mangement records
		$copyeditorSubmissionDao = &DAORegistry::getDAO('CopyeditorSubmissionDAO');
		$copyeditorSubmission = &new CopyeditorSubmission();
		$copyeditorSubmission->setArticleId($article->getArticleId());
		$copyeditorSubmission->setCopyeditorId(0);
		$copyeditorSubmissionDao->insertCopyeditorSubmission($copyeditorSubmission);
		
		$layoutDao = &DAORegistry::getDAO('LayoutAssignmentDAO');
		$layoutAssignment = &new LayoutAssignment();
		$layoutAssignment->setArticleId($article->getArticleId());
		$layoutAssignment->setEditorId(0);
		$layoutDao->insertLayoutAssignment($layoutAssignment);

		$proofAssignmentDao = &DAORegistry::getDAO('ProofAssignmentDAO');
		$proofAssignment = &new ProofAssignment();
		$proofAssignment->setArticleId($article->getArticleId());
		$proofAssignment->setProofreaderId(0);
		$proofAssignmentDao->insertProofAssignment($proofAssignment);
		
		$user = &Request::getUser();
		
		// Update search index
		import('search.ArticleSearchIndex');
		ArticleSearchIndex::indexArticleMetadata($article);
		ArticleSearchIndex::indexArticleFiles($article);

		// Send author notification email
		import('mail.ArticleMailTemplate');
		$mail = &new ArticleMailTemplate($article, 'SUBMISSION_ACK');
		$mail->setFrom($journal->getSetting('contactEmail'), $journal->getSetting('contactName'));
		if ($mail->isEnabled()) {
			$mail->addRecipient($user->getEmail(), $user->getFullName());
			// If necessary, BCC the acknowledgement to someone.
			if($journal->getSetting('copySubmissionAckPrimaryContact')) {
				$mail->addBcc(
					$journal->getSetting('contactEmail'),
					$journal->getSetting('contactName')
				);
			}
			if($journal->getSetting('copySubmissionAckSpecified')) {
				$copyAddress = $journal->getSetting('copySubmissionAckAddress');
				if (!empty($copyAddress)) $mail->addBcc($copyAddress);
			}

			$mail->assignParams(array(
				'authorName' => $user->getFullName(),
				'authorUsername' => $user->getUsername(),
				'editorialContactSignature' => $journal->getSetting('contactName') . "\n" . $journal->getTitle(),
				'submissionUrl' => Request::url(null, 'author', 'submission', $article->getArticleId())
			));
			$mail->send();
		}

		import('article.log.ArticleLog');
		import('article.log.ArticleEventLogEntry');
		ArticleLog::logEvent($this->articleId, ARTICLE_LOG_ARTICLE_SUBMIT, ARTICLE_LOG_TYPE_AUTHOR, $user->getUserId(), 'log.author.submitted', array('submissionId' => $article->getArticleId(), 'authorName' => $user->getFullName()));
		
		return $this->articleId;
	}
	
}

?>
