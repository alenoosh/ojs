<?php

/**
 * @file AuthorSubmitStep2MergedForm.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package author.form.submit
 * @class AuthorSubmitStep2MergedForm
 *
 * Form for Step 2 of author article submission.
 *
 * $Id$
 */

/* Opatan Inc. : Form for Merged Step 2 */

import("author.form.submit.AuthorSubmitForm");

class AuthorSubmitStep2MergedForm extends AuthorSubmitForm {
	/** @var int the ID of the article */
	var $articleId;

	/** @var int the ID of the supplementary file */
	var $suppFileId;

	/** @var Article current article */
	var $article;

	/** @var SuppFile current file */
	var $suppFile;

	/**
	 * Constructor.
	 */
	function AuthorSubmitStep2MergedForm($article, $suppFileId = null) {
		parent::AuthorSubmitForm($article, 2);

		$journal = &Request::getJournal();

		$this->articleId = $article->getArticleId();

		if (isset($suppFileId) && !empty($suppFileId)) {
			$suppFileDao = &DAORegistry::getDAO('SuppFileDAO');
			$this->suppFile = &$suppFileDao->getSuppFile($suppFileId, $article->getArticleId());
			if (isset($this->suppFile)) {
				$this->suppFileId = $suppFileId;
			}
		}

		// Validation checks for this form
		$this->addCheck(new FormValidatorCustom($this, 'authors', 'required', 'author.submit.form.authorRequired', create_function('$authors', 'return count($authors) > 0;')));
		$this->addCheck(new FormValidatorArray($this, 'authors', 'required', 'author.submit.form.authorRequiredFields', array('firstName', 'lastName', 'email')));
		$this->addCheck(new FormValidatorLocale($this, 'title', 'required', 'author.submit.form.titleRequired'));
		// Validation checks for this form
		//$this->addCheck(new FormValidatorLocale($this, 'supp_title', 'required', 'author.submit.suppFile.form.titleRequired'));
		$this->addCheck(new FormValidatorPost($this));        
	}

	/**
	 * Initialize form data from current article.
	 */
	function initData() {
		$sectionDao = &DAORegistry::getDAO('SectionDAO');
		if (isset($this->article)) {
			$article = &$this->article;
			$this->_data = array(
				'authors' => array(),
				'title' => $article->getTitle(null), // Localized
				// Opatan Inc. : runningTitle is added
	        	        'runningTitle' => $article->getRunningTitle(null), // Localized 
 			    	'abstract' => $article->getAbstract(null), // Localized
				'discipline' => $article->getDiscipline(null), // Localized
				'subjectClass' => $article->getSubjectClass(null), // Localized
				'subject' => $article->getSubject(null), // Localized
				'coverageGeo' => $article->getCoverageGeo(null), // Localized
				'coverageChron' => $article->getCoverageChron(null), // Localized
				'coverageSample' => $article->getCoverageSample(null), // Localized
				'type' => $article->getType(null), // Localized
				'language' => $article->getLanguage(),
				'sponsor' => $article->getSponsor(null), // Localized
				'section' => $sectionDao->getSection($article->getSectionId())
			);

			$authors = &$article->getAuthors();
			for ($i=0, $count=count($authors); $i < $count; $i++) {
				array_push(
					$this->_data['authors'],
					array(
						'authorId' => $authors[$i]->getAuthorId(),
						'firstName' => $authors[$i]->getFirstName(),
						'middleName' => $authors[$i]->getMiddleName(),
						'lastName' => $authors[$i]->getLastName(),
						'affiliation' => $authors[$i]->getAffiliation(),
						'country' => $authors[$i]->getCountry(),
						'email' => $authors[$i]->getEmail(),
						'url' => $authors[$i]->getUrl(),
						'competingInterests' => $authors[$i]->getCompetingInterests(null),
						'biography' => $authors[$i]->getBiography(null)
					)
				);
				if ($authors[$i]->getPrimaryContact()) {
					$this->setData('primaryContact', $i);
				}
			}
		}
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(
			array(
				'authors',
				'deletedAuthors',
				'primaryContact',
				'title',
			        'runningTitle', // Opatan Inc. : runningTitle is added
     				'abstract',
				'discipline',
				'subjectClass',
				'subject',
				'coverageGeo',
				'coverageChron',
				'coverageSample',
				'type',
				'language',
				'sponsor',
                'supp_title'
			)
		);

		// Load the section. This is used in the step 2 form to
		// determine whether or not to display indexing options.
		$sectionDao = &DAORegistry::getDAO('SectionDAO');
		$this->_data['section'] = &$sectionDao->getSection($this->article->getSectionId());

		if ($this->_data['section']->getAbstractsDisabled() == 0) {
			$this->addCheck(new FormValidatorLocale($this, 'abstract', 'required', 'author.submit.form.abstractRequired'));
		}

	}

