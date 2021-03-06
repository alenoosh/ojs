{**
 * step2.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Step 2 of author article submission.
 *
 * $Id$
 *}

{* Opatan Inc. : author.submit.step2 is changed to author.submit.step234 *} 
{assign var="pageTitle" value="author.submit.step234"}
{include file="author/submit/submitHeader.tpl"}

{url|assign:"competingInterestGuidelinesUrl" page="information" op="competingInterestGuidelines"}

<div class="separator"></div>

{* Opatan Inc. : enctype tag is added to allow file upload *}
<form name="submit" method="post" action="{url op="saveSubmit" path=$submitStep}" enctype="multipart/form-data">
<input type="hidden" name="articleId" value="{$articleId|escape}" />
{include file="common/formErrors.tpl"}

{literal}
<script type="text/javascript">
<!--
// Move author up/down
function moveAuthor(dir, authorIndex) {
	var form = document.submit;
	form.moveAuthor.value = 1;
	form.moveAuthorDir.value = dir;
	form.moveAuthorIndex.value = authorIndex;
	form.submit();
}
// -->
</script>
{/literal}

{literal}
<script type="text/javascript">
<!--
function suppTitleEntered() {
	titleEntered = document.submit.supp_title.value;
	if (!titleEntered) {
		alert("{/literal}{translate key="author.submit.suppTitleRequired"}{literal}");
		return false;
	}
	return true;
}
// -->
</script>
{/literal}

{* Opatan Inc. : the javascript function related to checking supp file upload is included in step 2 *} 
<script type="text/javascript">
{literal}
<!--
function confirmForgottenUpload() {
	var fieldValue = document.submitForm.uploadSuppFile.value;
	if (fieldValue) {
		return confirm("{/literal}{translate key="author.submit.forgottenSubmitSuppFile"}{literal}");
	}
	return true;
}
// -->
{/literal}
</script>

{if count($formLocales) > 1}
<table width="100%" class="data">
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="formLocale" key="form.formLanguage"}</td>
		<td width="80%" class="value">
			{url|assign:"submitFormUrl" op="submit" path="2" articleId=$articleId}
			{* Maintain localized author info across requests *}
			{foreach from=$authors key=authorIndex item=author}
				{* Opatan Inc. *}
				{foreach from=$author.firstName key="thisLocale" item="thisFirstName"}
					{if $thisLocale != $formLocale}<input type="hidden" name="authors[{$authorIndex|escape}][firstName][{$thisLocale|escape}]" value="{$thisFirstName|escape}" />{/if}
				{/foreach}
				{* Opatan Inc. *}
				{foreach from=$author.middleName key="thisLocale" item="thisMiddleName"}
					{if $thisLocale != $formLocale}<input type="hidden" name="authors[{$authorIndex|escape}][middleName][{$thisLocale|escape}]" value="{$thisMiddleName|escape}" />{/if}
				{/foreach}
				{* Opatan Inc. *}
				{foreach from=$author.lastName key="thisLocale" item="thisLastName"}
					{if $thisLocale != $formLocale}<input type="hidden" name="authors[{$authorIndex|escape}][lastName][{$thisLocale|escape}]" value="{$thisLastName|escape}" />{/if}
				{/foreach}				
				{* Opatan Inc. *}
				{foreach from=$author.affiliation key="thisLocale" item="thisAffiliation"}
					{if $thisLocale != $formLocale}<input type="hidden" name="authors[{$authorIndex|escape}][affiliation][{$thisLocale|escape}]" value="{$thisAffiliation|escape}" />{/if}
				{/foreach}				
				{if $currentJournal->getSetting('requireAuthorCompetingInterests')}
					{foreach from=$author.competingInterests key="thisLocale" item="thisCompetingInterests"}
						{if $thisLocale != $formLocale}<input type="hidden" name="authors[{$authorIndex|escape}][competingInterests][{$thisLocale|escape}]" value="{$thisCompetingInterests|escape}" />{/if}
					{/foreach}
				{/if}
				{foreach from=$author.biography key="thisLocale" item="thisBiography"}
					{if $thisLocale != $formLocale}<input type="hidden" name="authors[{$authorIndex|escape}][biography][{$thisLocale|escape}]" value="{$thisBiography|escape}" />{/if}
				{/foreach}
			{/foreach}
			{foreach from=$reviewers key=reviewerIndex item=reviewer}
				{foreach from=$reviewer.firstName key="thisLocale" item="thisRevFirstName"}
					{if $thisLocale != $formLocale}<input type="hidden" name="reviewers[{$reviewerIndex|escape}][firstName][{$thisLocale|escape}]" value="{$thisRevFirstName|escape}" />{/if}
				{/foreach}
				{foreach from=$reviewer.middleName key="thisLocale" item="thisRevMiddleName"}
					{if $thisLocale != $formLocale}<input type="hidden" name="reviewers[{$reviewerIndex|escape}][middleName][{$thisLocale|escape}]" value="{$thisRevMiddleName|escape}" />{/if}
				{/foreach}
				{foreach from=$reviewer.lastName key="thisLocale" item="thisRevLastName"}
					{if $thisLocale != $formLocale}<input type="hidden" name="reviewers[{$reviewerIndex|escape}][lastName][{$thisLocale|escape}]" value="{$thisRevLastName|escape}" />{/if}
				{/foreach}
				{foreach from=$reviewer.affiliation key="thisLocale" item="thisRevAffiliation"}
					{if $thisLocale != $formLocale}<input type="hidden" name="reviewers[{$reviewerIndex|escape}][affiliation][{$thisLocale|escape}]" value="{$thisRevAffiliation|escape}" />{/if}
				{/foreach}
			{/foreach}
			{form_language_chooser form="submit" url=$submitFormUrl}
			<span class="instruct">{translate key="form.formLanguage.description"}</span>
		</td>
	</tr>
