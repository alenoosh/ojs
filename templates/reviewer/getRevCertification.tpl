{assign var="pageTitle" value="common.queue.long.$pageToDisplay"}
{assign var="articleId" value=$submission->getArticleId()}
{assign var=editAssignments value=$submission->getEditAssignments()}

{include file="common/header.tpl"}
WELCOME
{include file="common/footer.tpl"}