	/**
	 * Get the names of fields for which data should be localized
	 * @return array
	 */
	function getLocaleFieldNames() {
		// Opatan Inc. : runningTitle is added
		return array('title', 'runningTitle', 'abstract', 'subjectClass', 'subject', 'coverageGeo', 'coverageChron', 'coverageSample', 'type', 'sponsor');
	}

	/**
	 * Display the form.
	 */
	function display() {
		$templateMgr =& TemplateManager::getManager();

		$countryDao =& DAORegistry::getDAO('CountryDAO');
		$countries =& $countryDao->getCountries();
		$templateMgr->assign_by_ref('countries', $countries);

		// Get submission files for this article
		$articleFileDao = &DAORegistry::getDAO('ArticleFileDAO');
		if ($this->article->getSubmissionFileId() != null) {
            $templateMgr->assign_by_ref('submissionFile', $articleFileDao->getArticleFile($this->article->getSubmissionFileId()));
		}

		// Get supplementary files for this article
		$suppFileDao = &DAORegistry::getDAO('SuppFileDAO');
		$templateMgr->assign_by_ref('suppFiles', $suppFileDao->getSuppFilesByArticle($this->articleId));

 		parent::display();
	}

	/**
	 * Save changes to article.
	 * @return int the article ID
	 */
	function execute() {
		$articleDao = &DAORegistry::getDAO('ArticleDAO');
		$authorDao = &DAORegistry::getDAO('AuthorDAO');

		// Update article
		$article = &$this->article;
		$article->setTitle($this->getData('title'), null); // Localized
		// Opatan Inc. : runningTitle is added
	 	$article->setRunningTitle($this->getData('runningTitle'), null); // Localized
	    	$article->setAbstract($this->getData('abstract'), null); // Localized
		$article->setDiscipline($this->getData('discipline'), null); // Localized
		$article->setSubjectClass($this->getData('subjectClass'), null); // Localized
		$article->setSubject($this->getData('subject'), null); // Localized
		$article->setCoverageGeo($this->getData('coverageGeo'), null); // Localized
		$article->setCoverageChron($this->getData('coverageChron'), null); // Localized
		$article->setCoverageSample($this->getData('coverageSample'), null); // Localized
		$article->setType($this->getData('type'), null); // Localized
		$article->setLanguage($this->getData('language'));
		$article->setSponsor($this->getData('sponsor'), null); // Localized
		if ($article->getSubmissionProgress() <= $this->step) {
			$article->stampStatusModified();
			$article->setSubmissionProgress($this->step + 3);  // OPATAN: ($this->step + 1) changed to ($this->step + 3)
		}

		// Update authors
		$authors = $this->getData('authors');
		for ($i=0, $count=count($authors); $i < $count; $i++) {
			if ($authors[$i]['authorId'] > 0) {
				// Update an existing author
				$author = &$article->getAuthor($authors[$i]['authorId']);
				$isExistingAuthor = true;

			} else {
				// Create a new author
				$author = &new Author();
				$isExistingAuthor = false;
			}

			if ($author != null) {
				$author->setFirstName($authors[$i]['firstName']);
				$author->setMiddleName($authors[$i]['middleName']);
				$author->setLastName($authors[$i]['lastName']);
				$author->setAffiliation($authors[$i]['affiliation']);
				$author->setCountry($authors[$i]['country']);
				$author->setEmail($authors[$i]['email']);
				$author->setUrl($authors[$i]['url']);
				if (array_key_exists('competingInterests', $authors[$i])) {
					$author->setCompetingInterests($authors[$i]['competingInterests'], null);
				}
				$author->setBiography($authors[$i]['biography'], null);
				$author->setPrimaryContact($this->getData('primaryContact') == $i ? 1 : 0);
				$author->setSequence($authors[$i]['seq']);

				if ($isExistingAuthor == false) {
					$article->addAuthor($author);
				}
			}
		}

		// Remove deleted authors
		$deletedAuthors = explode(':', $this->getData('deletedAuthors'));
		for ($i=0, $count=count($deletedAuthors); $i < $count; $i++) {
			$article->removeAuthor($deletedAuthors[$i]);
		}

		// Save the article
		$articleDao->updateArticle($article);

		return $this->articleId;
	}

