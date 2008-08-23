{assign var="pageTitle" value="common.queue.long.revCertification"}
{assign var="articleId" value=$submission->getArticleId()}
{assign var=editAssignments value=$submission->getEditAssignments()}

{include file="common/header.tpl"}
Date: {$date}
Subject: Review certification
Reviewer: {$name}
Journal: {$journal}
Manuscript: {$articleTitle}

To whom it may concern

This is to certificate that Dr "{$name}", has made excelent contribution to the quality of work the "{$journal}" publishes, by reviewing the manuscript "{$articleTitle}".

I behalf of the editorial team I acknowledge Dr "{$name}" for this important review.

{$editorialSignature}
{include file="common/footer.tpl"}

