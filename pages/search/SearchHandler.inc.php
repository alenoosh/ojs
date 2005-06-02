<?php

/**
 * SearchHandler.inc.php
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package pages.search
 *
 * Handle site index requests. 
 *
 * $Id$
 */

class SearchHandler extends Handler {

	/**
	 * Show basic search form.
	 */
	function index() {
		parent::validate();
		SearchHandler::setupTemplate();
		$templateMgr = &TemplateManager::getManager();

		if (Request::getJournal() == null) {
			$journalDao = &DAORegistry::getDAO('JournalDAO');
			$journals = &$journalDao->getEnabledJournalTitles(); //Enabled added
			$templateMgr->assign('siteSearch', true);
			$templateMgr->assign('journalOptions', array('' => Locale::Translate('search.allJournals')) + $journals);
			$journalPath = Request::getRequestedJournalPath();
		}
		
		$templateMgr->display('search/search.tpl');
	}
	
	/**
	 * Show basic search form.
	 */
	function search() {
		parent::validate();
		SearchHandler::index();
	}

	/**
	 * Show advanced search form.
	 */
	function advanced() {
		parent::validate();
		SearchHandler::setupTemplate(true);
		$templateMgr = &TemplateManager::getManager();
		
		if (Request::getJournal() == null) {
			$journalDao = &DAORegistry::getDAO('JournalDAO');
			$journals = &$journalDao->getEnabledJournalTitles();  //Enabled added
			$templateMgr->assign('siteSearch', true);
			$templateMgr->assign('journalOptions', array('' => Locale::Translate('search.allJournals')) + $journals);
			$journalPath = Request::getRequestedJournalPath();
		}
		
		SearchHandler::assignAdvancedSearchParameters(&$templateMgr);

		$templateMgr->display('search/advancedSearch.tpl');
	}
	
	/**
	 * Show index of published articles by author.
	 */
	function authors($args) {
		parent::validate();
		SearchHandler::setupTemplate(true);

		$journal = Request::getJournal();

		$authorDao = &DAORegistry::getDAO('AuthorDAO');

		if (isset($args[0]) && $args[0] == 'view') {
			$firstName = Request::getUserVar('firstName');
			$middleName = Request::getUserVar('middleName');
			$lastName = Request::getUserVar('lastName');
			$affiliation = Request::getUserVar('affiliation');

			$publishedArticles = $authorDao->getPublishedArticlesForAuthor($firstName, $middleName, $lastName, $affiliation);

			// Load information associated with each article.
			$issues = array();
			$sections = array();
			$issuesUnavailable = array();

			$issueDao = &DAORegistry::getDAO('IssueDAO');
			$sectionDao = &DAORegistry::getDAO('SectionDAO');

			foreach ($publishedArticles as $article) {
				$issueId = $article->getIssueId();
				$sectionId = $article->getSectionId();

				if (!isset($issues[$issueId])) {
					import('issue.IssueAction');
					$issue = &$issueDao->getIssueById($issueId);
					$issues[$issueId] = &$issue;
					$issuesUnavailable[$issueId] = IssueAction::subscriptionRequired($issue) && (!IssueAction::subscribedUser() && !IssueAction::subscribedDomain());
				}
				if (!isset($sections[$sectionId])) $sections[$sectionId] = &$sectionDao->getSection($sectionId);
			}

			if (empty($publishedArticles)) {
				Request::redirect(Request::getPageUrl());
				return;
			}

			$templateMgr = &TemplateManager::getManager();
			$templateMgr->assign('publishedArticles', $publishedArticles);
			$templateMgr->assign('issues', &$issues);
			$templateMgr->assign('issuesUnavailable', &$issuesUnavailable);
			$templateMgr->assign('sections', &$sections);
			$templateMgr->assign('firstName', $firstName);
			$templateMgr->assign('middleName', $middleName);
			$templateMgr->assign('lastName', $lastName);
			$templateMgr->assign('affiliation', $affiliation);
			$templateMgr->display('search/authorDetails.tpl');
		} else {
			$rangeInfo = Handler::getRangeInfo('authors');

			$authors = &$authorDao->getAuthorsAlphabetizedByJournal(isset($journal)?$journal->getJournalId():null, $rangeInfo);

			$templateMgr = &TemplateManager::getManager();
			$templateMgr->assign('authors', &$authors);
			$templateMgr->display('search/authorIndex.tpl');
		}
	}
	