</table>
{/if}

<h3>{translate key="article.authors"}</h3>
<input type="hidden" name="deletedAuthors" value="{$deletedAuthors|escape}" />
<input type="hidden" name="moveAuthor" value="0" />
<input type="hidden" name="moveAuthorDir" value="" />
<input type="hidden" name="moveAuthorIndex" value="" />

{foreach name=authors from=$authors key=authorIndex item=author}
<input type="hidden" name="authors[{$authorIndex|escape}][authorId]" value="{$author.authorId|escape}" />
<input type="hidden" name="authors[{$authorIndex|escape}][seq]" value="{$authorIndex+1}" />
{if $smarty.foreach.authors.total <= 1}
<input type="hidden" name="primaryContact" value="{$authorIndex|escape}" />
{/if}

<table width="100%" class="data">
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-$authorIndex-firstName" required="true" key="user.firstName"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[{$authorIndex|escape}][firstName][{$formLocale|escape}]" id="authors-{$authorIndex|escape}-firstName" value="{$author.firstName[$formLocale]|escape}" size="20" maxlength="40" /></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-$authorIndex-middleName" key="user.middleName"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[{$authorIndex|escape}][middleName][{$formLocale|escape}]" id="authors-{$authorIndex|escape}-middleName" value="{$author.middleName[$formLocale]|escape}" size="20" maxlength="40" /></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-$authorIndex-lastName" required="true" key="user.lastName"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[{$authorIndex|escape}][lastName][{$formLocale|escape}]" id="authors-{$authorIndex|escape}-lastName" value="{$author.lastName[$formLocale]|escape}" size="20" maxlength="90" /></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-$authorIndex-affiliation" key="user.affiliation"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[{$authorIndex|escape}][affiliation][{$formLocale|escape}]" id="authors-{$authorIndex|escape}-affiliation" value="{$author.affiliation[$formLocale]|escape}" size="30" maxlength="255"/></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-$authorIndex-country" key="common.country"}</td>
	<td width="80%" class="value">
		<select name="authors[{$authorIndex|escape}][country]" id="authors-{$authorIndex|escape}-country" class="selectMenu">
			<option value=""></option>
			{html_options options=$countries selected=$author.country}
		</select>
	</td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-$authorIndex-email" required="true" key="user.email"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[{$authorIndex|escape}][email]" id="authors-{$authorIndex|escape}-email" value="{$author.email|escape}" size="30" maxlength="90" /></td>
</tr>
<tr valign="top">
	<td class="label">{fieldLabel name="authors-$authorIndex-url" key="user.url"}</td>
	<td class="value"><input type="text" name="authors[{$authorIndex|escape}][url]" id="authors-{$authorIndex|escape}-url" value="{$author.url|escape}" size="30" maxlength="90" class="textField" /></td>
