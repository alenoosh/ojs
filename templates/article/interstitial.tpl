{**
 * interstitial.tpl
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Interstitial page used to display a note
 * before downloading a file
 *
 * $Id$
 *}

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$defaultCharset}" />
	<title>{translate key="article.nonpdf.title"}</title>
	<link rel="stylesheet" href="{$baseUrl}/styles/common.css" type="text/css" />
	<link rel="stylesheet" href="{$baseUrl}/styles/articleView.css" type="text/css" />
	<meta http-equiv="refresh" content="2;URL={$requestPageUrl}/download/{$articleId}/{$galley->getFileId()}"/>

</head>
<body>

<div id="container">
<div id="body">
<div id="main">
<div id="content">
		<h3>{translate key="article.nonpdf.title"}</h3>

<p>{translate key="article.nonpdf.note" pdfUrl=$requestPageUrl/download/`$articleId`/`$galley->getFileId()`}</p>

</div>
</div>
</div>
</div>
</body>
</html>
