{**
 * futureIssues.tpl
 *
 * Copyright (c) 2003-2005 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Listings of future (unpublished) issues.
 *
 * $Id$
 *}

{assign var="pageTitle" value="editor.issues.futureIssues"}
{assign var="currentUrl" value="$pageUrl/editor/issues"}
{include file="common/header.tpl"}

<ul class="menu">
        <li><a href="{$pageUrl}/editor/createIssue">{translate key="editor.navigation.createIssue"}</a></li>
        <li><a href="{$pageUrl}/editor/schedulingQueue">{translate key="editor.navigation.submissionsInScheduling"}</a></li>
        <li class="current"><a href="{$pageUrl}/editor/futureIssues">{translate key="editor.navigation.futureIssues"}</a></li>
        <li><a href="{$pageUrl}/editor/backIssues">{translate key="editor.navigation.issueArchive"}</a></li>
</ul>

<br/>

<form method="post" action="{$requestPageUrl}/updateFutureIssues">

<table width="100%" class="listing">
	<tr>
		<td colspan="3" class="headseparator"></td>
	</tr>
	<tr class="heading" valign="bottom">
		<td width="80%">{translate key="issue.issue"}</td>
		<td width="15%">{translate key="editor.issues.numArticles"}</td>
		<td width="5%">{translate key="common.remove"}</td>
	</tr>
	<tr>
		<td colspan="3" class="headseparator"></td>
	</tr>
	{foreach from=$issues item=issue name="issues"}
	<tr valign="top">
		<td><a href="{$requestPageUrl}/issueToc/{$issue->getIssueId()}" class="action">{$issue->getIssueIdentification()}</a></td>
		<td>{$issue->getNumArticles()}</td>
		<td><input name="select[]" type="checkbox" value="{$issue->getIssueId()}" /></td>
	</tr>
	<tr>
		<td colspan="3" class="{if $smarty.foreach.issues.last}end{/if}separator"></td>
	</tr>
	{foreachelse}
	<tr>
		<td colspan="3" class="nodata">{translate key="issue.noIssues"}</td>
	</tr>
	<tr>
		<td colspan="3" class="endseparator"></td>
	</tr>
	{/foreach}
</table>

<input type="submit" value="{translate key="common.saveChanges"}" class="button defaultButton" />

</form>

{include file="common/footer.tpl"}