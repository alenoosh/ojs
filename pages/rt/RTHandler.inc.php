<?php

/**
 * RTHandler.inc.php
 *
 * Copyright (c) 2003-2005 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package pages.rt
 *
 * Handle Reading Tools requests. 
 *
 * $Id$
 */

import('rt.RT');

import('rt.ojs.RTDAO');
import('rt.ojs.JournalRT');

import('article.ArticleHandler');

class RTHandler extends ArticleHandler {
	function bio($args) {
		$journal = &Request::getJournal();
		$rtDao = &DAORegistry::getDAO('RTDAO');
		$journalRt = &$rtDao->getJournalRTByJournalId($journal->getJournalId());

		if (!$journalRt || !$journalRt->getAuthorBio()) {
			Request::redirect(Request::getPageUrl());
			return;
		}

		$articleId = isset($args[0]) ? (int) $args[0] : 0;
		$galleyId = isset($args[1]) ? (int) $args[1] : 0;
		RTHandler::validate($articleId, $galleyId);

		RTHandler::setupTemplate($articleId);

		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('articleId', $articleId);
		$templateMgr->assign('galleyId', $galleyId);
		$templateMgr->display('rt/bio.tpl');
	}
	
	function metadata($args) {
		$journal = &Request::getJournal();
		$rtDao = &DAORegistry::getDAO('RTDAO');
		$journalRt = &$rtDao->getJournalRTByJournalId($journal->getJournalId());

		if (!$journalRt || !$journalRt->getViewMetadata()) {
			Request::redirect(Request::getPageUrl());
			return;
		}

		$articleId = isset($args[0]) ? (int) $args[0] : 0;
		$galleyId = isset($args[1]) ? (int) $args[1] : 0;
		RTHandler::validate($articleId, $galleyId);

		RTHandler::setupTemplate($articleId);

		$publishedArticleDao = &DAORegistry::getDAO('PublishedArticleDAO');
		$publishedArticle = &$publishedArticleDao->getPublishedArticleByArticleId($articleId);

		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('articleId', $articleId);
		$templateMgr->assign('galleyId', $galleyId);
		$templateMgr->assign('publishedArticle', $publishedArticle);
		$templateMgr->assign('journalSettings', $journal->getSettings());
		$templateMgr->display('rt/metadata.tpl');
	}
	
	function context($args) {
		$journal = &Request::getJournal();
		$rtDao = &DAORegistry::getDAO('RTDAO');
		$journalRt = &$rtDao->getJournalRTByJournalId($journal->getJournalId());

		$articleId = isset($args[0]) ? (int) $args[0] : 0;
		$galleyId = isset($args[1]) ? (int) $args[1] : 0;
		$contextId = Isset($args[2]) ? (int) $args[2] : 0;

		$context = &$rtDao->getContext($contextId);
		$version = &$rtDao->getVersion($context->getVersionId(), $journal->getJournalId());

		if (!$journalRt || $journalRt->getVersion() !=  $context->getVersionId() || !$version) {
			Request::redirect(Request::getPageUrl());
			return;
		}

		RTHandler::validate($articleId, $galleyId);

		RTHandler::setupTemplate($articleId);

		$articleDao = &DAORegistry::getDAO('ArticleDAO');
		$article = $articleDao->getArticle($articleId);
		$publishedArticleDao = &DAORegistry::getDAO('PublishedArticleDAO');
		$publishedArticle = &$publishedArticleDao->getPublishedArticleByArticleId($articleId);

		// Deal with the post and URL parameters for each search
		// so that the client browser can properly submit the forms
		// with a minimum of client-side processing.
		$searches = array();
		foreach ($context->getSearches() as $search) {
			$postParams = explode('&', $search->getSearchPost());
			$params = array();
			foreach ($postParams as $param) {
				// Split name and value from each parameter
				$nameValue = explode('=', $param);
				if (!isset($nameValue[0])) break;

				$name = trim($nameValue[0]);
				$value = trim(isset($nameValue[1])?$nameValue[1]:'');
				if (!empty($name)) $params[] = array('name' => $name, 'value' => $value);
			}

			if (count($params)!=0) {
				$lastElement = &$params[count($params)-1];
				if ($lastElement['value']=='') $lastElement['needsKeywords'] = true;
			}

			$search->postParams = $params;
			$search->urlNeedsKeywords = substr($search->getSearchUrl(), -1, 1)=='=';
			$searches[] = $search;
		}

		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('articleId', $articleId);
		$templateMgr->assign('galleyId', $galleyId);
		$templateMgr->assign('publishedArticle', $publishedArticle);
		$templateMgr->assign('version', $version);
		$templateMgr->assign('context', $context);
		$templateMgr->assign('searches', &$searches);
		$templateMgr->assign('defineTerm', Request::getUserVar('defineTerm'));
		$templateMgr->assign('keywords', explode(';', $article->getSubject()));
		$templateMgr->assign('journalSettings', $journal->getSettings());
		$templateMgr->display('rt/context.tpl');
	}
	