</tr>
{if $currentJournal->getSetting('requireAuthorCompetingInterests')}
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="authors-$authorIndex-competingInterests" key="author.competingInterests" competingInterestGuidelinesUrl=$competingInterestGuidelinesUrl}</td>
		<td width="80%" class="value"><textarea name="authors[{$authorIndex|escape}][competingInterests][{$formLocale|escape}]" class="textArea" id="authors-{$authorIndex|escape}-competingInterests" rows="5" cols="40">{$author.competingInterests[$formLocale]|escape}</textarea></td>
	</tr>
{/if}{* requireAuthorCompetingInterests *}
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-$authorIndex-biography" key="user.biography"}<br />{translate key="user.biography.description"}</td>
	<td width="80%" class="value"><textarea name="authors[{$authorIndex|escape}][biography][{$formLocale|escape}]" class="textArea" id="authors-{$authorIndex|escape}-biography" rows="5" cols="40">{$author.biography[$formLocale]|escape}</textarea></td>
</tr>
{if $smarty.foreach.authors.total > 1}
<tr valign="top">
	<td colspan="2">
		<a href="javascript:moveAuthor('u', '{$authorIndex|escape}')" class="action">&uarr;</a> <a href="javascript:moveAuthor('d', '{$authorIndex|escape}')" class="action">&darr;</a>
		{translate key="author.submit.reorderInstructions"}
	</td>
</tr>
<tr valign="top">
	<td width="80%" class="value" colspan="2"><input type="radio" name="primaryContact" value="{$authorIndex|escape}"{if $primaryContact == $authorIndex} checked="checked"{/if} /> <label for="primaryContact">{translate key="author.submit.selectPrincipalContact"}</label> <input type="submit" name="delAuthor[{$authorIndex|escape}]" value="{translate key="author.submit.deleteAuthor"}" class="button" /></td>
</tr>
<tr>
	<td colspan="2"><br/></td>
</tr>
{/if}
</table>
{foreachelse}
<input type="hidden" name="authors[0][authorId]" value="0" />
<input type="hidden" name="primaryContact" value="0" />
<input type="hidden" name="authors[0][seq]" value="1" />
<table width="100%" class="data">
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-0-firstName" required="true" key="user.firstName"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[0][firstName][{$formLocale|escape}]" id="authors-0-firstName" size="20" maxlength="40" /></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-0-middleName" key="user.middleName"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[0][middleName][{$formLocale|escape}]" id="authors-0-middleName" size="20" maxlength="40" /></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-0-lastName" required="true" key="user.lastName"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[0][lastName][{$formLocale|escape}]" id="authors-0-lastName" size="20" maxlength="90" /></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-0-affiliation" key="user.affiliation"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[0][affiliation][{$formLocale|escape}]" id="authors-0-affiliation" size="30" maxlength="255" /></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-0-country" key="common.country"}</td>
	<td width="80%" class="value">
		<select name="authors[0][country]" id="authors-0-country" class="selectMenu">
			<option value=""></option>
			{html_options options=$countries}
		</select>
	</td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-0-email" required="true" key="user.email"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[0][email]" id="authors-0-email" size="30" maxlength="90" /></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-0-url" required="true" key="user.url"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="authors[0][url]" id="authors-0-url" size="30" maxlength="90" /></td>
</tr>
{if $currentJournal->getSetting('requireAuthorCompetingInterests')}
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-0-competingInterests" key="author.competingInterests" competingInterestGuidelinesUrl=$competingInterestGuidelinesUrl}</td>
	<td width="80%" class="value"><textarea name="authors[0][competingInterests][{$formLocale|escape}]" class="textArea" id="authors-0-competingInterests" rows="5" cols="40"></textarea></td>
</tr>
{/if}
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="authors-0-biography" key="user.biography"}<br />{translate key="user.biography.description"}</td>
	<td width="80%" class="value"><textarea name="authors[0][biography][{$formLocale|escape}]" class="textArea" id="authors-0-biography" rows="5" cols="40"></textarea></td>
</tr>
</table>
{/foreach}

<p><input type="submit" class="button" name="addAuthor" value="{translate key="author.submit.addAuthor"}" /></p>

<div class="separator"></div>

<h3>{if $section->getAbstractsDisabled()==1}{translate key="article.title"}{else}{translate key="submission.titleAndAbstract"}{/if}</h3>

<table width="100%" class="data">