 	/**
	 * Upload the submission file.
	 * @param $fileName string
	 * @return boolean
	 */
	function uploadSubmissionFile($fileName) {
		import("file.ArticleFileManager");

		$articleFileManager = &new ArticleFileManager($this->articleId);
		$articleDao = &DAORegistry::getDAO('ArticleDAO');

		if ($articleFileManager->uploadedFileExists($fileName)) {
			// upload new submission file, overwriting previous if necessary
			$submissionFileId = $articleFileManager->uploadSubmissionFile($fileName, $this->article->getSubmissionFileId(), true);
		}

		if (isset($submissionFileId)) {
			$this->article->setSubmissionFileId($submissionFileId);
			return $articleDao->updateArticle($this->article);

		} else {
			return false;
		}
	}   

 	/**
	 * Save changes to the supplementary file.
	 * @return int the supplementary file ID
	 */
	function uploadSuppFile() {
		import("file.ArticleFileManager");
		$articleFileManager = &new ArticleFileManager($this->articleId);
		$suppFileDao = &DAORegistry::getDAO('SuppFileDAO');

		$fileName = 'uploadSuppFile';

		// edit an existing supp file, otherwise create new supp file entry	
		if (isset($this->suppFile)) {
			$suppFile = &$this->suppFile;

			// Remove old file and upload new, if file is selected.
			if ($articleFileManager->uploadedFileExists($fileName)) {
				$articleFileDao = &DAORegistry::getDAO('ArticleFileDAO');
				$suppFileId = $articleFileManager->uploadSuppFile($fileName, $suppFile->getFileId(), true);
				$suppFile->setFileId($suppFileId);
			}

			// Update existing supplementary file
			$this->setSuppFileData($suppFile);
			$suppFileDao->updateSuppFile($suppFile);

		} else {
			// Upload file, if file selected.
			if ($articleFileManager->uploadedFileExists($fileName)) {
				$fileId = $articleFileManager->uploadSuppFile($fileName);
			} else {
				$fileId = 0;
			}

			// Insert new supplementary file		
			$suppFile = &new SuppFile();
			$suppFile->setArticleId($this->articleId);
			$suppFile->setFileId($fileId);
			$this->setSuppFileData($suppFile);
			$suppFileDao->insertSuppFile($suppFile);
			$this->suppFileId = $suppFile->getSuppFileId();
		}

		return $this->suppFileId;
	}   

 	/**
	 * Assign form data to a SuppFile.
	 * @param $suppFile SuppFile
	 */
	function setSuppFileData(&$suppFile) {
		$suppFile->setTitle($this->getData('supp_title'), null); // Null
		//$suppFile->setCreator($this->getData('creator'), null); // Null
		//$suppFile->setSubject($this->getData('subject'), null); // Null
		//$suppFile->setType($this->getData('type'));
		//$suppFile->setTypeOther($this->getData('typeOther'), null); // Null
		//$suppFile->setDescription($this->getData('description'), null); // Null
		//$suppFile->setPublisher($this->getData('publisher'), null); // Null
		//$suppFile->setSponsor($this->getData('sponsor'), null); // Null
		$suppFile->setDateCreated($this->getData('dateCreated') == '' ? Core::getCurrentDate() : $this->getData('dateCreated'));
		//$suppFile->setSource($this->getData('source'), null); // Null
		//$suppFile->setLanguage($this->getData('language'));
		//$suppFile->setShowReviewers($this->getData('showReviewers'));
	}   
}

?>
