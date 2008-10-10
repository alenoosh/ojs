{assign var="pageTitle" value="common.queue.long.revCertification"}
{assign var="articleTitle" value=$submission->getArticleTitle()}

{include file="common/header.tpl"}
<h2>
Date: {showdate value=$date format=$datetimeFormatShort}<br />
Subject: Review certification
Reviewer: {$reviewerName}<br />
Journal: {$journalTitle}<br />
Manuscript: {$articleTitle}<br /><br />

To whom it may concern<br /><br />


This is to certificate that Dr. "{$reviewerName}", has made excelent contribution to the quality of work the "{$journalTitle}" publishes, by reviewing the manuscript "{$articleTitle}".<br />

I behalf of the editorial team, acknowledge Dr. "{$reviewerName}" for this important review.<br />

{$editorialSignature}
</h2>
{include file="common/footer.tpl"}

