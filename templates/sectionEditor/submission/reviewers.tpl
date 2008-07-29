{**
 * reviewers.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Subtemplate defining the submission reviewers table.
 *
 * $Id$
 *}
<a name="reviewers"></a>
<h3>{translate key="user.role.suggestedReviewers"}</h3>
<input type="hidden" name="articleId" value="{$submission->getArticleId()}"/>
<table class="listing" width="100%">
<tr class="heading" valign="bottom">
	<td width="45%">{translate key="user.name"}</td>
	<td width="45%">{translate key="user.email"}</td>
	<td width="10%" class="heading" align="center">{translate key="common.action"}</td>
</tr>
{iterate from=reviewers item=reviewer}
{assign var="reviewerId" value=$reviewer->getReviewerId()}

<tr valign="top">
	<td>{$reviewer->getFullName()|escape}</td>
	<td>{$reviewer->getEmail()|escape}</td>
	<td align="center">
		{if $reviewer->getStatus() eq 0}
			<a class="action" href="{url op="notifySuggestedReviewer" reviewerId=$reviewer->getReviewerId() articleId=$submission->getArticleId()}">{translate key="common.assign"}</a>
		{elseif $reviewer->getStatus() eq 1}
			{translate key="common.considered"}
		{elseif $reviewer->getStatus() eq 2}
			{translate key="common.alreadyAssigned"}
		{/if}
	</td>
</tr>
{/iterate}

</table>