	/**
	 * Show index of published articles by title.
	 */
	function titles($args) {
		parent::validate();
		SearchHandler::setupTemplate(true);

		$journal = Request::getJournal();

		$publishedArticleDao = &DAORegistry::getDAO('PublishedArticleDAO');

		$rangeInfo = Handler::getRangeInfo('search');

		$articleIds = &$publishedArticleDao->getPublishedArticleIdsAlphabetizedByJournal(isset($journal)?$journal->getJournalId():null, $rangeInfo);
		$totalResults = count($articleIds);
		$articleIds = &array_slice(&$articleIds, $rangeInfo->getCount() * ($rangeInfo->getPage()-1), $rangeInfo->getCount());
		$results = new VirtualArrayIterator(ArticleSearch::formatResults(&$articleIds), $totalResults, $rangeInfo->getPage(), $rangeInfo->getCount());

		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign_by_ref('results', &$results);
		$templateMgr->display('search/titleIndex.tpl');
	}
	
	/**
	 * Show basic search results.
	 */
	function results() {
		parent::validate();
		SearchHandler::setupTemplate(true);

		$rangeInfo = Handler::getRangeInfo('search');

		$journal = Request::getJournal();
		$searchJournal = Request::getUserVar('searchJournal');
		if (!empty($searchJournal)) {
			$journalDao = &DAORegistry::getDAO('JournalDAO');
			$journal = &$journalDao->getJournal($searchJournal);
		}

		$searchType = Request::getUserVar('searchField');
		if (!is_numeric($searchType)) $searchType = null;

		// Load the keywords array with submitted values
		$keywords = array($searchType => ArticleSearchIndex::getKeywords(Request::getUserVar('query')));

		$results = &ArticleSearch::retrieveResults($journal, &$keywords, null, null, $rangeInfo);

		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign_by_ref('results', &$results);
		$templateMgr->assign('basicQuery', Request::getUserVar('query'));
		$templateMgr->assign('searchField', Request::getUserVar('searchField'));
		$templateMgr->display('search/searchResults.tpl');
	}
	
