{**
 * citation.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Article reading tools -- Capture Citation MLA format
 *
 * $Id$
 *}
<div class="separator"></div>

{assign var=authors value=$article->getAuthors()}
{assign var=authorCount value=$authors|@count}
{foreach from=$authors item=author name=authors key=i}
	{* Opatan Inc. : getFirstName is replaced with getAuthorFirstName and getLastName is replaced with getAuthorLastName *}
	{assign var=firstName value=$author->getAuthorFirstName()}
	{$author->getAuthorLastName()|escape}, {$firstName|escape}{if $i==$authorCount-2}, {translate key="rt.context.and"} {elseif $i<$authorCount-1}, {else}.{/if}
{/foreach}

"{$article->getArticleTitle()|strip_unsafe_html}" <i>{$journal->getJournalTitle()|escape}</i> [{translate key="rt.captureCite.online"}], {if $issue}{$issue->getVolume()|escape} {/if}{showdate value=$article->getDatePublished() format='%e %b %Y'}

