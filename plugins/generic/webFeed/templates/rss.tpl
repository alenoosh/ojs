<?xml version="1.0" encoding="{$defaultCharset}"?>
<rdf:RDF
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns="http://purl.org/rss/1.0/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:prism="http://prismstandard.org/namespaces/1.2/basic/">
    
	<channel rdf:about="{$journal->getUrl()}">
		{* required elements *}
		<title>{$journal->getJournalTitle()|escape:"html"|strip|strip_tags}</title>
		<link>{$journal->getUrl()}</link>
		{if $journal->getJournalDescription()}
			{assign var="description" value=$journal->getJournalDescription()}
		{elseif $journal->getLocalizedSetting('searchDescription')}
			{assign var="description" value=$journal->getLocalizedSetting('searchDescription')}
		{/if}
		<description>{$description|escape:"html"|strip|strip_tags}</description>

		{* optional elements *}
		{assign var="publisherInstitution" value=$journal->getSetting('publisherInstitution')}
		{if $publisherInstitution}
		<dc:publisher>{$publisherInstitution|escape:"html"|strip|strip_tags}</dc:publisher>
		{/if}
		{if $journal->getLocale()}
		<dc:language>{$journal->getLocale()|replace:'_':'-'|escape:"html"|strip|strip_tags}</dc:language>
		{/if}
		<prism:publicationName>{$journal->getJournalTitle()|escape:"html"|strip|strip_tags}</prism:publicationName>

		{if $journal->getSetting('printIssn')}
			{assign var="ISSN" value=$journal->getSetting('printIssn')}
		{elseif $journal->getSetting('issn')}
			{assign var="ISSN" value=$journal->getSetting('issn')}
		{elseif $journal->getSetting('onlineIssn')}
			{assign var="ISSN" value=$journal->getSetting('onlineIssn')}
		{/if}
		{if $ISSN}
		<prism:issn>{$ISSN}</prism:issn>
		{/if}
		{if $journal->getLocalizedSetting('copyrightNotice')}
		<prism:copyright>{$journal->getLocalizedSetting('copyrightNotice')|escape:"html"|strip|strip_tags}</prism:copyright>
		{/if}

		<items>
			<rdf:Seq>
			{foreach name=sections from=$publishedArticles item=section key=sectionId}
			{foreach from=$section.articles item=article}
				<rdf:li rdf:resource="{url page="article" op="view" path=$article->getBestArticleId($currentJournal)}"/>
			{/foreach}
			{/foreach}
			</rdf:Seq>
		</items>
	</channel>

{foreach name=sections from=$publishedArticles item=section key=sectionId}
{foreach from=$section.articles item=article}
	<item rdf:about="{url page="article" op="view" path=$article->getBestArticleId($currentJournal)}">
		{* required elements *}
		<title>{$article->getArticleTitle()|strip|strip_tags|escape:"html"}</title>
		<link>{url page="article" op="view" path=$article->getBestArticleId($currentJournal)}</link>

		{* optional elements *}
		{if $article->getArticleAbstract()}
		<description>{$article->getArticleAbstract()|strip|strip_tags|escape:"html"}</description>
		{/if}
		{foreach from=$article->getAuthors() item=author name=authorList}
		<dc:creator>{$author->getFullName()|strip|strip_tags|escape:"html"}</dc:creator>
		{/foreach}
		<dc:date>{$article->getDatePublished()|date_format:"%Y-%m-%d"}</dc:date>
		<prism:volume>{$issue->getVolume()}</prism:volume>
		<prism:publicationDate>{$article->getDatePublished()|date_format:"%Y-%m-%d"}</prism:publicationDate>
	</item>
{/foreach}
{/foreach}

</rdf:RDF>