	/**
	 * Show advanced search results.
	 */
	function advancedResults() {
		parent::validate();
		SearchHandler::setupTemplate(true);

		$rangeInfo = Handler::getRangeInfo('search');

		$journal = Request::getJournal();
		$searchJournal = Request::getUserVar('searchJournal');
		if (!empty($searchJournal)) {
			$journalDao = &DAORegistry::getDAO('JournalDAO');
			$journal = &$journalDao->getJournal($searchJournal);
		}

		// Load the keywords array with submitted values
		$keywords = array(null => ArticleSearchIndex::getKeywords(Request::getUserVar('query')));
		$keywords[ARTICLE_SEARCH_AUTHOR] = ArticleSearchIndex::getKeywords(Request::getUserVar('author'));
		$keywords[ARTICLE_SEARCH_TITLE] = ArticleSearchIndex::getKeywords(Request::getUserVar('title'));
		$keywords[ARTICLE_SEARCH_DISCIPLINE] = ArticleSearchIndex::getKeywords(Request::getUserVar('discipline'));
		$keywords[ARTICLE_SEARCH_SUBJECT] = ArticleSearchIndex::getKeywords(Request::getUserVar('subject'));
		$keywords[ARTICLE_SEARCH_TYPE] = ArticleSearchIndex::getKeywords(Request::getUserVar('type'));
		$keywords[ARTICLE_SEARCH_COVERAGE] = ArticleSearchIndex::getKeywords(Request::getUserVar('coverage'));
		$keywords[ARTICLE_SEARCH_GALLEY_FILE] = ArticleSearchIndex::getKeywords(Request::getUserVar('fullText'));
		$keywords[ARTICLE_SEARCH_SUPPLEMENTARY_FILE] = ArticleSearchIndex::getKeywords(Request::getUserVar('supplementaryFiles'));

		$fromMonth = Request::getUserVar('dateFromMonth');
                $fromDay = Request::getUserVar('dateFromDay');
                $fromYear = Request::getUserVar('dateFromYear');
		if (!empty($fromYear)) $fromDate = date('Y-m-d H:i:s',mktime(0,0,0,$fromMonth==null?12:$fromMonth,$fromDay==null?31:$fromDay,$fromYear));
		else $fromDate = null;

		$toMonth = Request::getUserVar('dateToMonth');
                $toDay = Request::getUserVar('dateToDay');
                $toYear = Request::getUserVar('dateToYear');
		if (!empty($toYear)) $toDate = date('Y-m-d H:i:s',mktime(23,59,0,$toMonth==null?12:$toMonth,$toDay==null?31:$toDay,$toYear));
		else $toDate = null;

		$results = &ArticleSearch::retrieveResults($journal, &$keywords, $fromDate, $toDate, $rangeInfo);

		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign_by_ref('results', &$results);
		SearchHandler::assignAdvancedSearchParameters(&$templateMgr);

		$templateMgr->display('search/searchResults.tpl');
	}
	
	/**
	 * Setup common template variables.
	 * @param $subclass boolean set to true if caller is below this handler in the hierarchy
	 */
	function setupTemplate($subclass = false) {
		parent::validate();
		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('helpTopicId', 'user.searchAndBrowse');
		$templateMgr->assign('pageHierarchy',
			$subclass ? array(array('search', 'navigation.search'))
				: array()
		);
	}

	function assignAdvancedSearchParameters(&$templateMgr) {
		$templateMgr->assign('query', Request::getUserVar('query'));
		$templateMgr->assign('searchJournal', Request::getUserVar('searchJournal'));
		$templateMgr->assign('author', Request::getUserVar('author'));
		$templateMgr->assign('title', Request::getUserVar('title'));
		$templateMgr->assign('fullText', Request::getUserVar('fullText'));
		$templateMgr->assign('supplementaryFiles', Request::getUserVar('supplementaryFiles'));
		$templateMgr->assign('discipline', Request::getUserVar('discipline'));
		$templateMgr->assign('subject', Request::getUserVar('subject'));
		$templateMgr->assign('type', Request::getUserVar('type'));
		$templateMgr->assign('coverage', Request::getUserVar('coverage'));
		$fromMonth = Request::getUserVar('dateFromMonth');
                $fromDay = Request::getUserVar('dateFromDay');
                $fromYear = Request::getUserVar('dateFromYear');
		$templateMgr->assign('dateFromMonth', $fromMonth);
		$templateMgr->assign('dateFromDay', $fromDay);
		$templateMgr->assign('dateFromYear', $fromYear);
		if (!empty($fromYear)) $templateMgr->assign('dateFrom', date('Y-m-d H:i:s',mktime(0,0,0,$fromMonth==null?12:$fromMonth,$fromDay==null?31:$fromDay,$fromYear)));

		$toMonth = Request::getUserVar('dateToMonth');
                $toDay = Request::getUserVar('dateToDay');
                $toYear = Request::getUserVar('dateToYear');
		$templateMgr->assign('dateToMonth', $toMonth);
		$templateMgr->assign('dateToDay', $toDay);
		$templateMgr->assign('dateToYear', $toYear);
		if (!empty($toYear)) $templateMgr->assign('dateTo', date('Y-m-d H:i:s',mktime(0,0,0,$toMonth==null?12:$toMonth,$toDay==null?31:$toDay,$toYear)));
	}
}

?>