<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="title" required="true" key="article.title"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="title[{$formLocale|escape}]" id="title" value="{$title[$formLocale]|escape}" size="60" maxlength="255" /></td>
</tr>
{* Opatan Inc. : running title input box is added *}
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="runningTitle" required="true" key="article.runningTitle"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="runningTitle[{$formLocale|escape}]" id="runningTitle" value="{$runningTitle[$formLocale]|escape}" size="60" maxlength="255" /></td>
</tr>
{if $section->getAbstractsDisabled()==0}
<tr valign="top">
{*Opatan Inc. -> Abstract Length Note*}
	<td width="20%" class="label">{fieldLabel name="abstract" required="true" key="article.abstract"} 
	<br />{if $abstractMinimumLength neq 0 && $abstractMaximumLength neq 0} {translate key="author.submit.form.abstractLengthNote" abstractMinLength=$abstractMinimumLength abstractMaxLength=$abstractMaximumLength}
	{elseif $abstractMinimumLength neq 0 || $abstractMaximumLength neq 0} {translate key="author.submit.form.abstractLengthNote" abstractMinLength=$abstractMinimumLength abstractMaxLength=$abstractMaximumLength}{/if}
	</td>

	<td width="80%" class="value"><textarea name="abstract[{$formLocale|escape}]" id="abstract" class="textArea" rows="15" cols="60">{$abstract[$formLocale]|escape}</textarea></td>
</tr>
{/if}{* Abstracts enabled *}
</table>

<div class="separator"></div>

