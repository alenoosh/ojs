{**
 * copyedit.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Subtemplate defining the copyediting table.
 *
 * $Id$
 *}
<a name="copyedit"></a>
<h3>{translate key="submission.copyediting"}</h3>

{if $useCopyeditors}
<table class="data" width="100%">
	<tr>
		<td class="label" width="20%">{translate key="user.role.copyeditor"}</td>
		<td class="label" width="80%">{if $submission->getCopyeditorId()}{$copyeditor->getFullName()|escape}{else}{translate key="common.none"}{/if}</td>
	</tr>
</table>
{/if}

<table width="100%" class="info">
	<tr>
		<td width="40%" colspan="2"><a href="{url op="viewMetadata" path=$submission->getArticleId()}" class="action">{translate key="submission.reviewMetadata"}</a></td>
		<td width="20%" class="heading">{translate key="submission.request"}</td>
		<td width="20%" class="heading">{translate key="submission.underway"}</td>
		<td width="20%" class="heading">{translate key="submission.complete"}</td>
	</tr>
	<tr>
		<td width="5%">1.</td>
		<td width="35%">{translate key="submission.copyedit.initialCopyedit"}</td>
		<td>{showdate value=$submission->getCopyeditorDateNotified() format=$dateFormatShort type=$calType}</td>
		<td>{showdate value=$submission->getCopyeditorDateUnderway() format=$dateFormatShort type=$calType}</td>
		<td>{showdate value=$submission->getCopyeditorDateCompleted() format=$dateFormatShort type=$calType}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="4">
			{translate key="common.file"}:
			{if $submission->getCopyeditorDateCompleted() && $initialCopyeditFile}
				<a href="{url op="downloadFile" path=$submission->getArticleId()|to_array:$initialCopyeditFile->getFileId():$initialCopyeditFile->getRevision()}" class="file">{$initialCopyeditFile->getFileName()|escape}</a>&nbsp;&nbsp;{showdate value=$initialCopyeditFile->getDateModified() format=$dateFormatShort type=$calType}
			{else}
				{translate key="common.none"}
			{/if}
		</td>
	</tr>
	<tr>
		<td colspan="5" class="separator">&nbsp;</td>
	</tr>
	<tr>
		<td>2.</td>
		<td>{translate key="submission.copyedit.editorAuthorReview"}</td>
		<td>{showdate value=$submission->getCopyeditorDateAuthorNotified() format=$dateFormatShort type=$calType}</td>
		<td>{showdate value=$submission->getCopyeditorDateAuthorUnderway() format=$dateFormatShort type=$calType}</td>
		<td>
			{if not $submission->getCopyeditorDateAuthorNotified() or $submission->getCopyeditorDateAuthorCompleted()}
				{icon name="mail" disabled="disabled"}
			{else}
				{url|assign:"url" op="completeAuthorCopyedit" articleId=$submission->getArticleId()}
				{translate|assign:"confirmMessage" key="common.confirmComplete"}
				{icon name="mail" onclick="return confirm('$confirmMessage')" url=$url}
			{/if}
			{showdate value=$submission->getCopyeditorDateAuthorCompleted() format=$dateFormatShort type=$calType default=""}
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="5">
			{translate key="common.file"}:
			{if $submission->getCopyeditorDateAuthorNotified() && $editorAuthorCopyeditFile}
				<a href="{url op="downloadFile" path=$submission->getArticleId()|to_array:$editorAuthorCopyeditFile->getFileId():$editorAuthorCopyeditFile->getRevision()}" class="file">{$editorAuthorCopyeditFile->getFileName()|escape}</a>&nbsp;&nbsp;{showdate value=$editorAuthorCopyeditFile->getDateModified() format=$dateFormatShort type=$calType}
			{else}
				{translate key="common.none"}
			{/if}
			<br />
			<form method="post" action="{url op="uploadCopyeditVersion"}"  enctype="multipart/form-data">
				<input type="hidden" name="articleId" value="{$submission->getArticleId()}" />
				<input type="hidden" name="copyeditStage" value="author" />
				<input type="file" name="upload"{if not $submission->getCopyeditorDateAuthorNotified() or $submission->getCopyeditorDateAuthorCompleted()} disabled="disabled"{/if} class="uploadField" />
				<input type="submit" class="button" value="{translate key="common.upload"}"{if not $submission->getCopyeditorDateAuthorNotified() or $submission->getCopyeditorDateAuthorCompleted()} disabled="disabled"{/if} />
			</form>
		</td>
	</tr>
	<tr>
		<td colspan="5" class="separator">&nbsp;</td>
	</tr>
	<tr>
		<td>3.</td>
		<td>{translate key="submission.copyedit.finalCopyedit"}</td>
		<td>{showdate value=$submission->getCopyeditorDateFinalNotified() format=$dateFormatShort type=$calType}</td>
		<td>{showdate value=$submission->getCopyeditorDateFinalUnderway() format=$dateFormatShort type=$calType}</td>
		<td>{showdate value=$submission->getCopyeditorDateFinalCompleted() format=$dateFormatShort type=$calType}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="4">
			{translate key="common.file"}:
			{if $submission->getCopyeditorDateFinalCompleted() && $finalCopyeditFile}
				<a href="{url op="downloadFile" path=$submission->getArticleId()|to_array:$finalCopyeditFile->getFileId():$finalCopyeditFile->getRevision()}" class="file">{$finalCopyeditFile->getFileName()|escape}</a>&nbsp;&nbsp;{showdate value=$finalCopyeditFile->getDateModified() format=$dateFormatShort type=$calType}
			{else}
				{translate key="common.none"}
			{/if}
		</td>
	</tr>
	<tr>
		<td colspan="5" class="separator">&nbsp;</td>
	</tr>
</table>

{translate key="submission.copyedit.copyeditComments"}
{if $submission->getMostRecentCopyeditComment()}
	{assign var="comment" value=$submission->getMostRecentCopyeditComment()}
	<a href="javascript:openComments('{url op="viewCopyeditComments" path=$submission->getArticleId() anchor=$comment->getCommentId()}');" class="icon">{icon name="comment"}</a>{showdate value=$comment->getDatePosted() format=$dateFormatShort type=$calType}
{else}
	<a href="javascript:openComments('{url op="viewCopyeditComments" path=$submission->getArticleId()}');" class="icon">{icon name="comment"}</a>
{/if}

{if $currentJournal->getLocalizedSetting('copyeditInstructions') != ''}
&nbsp;&nbsp;
<a href="javascript:openHelp('{url op="instructions" path="copy"}')" class="action">{translate key="submission.copyedit.instructions"}</a>
{/if}
