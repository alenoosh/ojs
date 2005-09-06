<?php

/**
 * HelpTopicDAO.inc.php
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package help
 *
 * Class for HelpTopic DAO.
 * Operations for retrieving HelpTopic objects.
 *
 * $Id$
 */

import('help.HelpTopic');

class HelpTopicDAO extends XMLDAO {

	/**
	 * Constructor.
	 */
	function HelpTopicDAO() {
		parent::XMLDAO();
	}

	/**
	 * Retrieve a topic by its ID.
	 * @param $topicId string
	 * @return HelpTopic
	 */
	function &getTopic($topicId) {
		$helpFile = sprintf('help/%s/%s.xml', Locale::getLocale(), $topicId);
		$cacheFile = sprintf('help/cache/%s.%s.inc.php', Locale::getLocale(), str_replace('/','.',$topicId));

		// if available, load up cache of this topic otherwise load xml file
		if (file_exists($cacheFile) && filemtime($helpFile) < filemtime($cacheFile)) {
			require($cacheFile);

		} else {
			$data = &$this->parseStruct($helpFile);

			// check if data exists before saving it to cache
			if ($data === false) {
				$returner = false;
				return $returner;
			}

			// Cache array
			if ((file_exists($cacheFile) && is_writable($cacheFile)) || (!file_exists($cacheFile) && is_writable(dirname($cacheFile)))) {
				$fp = fopen($cacheFile, 'w');
				if (function_exists('var_export')) {
					fwrite($fp, '<?php $data = ' . var_export($data, true) . '; ?>');
				} else {
					fwrite($fp, '<?php $data = ' . $this->custom_var_export($data, true) . '; ?>');
				}
				fclose($fp);
			}
		}

		// check if data exists after loading
		if (!is_array($data)) {
			$returner = false;
			return $returner;
		}

		$topic = &new HelpTopic();

		$topic->setId($data['topic'][0]['attributes']['id']);
		$topic->setTitle($data['topic'][0]['attributes']['title']);
		$topic->setTocId($data['topic'][0]['attributes']['toc']);
		if (isset($data['topic'][0]['attributes']['subtoc'])) {
			$topic->setSubTocId($data['topic'][0]['attributes']['subtoc']);
		}

		if (isset($data['section'])) {
			foreach ($data['section'] as $sectionData) {
				$section = &new HelpTopicSection();
				$section->setTitle(isset($sectionData['attributes']['title']) ? $sectionData['attributes']['title'] : null);
				$section->setContent($sectionData['value']);
				$topic->addSection($section);
			}
		}

		if (isset($data['related_topic'])) {
			foreach ($data['related_topic'] as $relatedTopic) {
				$relatedTopicArray = array('id' => $relatedTopic['attributes']['id'], 'title' => $relatedTopic['attributes']['title']);
				$topic->addRelatedTopic($relatedTopicArray);
			}
		}

		return $topic;
	}

	/**
	 * Returns a set of topics matching a specified keyword.
	 * @param $keyword string
	 * @return array matching HelpTopics
	 */
	function &getTopicsByKeyword($keyword) {
		$keyword = String::strtolower($keyword);
		$matchingTopics = array();
		$topicsDir = sprintf('help/%s', Locale::getLocale());
		$dir = opendir($topicsDir);
		while (($file = readdir($dir)) !== false) {
			$currFile = sprintf('%s/%s',$topicsDir,$file);
			if (is_dir($currFile) && $file != 'toc' && $file != '.' && $file != '..') {
				HelpTopicDAO::searchDirectory($matchingTopics,$keyword,$currFile);
			}
		}
		closedir($dir);

		arsort($matchingTopics);

		$topics = array();

		foreach ($matchingTopics as $topicId => $numMatches) {
			$topics[] = &$this->getTopic($topicId);
		}

		return $topics;
	}

	/**
	 * Parses deeper into folders if subdirectories exists otherwise scans the topic xml files
	 * @param $matchingTopics array stores topics that match the keyword
	 * @param $keyword string
	 * @param $dir string
	 * @modifies $matchingTopics array by reference by making appropriate calls to functions
	 */
	function searchDirectory(&$matchingTopics,$keyword,$dir) {
		$currDir = opendir($dir);
		while (($file = readdir($currDir)) !== false) {
			$currFile = sprintf('%s/%s',$dir,$file);
			if (is_dir($currFile) && $file != '.' && $file != '..' && $file != 'toc') {
				HelpTopicDAO::searchDirectory($matchingTopics,$keyword,$currFile);
			} else {
				HelpTopicDAO::scanTopic($matchingTopics,$keyword,$dir,$file);
			}
		}
		closedir($currDir);
	}

	/**
	 * Scans topic xml files for keywords
	 * @param $matchingTopics array stores topics that match the keyword
	 * @param $keyword string
	 * @param $dir string
	 * @param $file string
	 * @modifies $matchingTopics array by reference
	 */
	function scanTopic(&$matchingTopics,$keyword,$dir,$file) {
		if (preg_match('/^\d{6,6}\.xml$/', $file)) {
			$fileContents = String::regexp_replace('/(<!\[CDATA\[)|(\]\]>)|(<[^>]*>)/', '', join('', file("$dir/$file")));
			if (($numMatches = String::substr_count(String::strtolower($fileContents), $keyword)) > 0) {
				// remove the help/<locale> from directory path and use the latter half or url
				$url = split('/',$dir,3);
				$matchingTopics[$url[2] . '/' . str_replace('.xml', '', $file)] = $numMatches;
			}
		}
	}
}

?>
