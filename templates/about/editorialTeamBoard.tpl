{**
 * editorialTeam.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * About the Journal index.
 *
 * $Id$
 *}
{assign var="pageTitle" value="about.editorialTeam"}
{include file="common/header.tpl"}
{* Opatan Inc.*}
{if $customTemplateExist}
{include file="$customTemplateName"}
{else}
{foreach from=$groups item=group}
<h4>{$group->getGroupTitle()}</h4>
{assign var=groupId value=$group->getGroupId()}
{assign var=members value=$teamInfo[$groupId]}

{foreach from=$members item=member}
	{assign var=user value=$member->getUser()}
	{* Opatan Inc. : getAffiliation is replaced with getUserAffiliation() *}
	<a href="javascript:openRTWindow('{url op="editorialTeamBio" path=$user->getUserId()}')">{$user->getFullName()|escape}</a>{if $user->getUserAffiliation()}, {$user->getUserAffiliation()|escape}{/if}{if $user->getCountry()}{assign var=countryCode value=$user->getCountry()}{assign var=country value=$countries.$countryCode}, {$country|escape}{/if}
	<br />
{/foreach}
{/foreach}
{/if}

{include file="common/footer.tpl"}
