<?php

/**
 * @file AuthorDAO.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package article
 * @class AuthorDAO
 *
 * Class for Author DAO.
 * Operations for retrieving and modifying Author objects.
 *
 * $Id$
 */

import('article.Author');
import('article.Article');

class AuthorDAO extends DAO {
	/**
	 * Retrieve an author by ID.
	 * @param $authorId int
	 * @return Author
	 */
	function &getAuthor($authorId) {
		$locale = Locale::getLocale();
		$result = &$this->retrieve(
			'SELECT 
				aa.*, 				
				aaf.setting_value AS first_name,
				aam.setting_value AS middle_name,
				aal.setting_value AS last_name,
				aaaf.setting_value AS affiliation 
			 FROM 
			 	article_authors aa
				LEFT JOIN article_author_settings aaf ON (aa.author_id = aaf.author_id AND aaf.setting_name = ? AND aaf.locale = ?)
				LEFT JOIN article_author_settings aal ON (aa.author_id = aal.author_id AND aal.setting_name = ? AND aal.locale = ?)			
				LEFT JOIN article_author_settings aam ON (aa.author_id = aam.author_id AND aam.setting_name = ? AND aam.locale = ?)
				LEFT JOIN article_author_settings aaaf ON (aa.author_id = aaaf.author_id AND aaaf.setting_name = ? AND aaaf.locale = ?)
			 WHERE author_id = ?', 
			 array('firstName', $locale, 'lastName', $locale, 'middleName', 
			       $locale, 'affiliation', $locale, $authorId)
		);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = &$this->_returnAuthorFromRow($result->GetRowAssoc(false));
		}

		$result->Close();
		unset($result);

