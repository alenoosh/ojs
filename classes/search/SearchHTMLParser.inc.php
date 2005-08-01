<?php

/**
 * SearchHTMLParser.inc.php
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package search
 *
 * Class to extract text from an HTML file.
 *
 * $Id$
 */

import('search.SearchFileParser');

class SearchHTMLParser extends SearchFileParser {

	function doRead() {
		return fgetss($this->fp, 4096);
	}
	
}

?>
