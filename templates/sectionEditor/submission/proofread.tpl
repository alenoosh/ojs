{**
 * proofread.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Subtemplate defining the proofreading table.
 *
 * $Id$
 *}
<a name="proofread"></a>
<h3>{translate key="submission.proofreading"}</h3>

{if $useProofreaders}
<table class="data" width="100%">
	<tr>
		<td width="20%" class="label">{translate key="user.role.proofreader"}</td>
		{if $proofAssignment->getProofreaderId()}<td class="value" width="20%">{$proofAssignment->getProofreaderFullName()|escape}</td>{/if}
		<td class="value"><a href="{url op="selectProofreader" path=$submission->getArticleId()}" class="action">{translate key="editor.article.selectProofreader"}</a></td>
	</tr>
</table>
{/if}

<table width="100%" class="info">
	<tr>
		<td width="28%" colspan="2">&nbsp;</td>
		<td width="18%" class="heading">{translate key="submission.request"}</td>
		<td width="18%" class="heading">{translate key="submission.underway"}</td>
		<td width="18%" class="heading">{translate key="submission.complete"}</td>
		<td width="18%" class="heading">{translate key="submission.acknowledge"}</td>
	</tr>
	<tr>
		<td width="2%">1.</td>
		<td width="26%">{translate key="user.role.author"}</td>
		<td>
			{url|assign:"url" op="notifyAuthorProofreader" articleId=$submission->getArticleId()}
			{if $proofAssignment->getDateAuthorUnderway()}
				{translate|escape:"javascript"|assign:"confirmText" key="sectionEditor.author.confirmRenotify"}
				{icon name="mail" onclick="return confirm('$confirmText')" url=$url}
			{else}
				{icon name="mail" url=$url}
			{/if}

			{showdate value=$proofAssignment->getDateAuthorNotified() format=$dateFormatShort default="" type=$calType}
		</td>
		<td>
				{showdate value=$proofAssignment->getDateAuthorUnderway() format=$dateFormatShort type=$calType}
		</td>
		<td>
			{showdate value=$proofAssignment->getDateAuthorCompleted() format=$dateFormatShort type=$calType}
		</td>
		<td>
			{if $proofAssignment->getDateAuthorCompleted() && !$proofAssignment->getDateAuthorAcknowledged()}
				{url|assign:"url" op="thankAuthorProofreader" articleId=$submission->getArticleId()}
				{icon name="mail" url=$url}
			{else}
				{icon name="mail" disabled="disable"}
			{/if}
			{showdate value=$proofAssignment->getDateAuthorAcknowledged() format=$dateFormatShort default="" type=$calType}
		</td>
	</tr>
	<tr>
		<td>2.</td>
		<td>{translate key="user.role.proofreader"}</td>
		<td>
			{if $useProofreaders}
				{if $proofAssignment->getProofreaderId() && $proofAssignment->getDateAuthorCompleted()}
					{url|assign:"url" op="notifyProofreader" articleId=$submission->getArticleId()}
					{if $proofAssignment->getDateProofreaderUnderway()}
						{translate|escape:"javascript"|assign:"confirmText" key="sectionEditor.proofreader.confirmRenotify"}
						{icon name="mail" onclick="return confirm('$confirmText')" url=$url}
					{else}
						{icon name="mail" url=$url}
					{/if}
				{else}
					{icon name="mail" disabled="disable"}
				{/if}
			{else}
				{if !$proofAssignment->getDateProofreaderNotified()}
					<a href="{url op="editorInitiateProofreader" articleId=$submission->getArticleId()}" class="action">{translate key="common.initiate"}</a>
				{/if}
			{/if}
			{showdate value=$proofAssignment->getDateProofreaderNotified() format=$dateFormatShort default="" type=$calType}
		</td>
		<td>
			{if $useProofreaders}
					{showdate value=$proofAssignment->getDateProofreaderUnderway() format=$dateFormatShort type=$calType}
			{else}
				{translate key="common.notApplicableShort"}
			{/if}
		</td>
		<td>
			{if !$useProofreaders && !$proofAssignment->getDateProofreaderCompleted() && $proofAssignment->getDateProofreaderNotified()}
				<a href="{url op="editorCompleteProofreader" articleId=$submission->getArticleId()}" class="action">{translate key="common.complete"}</a>
			{else}
				{showdate value=$proofAssignment->getDateProofreaderCompleted() format=$dateFormatShort type=$calType}
			{/if}
		</td>
		<td>
			{if $useProofreaders}
				{if $proofAssignment->getDateProofreaderCompleted() && !$proofAssignment->getDateProofreaderAcknowledged()}
					{url|assign:"url" op="thankProofreader" articleId=$submission->getArticleId()}
					{icon name="mail" url=$url}
				{else}
					{icon name="mail" disabled="disable"}
				{/if}
				{showdate value=$proofAssignment->getDateProofreaderAcknowledged() format=$dateFormatShort default="" type=$calType}
			{else}
				{translate key="common.notApplicableShort"}
			{/if}
		</td>
	</tr>
	<tr>
		<td>3.</td>
		<td>{translate key="user.role.layoutEditor"}</td>
		<td>
			{if $useLayoutEditors}
				{if $layoutAssignment->getEditorId() && $proofAssignment->getDateProofreaderCompleted()}
					{url|assign:"url" op="notifyLayoutEditorProofreader" articleId=$submission->getArticleId()}
					{if $proofAssignment->getDateLayoutEditorUnderway()}
						{translate|escape:"javascript"|assign:"confirmText" key="sectionEditor.layout.confirmRenotify"}
						{icon name="mail" onclick="return confirm('$confirmText')" url=$url}
					{else}
						{icon name="mail" url=$url}
					{/if}
				{else}
					{icon name="mail" disabled="disable"}
				{/if}
			{else}
				{if !$proofAssignment->getDateLayoutEditorNotified()}
					<a href="{url op="editorInitiateLayoutEditor" articleId=$submission->getArticleId()}" class="action">{translate key="common.initiate"}</a>
				{/if}
			{/if}
				{showdate value=$proofAssignment->getDateLayoutEditorNotified() format=$dateFormatShort default="" type=$calType}
		</td>
		<td>
			{if $useLayoutEditors}
				{showdate value=$proofAssignment->getDateLayoutEditorUnderway() format=$dateFormatShort type=$calType}
			{else}
				{translate key="common.notApplicableShort"}
			{/if}
		</td>
		<td>
			{if $useLayoutEditors}
				{showdate value=$proofAssignment->getDateLayoutEditorCompleted() format=$dateFormatShort type=$calType}
			{elseif $proofAssignment->getDateLayoutEditorCompleted()}
				{showdate value=$proofAssignment->getDateLayoutEditorCompleted() format=$dateFormatShort type=$calType}
			{elseif $proofAssignment->getDateLayoutEditorNotified()}
				<a href="{url op="editorCompleteLayoutEditor" articleId=$submission->getArticleId()}" class="action">{translate key="common.complete"}</a>
			{else}
				&mdash;
			{/if}
		</td>
		<td>
			{if $useLayoutEditors}
				{if $proofAssignment->getDateLayoutEditorCompleted() && !$proofAssignment->getDateLayoutEditorAcknowledged()}
					{url|assign:"url" op="thankLayoutEditorProofreader" articleId=$submission->getArticleId()}
					{icon name="mail" url=$url}
				{else}
					{icon name="mail" disabled="disable"}
				{/if}
				{showdate value=$proofAssignment->getDateLayoutEditorAcknowledged() format=$dateFormatShort default="" type=$calType}
			{else}
				{translate key="common.notApplicableShort"}
			{/if}
		</td>
	</tr>
	<tr>
		<td colspan="6" class="separator">&nbsp;</td>
	</tr>
</table>

{translate key="submission.proofread.corrections"}
{if $submission->getMostRecentProofreadComment()}
	{assign var="comment" value=$submission->getMostRecentProofreadComment()}
	<a href="javascript:openComments('{url op="viewProofreadComments" path=$submission->getArticleId() anchor=$comment->getCommentId()}');" class="icon">{icon name="comment"}</a>{showdate value=$comment->getDatePosted() format=$dateFormatShort type=$calType}
{else}
	<a href="javascript:openComments('{url op="viewProofreadComments" path=$submission->getArticleId()}');" class="icon">{icon name="comment"}</a>
{/if}

{if $currentJournal->getLocalizedSetting('proofInstructions')}
&nbsp;&nbsp;
<a href="javascript:openHelp('{url op="instructions" path="proof"}')" class="action">{translate key="submission.proofread.instructions"}</a>
{/if}

