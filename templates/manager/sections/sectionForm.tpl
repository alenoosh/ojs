{**
 * sectionForm.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Form to create/modify a journal section.
 *
 * $Id$
 *}

{assign var="pageTitle" value="section.section"}
{assign var="pageCrumbTitle" value="section.sections"}
{url|assign:"currentUser" op="sections"}
{include file="common/header.tpl"}

<form name="section" method="post" action="{url op="updateSection" path=$sectionId}">
<input type="hidden" name="action" value="" />
<input type="hidden" name="userId" value="" />

{literal}
<script type="text/javascript">
<!--

function addSectionEditor(editorId) {
	document.section.action.value = "addSectionEditor";
	document.section.userId.value = editorId;
	document.section.submit();
}

function removeSectionEditor(editorId) {
	document.section.action.value = "removeSectionEditor";
	document.section.userId.value = editorId;
	document.section.submit();
}

// -->
</script>
{/literal}

{include file="common/formErrors.tpl"}

<table class="data" width="100%">
{if count($formLocales) > 1}
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="formLocale" required="true" key="common.language"}</td>
		<td width="80%" class="value">
			{if $sectionId}{url|assign:"sectionFormUrl" op="editSection" path=$sectionId}
			{else}{url|assign:"sectionFormUrl" op="createSection" path=$sectionId}
			{/if}
			{form_language_chooser form="section" url=$sectionFormUrl}
		</td>
	</tr>
{/if}
<tr valign="top">
	<td width="20%" class="label">{fieldLabel name="title" required="true" key="section.title"}</td>
	<td width="80%" class="value"><input type="text" name="title[{$formLocale|escape}]" value="{$title[$formLocale]|escape}" id="title" size="40" maxlength="120" class="textField" /></td>
</tr>
<tr valign="top">
	<td class="label">{fieldLabel name="abbrev" required="true" key="section.abbreviation"}</td>
	<td class="value"><input type="text" name="abbrev[{$formLocale|escape}]" id="abbrev" value="{$abbrev[$formLocale]|escape}" size="20" maxlength="20" class="textField" />&nbsp;&nbsp;{translate key="section.abbreviation.example"}</td>
</tr>
<tr valign="top">
	<td class="label">{fieldLabel name="policy" key="manager.sections.policy"}</td>
	<td class="value"><textarea name="policy[{$formLocale|escape}]" rows="4" cols="40" id="policy" class="textArea">{$policy[$formLocale]|escape}</textarea></td>
</tr>
<tr valign="top">
	<td rowspan="4" class="label">{fieldLabel suppressId="true" key="submission.indexing"}</td>
	<td class="value">
		{translate key="manager.section.submissionsToThisSection"}<br/>
		<input type="checkbox" name="metaReviewed" id="metaReviewed" value="1" {if $metaReviewed}checked="checked"{/if} />
		{fieldLabel name="metaReviewed" key="manager.sections.submissionReview"}
	</td>
</tr>
<tr valign="top">
	<td class="value">
		<input type="checkbox" name="abstractsDisabled" id="abstractsDisabled" value="1" {if $abstractsDisabled}checked="checked"{/if} />
		{fieldLabel name="abstractsDisabled" key="manager.sections.abstractsDisabled"}
	</td>
</tr>
<tr valign="top">
	<td class="value">
		<input type="checkbox" name="metaIndexed" id="metaIndexed" value="1" {if $metaIndexed}checked="checked"{/if} />
		{fieldLabel name="metaIndexed" key="manager.sections.submissionIndexing"}
	</td>
</tr>
<tr valign="top">
	<td class="value">
		{fieldLabel name="identifyType" key="manager.sections.identifyType"} <input type="text" name="identifyType[{$formLocale|escape}]" id="identifyType" value="{$identifyType[$formLocale]|escape}" size="20" maxlength="60" class="textField" />
		<br />
		<span class="instruct">{translate key="manager.sections.identifyTypeExamples"}</span>
	</td>
</tr>
<tr valign="top">
	<td class="label">{fieldLabel suppressId="true" key="submission.restrictions"}</td>
	<td class="value">
		<input type="checkbox" name="editorRestriction" id="editorRestriction" value="1" {if $editorRestriction}checked="checked"{/if} />
		{fieldLabel name="editorRestriction" key="manager.sections.editorRestriction"}
	</td>
