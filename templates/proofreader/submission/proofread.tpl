{**
 * proofread.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Subtemplate defining the proofreader's proofreading table.
 *
 * $Id$
 *}
<a name="proofread"></a>
<h3>{translate key="submission.proofreading"}</h3>

<table width="100%" class="data">
	<tr>
		<td class="label" width="20%">{translate key="user.role.proofreader"}</td>
		<td class="value" width="80%">{$proofAssignment->getProofreaderFullName()}</td>
	</tr>
</table>

<a href="{url op="viewMetadata" path=$proofAssignment->getArticleId()}" class="action" target="_new">{translate key="submission.reviewMetadata"}</a>

<table width="100%" class="info">
	<tr>
		<td width="40%" colspan="2">&nbsp;</td>
		<td width="20%" class="heading">{translate key="submission.request"}</td>
		<td width="20%" class="heading">{translate key="submission.underway"}</td>
		<td width="20%" class="heading">{translate key="submission.complete"}</td>
	</tr>
	<tr>
		<td width="5%">1.</td>
		<td width="35%">{translate key="editor.article.authorComments"}</td>
		<td>{showdate value=$proofAssignment->getDateAuthorNotified() format=$dateFormatShort}</td>
		<td>{showdate value=$proofAssignment->getDateAuthorUnderway() format=$dateFormatShort}</td>
		<td>{showdate value=$proofAssignment->getDateAuthorCompleted() format=$dateFormatShort}</td>
	</tr>
	<tr>
		<td>2.</td>
		<td>{translate key="editor.article.proofreaderComments"}</td>
		<td>{showdate value=$proofAssignment->getDateProofreaderNotified() format=$dateFormatShort}</td>
		<td>{showdate value=$proofAssignment->getDateProofreaderUnderway() format=$dateFormatShort}</td>
		<td>
			{url|assign:"url" op="completeProofreader" articleId=$submission->getArticleId()}
			{if not $proofAssignment->getDateProofreaderNotified() or not $useProofreaders or $proofAssignment->getDateProofreaderCompleted()}
				{icon name="mail" disabled="disabled" url=$url}
			{else}
				{translate|assign:"confirmMessage" key="common.confirmComplete"}
				{icon name="mail" onclick="return confirm('$confirmMessage')" url=$url}
			{/if}
			{showdate value=$proofAssignment->getDateProofreaderCompleted() format=$dateFormatShort default=""}
		</td>
	</tr>
	<tr>
		<td>3.</td>
		<td>{translate key="editor.article.layoutEditorFinal"}</td>
		<td>{showdate value=$proofAssignment->getDateLayoutEditorNotified() format=$dateFormatShort}</td>
		<td>{showdate value=$proofAssignment->getDateLayoutEditorUnderway() format=$dateFormatShort}</td>
		<td>{showdate value=$proofAssignment->getDateLayoutEditorCompleted() format=$dateFormatShort}</td>
	</tr>
	<tr>
		<td colspan="5" class="separator">&nbsp;</td>
	</tr>
</table>

{translate key="submission.proofread.corrections"}
{if $submission->getMostRecentProofreadComment()}
	{assign var="comment" value=$submission->getMostRecentProofreadComment()}
	<a href="javascript:openComments('{url op="viewProofreadComments" path=$submission->getArticleId() anchor=$comment->getCommentId()}');" class="icon">{icon name="comment"}</a>{showdate value=$comment->getDatePosted() format=$dateFormatShort}
{else}
	<a href="javascript:openComments('{url op="viewProofreadComments" path=$submission->getArticleId()}');" class="icon">{icon name="comment"}</a>
{/if}

{if $currentJournal->getLocalizedSetting('proofInstructions')}
&nbsp;&nbsp;
<a href="javascript:openHelp('{url op="instructions" path="proof"}')" class="action">{translate key="submission.proofread.instructions"}</a>
{/if}
