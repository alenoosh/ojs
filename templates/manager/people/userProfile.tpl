{**
 * userProfile.tpl
 *
 * Copyright (c) 2003-2005 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display user profile.
 *
 * $Id$
 *}

{assign var="pageTitle" value="manager.people"}
{include file="common/header.tpl"}

<h3>{$user->getFullName()|escape}</h3>

<h4>{translate key="user.profile"}</h4>

<p><a href="{$pageUrl}/manager/editUser/{$user->getUserId()}" class="action">{translate key="manager.people.editProfile"}</a></p>

<table width="100%" class="data">
	<tr valign="top">
		<td width="20%" class="label">{translate key="user.username"}</td>
		<td width="80%" class="data"><strong>{$user->getUsername()|escape}</strong></td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.firstName"}</td>
		<td class="value">{$user->getFirstName()|escape|default:"&mdash;"}</td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.middleName"}</td>
		<td class="value">{$user->getMiddleName()|escape|default:"&mdash;"}</td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.lastName"}</td>
		<td class="value">{$user->getLastName()|escape|default:"&mdash;"}</td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.affiliation"}</td>
		<td class="value">{$user->getAffiliation()|escape|default:"&mdash;"}</td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.email"}</td>
		<td class="value">
			{$user->getEmail()|escape} 
			{assign var=emailString value="`$user->getFullName()` <`$user->getEmail()`>"}
			{assign var=emailStringEscaped value=$emailString|escape:"url"}
			{assign var=urlEscaped value=$currentUrl|escape:"url"}
			{icon name="mail" url="`$pageUrl`/user/email?to[]=$emailStringEscaped&redirectUrl=$urlEscaped"}
		</td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.phone"}</td>
		<td class="value">{$user->getPhone()|escape|default:"&mdash;"}</td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.fax"}</td>
		<td class="value">{$user->getFax()|escape|default:"&mdash;"}</td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.interests"}</td>
		<td class="value">{$user->getInterests()|escape|default:"&mdash;"}</td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="common.mailingAddress"}</td>
		<td class="value">{$user->getMailingAddress()|escape|nl2br|default:"&mdash;"}</td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.biography"}</td>
		<td class="value">{$user->getBiography()|escape|nl2br|default:"&mdash;"}</td>
	</tr>
	{if $profileLocalesEnabled}
	<tr valign="top">
		<td class="label">{translate key="user.workingLanguages"}</td>
		<td class="value">{foreach name=workingLanguages from=$user->getLocales() item=localeKey}{$localeNames.$localeKey}{if !$smarty.foreach.workingLanguages.last}; {/if}{foreachelse}&mdash;{/foreach}</td>
	</tr>
	{/if}
	<tr valign="top">
		<td colspan="2" class="separator"></td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.dateRegistered"}</td>
		<td class="value">{$user->getDateRegistered()|date_format:$datetimeFormatLong}</td>
	</tr>
	<tr valign="top">
		<td class="label">{translate key="user.dateLastLogin"}</td>
		<td class="value">{$user->getDateLastLogin()|date_format:$datetimeFormatLong}</td>
	</tr>
</table>

<div class="separator"></div>

<h4>{translate key="manager.people.enrollment"}</h4>

<ul>
{section name=role loop=$userRoles}
	<li>{translate key=$userRoles[role]->getRoleName()} <a href="{$pageUrl}/manager/unEnroll?userId={$user->getUserId()}&amp;roleId={$userRoles[role]->getRoleId()}" onclick="return confirm('{translate|escape:"javascript" key="manager.people.confirmUnenroll"}')" class="action">{translate key="manager.people.unenroll"}</a></li>
{/section}
</ul>

{include file="common/footer.tpl"}