</tr>
<tr valign="top">
	<td class="label">{fieldLabel name="hideTitle" key="issue.toc"}</td>
	<td class="value">
		<input type="checkbox" name="hideTitle" id="hideTitle" value="1" {if $hideTitle}checked="checked"{/if} />
		{fieldLabel name="hideTitle" key="manager.sections.hideTocTitle"}
	</td>
</tr>
<tr valign="top">
	<td class="label">{fieldLabel name="hideAbout" key="navigation.about"}</td>
	<td class="value">
		<input type="checkbox" name="hideAbout" id="hideAbout" value="1" {if $hideAbout}checked="checked"{/if} />
		{fieldLabel name="hideAbout" key="manager.sections.hideAbout"}
	</td>
</tr>
</table>
<div class="separator"></div>

<h3>{translate key="user.role.sectionEditors"}</h3>
<p><span class="instruct">{translate key="manager.section.sectionEditorInstructions"}</span></p>
<h4>{translate key="manager.sections.unassigned"}</h4>

<table width="100%" class="listing" id="sectionEditors">
	<tr>
		<td colspan="3" class="headseparator">&nbsp;</td>
	</tr>
	<tr valign="top" class="heading">
		<td width="20%">{translate key="user.username"}</td>
		<td width="60%">{translate key="user.name"}</td>
		<td width="20%" align="right">{translate key="common.action"}</td>
	</tr>
	<tr>
		<td colspan="3" class="headseparator">&nbsp;</td>
	</tr>
	{foreach from=$unassignedEditors item=editor}
		<tr valign="top">
			<td>{$editor->getUsername()|escape}</td>
			<td>{$editor->getFullName()|escape}</td>
			<td align="right">
				<a class="action" href="javascript:addSectionEditor({$editor->getUserId()})">{translate key="common.add"}</a>
			</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="3" class="nodata">{translate key="common.none"}</td>
		</tr>
	{/foreach}
	<tr>
		<td colspan="3" class="endseparator">&nbsp;</td>
	</tr>
</table>

<h4>{translate key="manager.sections.assigned"}</h4>

<table width="100%" class="listing" id="sectionEditors">
	<tr>
		<td colspan="5" class="headseparator">&nbsp;</td>
	</tr>
	<tr valign="top" class="heading">
		<td width="20%">{translate key="user.username"}</td>
		<td width="40%">{translate key="user.name"}</td>
		<td width="10%" align="center">{translate key="submission.review"}</td>
		<td width="10%" align="center">{translate key="submission.editing"}</td>
		<td width="20%" align="right">{translate key="common.action"}</td>
	</tr>
	<tr>
		<td colspan="5" class="headseparator">&nbsp;</td>
	</tr>
	{foreach from=$assignedEditors item=editorEntry}
		{assign var=editor value=$editorEntry.user}
		<input type="hidden" name="assignedEditorIds[]" value="{$editor->getUserId()|escape}" />
		<tr valign="top">
			<td>{$editor->getUsername()|escape}</td>
			<td>{$editor->getFullName()|escape}</td>
			<td align="center"><input type="checkbox" {if $editorEntry.canReview}checked="checked"{/if} name="canReview-{$editor->getUserId()}" /></td>
			<td align="center"><input type="checkbox" {if $editorEntry.canEdit}checked="checked"{/if} name="canEdit-{$editor->getUserId()}" /></td>
			<td align="right">
				<a class="action" href="javascript:removeSectionEditor({$editor->getUserId()})">{translate key="common.remove"}</a>
			</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="5" class="nodata">{translate key="common.none"}</td>
		</tr>
	{/foreach}
	<tr>
		<td colspan="5" class="endseparator">&nbsp;</td>
	</tr>
</table>

<p><input type="submit" value="{translate key="common.save"}" class="button defaultButton" /> <input type="button" value="{translate key="common.cancel"}" class="button" onclick="document.location.href='{url op="sections" escape=false}'" /></p>

</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
{include file="common/footer.tpl"}
