{**
 * createIssue.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Form for creation of an issue
 *
 * $Id$
 *}

{assign var="pageTitle" value="editor.issues.createIssue"}
{url|assign:"currentUrl" page="editor" op="createIssue"}
{include file="common/header.tpl"}

{include file="common/formErrors.tpl"}

<ul class="menu">
	<li class="current"><a href="{url op="createIssue"}">{translate key="editor.navigation.createIssue"}</a></li>
	<li><a href="{url op="futureIssues"}">{translate key="editor.navigation.futureIssues"}</a></li>
	<li><a href="{url op="backIssues"}">{translate key="editor.navigation.issueArchive"}</a></li>
</ul>

<br />

<form action="#">
{translate key="issue.issue"}: <select name="issue" class="selectMenu" onchange="if(this.options[this.selectedIndex].value > 0) location.href='{url op="issueToc" path="ISSUE_ID" escape=false}'.replace('ISSUE_ID', this.options[this.selectedIndex].value)" size="1">{html_options options=$issueOptions selected=$issueId}</select>
</form>

<form method="post" action="{url op="saveIssue"}" enctype="multipart/form-data">

<div class="separator"></div>

<h3>{translate key="editor.issues.identification"}</h3>

<table width="100%" class="data">
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="volume" key="issue.volume"}</td>
		<td width="80%" class="value"><input type="text" name="volume" id="volume" value="{$volume|escape}" size="5" maxlength="5" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="number" key="issue.number"}</td>
		<td class="value"><input type="text" name="number" id="number" value="{$number|escape}" size="5" maxlength="5" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="year" key="issue.year"}</td>
		<td class="value"><input type="text" name="year" id="year" value="{$year|escape}" size="5" maxlength="4" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="labelFormat" key="editor.issues.issueIdentification"}</td>
		<td class="value"><input type="checkbox" name="showVolume" id="showVolume" value="1"{if $showVolume} checked="checked"{/if} /><label for="showVolume"> {translate key="issue.volume"}</label><br /><input type="checkbox" name="showNumber" id="showNumber" value="1"{if $showNumber} checked="checked"{/if} /><label for="showNumber"> {translate key="issue.number"}</label><br /><input type="checkbox" name="showYear" id="showYear" value="1"{if $showYear} checked="checked"{/if} /><label for="showYear"> {translate key="issue.year"}</label><br /><input type="checkbox" name="showTitle" id="showTitle" value="1"{if $showTitle} checked="checked"{/if} /><label for="showTitle"> {translate key="issue.title"}</label></td>
	</tr>
	{if $enablePublicIssueId}
	<tr valign="top">
		<td class="label">{fieldLabel name="publicIssueId" key="editor.issues.publicIssueIdentifier"}</td>
		<td class="value"><input type="text" name="publicIssueId" id="publicIssueId" value="{$publicIssueId|escape}" size="20" maxlength="255" class="textField" /></td>
	</tr>
	{/if}
	<tr valign="top">
		<td class="label">{fieldLabel name="title" key="issue.title"}</td>
		<td class="value"><input type="text" name="title" id="title" value="{$title|escape}" size="40" maxlength="120" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="description" key="editor.issues.description"}</td>
		<td class="value"><textarea name="description" id="description" cols="40" rows="5" class="textArea">{$description|escape}</textarea></td>
	</tr>
</table>

{if $enableSubscriptions && !$enableDelayedOpenAccess}
<div class="separator"></div>
<h3>{translate key="editor.issues.access"}</h3>
<table width="100%" class="data">
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="accessStatus" key="editor.issues.accessStatus"}</td>
		<td width="80%" class="value"><select name="accessStatus" id="accessStatus" class="selectMenu">{html_options options=$accessOptions selected=$accessStatus}</select></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="openAccessDate" key="editor.issues.accessDate"}</td>
		{if ($Date_Year && $Date_Month && $Date_Day)} 
			<td class="value">{html_select_date time="$Date_Year-$Date_Month-$Date_Day" end_year="+20" all_extra="class=\"selectMenu\""}</td>
		{else}
			<td class="value">{html_select_date end_year="+20" all_extra="class=\"selectMenu\""}</td>
		{/if}
	</tr>
</table>
{/if}

<div class="separator"></div>

<h3>{translate key="editor.issues.cover"}</h3>
<table width="100%" class="data">
	<tr valign="top">
		<td class="label" colspan="2"><input type="checkbox" name="showCoverPage" id="showCoverPage" value="1" {if $showCoverPage} checked="checked"{/if} /> <label for="showCoverPage">{translate key="editor.issues.showCoverPage"}</label></td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="coverPage" key="editor.issues.coverPage"}</td>
		<td width="80%" class="value">
			<input type="file" name="coverPage" id="coverPage" class="uploadField" />&nbsp;&nbsp;{translate key="form.saveToUpload"}<br/>
			{translate key="editor.issues.coverPageInstructions"}
		</td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="coverPageDescription" key="editor.issues.coverPageCaption"}</td>
		<td class="value"><textarea name="coverPageDescription" id="coverPageDescription" cols="40" rows="5" class="textArea">{$coverPageDescription|escape}</textarea></td>
	</tr>
</table>

<p><input type="submit" value="{translate key="common.save"}" class="button defaultButton" /> <input type="button" value="{translate key="common.cancel"}" onclick="document.location.href='{url op="index" escape=false}'" class="button" /></p>

</form>

{include file="common/footer.tpl"}
