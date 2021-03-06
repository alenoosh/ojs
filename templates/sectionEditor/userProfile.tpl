{**
 * userProfile.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display user profile.
 *
 * $Id$
 *}
{assign var="pageTitle" value="manager.people"}
{include file="common/header.tpl"}

<h3>{translate key="user.profile"}: {$user->getFullName()|escape}</h3>

<table class="data" width="100%">
<tr valign="top">
	<td width="20%" class="label">{translate key="user.salutation"}:</td>
	{* Opatan Inc. : getSalutation() is replaced with getUserSalutation() *}
	<td width="80%" class="value">{$user->getUserSalutation()|escape}</td>
</tr>
<tr valign="top">
	<td width="20%" class="label">{translate key="user.username"}:</td>
	<td width="80%" class="value">{$user->getUsername()|escape}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.firstName"}:</td>
	{* Opatan Inc. : getFirstName() is replaced with getUserFirstName() *}
	<td class="value">{$user->getUserFirstName()|escape}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.middleName"}:</td>
	{* Opatan Inc. : getMiddleName() is replaced with getUserMiddleName() *}
	<td class="value">{$user->getUserMiddleName()|escape}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.lastName"}:</td>
	{* Opatan Inc. : getLastName() is replaced with getUserLastName() *}
	<td class="value">{$user->getUserLastName()|escape}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.gender"}</td>
	<td class="value">
		{if $user->getGender() == "M"}{translate key="user.masculine"}
		{elseif $user->getGender() == "F"}{translate key="user.feminine"}
		{else}&mdash;
		{/if}
	</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.affiliation"}:</td>
	{* Opatan Inc. : getAffiliation() is replaced with getUserAffiliation() *}
	<td class="value">{$user->getUserAffiliation()|escape}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.signature"}:</td>
	<td class="value">{$user->getUserSignature()|escape|nl2br}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.email"}:</td>
	<td class="value">
		{$user->getEmail()|escape} 
		{assign var=emailString value="`$user->getFullName()` <`$user->getEmail()`>"}
		{url|assign:"url" page="user" op="email" to=$emailString|to_array redirectUrl=$currentUrl}
		{icon name="mail" url=$url}
	</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.url"}:</td>
	<td class="value"><a href="{$user->getUrl()|escape:"quotes"}">{$user->getUrl()|escape}</a></td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.phone"}:</td>
	<td class="value">{$user->getPhone()|escape}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.fax"}:</td>
	<td class="value">{$user->getFax()|escape}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.interests"}:</td>
	<td class="value">{$user->getUserInterests()|escape}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="common.mailingAddress"}:</td>
	<td class="value">{$user->getMailingAddress()|strip_unsafe_html|nl2br}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="common.discipline"}</td>
	<td class="value">{$discipline|escape}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.biography"}:</td>
	<td class="value">{$user->getUserBiography()|strip_unsafe_html|nl2br}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.workingLanguages"}:</td>
	<td class="value">{foreach name=workingLanguages from=$user->getLocales() item=localeKey}{$localeNames.$localeKey|escape}{if !$smarty.foreach.workingLanguages.last}; {/if}{/foreach}</td>
</tr>
<tr valign="top">
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.dateRegistered"}:</td>
	<td class="value">{showdate value=$user->getDateRegistered() format=$datetimeFormatLong}</td>
</tr>
<tr valign="top">
	<td class="label">{translate key="user.dateLastLogin"}:</td>
	<td class="value">{showdate value=$user->getDateLastLogin() format=$datetimeFormatLong}</td>
</tr>
</table>

{include file="common/footer.tpl"}
