{**
 * suppFile.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Add/edit a supplementary file.
 *
 * $Id$
 *}

{* Opatan Inc. : step 4a title is replaced with edit supp file title *}
{assign var="pageTitle" value="author.submit.editSupp"}
{* Opatan Inc. : the submission steps header is removed *}
{assign var="pageCrumbTitle" value="author.submit"}
{include file="common/header.tpl"}

<form name="submit" method="post" action="{url op="saveSubmitSuppFile" path=$suppFileId}" enctype="multipart/form-data">
<input type="hidden" name="articleId" value="{$articleId|escape}" />
{include file="common/formErrors.tpl"}

{if count($formLocales) > 1}
<table width="100%" class="data">
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="formLocale" key="form.formLanguage"}</td>
		<td width="80%" class="value">
			{url|assign:"submitFormUrl" path=$suppFileId articleId=$articleId}
			{form_language_chooser form="submit" url=$submitFormUrl}
			<span class="instruct">{translate key="form.formLanguage.description"}</span>
		</td>
	</tr>
</table>
{/if}

<h3>{translate key="author.submit.supplementaryFileData"}</h3>

<p>{translate key="author.submit.supplementaryFileDataDescription"}</p>

<table class="data" width="100%">
{* Opatan Inc. : all the input boxes except the supp file title are removed *}
<tr valign="top">
	<td width="20%" class="label">{fieldLabel required="true" name="title" key="common.title"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="title[{$formLocale|escape}]" id="title" value="{$title[$formLocale]|escape}" size="60" maxlength="255" /></td>
</tr>
</table>

<div class="separator"></div>

<h3>{translate key="author.submit.supplementaryFileUpload"}</h3>

<table class="data" width="100%">
{if $suppFile && $suppFile->getFileId()}
<tr valign="top">
	<td width="20%" class="label">{translate key="common.fileName"}</td>
	<td width="80%" class="value"><a href="{url op="download" path=$articleId|to_array:$suppFile->getFileId()}">{$suppFile->getFileName()|escape}</a></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{translate key="common.originalFileName"}</td>
	<td width="80%" class="value">{$suppFile->getOriginalFileName()|escape}</td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{translate key="common.fileSize"}</td>
	<td width="80%" class="value">{$suppFile->getNiceFileSize()}</td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{translate key="common.dateUploaded"}</td>
	<td width="80%" class="value">{$suppFile->getDateUploaded()|date_format:$datetimeFormatShort}</td>
</tr>
</table>

<table class="data" width="100%">
<tr valign="top">
	<td>&nbsp;</td>
	<td class="value"><input type="checkbox" name="showReviewers" id="showReviewers" value="1"{if $showReviewers==1} checked="checked"{/if} /> 
	<label for="showReviewers">{translate key="author.submit.suppFile.availableToPeers"}</label></td>
</tr>
</table>
{else}
<tr valign="top">
	<td colspan="2" class="nodata">{translate key="author.submit.suppFile.noFile"}</td>
</tr>
</table>
{/if}

<div class="separator"></div>

<table class="data" width="100%">
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="uploadSuppFile" key="common.replaceFile"}</td>
	<td width="80%" class="value"><input type="file" name="uploadSuppFile" id="uploadSuppFile" class="uploadField" />&nbsp;&nbsp;{translate key="form.saveToUpload"}</td>
</tr>
{if not $suppFile}
<tr valign="top">
	<td>&nbsp;</td>
        <td class="value"><input type="checkbox" name="showReviewers" id="showReviewers" value="1"{if $showReviewers==1} checked="checked"{/if} />&nbsp;
	<label for="showReviewers">{translate key="author.submit.suppFile.availableToPeers"}</label></td>
</tr>
{/if}
</table>

<div class="separator"></div>

<p><input type="submit" value="{translate key="common.saveAndContinue"}" class="button defaultButton" /> <input type="button" value="{translate key="common.cancel"}" class="button" onclick="document.location.href='{url op="submit" path="4" articleId=$articleId escape=false}'" /></p>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>

</form>

{include file="common/footer.tpl"}