{if $section->getMetaIndexed()==1}
	<h3>{translate key="submission.indexing"}</h3>
	{if $journalSettings.metaDiscipline || $journalSettings.metaSubjectClass || $journalSettings.metaSubject || $journalSettings.metaCoverage || $journalSettings.metaType}<p>{translate key="author.submit.submissionIndexingDescription"}</p>{/if}
	<table width="100%" class="data">
	{if $journalSettings.metaDiscipline}
	<tr valign="top">
		<td{if $currentJournal->getLocalizedSetting('metaDisciplineExamples') != ''} rowspan="2"{/if} width="20%" class="label">{fieldLabel name="discipline" key="article.discipline"}</td>
		<td width="80%" class="value"><input type="text" class="textField" name="discipline[{$formLocale|escape}]" id="discipline" value="{$discipline[$formLocale]|escape}" size="40" maxlength="255" /></td>
	</tr>
	{if $currentJournal->getLocalizedSetting('metaDisciplineExamples')}
	<tr valign="top">
		<td><span class="instruct">{$currentJournal->getLocalizedSetting('metaDisciplineExamples')|escape}</span></td>
	</tr>
	{/if}
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	{/if}
	
	{if $journalSettings.metaSubjectClass}
	<tr valign="top">
		<td rowspan="2" width="20%" class="label">{fieldLabel name="subjectClass" key="article.subjectClassification"}</td>
		<td width="80%" class="value"><input type="text" class="textField" name="subjectClass[{$formLocale|escape}]" id="subjectClass" value="{$subjectClass[$formLocale]|escape}" size="40" maxlength="255" /></td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label"><a href="{$currentJournal->getLocalizedSetting('metaSubjectClassUrl')|escape}" target="_blank">{$currentJournal->getLocalizedSetting('metaSubjectClassTitle')|escape}</a></td>
	</tr>
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	{/if}
	
	{if $journalSettings.metaSubject}
	<tr valign="top">
		<td{if $currentJournal->getLocalizedSetting('metaSubjectExamples') != ''} rowspan="2"{/if} width="20%" class="label">{fieldLabel name="subject" key="article.subject"}</td>
		<td width="80%" class="value"><input type="text" class="textField" name="subject[{$formLocale|escape}]" id="subject" value="{$subject[$formLocale]|escape}" size="40" maxlength="255" /></td>
	</tr>
	{if $currentJournal->getLocalizedSetting('metaSubjectExamples') != ''}
	<tr valign="top">
		<td><span class="instruct">{$currentJournal->getLocalizedSetting('metaSubjectExamples')|escape}</span></td>
	</tr>
	{/if}
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	{/if}
	
	{if $journalSettings.metaCoverage}
	<tr valign="top">
		<td{if $currentJournal->getLocalizedSetting('metaCoverageGeoExamples') != ''} rowspan="2"{/if} width="20%" class="label">{fieldLabel name="coverageGeo" key="article.coverageGeo"}</td>
		<td width="80%" class="value"><input type="text" class="textField" name="coverageGeo[{$formLocale|escape}]" id="coverageGeo" value="{$coverageGeo[$formLocale]|escape}" size="40" maxlength="255" /></td>
	</tr>
	{if $currentJournal->getLocalizedSetting('metaCoverageGeoExamples')}
	<tr valign="top">
		<td><span class="instruct">{$currentJournal->getLocalizedSetting('metaCoverageGeoExamples')|escape}</span></td>
	</tr>
	{/if}
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr valign="top">
		<td{if $currentJournal->getLocalizedSetting('metaCoverageChronExamples') != ''} rowspan="2"{/if} width="20%" class="label">{fieldLabel name="coverageChron" key="article.coverageChron"}</td>
		<td width="80%" class="value"><input type="text" class="textField" name="coverageChron[{$formLocale|escape}]" id="coverageChron" value="{$coverageChron[$formLocale]|escape}" size="40" maxlength="255" /></td>
	</tr>
	{if $currentJournal->getLocalizedSetting('metaCoverageChronExamples') != ''}
	<tr valign="top">
		<td><span class="instruct">{$currentJournal->getLocalizedSetting('metaCoverageChronExamples')|escape}</span></td>
	</tr>
	{/if}
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr valign="top">
		<td{if $currentJournal->getLocalizedSetting('metaCoverageResearchSampleExamples') != ''} rowspan="2"{/if} width="20%" class="label">{fieldLabel name="coverageSample" key="article.coverageSample"}</td>
		<td width="80%" class="value"><input type="text" class="textField" name="coverageSample[{$formLocale|escape}]" id="coverageSample" value="{$coverageSample[$formLocale]|escape}" size="40" maxlength="255" /></td>
	</tr>
	{if $currentJournal->getLocalizedSetting('metaCoverageResearchSampleExamples') != ''}
	<tr valign="top">
		<td><span class="instruct">{$currentJournal->getLocalizedSetting('metaCoverageResearchSampleExamples')|escape}</span></td>
	</tr>
	{/if}
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	{/if}
	
	{if $journalSettings.metaType}
	<tr valign="top">
		<td width="20%" {if $currentJournal->getLocalizedSetting('metaTypeExamples') != ''}rowspan="2" {/if}class="label">{fieldLabel name="type" key="article.type"}</td>
		<td width="80%" class="value"><input type="text" class="textField" name="type[{$formLocale|escape}]" id="type" value="{$type[$formLocale]|escape}" size="40" maxlength="255" /></td>
	</tr>

	{if $currentJournal->getLocalizedSetting('metaTypeExamples') != ''}
	<tr valign="top">
		<td><span class="instruct">{$currentJournal->getLocalizedSetting('metaTypeExamples')|escape}</span></td>
	</tr>
	{/if}
	<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	{/if}
	
	<tr valign="top">
		<td rowspan="2" width="20%" class="label">{fieldLabel name="language" key="article.language"}</td>
		<td width="80%" class="value"><input type="text" class="textField" name="language" id="language" value="{$language|escape}" size="5" maxlength="10" /></td>
	</tr>
	<tr valign="top">
		<td><span class="instruct">{translate key="author.submit.languageInstructions"}</span></td>
	</tr>
	</table>

<div class="separator"></div>

{/if}


<h3>{translate key="author.submit.submissionSupportingAgencies"}</h3>
<p>{translate key="author.submit.submissionSupportingAgenciesDescription"}</p>

<table width="100%" class="data">
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="sponsor" key="author.submit.agencies"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="sponsor[{$formLocale|escape}]" id="sponsor" value="{$sponsor[$formLocale]|escape}" size="60" maxlength="255" /></td>
</tr>
</table>

<div class="separator"></div>

