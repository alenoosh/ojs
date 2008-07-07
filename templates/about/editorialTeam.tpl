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

{if count($editors) > 0}
	{if count($editors) == 1}
		<h4>{translate key="user.role.editor"}</h4>
	{else}
		<h4>{translate key="user.role.editors"}</h4>
	{/if}

{foreach from=$editors item=editor}
	{* Opatan Inc. : getAffiliation() is replaced with getUserAffiliation() *}
	<a href="javascript:openRTWindow('{url op="editorialTeamBio" path=$editor->getUserId()}')">{$editor->getFullName()|escape}</a>{if $editor->getUserAffiliation()}, {$editor->getUserAffiliation()|escape}{/if}{if $editor->getCountry()}{assign var=countryCode value=$editor->getCountry()}{assign var=country value=$countries.$countryCode}, {$country|escape}{/if}
	<br/>
{/foreach}
{/if}

{if count($sectionEditors) > 0}
	{if count($sectionEditors) == 1}
		<h4>{translate key="user.role.sectionEditor"}</h4>
	{else}
		<h4>{translate key="user.role.sectionEditors"}</h4>
	{/if}

{foreach from=$sectionEditors item=sectionEditor}
	{* Opatan Inc. : getAffiliation() is replaced with getUserAffiliation() *}
	<a href="javascript:openRTWindow('{url op="editorialTeamBio" path=$sectionEditor->getUserId()}')">{$sectionEditor->getFullName()|escape}</a>{if $sectionEditor->getUserAffiliation()}, {$sectionEditor->getUserAffiliation()|escape}{/if}{if $sectionEditor->getCountry()}{assign var=countryCode value=$sectionEditor->getCountry()}{assign var=country value=$countries.$countryCode}, {$country|escape}{/if}
	<br/>
{/foreach}
{/if}

{if count($layoutEditors) > 0}
	{if count($layoutEditors) == 1}
		<h4>{translate key="user.role.layoutEditor"}</h4>
	{else}
		<h4>{translate key="user.role.layoutEditors"}</h4>
	{/if}

{foreach from=$layoutEditors item=layoutEditor}
	{* Opatan Inc. : getAffiliation() is replaced with getUserAffiliation() *}
	<a href="javascript:openRTWindow('{url op="editorialTeamBio" path=$layoutEditor->getUserId()}')">{$layoutEditor->getFullName()|escape}</a>{if $layoutEditor->getUserAffiliation()}, {$layoutEditor->getUserAffiliation()|escape}{/if}{if $layoutEditor->getCountry()}{assign var=countryCode value=$layoutEditor->getCountry()}{assign var=country value=$countries.$countryCode}, {$country|escape}{/if}
	<br/>
{/foreach}
{/if}

{if count($copyEditors) > 0}
	{if count($copyEditors) == 1}
		<h4>{translate key="user.role.copyeditor"}</h4>
	{else}
		<h4>{translate key="user.role.copyeditors"}</h4>
	{/if}

{foreach from=$copyEditors item=copyEditor}
	{* Opatan Inc. : getAffiliation() is replaced with getUserAffiliation() *}
	<a href="javascript:openRTWindow('{url op="editorialTeamBio" path=$copyEditor->getUserId()}')">{$copyEditor->getFullName()|escape}</a>{if $copyEditor->getUserAffiliation()}, {$copyEditor->getUserAffiliation()|escape}{/if}{if $copyEditor->getCountry()}{assign var=countryCode value=$copyEditor->getCountry()}{assign var=country value=$countries.$countryCode}, {$country|escape}{/if}
	<br/>
{/foreach}
{/if}

{if count($proofreaders) > 0}
	{if count($proofreaders) == 1}
		<h4>{translate key="user.role.proofreader"}</h4>
	{else}
		<h4>{translate key="user.role.proofreaders"}</h4>
	{/if}

{foreach from=$proofreaders item=proofreader}
	{* Opatan Inc. : getAffiliation() is replaced with getUserAffiliation() *}
	<a href="javascript:openRTWindow('{url op="editorialTeamBio" path=$proofreader->getUserId()}')">{$proofreader->getFullName()|escape}</a>{if $proofreader->getUserAffiliation()}, {$proofreader->getUserAffiliation()|escape}{/if}{if $proofreader->getCountry()}{assign var=countryCode value=$proofreader->getCountry()}{assign var=country value=$countries.$countryCode}, {$country|escape}{/if}
	<br/>
{/foreach}
{/if}

{include file="common/footer.tpl"}
