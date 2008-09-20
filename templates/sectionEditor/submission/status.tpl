{**
 * status.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Subtemplate defining the submission status table.
 *
 * $Id$
 *}
<a name="status"></a>
<h3>{translate key="common.status"}</h3>

<table width="100%" class="data">
	<tr>
		{assign var="status" value=$submission->getSubmissionStatus()}
		<td width="20%" class="label">{translate key="common.status"}</td>
		<td width="30%" class="value">
			{if $status == STATUS_ARCHIVED}{translate key="submissions.archived"}
			{elseif $status==STATUS_QUEUED_UNASSIGNED}{translate key="submissions.queuedUnassigned"}
			{elseif $status==STATUS_QUEUED_EDITING}{translate key="submissions.queuedEditing"}
			{elseif $status==STATUS_QUEUED_REVIEW}{translate key="submissions.queuedReview"}
			{elseif $status==STATUS_PUBLISHED}{translate key="submissions.published"}&nbsp;&nbsp;&nbsp;&nbsp;{$issue->getIssueIdentification()}
			{elseif $status==STATUS_DECLINED}{translate key="submissions.declined"}
			{/if}
		</td>
		<td width="50%" class="value">
			{if $status != STATUS_ARCHIVED}
				<a href="{url op="unsuitableSubmission" articleId=$submission->getArticleId()}" class="action">{translate key="editor.article.archiveSubmission"}</a>
			{else}
				<a href="{url op="restoreToQueue" path=$submission->getArticleId()}" class="action">{translate key="editor.article.restoreToQueue"}</a>
			{/if}
		</td>
	</tr>
	<tr>
		<td class="label">{translate key="submission.initiated"}</td>
		<td colspan="2" class="value">{showdate value=$submission->getDateStatusModified() format=$dateFormatShort type=$calType}</td>
	</tr>
	<tr>
		<td class="label">{translate key="submission.lastModified"}</td>
		<td colspan="2" class="value">{showdate value=$submission->getLastModified() format=$dateFormatShort type=$calType}</td>
	</tr>
{*Opatan Inc.*}

	<tr>	

		<td></td>
		<td></td>
		<td>
			<a href="{url op="changeStatusToIncomplete" path=$articleId}" class="action">{translate key="submission.changeToIncomplete"}</a>
		</td>
	</tr>
</table>
