{**
 * searchUsers.tpl
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Search form for enrolled users.
 *
 * $Id$
 *
 *}

{assign var="start" value="A"|ord}

{assign var="pageTitle" value="manager.people.enrollment"}
{include file="common/header.tpl"}

<form name="submit" method="post" action="{$requestPageUrl}/enrollSearch/{$articleId}">
	<select name="searchField" size="1" class="selectMenu">
		{html_options_translate options=$fieldOptions}
	</select>
	<select name="searchMatch" size="1" class="selectMenu">
		<option value="contains">{translate key="form.contains"}</option>
		<option value="is">{translate key="form.is"}</option>
	</select>
	<input type="text" size="15" name="search" class="textField" />&nbsp;<input type="submit" value="{translate key="common.search"}" class="button" />
</form>

<p>{section loop=26 name=letters}<a href="{$requestPageUrl}/enrollSearch/{$articleId}?search_initial={$smarty.section.letters.index+$start|chr}">{$smarty.section.letters.index+$start|chr}</a> {/section}</p>

<table width="100%" class="listing">
<tr><td colspan="5" class="headseparator"></tr>
<tr class="heading" valign="bottom">
	<td width="5%"></td>
	<td width="25%">{translate key="user.username"}</td>
	<td width="30%">{translate key="user.name"}</td>
	<td width="30%">{translate key="user.email"}</td>
	<td width="10%">{translate key="common.action"}</td>
</tr>
<form action="{$requestPageUrl}/enroll/{$articleId}" method="post">
<tr><td colspan="5" class="headseparator"></tr>
{foreach from=$users item=user name=users}
{assign var="userid" value=$user->getUserId()}
{assign var="stats" value=$statistics[$userid]}
<tr valign="top">
	<td><input type="checkbox" name="users[]" value="{$user->getUserId()}" /></td>
	<td><a class="action" href="{$requestPageUrl}/userProfile/{$userid}">{$user->getUsername()}</a></td>
	<td>{$user->getFullName(true)}</td>
	<td>{$user->getEmail(true)}</td>
	<td><a href="{$requestPageUrl}/enroll/{$articleId}?userId={$user->getUserId()}" class="action">{translate key="manager.people.enroll"}</a></td>
</tr>
<tr><td colspan="5" class="{if $smarty.foreach.users.last}end{/if}separator"></tr>
{foreachelse}
<tr>
<td colspan="5" class="nodata">{translate key="common.none"}</td>
</tr>
<tr><td colspan="5" class="endseparator"></tr>
{/foreach}
</table>

<input type="submit" value="{translate key="manager.people.enrollSelected"}" class="button defaultButton" /> <input type="button" value="{translate key="common.cancel"}" class="button" onclick="document.location.href='{$pageUrl}/manager'" />

</form>


{if $backLink}
<a href="{$backLink}">{translate key="$backLinkLabel"}</a>
{/if}

{include file="common/footer.tpl"}
