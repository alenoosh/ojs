<?php

/**
 * AuthorDAO.inc.php
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package article
 *
 * Class for Author DAO.
 * Operations for retrieving and modifying Author objects.
 *
 * $Id$
 */

class AuthorDAO extends DAO {

	/**
	 * Constructor.
	 */
	function AuthorDAO() {
		parent::DAO();
	}
	
	/**
	 * Retrieve an author by ID.
	 * @param $authorId int
	 * @return Author
	 */
	function &getAuthor($authorId) {
		$result = &$this->retrieve(
			'SELECT * FROM article_authors WHERE author_id = ?', $authorId
		);
		
		if ($result->RecordCount() == 0) {
			return null;
			
		} else {
			return $this->_returnAuthorFromRow($result->GetRowAssoc(false));
		}
	}
	
	/**
	 * Retrieve all authors for an article.
	 * @param $articleId int
	 * @return array Authors ordered by sequence
	 */
	function &getAuthorsByArticle($articleId) {
		$authors = array();
		
		$result = &$this->retrieve(
			'SELECT * FROM article_authors WHERE article_id = ? ORDER BY seq',
			$articleId
		);
		
		while (!$result->EOF) {
			$authors[] = &$this->_returnAuthorFromRow($result->GetRowAssoc(false));
			$result->moveNext();
		}
		$result->Close();
	
		return $authors;
	}

	/**
	 * Retrieve all published articles associated with authors with
	 * the given first name, middle name, last name, and affiliation.
	 * @param firstName string
	 * @param middleName string
	 * @param lastName string
	 * @param affiliation string
	 */
	function &getPublishedArticlesForAuthor($firstName, $middleName, $lastName, $affiliation) {
		$publishedArticles = array();
		$publishedArticleDao = &DAORegistry::getDAO('PublishedArticleDAO');
		
		$result = &$this->retrieve(
			'SELECT DISTINCT article_id FROM article_authors WHERE first_name = ? AND middle_name = ? AND last_name = ? AND affiliation = ?',
			array($firstName, $middleName, $lastName, $affiliation)
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

		return $publishedArticles;
	}

	/**
	 * Retrieve all published authors for a journal in an associative array by
	 * the first letter of the last name, for example:
	 * $returnedArray['S'] gives array($misterSmithObject, $misterSmytheObject, ...)
	 * All 26 capital letters from A-Z are guaranteed as keys, with others
	 * possibly occuring should a last name start with a different letter.
	 * Keys will appear in sorted order.
	 * @param $journalId int
	 * @return array Authors ordered by sequence
	 */
	function &getAuthorsAlphabetizedByJournal($journalId) {
		$authors = array();
		for ($i=ord('A'); $i<=ord('Z'); $i++) {
			$authors[chr($i)] = array();
		}
		
		$result = &$this->retrieve(
			'SELECT DISTINCT NULL AS author_id, NULL AS article_id, NULL AS email, NULL AS biography, NULL AS primary_contact, NULL AS seq, aa.first_name AS first_name, aa.middle_name AS middle_name, aa.last_name AS last_name, aa.affiliation AS affiliation FROM article_authors aa, articles a, published_articles pa WHERE aa.article_id = a.article_id AND a.journal_id = ? AND pa.article_id = a.article_id AND (aa.last_name IS NOT NULL AND aa.last_name <> \'\') ORDER BY aa.last_name, aa.first_name',
			$journalId
		);
		
		while (!$result->EOF) {
			$row = &$result->GetRowAssoc(false);
			$author = &$this->_returnAuthorFromRow(&$row);
			$firstLetter = strtoupper($row['last_name'][0]);

			if (!isset($authors[$firstLetter])) {
				// An additional last name starting letter was found
				$authors[$firstLetter] = array(&$author);
			} else {
				$authors[$firstLetter][] = &$author;
			}
			$result->moveNext();
		}
		$result->Close();

		ksort (&$authors);
		return $authors;
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
	
		return $authors;
	}
	
	/**
	 * Internal function to return an Author object from a row.
	 * @param $row array
	 * @return Author
	 */
	function &_returnAuthorFromRow(&$row) {
		$author = &new Author();
		$author->setAuthorId($row['author_id']);
		$author->setArticleId($row['article_id']);
		$author->setFirstName($row['first_name']);
		$author->setMiddleName($row['middle_name']);
		$author->setLastName($row['last_name']);
		$author->setAffiliation($row['affiliation']);
		$author->setEmail($row['email']);
		$author->setBiography($row['biography']);
		$author->setPrimaryContact($row['primary_contact']);
		$author->setSequence($row['seq']);
		
		return $author;
	}

	/**
	 * Insert a new Author.
	 * @param $author Author
	 */	
	function insertAuthor(&$author) {
		$this->update(
			'INSERT INTO article_authors
				(article_id, first_name, middle_name, last_name, affiliation, email, biography, primary_contact, seq)
				VALUES
				(?, ?, ?, ?, ?, ?, ?, ?, ?)',
			array(
				$author->getArticleId(),
				$author->getFirstName(),
				$author->getMiddleName(),
				$author->getLastName(),
				$author->getAffiliation(),
				$author->getEmail(),
				$author->getBiography(),
				$author->getPrimaryContact(),
				$author->getSequence()
			)
		);
		$author->setAuthorId($this->getInsertAuthorId());
	}
	
	/**
	 * Update an existing Author.
	 * @param $author Author
	 */
	function updateAuthor(&$author) {
		return $this->update(
			'UPDATE article_authors
				SET
					first_name = ?,
					middle_name = ?,
					last_name = ?,
					affiliation = ?,
					email = ?,
					biography = ?,
					primary_contact = ?,
					seq = ?
				WHERE author_id = ?',
			array(
				$author->getFirstName(),
				$author->getMiddleName(),
				$author->getLastName(),
				$author->getAffiliation(),
				$author->getEmail(),
				$author->getBiography(),
				$author->getPrimaryContact(),
				$author->getSequence(),
				$author->getAuthorId()
			)
		);
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
		if (isset($articleId)) {
			return $this->update(
				'DELETE FROM article_authors WHERE author_id = ? AND article_id = ?',
				array($authorId, $articleId)
			);
		
		} else {
			return $this->update(
				'DELETE FROM article_authors WHERE author_id = ?', $authorId
			);
		}
	}
	
	/**
	 * Delete authors by article.
	 * @param $articleId int
	 */
	function deleteAuthorsByArticle($articleId) {
		return $this->update(
			'DELETE FROM article_authors WHERE article_id = ?', $articleId
		);
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
