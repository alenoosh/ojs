{**
 * authorIndex.tpl
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Index of published articles by author.
 *
 * $Id$
 *}

{assign var="start" value="A"|ord}

{assign var="pageTitle" value="search.authorIndex"}
{include file="common/header.tpl"}

<p>{section loop=26 name=letters}<a href="{url op="authors" searchInitial=$smarty.section.letters.index+$start|chr}">{if chr($smarty.section.letters.index+$start) == $searchInitial}<strong>{$smarty.section.letters.index+$start|chr}</strong>{else}{$smarty.section.letters.index+$start|chr}{/if}</a> {/section}<a href="{url op="authors"}">{if $searchInitial==''}<strong>{translate key="common.all"}</strong>{else}{translate key="common.all"}{/if}</a></p>

{iterate from=authors item=author}
	{assign var=lastFirstLetter value=$firstLetter}
	{assign var=firstLetter value=$author->getLastName()|String_substr:0:1}

	{if $lastFirstLetter != $firstLetter}
		<a name="{$firstLetter|escape}"></a>
		<h3>{$firstLetter|escape}</h3>
	{/if}

	<a href="{url op="authors" path="view" firstName=$author->getFirstName() middleName=$author->getMiddleName() lastName=$author->getLastName() affiliation=$author->getAffiliation()}">
		{$author->getLastName(true)|escape},
		{$author->getFirstName()|escape}{if $author->getMiddleName()} {$author->getMiddleName|escape}{/if}{if $author->getAffiliation()}, {$author->getAffiliation()|escape}{/if}
	</a>
	<br/>
{/iterate}
{if !$authors->wasEmpty()}
	<br />
	{page_info iterator=$authors}&nbsp;&nbsp;&nbsp;&nbsp;{page_links iterator=$authors name="authors"}
{else}
{/if}

{include file="common/footer.tpl"}
