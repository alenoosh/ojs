{**
 * active.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Show reviewer's active submissions.
 *
 * $Id$
 *}
<a name="submissions"></a>
<table class="listing" width="100%">
	<tr><td colspan="7" class="headseparator">&nbsp;</td></tr>
	<tr class="heading" valign="bottom">
		<td width="5%"></td>
		<td width="5%">{translate key="common.id"}</td>
		<td width="5%"><span class="disabled">MM-DD</span><br />{translate key="common.assigned"}</td>
		<td width="5%">{translate key="submissions.sec"}</td>
		<td width="65%">{translate key="article.title"}</td>
		<td width="5%">{translate key="submission.due"}</td>
		<td width="10%">{translate key="submissions.reviewRound"}</td>
	</tr>
	<tr><td colspan="7" class="headseparator">&nbsp;</td></tr>
<form name="formName" action="#">
{iterate from=submissions item=submission}
	{assign var="articleId" value=$submission->getArticleId()}
	{assign var="reviewId" value=$submission->getReviewId()}
	{assign var=editAssignments value=$submission->getEditAssignments()}
	{foreach from=$editAssignments item=editAssignment}
		{assign var=emailString value="`$editAssignment->getEditorFullName()` <`$editAssignment->getEditorEmail()`>"}
	{/foreach}

	<tr valign="top">
		<td><input type="radio" name="radioButtonName" value="{$reviewId}" />
		<input type="hidden" name="sendMail" value="{url page="user" op="email" to=$emailString|to_array redirectUrl=$currentUrl subject=$submission->getArticleTitle() articleId=$reviewId}">
		</td>

		<td>{$articleId|escape}</td>
		<td>{showdate value=$submission->getDateNotified() format=$dateFormatTrunc}</td>
		<td>{$submission->getSectionAbbrev()|escape}</td>
		<td><a href="{url op="submission" path=$reviewId}" class="action">{$submission->getArticleTitle()|strip_unsafe_html|truncate:60:"..."}</a></td>
		<td class="nowrap">{showdate value=$submission->getDateDue() format=$dateFormatTrunc}</td>
		<td>{$submission->getRound()}</td>
	</tr>
	<tr>
		<td colspan="7" class="{if $submissions->eof()}end{/if}separator">&nbsp;</td>
	</tr>
{/iterate}
</form>

{if $submissions->wasEmpty()}
<tr>
		<td colspan="7" class="nodata">{translate key="submissions.noSubmissions"}</td>
	</tr>
	<tr>
		<td colspan="7" class="endseparator">&nbsp;</td>
	</tr>
{else}
	<tr>
		<td colspan="3" align="left">{page_info iterator=$submissions}</td>
		<td colspan="3" align="right">{page_links anchor="submissions" name="submissions" iterator=$submissions}</td>
	</tr>
{/if}

</table>



