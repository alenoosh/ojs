{**
 * index.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Reviewer index.
 *
 * $Id$
 *}
{assign var="pageTitle" value="common.queue.long.$pageToDisplay"}
{assign var="articleId" value=$submission->getArticleId()}
{assign var=editAssignments value=$submission->getEditAssignments()}

{include file="common/header.tpl"}

<ul class="menu">
	<li{if ($pageToDisplay == "active")} class="current"{/if}><a href="{url path="active"}">{translate key="common.queue.short.active"}</a></li>
	<li{if ($pageToDisplay == "completed")} class="current"{/if}><a href="{url path="completed"}">{translate key="common.queue.short.completed"}</a></li>
</ul>
<div class="separator"></div>

<ul class="menu">

	<li><a href="#" onclick="sendMail()">{translate key="common.queue.short.emailTheEditor"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id" anchor="anchor"}','submission','reviewerGuidelines')">{translate key="reviewer.article.reviewerGuidelines"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id"}','submission')">{translate key="common.queue.short.viewDetails"}</a></li>
	<li>{if ($pageToDisplay == "completed")}<a href="{url path="completed"}">{translate key="common.queue.short.getReviewerCertification"}</a>{/if}</li>
</ul>

<br />

{include file="reviewer/$pageToDisplay.tpl"}

<div class="separator"></div>

<br />

<ul class="menu">
	<li><a href="#" onclick="sendMail()">{translate key="common.queue.short.emailTheEditor"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id" anchor="anchor"}','submission','reviewerGuidelines')">{translate key="reviewer.article.reviewerGuidelines"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id"}','submission')">{translate key="common.queue.short.viewDetails"}</a></li>
	<li>{if ($pageToDisplay == "completed")}<a href="#" onclick="callSelectedUrl('{url op="function" path="article_id"}','getRevCertification')">{translate key="common.queue.short.getReviewerCertification"}</a>{/if}</li>
	
</ul>

<div class="separator"></div>

<br />

<ul class="menu">
	<li{if ($pageToDisplay == "active")} class="current"{/if}><a href="{url path="active"}">{translate key="common.queue.short.active"}</a></li>
	<li{if ($pageToDisplay == "completed")} class="current"{/if}><a href="{url path="completed"}">{translate key="common.queue.short.completed"}</a></li>
</ul>

<div id="content"></div>

{include file="common/footer.tpl"}
