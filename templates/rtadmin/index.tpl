{**
 * index.tpl
 *
 * Copyright (c) 2003-2005 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Research Tool Administrator index.
 *
 * $Id$
 *}

{assign var="pageTitle" value="rt.researchTools"}
{include file="common/header.tpl"}

<h3>{translate key="rt.admin.configuration"}</h3>
<ul class="plain">
	<li>&#187; <a href="{$pageUrl}/rtadmin/settings">{translate key="rt.admin.settings"}</a></li>
	<li>&#187; <a href="{$pageUrl}/rtadmin/versions">{translate key="rt.admin.versions"}</a></li>
</ul>

<h3>{translate key="rt.admin.management"}</h3>
<ul class="plain">
	<li>&#187; <a href="{$pageUrl}/rtadmin/FIXME">{translate key="rt.admin.validateUrls"}</a></li>
</ul>

{include file="common/footer.tpl"}