{**
 * index.tpl
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Journal author index.
 *
 * $Id$
 *}
{assign var="pageTitle" value="common.queue.long.$pageToDisplay"}
{include file="common/header.tpl"}
<ul class="menu">
	<li{if ($pageToDisplay == "active")} class="current"{/if}><a href="{url op="index" path="active"}">{translate key="common.queue.short.active"}</a></li>
	<li{if ($pageToDisplay == "completed")} class="current"{/if}><a href="{url op="index" path="completed"}">{translate key="common.queue.short.completed"}</a></li>
</ul>
<div class="separator"></div>
<ul class="menu">
	<li><a href="{url op="submit" path=""}">{translate key="common.queue.short.startNewSubmission"}</a></li>

	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id"}','addSuppFile')">{translate key="common.queue.short.uploadSuppFile"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id"}','submission')">{translate key="common.queue.short.viewDetails"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id" anchor="anchor"}','submissionReview','editorDecision')">{translate key="common.queue.short.SubmitRevisedVersion"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id"}','viewMetadata')">{translate key="common.queue.short.editMetadata"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id" anchor="anchor"}','submissionEditing','copyedit')">{translate key="common.queue.short.viewCopyeditedArticle"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id" anchor="anchor"}','submissionEditing','layout')">{translate key="common.queue.short.viewLayoutEditedArticle"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" articleId="article_id"}','emailEditorDecisionComment')">{translate key="common.queue.short.notifyEditor"}</a></li>
	<li><a href="/ojs/index.php/{$journalTitle}/about/submissions#authorGuidelines">{translate key="common.queue.short.authorGuidelines"}</a></li>

</ul>

<br />

{include file="author/$pageToDisplay.tpl"}

<div class="separator"></div>
<br />
<ul class="menu">
	<li><a href="{url op="submit" path=""}">{translate key="common.queue.short.startNewSubmission"}</a></li>

	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id"}','addSuppFile')">{translate key="common.queue.short.uploadSuppFile"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id"}','submission')">{translate key="common.queue.short.viewDetails"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id" anchor="anchor"}','submissionReview','editorDecision')">{translate key="common.queue.short.SubmitRevisedVersion"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id"}','viewMetadata')">{translate key="common.queue.short.editMetadata"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id" anchor="anchor"}','submissionEditing','copyedit')">{translate key="common.queue.short.viewCopyeditedArticle"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" path="article_id" anchor="anchor"}','submissionEditing','layout')">{translate key="common.queue.short.viewLayoutEditedArticle"}</a></li>
	<li><a href="#" onclick="callSelectedUrl('{url op="function" articleId="article_id"}','emailEditorDecisionComment')">{translate key="common.queue.short.notifyEditor"}</a></li>
	<li><a href="/ojs/index.php/{$journalTitle}/about/submissions#authorGuidelines">{translate key="common.queue.short.authorGuidelines"}</a></li>

</ul>

<div class="separator"></div>

<br />

<ul class="menu">
	<li{if ($pageToDisplay == "active")} class="current"{/if}><a href="{url op="index" path="active"}">{translate key="common.queue.short.active"}</a></li>
	<li{if ($pageToDisplay == "completed")} class="current"{/if}><a href="{url op="index" path="completed"}">{translate key="common.queue.short.completed"}</a></li>

</ul>

<div id="content"></div>

<h4>{translate key="author.submit.startHereTitle"}</h4>
{url|assign:"submitUrl" op="submit"}
{translate submitUrl=$submitUrl key="author.submit.startHereLink"}<br />

{include file="common/footer.tpl"}
