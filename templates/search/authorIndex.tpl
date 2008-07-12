{**
 * authorIndex.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Index of published articles by author.
 *
 * $Id$
 *}
{assign var="pageTitle" value="search.authorIndex"}
{include file="common/header.tpl"}

<p>{foreach from=$alphaList item=letter}<a href="{url op="authors" searchInitial=$letter}">{if $letter == $searchInitial}<strong>{$letter|escape}</strong>{else}{$letter|escape}{/if}</a> {/foreach}<a href="{url op="authors"}">{if $searchInitial==''}<strong>{translate key="common.all"}</strong>{else}{translate key="common.all"}{/if}</a></p>

<a name="authors"></a>

{iterate from=authors item=author}
	{assign var=lastFirstLetter value=$firstLetter}
	{* Opatan Inc. : getLastName is replaced with getAuthorLastName *}
	{assign var=firstLetter value=$author->getAuthorLastName()|String_substr:0:1}

	{if $lastFirstLetter != $firstLetter}
		<a name="{$firstLetter|escape}"></a>
		<h3>{$firstLetter|escape}</h3>
	{/if}
	
	{* Opatan Inc. : (true) ???? *}
	{* Opatan Inc. : getFirstName is replaced with getAuthorFirstName and getLastName(true) is replaced with getAuthorLastName and getMiddleName is replaced with getAuthorMiddleName and getAffiliation is replaced with getAuthorAffiliation *}
	<a href="{url op="authors" path="view" firstName=$author->getAuthorFirstName() middleName=$author->getAuthorMiddleName() lastName=$author->getAuthorLastName() affiliation=$author->getAuthorAffiliation() country=$author->getCountry()}">
		{$author->getAuthorLastName()|escape},
		{$author->getAuthorFirstName()|escape}{if $author->getAuthorMiddleName()} {$author->getAuthorMiddleName()|escape}{/if}{if $author->getAuthorAffiliation()}, {$author->getAuthorAffiliation()|escape}{/if}
	</a>
	<br/>
{/iterate}
{if !$authors->wasEmpty()}
	<br />
	{page_info iterator=$authors}&nbsp;&nbsp;&nbsp;&nbsp;{page_links anchor="authors" iterator=$authors name="authors" searchInitial=$searchInitial}
{else}
{/if}

{include file="common/footer.tpl"}