	function cite($args) {
		// FIXME
	}
	
	function printerFriendly($args) {
		// FIXME
	}
	
	function emailColleague($args) {
		$journal = &Request::getJournal();
		$rtDao = &DAORegistry::getDAO('RTDAO');
		$journalRt = &$rtDao->getJournalRTByJournalId($journal->getJournalId());
		$user = &Request::getUser();

		if (!$journalRt || !$journalRt->getEmailOthers() || !$user) {
			Request::redirect(Request::getPageUrl());
			return;
		}

		$articleId = isset($args[0]) ? (int) $args[0] : 0;
		$galleyId = isset($args[1]) ? (int) $args[1] : 0;

		$articleDao = &DAORegistry::getDAO('ArticleDAO');
		$article = $articleDao->getArticle($articleId);

		RTHandler::validate($articleId, $galleyId);

		RTHandler::setupTemplate($articleId);

		$publishedArticleDao = &DAORegistry::getDAO('PublishedArticleDAO');
		$publishedArticle = &$publishedArticleDao->getPublishedArticleByArticleId($articleId);

		$email = &new MailTemplate();
		$email->setFrom($user->getEmail(), $user->getFullName());

		if (Request::getUserVar('send') && !$email->hasErrors()) {
			$email->send();

			$templateMgr = &TemplateManager::getManager();
			$templateMgr->display('rt/sent.tpl');
		} else {
			if (!Request::getUserVar('continued')) {
				$email->setSubject('[' . $journal->getSetting('journalInitials') . '] ' . $article->getArticleTitle());
			}
			$email->displayEditForm(Request::getPageUrl() . '/' . Request::getRequestedPage() . '/emailColleague/' . $articleId . '/' . $galleyId, null, 'rt/email.tpl');
		}
	}

	function emailAuthor($args) {
		$journal = &Request::getJournal();
		$rtDao = &DAORegistry::getDAO('RTDAO');
		$journalRt = &$rtDao->getJournalRTByJournalId($journal->getJournalId());
		$user = &Request::getUser();

		if (!$journalRt || !$journalRt->getEmailAuthor() || !$user) {
			Request::redirect(Request::getPageUrl());
			return;
		}

		$articleId = isset($args[0]) ? (int) $args[0] : 0;
		$galleyId = isset($args[1]) ? (int) $args[1] : 0;

		$articleDao = &DAORegistry::getDAO('ArticleDAO');
		$article = $articleDao->getArticle($articleId);

		RTHandler::validate($articleId, $galleyId);

		RTHandler::setupTemplate($articleId);

		$publishedArticleDao = &DAORegistry::getDAO('PublishedArticleDAO');
		$publishedArticle = &$publishedArticleDao->getPublishedArticleByArticleId($articleId);

		$email = &new MailTemplate();
		$email->setFrom($user->getEmail(), $user->getFullName());

		if (Request::getUserVar('send') && !$email->hasErrors()) {
			$email->send();

			$templateMgr = &TemplateManager::getManager();
			$templateMgr->display('rt/sent.tpl');
		} else {
			if (!Request::getUserVar('continued')) {
				$email->setSubject('[' . $journal->getSetting('journalInitials') . '] ' . $article->getArticleTitle());
				$authors = &$article->getAuthors();
				$author = &$authors[0];
				$email->addRecipient($author->getEmail(), $author->getFullName());
			}
			$email->displayEditForm(Request::getPageUrl() . '/' . Request::getRequestedPage() . '/emailAuthor/' . $articleId . '/' . $galleyId, null, 'rt/email.tpl');
		}
	}

	function addComment($args) {
	}
	
	function suppFiles($args) {
	}
	
	function suppFileMetadata($args) {
	}
}

?>
