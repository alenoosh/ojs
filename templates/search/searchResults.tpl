{**
 * searchResults.tpl
 *
 * Copyright (c) 2003-2005 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display article search results.
 *
 * $Id$
 *}

{assign var=pageTitle value="search.searchResults"}

{include file="common/header.tpl"}

<br/>

<table width="100%" class="listing">
<tr><td colspan="3" class="headseparator"></tr>
<tr class="heading" valign="bottom">
	<td>{translate key="journal.journal"}</td>
	<td>{translate key="issue.issue"}</td>
	<td>{translate key="article.title"}</td>
</tr>
<tr><td colspan="3" class="headseparator"></tr>

{foreach from=$results item=result name=results key=match}
{assign var=publishedArticle value=$result.publishedArticle}
{assign var=article value=$result.article}
{assign var=issue value=$result.issue}
{assign var=journal value=$result.journal}
<tr valign="top">
	<td><a href="{$indexUrl}/{$journal->getPath()}" class="action">{$journal->getTitle()}</a></td>
	<td>{$issue->getTitle()}</td>
	<td>{$article->getArticleTitle()}</td>
</tr>
<tr><td colspan="3" class="{if $smarty.foreach.results.last}end{/if}separator"></tr>
{foreachelse}
<tr>
<td colspan="3" class="nodata">{translate key="search.noResults"}</td>
</tr>
<tr><td colspan="3" class="endseparator"></tr>
{/foreach}
</table>

{include file="common/footer.tpl"}
