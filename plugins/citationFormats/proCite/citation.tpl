{**
 * citation.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * ProCite citation format generator
 *
 * $Id$
 *}
{if $galleyId}
	{url|assign:"articleUrl" page="article" op="view" path=$articleId|to_array:$galleyId}
{else}
	{url|assign:"articleUrl" page="article" op="view" path=$articleId}
{/if}
TY  - JOUR
{foreach from=$article->getAuthors() item=author}
AU  - {$author->getFullName(true)|escape}
{/foreach}
PY  - {showdate value=$article->getDatePublished() format="%Y"}
TI  - {$article->getArticleTitle()|strip_tags}
JF  - {$journal->getJournalTitle()}{if $issue}; {$issue->getIssueIdentification()}{/if}

Y2  - {showdate value=$article->getDatePublished() format="%Y"}
KW  - {$article->getArticleSubject()|escape}
N2  - {$article->getArticleAbstract()|strip_tags|replace:"\n":" "|replace:"\r":" "}
UR  - {$articleUrl}

