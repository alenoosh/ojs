{**
 * completed.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Show the details of completed submissions.
 *
 * $Id$
 *}
<a name="submissions"></a>

<table class="listing" width="100%">
	<tr><td class="headseparator" colspan="{if $statViews}8{else}7{/if}">&nbsp;</td></tr>
	<tr valign="bottom" class="heading">
		<td width="5%"></td>
		<td width="5%">{translate key="common.id"}</td>
		<td width="5%"><span class="disabled">MM-DD</span><br />{translate key="submissions.submit"}</td>
		<td width="5%">{translate key="submissions.sec"}</td>
		<td width="20%">{translate key="article.authors"}</td>
		<td width="30%">{translate key="article.title"}</td>
		{if $statViews}<td width="5%">{translate key="submission.views"}</td>{/if}
		<td width="25%" align="right">{translate key="common.status"}</td>
	</tr>
	<tr><td class="headseparator" colspan="{if $statViews}8{else}7{/if}">&nbsp;</td></tr>
<form name="formName" action="#">
{iterate from=submissions item=submission}
	{assign var="articleId" value=$submission->getArticleId()}
	<tr valign="top">
		<td><input type="radio" name="radioButtonName" value="{$articleId}" id="{$progress}" /></td>
		<td>{$articleId|escape}</td>
		<td>{showdate value=$submission->getDateSubmitted() format=$dateFormatTrunc}</td>
		<td>{$submission->getSectionAbbrev()|escape}</td>
		<td>{$submission->getAuthorString(true)|truncate:40:"..."|escape}</td>
		<td><a href="{url op="submission" path=$articleId}" class="action">{$submission->getArticleTitle()|strip_unsafe_html|truncate:60:"..."}</a></td>
		{assign var="status" value=$submission->getSubmissionStatus()}
		{if $statViews}
			<td>
				{if $status==STATUS_PUBLISHED}
					{assign var=viewCount value=0}
					{foreach from=$submission->getGalleys() item=galley}
						{assign var=thisCount value=$galley->getViews()}
						{assign var=viewCount value=$viewCount+$thisCount}
					{/foreach}
					{$viewCount|escape}
				{else}
					&mdash;
				{/if}
			</td>
		{/if}
		<td align="right">
			{if $status == STATUS_ARCHIVED}{translate key="submissions.archived"}
			{elseif $status==STATUS_QUEUED_UNASSIGNED}{translate key="submissions.queuedUnassigned"}
			{elseif $status==STATUS_QUEUED_EDITING}{translate key="submissions.queuedEditing"}
			{elseif $status==STATUS_QUEUED_REVIEW}{translate key="submissions.queuedReview"}
			{elseif $status==STATUS_PUBLISHED}{print_issue_id articleId="$articleId"}
			{elseif $status==STATUS_DECLINED}{translate key="submissions.declined"}
			{/if}
		</td>
	</tr>

	<tr>
		<td colspan="{if $statViews}8{else}7{/if}" class="{if $submissions->eof()}end{/if}separator">&nbsp;</td>
	</tr>
{/iterate}
</form>
{if $submissions->wasEmpty()}
	<tr>
		<td colspan="{if $statViews}8{else}7{/if}" class="nodata">{translate key="submissions.noSubmissions"}</td>
	</tr>
	<tr>
		<td colspan="{if $statViews}8{else}7{/if}" class="endseparator">&nbsp;</td>
	</tr>
{else}
	<tr>
		<td colspan="{if $statViews}5{else}4{/if}" align="left">{page_info iterator=$submissions}</td>
		<td colspan="2" align="right">{page_links anchor="submissions" name="submissions" iterator=$submissions}</td>
	</tr>
{/if}
</table>
