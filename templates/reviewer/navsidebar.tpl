{**
 * navsidebar.tpl
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Reviewer navigation sidebar.
 *
 * $Id$
 *}

{* Note that if the user has come in via an access key, the submission counts won't
   be available as the user isn't actually logged in. Therefore we must check to
   see if the user object actually exists before displaying submission counts. *}

{if $currentUser}
	<div class="block">
		<span class="blockTitle">{translate key="user.role.reviewer"}</span>
		<span class="blockSubtitle">{translate key="article.submissions"}</span>
		<ul>
			<li><a href="{$pageUrl}/reviewer/index/active">{translate key="common.queue.short.active"}</a>&nbsp;({if $submissionsCount[0]}<strong>{$submissionsCount[0]}</strong>{else}0{/if})</li>
			<li><a href="{$pageUrl}/reviewer/index/completed">{translate key="common.queue.short.completed"}</a>&nbsp;({if $submissionsCount[1]}<strong>{$submissionsCount[1]}</strong>{else}0{/if})</li>
		</ul>
	</div>
{/if}
