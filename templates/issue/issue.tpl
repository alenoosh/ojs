{**
 * issue.tpl
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Issue
 *
 * $Id$
 *}

{foreach from=$publishedArticles item=section key=sectionTitle}

<div>
	<div><b>{$sectionTitle}</b></div>

	<div>
	{foreach from=$section item=article}
	<table width="100%">
	<tr>
		<td>{$article->getArticleTitle()}</td>
		<td align="right">
			{foreach from=$article->getGalleys() item=galley name=galleyList}
				<a href="{$pageUrl}/article/{if not $galley->isHtmlGalley()}download/{$article->getArticleId()}/{$galley->getFileId()}{else}view/{$article->getArticleId()}/{$galley->getGalleyId()}{/if}" class="file">{$galley->getLabel()}</a>{if !$smarty.foreach.galleyList.last}&nbsp;{/if}
			{foreachelse}
				<a href="{$pageUrl}/article/view/{$article->getArticleId()}" class="file">{translate key="issue.abstract"}</a>
			{/foreach}
		</td>
	</tr>
	<tr>
		<td style="padding-left: 30px;font-style: italic;">
			{foreach from=$article->getAuthors() item=author name=authorList}
				{$author->getFullName()}{if !$smarty.foreach.authorList.last},{/if}
			{/foreach}
		</td>
		<td align="right">{$article->getPages()}</td>
	</tr>
	</table>
	{/foreach}
	</div>

</div>

<br />

{/foreach}