{if $authorCanSpecifyReviewers}
<h3>{translate key="author.submit.reviewerRecommendation"}</h3>
<table class="data" width="100%">
	<tr class="heading" valign="bottom">
		{if $countOfReviewers > 1}<td width="25%">&nbsp;</td>{/if}
		<td width="15%" class="label" align="center">
			{fieldLabel name="reviewers-$reviewerIndex-firstName" required="true" key="user.firstName"}
		</td>
		<td width="15%" class="label" align="center">{fieldLabel name="reviewers-$reviewerIndex-middleName" key="user.middleName"}</td>
		<td width="15%" class="label" align="center">{fieldLabel name="reviewers-$reviewerIndex-lastName" required="true" key="user.lastName"}</td>
		<td width="15%" class="label" align="center">{fieldLabel name="reviewers-$reviewerIndex-affiliation" key="user.affiliation"}</td>
		<td width="15%" class="label" align="center">{fieldLabel name="reviewers-$reviewerIndex-email" required="true" key="user.email"}</td>
	</tr>
{foreach name=reviewers from=$reviewers item=reviewer key=reviewerIndex}
	<tr>
		{if $countOfReviewers > 1}
			<td width="25%">{translate key="author.submit.reviewerNumber"}&nbsp;{$reviewerIndex+1}</td>
		{/if}
		<input type="hidden" name="reviewers[{$reviewerIndex|escape}][reviewerId]" value="{$reviewer.reviewerId|escape}" />
		<td width="15%" class="value">
			<input type="text" class="textField" name="reviewers[{$reviewerIndex|escape}][firstName][{$formLocale|escape}]"
			       id="reviewers-{$reviewerIndex|escape}-firstName" value="{$reviewer.firstName[$formLocale]|escape}"
			       size="20" maxlength="40" />
		</td>
		<td width="15%" class="value">
			<input type="text" class="textField"
		    	       name="reviewers[{$reviewerIndex|escape}][middleName][{$formLocale|escape}]"
		    	       id="reviewers-{$reviewerIndex|escape}-middleName" value="{$reviewer.middleName[$formLocale]|escape}"
		    	       size="20" maxlength="40" />
		</td>
		<td width="15%" class="value">
			<input type="text" class="textField"
		    	       name="reviewers[{$reviewerIndex|escape}][lastName][{$formLocale|escape}]"
		               id="reviewers-{$reviewerIndex|escape}-lastName" value="{$reviewer.lastName[$formLocale]|escape}"
		               size="20" maxlength="90" />
		</td>
		<td width="15%" class="value">
			<input type="text" class="textField"
		               name="reviewers[{$reviewerIndex|escape}][affiliation][{$formLocale|escape}]"
		               id="reviewers-{$reviewerIndex|escape}-affiliation" value="{$reviewer.affiliation[$formLocale]|escape}"
		               size="20" maxlength="255" />
		</td>
		<td width="15%" class="value">
			<input type="text" class="textField" name="reviewers[{$reviewerIndex|escape}][email]"
		               id="reviewers-{$reviewerIndex|escape}-email" value="{$reviewer.email|escape}"
		               size="20" maxlength="90" />
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
{/foreach}
</table>
<div class="separator"></div>
{/if}


{* Opatan Inc. : STEP 3 MERGED *}
<h3>{translate key="author.submit.submissionFile"}</h3>
{if !$submissionFile}
	{translate key="author.submit.uploadInstructions"}
	{if $journalSettings.supportPhone}
		{assign var="howToKeyName" value="author.submit.howToSubmit"}
	{else}
		{assign var="howToKeyName" value="author.submit.howToSubmitNoPhone"}
	{/if}
	<div class="separator"></div>
{/if}

<p>{translate key=$howToKeyName supportName=$journalSettings.supportName supportEmail=$journalSettings.supportEmail supportPhone=$journalSettings.supportPhone}</p>


<table class="data" width="100%">
{if $submissionFile}
<tr valign="top">
	<td width="20%" class="label">{translate key="common.fileName"}</td>
	<td width="80%" class="value"><a href="{url op="download" path=$articleId|to_array:$submissionFile->getFileId()}">{$submissionFile->getFileName()|escape}</a></td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{translate key="common.originalFileName"}</td>
	<td width="80%" class="value">{$submissionFile->getOriginalFileName()|escape}</td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{translate key="common.fileSize"}</td>
	<td width="80%" class="value">{$submissionFile->getNiceFileSize()}</td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{translate key="common.dateUploaded"}</td>
	<td width="80%" class="value">{showdate value=$submissionFile->getDateUploaded() format=$datetimeFormatShort}</td>
</tr>
{else}
<tr valign="top">
	<td colspan="2" class="nodata">{translate key="author.submit.noSubmissionFile"}</td>
</tr>
{/if}
</table>

<div class="separator"></div>

