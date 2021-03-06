{**
 * atom.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Atom feed template
 *
 * $Id$
 *}
<?xml version="1.0" encoding="{$defaultCharset|escape}"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	{* required elements *}
	<id>{$journal->getUrl()}/issue/feed</id>
	<title>{$journal->getJournalTitle()|escape:"html"|strip|strip_tags}</title>
	<updated>{$issue->getDatePublished()|date_format:"%Y-%m-%dT%T%z"|regex_replace:"/00$/":":00"}</updated>

	{* recommended elements *}
	{if $journal->getSetting('contactName')}
	<author>
		<name>{$journal->getSetting('contactName')|escape:"html"|strip|strip_tags}</name>
		{if $journal->getSetting('contactEmail')}
		<email>{$journal->getSetting('contactEmail')|escape:"html"|strip|strip_tags}</email>
		{/if}
	</author>
	{/if}
	<link rel="alternate" href="{$journal->getUrl()}" />
	<link rel="self" type="application/atom+xml" href="{$journal->getUrl()}/feed/atom" />

	{* optional elements *}
	{* <category/> *}
	{* <contributor/> *}
	<generator uri="http://pkp.sfu.ca/ojs/" version="{$ojsVersion|escape}">Open Journal Systems</generator>
	{if $journal->getJournalDescription()}
		{assign var="description" value=$journal->getJournalDescription()}
	{elseif $journal->getLocalizedSetting('searchDescription')}
		{assign var="description" value=$journal->getLocalizedSetting('searchDescription')}
	{/if}
    {if $journal->getLocalizedSetting('copyrightNotice')}
    <rights>{$journal->getLocalizedSetting('copyrightNotice')|strip|strip_tags|escape:"html"}</rights>
    {/if}
	<subtitle>{$description|strip|strip_tags|escape:"html"}</subtitle>


{foreach name=sections from=$publishedArticles item=section key=sectionId}
{foreach from=$section.articles item=article}
	<entry>
		{* required elements *}
		<id>{url page="article" op="view" path=$article->getBestArticleId($currentJournal)}</id>
		<title>{$article->getArticleTitle()|strip|strip_tags|escape:"html"}</title>
		<updated>{$article->getLastModified()|date_format:"%Y-%m-%dT%T%z"|regex_replace:"/00$/":":00"}</updated>

		{* recommended elements *}
        {foreach from=$article->getAuthors() item=author name=authorList}
	  	<author>
			<name>{$author->getFullName()|strip|strip_tags|escape:"html"}</name>
			{if $author->getEmail()}
			<email>{$author->getEmail()|strip|strip_tags|escape:"html"}</email>
			{/if}
        </author>
		{/foreach}
		<link rel="alternate" href="{url page="article" op="view" path=$article->getBestArticleId($currentJournal)}" />
        {if $article->getArticleAbstract()}
		<summary type="html" xml:base="{url page="article" op="view" path=$article->getBestArticleId($currentJournal)}">{$article->getArticleAbstract()|strip|strip_tags|escape:"html"}</summary>
        {/if}

		{* optional elements *}
		{* <category/> *}
		{* <contributor/> *}
		<published>{$article->getDatePublished()|date_format:"%Y-%m-%dT%T%z"|regex_replace:"/00$/":":00"}</published>
		{* <source/> *}
		{* <rights/> *}
	</entry>
{/foreach}
{/foreach}

</feed>