		return $returner;
	}

	/**
	 * Retrieve all authors for an article.
	 * @param $articleId int
	 * @return array Authors ordered by sequence
	 */
	function &getAuthorsByArticle($articleId) {
		$locale = Locale::getLocale();
		$authors = array();
		$result = &$this->retrieve(
			'SELECT 
				aa.*,
				aaf.setting_value AS first_name,
				aam.setting_value AS middle_name,
				aal.setting_value AS last_name,
				aaaf.setting_value AS affiliation
			 FROM 
			 	article_authors aa
				LEFT JOIN article_author_settings aaf ON (aa.author_id = aaf.author_id AND aaf.setting_name = ? AND aaf.locale = ?)
				LEFT JOIN article_author_settings aal ON (aa.author_id = aal.author_id AND aal.setting_name = ? AND aal.locale = ?)			
				LEFT JOIN article_author_settings aam ON (aa.author_id = aam.author_id AND aam.setting_name = ? AND aam.locale = ?)
				LEFT JOIN article_author_settings aaaf ON (aa.author_id = aaaf.author_id AND aaaf.setting_name = ? AND aaaf.locale = ?)
			 WHERE article_id = ? ORDER BY seq',
			 array('firstName', $locale, 'lastName', $locale, 'middleName', 
			       $locale, 'affiliation', $locale, $articleId)
		);

		while (!$result->EOF) {
			$authors[] = &$this->_returnAuthorFromRow($result->GetRowAssoc(false));
			$result->moveNext();
		}

		$result->Close();
		unset($result);

		return $authors;
	}

	/**
	 * Retrieve all published articles associated with authors with
	 * the given first name, middle name, last name, affiliation, and country.
	 * @param $journalId int (null if no restriction desired)
	 * @param $firstName string
	 * @param $middleName string
	 * @param $lastName string
	 * @param $affiliation string
	 * @param $country string
	 */
	function &getPublishedArticlesForAuthor($journalId, $firstName, $middleName, $lastName, $affiliation, $country) {
		$publishedArticles = array();
		$locale = Locale::getLocale();
		$publishedArticleDao = &DAORegistry::getDAO('PublishedArticleDAO');
		$params = array('firstName', $locale, 'lastName', $locale, 'middleName', $locale, 'affiliation', $locale, $firstName, $middleName, $lastName, $affiliation, $country);
		if ($journalId !== null) $params[] = $journalId;

		$result = &$this->retrieve(
			'SELECT DISTINCT
				aa.article_id
			FROM article_authors aa
				LEFT JOIN articles a ON (aa.article_id = a.article_id)
				LEFT JOIN article_author_settings aaf ON (aa.author_id = aaf.author_id AND aaf.setting_name = ? AND aaf.locale = ?)
				LEFT JOIN article_author_settings aal ON (aa.author_id = aal.author_id AND aal.setting_name = ? AND aal.locale = ?)			
				LEFT JOIN article_author_settings aam ON (aa.author_id = aam.author_id AND aam.setting_name = ? AND aam.locale = ?)
				LEFT JOIN article_author_settings aaaf ON (aa.author_id = aaaf.author_id AND aaaf.setting_name = ? AND aaaf.locale = ?)			
			WHERE	aaf.setting_value = ?
				AND a.status = ' . STATUS_PUBLISHED . '
				AND (aam.setting_value = ?' . (empty($middleName)?' OR aam.setting_value IS NULL':'') . ')
				AND aal.setting_value = ?
				AND (aaaf.setting_value = ?' . (empty($affiliation)?' OR aaaf.setting_value IS NULL':'') . ')
				AND (aa.country = ?' . (empty($country)?' OR aa.country IS NULL':'') . ') ' . 
				($journalId!==null?(' AND a.journal_id = ?'):''),
			$params
		);

		while (!$result->EOF) {
			$row = &$result->getRowAssoc(false);
			$publishedArticle = &$publishedArticleDao->getPublishedArticleByArticleId($row['article_id']);
			if ($publishedArticle) {
				$publishedArticles[] = &$publishedArticle;
			}
			$result->moveNext();
		}

		$result->Close();
		unset($result);

		return $publishedArticles;
	}

	/**
	 * Retrieve all published authors for a journal in an associative array by
	 * the first letter of the last name, for example:
	 * $returnedArray['S'] gives array($misterSmithObject, $misterSmytheObject, ...)
	 * Keys will appear in sorted order. Note that if journalId is null,
	 * alphabetized authors for all journals are returned.
	 * @param $journalId int
	 * @param $initial An initial the last names must begin with
	 * @return array Authors ordered by sequence
	 */
	function &getAuthorsAlphabetizedByJournal($journalId = null, $initial = null, $rangeInfo = null) {
		$authors = array();
		$params = array();

		$locale   = Locale::getLocale();
		$params[] = 'firstName';
		$params[] = $locale;
		$params[] = 'lastName';
		$params[] = $locale;
		$params[] = 'middleName';
		$params[] = $locale;
		$params[] = 'affiliation';
		$params[] = $locale;

		if (isset($journalId)) $params[] = $journalId;
		if (isset($initial)) {
			$params[] = String::strtolower($initial) . '%';
			$initialSql = ' AND LOWER(aal.setting_value) LIKE LOWER(?)';
		} else {
			$initialSql = '';
		}
		
		// Opatan Inc. : joined with article_author_settings to provide setting_value of author firstName
		$result = &$this->retrieveRange(
			'SELECT DISTINCT
				CAST(\'\' AS CHAR(1)) AS url,
				0 AS author_id,
				0 AS article_id,
				CAST(\'\' AS CHAR(1)) AS email,
				0 AS primary_contact,
				0 AS seq,
				aaf.setting_value AS first_name,
				aam.setting_value AS middle_name,
				aal.setting_value AS last_name,
				aaaf.setting_value AS affiliation,
				aa.country
			FROM	article_authors aa
				LEFT JOIN article_author_settings aaf ON (aa.author_id = aaf.author_id AND aaf.setting_name = ? AND aaf.locale = ?)
				LEFT JOIN article_author_settings aal ON (aa.author_id = aal.author_id AND aal.setting_name = ? AND aal.locale = ?)
				LEFT JOIN article_author_settings aam ON (aa.author_id = aam.author_id AND aam.setting_name = ? AND aam.locale = ?)
				LEFT JOIN article_author_settings aaaf ON (aa.author_id = aaaf.author_id AND aaaf.setting_name = ? AND aaaf.locale = ?),
				articles a,
				published_articles pa,
				issues i
			WHERE	i.issue_id = pa.issue_id
				AND i.published = 1
				AND aa.article_id = a.article_id ' .
				(isset($journalId)?'AND a.journal_id = ? ':'') . '
				AND pa.article_id = a.article_id
				AND a.status = ' . STATUS_PUBLISHED . '
				AND (aal.setting_value IS NOT NULL AND aal.setting_value <> \'\')' .
				$initialSql . '
			ORDER BY last_name, first_name',
			empty($params)?false:$params,
			$rangeInfo
		);

		$returner = &new DAOResultFactory($result, $this, '_returnAuthorFromRow');
		return $returner;
	}

	/**
	 * Retrieve the IDs of all authors for an article.
	 * @param $articleId int
	 * @return array int ordered by sequence
	 */
	function &getAuthorIdsByArticle($articleId) {
		$authors = array();

		$result = &$this->retrieve(
			'SELECT author_id FROM article_authors WHERE article_id = ? ORDER BY seq',
			$articleId
		);

		while (!$result->EOF) {
			$authors[] = $result->fields[0];
			$result->moveNext();
		}

		$result->Close();
		unset($result);

		return $authors;
	}

	/**
	 * Get field names for which data is localized.
	 * @return array
	 */
	function getLocaleFieldNames() {
		return array('firstName', 'lastName', 'middleName', 'affiliation', 'biography', 'competingInterests');
	}

	/**
	 * Update the localized data for this object
	 * @param $author object
	 */
	function updateLocaleFields(&$author) {
		$this->updateDataObjectSettings('article_author_settings', $author, array(
			'author_id' => $author->getAuthorId()
		));

	}

	/**
	 * Internal function to return an Author object from a row.
	 * @param $row array
	 * @return Author
	 */
	function &_returnAuthorFromRow(&$row) {
		$locale = Locale::getLocale();
		$author = &new Author();
		$author->setAuthorId($row['author_id']);
		$author->setArticleId($row['article_id']);
		$author->setFirstName($row['first_name'], $locale); // Opatan Inc. : Localized author firstName
		$author->setMiddleName($row['middle_name'], $locale); // Opatan Inc. : Localized author middleName
		$author->setLastName($row['last_name'], $locale); // Opatan Inc. : Localized author lastName
		$author->setAffiliation($row['affiliation'], $locale); // Opatan Inc. : Localized author affiliation
		$author->setCountry($row['country']);
		$author->setEmail($row['email']);
		$author->setUrl($row['url']);
		$author->setPrimaryContact($row['primary_contact']);
		$author->setSequence($row['seq']);

		$this->getDataObjectSettings('article_author_settings', 'author_id', $row['author_id'], $author);

		HookRegistry::call('AuthorDAO::_returnAuthorFromRow', array(&$author, &$row));

		return $author;
	}

	/**
	 * Insert a new Author.
	 * @param $author Author
	 */	
	function insertAuthor(&$author) {
		// Opatan Inc. : first_name, middle_name, last_name and affiliation are removed
		$this->update(
			'INSERT INTO article_authors
				(article_id, country, email, url, primary_contact, seq)
				VALUES
				(?, ?, ?, ?, ?, ?)',
			array(
				$author->getArticleId(),
				$author->getCountry(),
				$author->getEmail(),
				$author->getUrl(),
				$author->getPrimaryContact(),
				$author->getSequence()
			)
		);

		$author->setAuthorId($this->getInsertAuthorId());
		$this->updateLocaleFields($author);

		return $author->getAuthorId();
	}

	/**
	 * Update an existing Author.
	 * @param $author Author
	 */
	function updateAuthor(&$author) {
		// Opatan Inc. : author firstName, middleName, lastName and affiliation are removed
		$returner = $this->update(
			'UPDATE article_authors
				SET
					country = ?,
					email = ?,
					url = ?,
					primary_contact = ?,
					seq = ?
				WHERE author_id = ?',
			array(
				$author->getCountry(),
				$author->getEmail(),
				$author->getUrl(),
				$author->getPrimaryContact(),
				$author->getSequence(),
				$author->getAuthorId()
			)
		);
		$this->updateLocaleFields($author);
		return $returner;
	}

	/**
	 * Delete an Author.
	 * @param $author Author
	 */
	function deleteAuthor(&$author) {
		return $this->deleteAuthorById($author->getAuthorId());
	}

	/**
	 * Delete an author by ID.
	 * @param $authorId int
	 * @param $articleId int optional
	 */
	function deleteAuthorById($authorId, $articleId = null) {
		$params = array($authorId);
		if ($articleId) $params[] = $articleId;
		$returner = $this->update(
			'DELETE FROM article_authors WHERE author_id = ?' .
			($articleId?' AND article_id = ?':''),
			$params
		);
		if ($returner) $this->update('DELETE FROM article_author_settings WHERE author_id = ?', array($authorId));
	}

	/**
	 * Delete authors by article.
	 * @param $articleId int
	 */
	function deleteAuthorsByArticle($articleId) {
		$authors =& $this->getAuthorsByArticle($articleId);
		foreach ($authors as $author) {
			$this->deleteAuthor($author);
		}
	}

	/**
	 * Sequentially renumber an article's authors in their sequence order.
	 * @param $articleId int
	 */
	function resequenceAuthors($articleId) {
		$result = &$this->retrieve(
			'SELECT author_id FROM article_authors WHERE article_id = ? ORDER BY seq', $articleId
		);

		for ($i=1; !$result->EOF; $i++) {
			list($authorId) = $result->fields;
			$this->update(
				'UPDATE article_authors SET seq = ? WHERE author_id = ?',
				array(
					$i,
					$authorId
				)
			);

			$result->moveNext();
		}

		$result->close();
		unset($result);
	}

	/**
	 * Get the ID of the last inserted author.
	 * @return int
	 */
	function getInsertAuthorId() {
		return $this->getInsertId('article_authors', 'author_id');
	}
}

?>