{if !$submissionFile}
<table class="data" width="100%">
<tr>
	<td width="30%" class="label">
		{fieldLabel name="submissionFile" key="author.submit.uploadSubmissionFile"}
	</td>
	<td width="70%" class="value">
		<input type="file" class="uploadField" name="submissionFile" id="submissionFile" /> <input name="uploadSubmissionFile" type="submit" class="button" value="{translate key="common.upload"}" />
		{if $currentJournal->getSetting('showEnsuringLink')}<a class="action" href="javascript:openHelp('{get_help_id key="editorial.sectionEditorsRole.review.blindPeerReview" url="true"}')">{translate key="reviewer.article.ensuringBlindReview"}</a>{/if}
	</td>
</tr>
</table>

<div class="separator"></div>
{/if}

{* Opatan Inc. : END OF STEP 3 MERGED *}

{* Opatan Inc. : STEP 4 MERGED *}
<h3>{translate key="author.submit.suppFile"}</h3>
<p>{translate key="author.submit.supplementaryFilesInstructions"}</p>

<table class="listing" width="100%">
<tr>
	<td colspan="5" class="headseparator">&nbsp;</td>
</tr>
<tr class="heading" valign="bottom">
	<td width="5%">{translate key="common.id"}</td>
	<td width="40%">{translate key="common.title"}</td>
	<td width="25%">{translate key="common.originalFileName"}</td>
	<td width="15%" class="nowrap">{translate key="common.dateUploaded"}</td>
	<td width="15%" align="right">{translate key="common.action"}</td>
</tr>
<tr>
	<td colspan="6" class="headseparator">&nbsp;</td>
</tr>
{foreach from=$suppFiles item=file}
<tr valign="top">
	<td>{$file->getSuppFileId()}</td>
	<td>{$file->getSuppFileTitle()|escape}</td>
	<td>{$file->getOriginalFileName()|escape}</td>
	<td>{showdate value=$file->getDateSubmitted() format=$dateFormatTrunc}</td>
	<td align="right"><a href="{url op="submitSuppFile" path=$file->getSuppFileId() articleId=$articleId}" class="action">{translate key="common.edit"}</a>&nbsp;|&nbsp;<a href="{url op="deleteSubmitSuppFile" path=$file->getSuppFileId() articleId=$articleId}" onclick="return confirm('{translate|escape:"jsparam" key="author.submit.confirmDeleteSuppFile"}')" class="action">{translate key="common.delete"}</a></td>
</tr>
{foreachelse}
<tr valign="top">
	<td colspan="6" class="nodata">{translate key="author.submit.noSupplementaryFiles"}</td>
</tr>
{/foreach}
</table>

<div class="separator"></div>

<table class="data" width="100%">
<tr valign="top">
	<td width="20%" class="label">{fieldLabel required="true" name="supp_title" key="common.title"}</td>
	<td width="80%" class="value"><input type="text" class="textField" name="supp_title[{$formLocale|escape}]" id="supp_title" size="60" maxlength="255" /></td>
</tr>
<tr>
	<td width="30%" class="label">{fieldLabel name="uploadSuppFile" key="author.submit.uploadSuppFile"}</td>
	<td width="70%" class="value">
		<input type="file" name="uploadSuppFile" id="uploadSuppFile" class="uploadField" /> <input name="submitUploadSuppFile" type="submit" onclick="return suppTitleEntered()" class="button" value="{translate key="common.upload"}" />
		{if $currentJournal->getSetting('showEnsuringLink')}<a class="action" href="javascript:openHelp('{get_help_id key="editorial.sectionEditorsRole.review.blindPeerReview" url="true"}')">{translate key="reviewer.article.ensuringBlindReview"}</a>{/if}
	</td>
</tr>
</table>

<div class="separator"></div>
{* Opatan Inc. : END OF STEP 4 MERGED *}

<p><input type="submit"{if !$submissionFile} onclick="return confirm('{translate|escape:"jsparam" key="author.submit.noSubmissionConfirm"}')"{/if} value="{translate key="common.saveAndContinue"}" class="button defaultButton" /> <input type="button" value="{translate key="common.cancel"}" class="button" onclick="confirmAction('{url page="author"}', '{translate|escape:"jsparam" key="author.submit.cancelSubmission"}')" /></p>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>

</form>

{include file="common/footer.tpl"}
