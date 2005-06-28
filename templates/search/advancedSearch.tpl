{**
 * advancedSearch.tpl
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Site/journal advanced search form.
 *
 * $Id$
 *}

{assign var="pageTitle" value="navigation.search"}
{include file="common/header.tpl"}

<script type="text/javascript">
{literal}
function ensureKeyword() {
	var allBlank = document.search.query.value == '';

	if (allBlank) {allBlank = document.search.author.value == '';}
	if (allBlank) {allBlank = document.search.title.value == '';}
	if (allBlank) {allBlank = document.search.discipline.value == '';}
	if (allBlank) {allBlank = document.search.subject.value == '';}
	if (allBlank) {allBlank = document.search.type.value == '';}
	if (allBlank) {allBlank = document.search.coverage.value == '';}
	if (allBlank) {allBlank = document.search.supplementaryFiles.value == '';}
	if (allBlank) {allBlank = document.search.fullText.value == '';}

	if (allBlank) {
		alert({/literal}'{translate|escape:"javascript" key="search.noKeywordError"}'{literal});
		return false;
	}
	document.search.submit();
	return true;
}
{/literal}
</script>

{if !$dateFrom}
{assign var="dateFrom" value="--"}
{/if}

{if !$dateTo}
{assign var="dateTo" value="--"}
{/if}

<form name="search" action="{$pageUrl}/search/advancedResults">

<table class="data" width="100%">
<tr valign="top">
	<td width="25%" class="label"><label for="query">{translate key="search.searchAllCategories"}</label></td>
	<td width="75%" class="value"><input type="text" id="query" name="query" size="40" maxlength="255" value="{$query}" class="textField" /></td>
</tr>
{if $siteSearch}
<tr valign="top">
	<td class="label"><label for="searchJournal">{translate key="search.withinJournal"}</label></td>
	<td class="value"><select name="searchJournal" id="searchJournal" class="selectMenu">{html_options options=$journalOptions selected=$searchJournal}</select></td>
</tr>
{/if}
<tr valign="top">
	<td class="label">{translate key="search.searchCategories"}</td>
	<td>&nbsp;</td>
</tr>
<tr valign="top">
	<td class="label"><label for="author">{translate key="search.author"}</label></td>
	<td class="value"><input type="text" name="author" id="author" size="40" maxlength="255" value="{$author}" class="textField" /></td>
</tr>
<tr valign="top">
	<td class="label"><label for="title">{translate key="article.title"}</label></td>
	<td class="value"><input type="text" id="title" name="title" size="40" maxlength="255" value="{$title}" class="textField" /></td>
</tr>
<tr valign="top">
	<td class="label"><label for="fullText">{translate key="search.fullText"}</label></td>
	<td class="value"><input type="text" id="fullText" name="fullText" size="40" maxlength="255" value="{$fullText}" class="textField" /></td>
</tr>
<tr valign="top">
	<td class="label"><label for="supplementaryFiles">{translate key="article.suppFiles"}</label></td>
	<td class="value"><input type="text" id="supplementaryFiles" name="supplementaryFiles" size="40" maxlength="255" value="{$supplementaryFiles}" class="textField" /></td>
</tr>
<tr valign="top">
	<td class="formSubLabel">{translate key="search.date"}</td>
	<td>&nbsp;</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="search.dateFrom"}</td>
	<td class="value">{html_select_date prefix="dateFrom" time=$dateFrom all_extra="class=\"selectMenu\"" year_empty="" month_empty="" day_empty="" start_year="-5" end_year="+1"}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="search.dateTo"}</td>
	<td class="value">{html_select_date prefix="dateTo" time=$dateTo all_extra="class=\"selectMenu\"" year_empty="" month_empty="" day_empty="" start_year="-5" end_year="+1"}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="search.indexTerms"}</td>
	<td>&nbsp;</td>
</tr>
<tr valign="top">
	<td class="label"><label for="discipline">{translate key="search.discipline"}</label></td>
	<td class="value"><input type="text" name="discipline" id="discipline" size="40" maxlength="255" value="{$discipline}" class="textField" /></td>
</tr>
<tr valign="top">
	<td class="label"><label for="subject">{translate key="search.subject"}</label></td>
	<td class="value"><input type="text" name="subject" id="subject" size="40" maxlength="255" value="{$subject}" class="textField" /></td>
</tr>
<tr valign="top">
	<td class="label"><label for="type">{translate key="search.typeMethodApproach"}</label></td>
	<td class="value"><input type="text" name="type" id="type" size="40" maxlength="255" value="{$type}" class="textField" /></td>
</tr>
<tr valign="top">
	<td class="label"><label for="coverage">{translate key="search.coverage"}</label></td>
	<td class="value"><input type="text" name="coverage" id="coverage" size="40" maxlength="255" value="{$coverage}" class="textField" /></td>
</tr>
</table>

<p><input type="button" onClick="ensureKeyword();" value="{translate key="common.search"}" class="button defaultButton" /></p>

<script type="text/javascript">document.search.query.focus();</script>
</form>

{include file="common/footer.tpl"}
