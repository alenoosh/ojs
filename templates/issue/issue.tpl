{**
 * issue.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Issue
 *
 * $Id$
 *} 
{foreach name=sections from=$publishedArticles item=section key=sectionId}
{if $section.title}<h4>{$section.title|escape}</h4>{/if}

{foreach from=$section.articles item=article}
<table width="100%">
<tr valign="top">
	{if $article->getFileName($locale) && $article->getShowCoverPage($locale)}
	<td rowspan="2">
		<div class="articleCoverImage">
		<a href="{url page="article" op="view" path=$article->getBestArticleId($currentJournal)}" class="file">
		<img src="{$coverPagePath|escape}{$article->getFileName($locale)|escape}"{if $article->getCoverPageAltText($locale) != ''} alt="{$article->getCoverPageAltText($locale)|escape}" title="{$article->getCoverPageAltText($locale)|escape}"{else} alt="{translate key="article.coverPage.altText"}" title="{translate key="article.coverPage.altText"}"{/if}/></a></div>
	</td>
	{/if}

	<td width="70%" height="100%">{$article->getArticleTitle()|strip_unsafe_html}</td>
	<td align="right" width="30%">
		{if $section.abstractsDisabled || $article->getArticleAbstract() == ""}
			{assign var=hasAbstract value=0}
		{else}
			{assign var=hasAbstract value=1}
		{/if}

		{assign var=articleId value=$article->getArticleId()}
		{if (!$subscriptionRequired || $article->getAccessStatus() || $subscribedUser || $subscribedDomain || ($subscriptionExpiryPartial && $articleExpiryPartial.$articleId))}
			{assign var=hasAccess value=1}
		{else}
			{assign var=hasAccess value=0}
		{/if}

		{if !$hasAccess || $hasAbstract}<a href="{url page="article" op="view" path=$article->getBestArticleId($currentJournal)}" class="file">{if $hasAbstract}{translate key=article.abstract}{else}{translate key="article.details"}{/if}</a>{/if}

		{if $hasAccess || ($subscriptionRequired && $showGalleyLinks)}
			{foreach from=$article->getLocalizedGalleys() item=galley name=galleyList}
				<a href="{url page="article" op="view" path=$article->getBestArticleId($currentJournal)|to_array:$galley->getGalleyId()}" class="file">{* Opatan Inc. : create file icons*}
	  {if $galley->getGalleyLabel()|escape == "PDF"}
		<img class="accessLogo" src="{$baseUrl}/templates/images/icons/pdf.gif" border="0" alt="PDF" title="PDF">
	  {elseif $galley->getGalleyLabel()|escape == "HTML"}
          	<img class="accessLogo" src="{$baseUrl}/templates/images/icons/html.gif" border="0" alt="HTML" title="HTML">
	  {elseif $galley->getGalleyLabel()|escape == "PostScript"}
          	<img class="accessLogo" src="{$baseUrl}/templates/images/icons/ps.gif" border="0" alt="PostScript" title="PostScript">
	  {elseif $galley->getGalleyLabel()|escape == "XML"}
          	<img class="accessLogo" src="{$baseUrl}/templates/images/icons/xml.gif" border="0" alt="XML" title="XML">
	  {else}
          	<img class="accessLogo" src="{$baseUrl}/templates/images/icons/untitled.gif" border="0" alt="UNTITLED" title="UNTITLED">
	  {/if}</a>
				{if $subscriptionRequired && $showGalleyLinks && $restrictOnlyPdf}
					{if $article->getAccessStatus() || !$galley->isPdfGalley()}	
						<img class="accessLogo" src="{$baseUrl}/templates/images/icons/fulltext_open_medium.gif">
					{else}
						<img class="accessLogo" src="{$baseUrl}/templates/images/icons/fulltext_restricted_medium.gif">
					{/if}
										
				{/if}
				
			{/foreach}
			{if $subscriptionRequired && $showGalleyLinks && !$restrictOnlyPdf}
				{if $article->getAccessStatus()}
					<img class="accessLogo" src="{$baseUrl}/templates/images/icons/fulltext_open_medium.gif">
				{else}
					<img class="accessLogo" src="{$baseUrl}/templates/images/icons/fulltext_restricted_medium.gif">
				{/if}
			{/if}				
		{/if}
	</td>
</tr>
<tr>
	<td style="padding-left: 30px;font-style: italic;" valign="top">
		{if (!$section.hideAuthor && $article->getHideAuthor() == 0) || $article->getHideAuthor() == 2}
			{foreach from=$article->getAuthors() item=author name=authorList}
				{$author->getFullName()|escape}{if !$smarty.foreach.authorList.last},{/if}
			{/foreach}
		{else}
			&nbsp;
		{/if}
	</td>
	<td align="right" valign="top">{$article->getPages()|escape}</td>
</tr>
</table>
{/foreach}

{if !$smarty.foreach.sections.last}
<div class="separator"></div>
{/if}
{/foreach}
