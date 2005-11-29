{**
 * statistics.tpl
 *
 * Copyright (c) 2003-2005 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Subtemplate defining the statistics table.
 *
 * $Id$
 *}

<a name="statistics"></a>
<h3>{translate key="manager.statistics.statistics"}</h3>

<table width="100%" class="data">

	<tr valign="top">
		<td class="label">{translate key="common.year"}</td>
		<td colspan="2" class="value">
			<a href="{url statisticsYear=$statisticsYear-1}">{translate key="navigation.previousPage"}</a>&nbsp;{$statisticsYear}&nbsp;<a href="{url statisticsYear=$statisticsYear+1}">{translate key="navigation.nextPage"}</a>
		</td>
	</tr>

	{* Issue statistics *}
	<tr valign="top">
		<td width="20%" class="label">{translate key="manager.statistics.statistics.numIssues"}</td>
		<td width="80%" colspan="2" class="value">{$issueStatistics.numPublishedIssues}</td>
	</tr>

	{* Submission statistics *}
	<tr valign="top">
		<td width="20%" class="label">{translate key="manager.statistics.statistics.itemsPublished"}</td>
		<td width="80%" colspan="2" class="value">{$articleStatistics.numPublishedSubmissions}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">{translate key="manager.statistics.statistics.numSubmissions"}</td>
		<td width="80%" colspan="2" class="value">{$articleStatistics.numSubmissions}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">&nbsp;&nbsp;{translate key="manager.statistics.statistics.count.accept"}</td>
		<td width="80%" colspan="2" class="value">{translate key="manager.statistics.statistics.count.value" count=$articleStatistics.submissionsAccept percentage=$articleStatistics.submissionsAcceptPercent}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">&nbsp;&nbsp;{translate key="manager.statistics.statistics.count.decline"}</td>
		<td width="80%" colspan="2" class="value">{translate key="manager.statistics.statistics.count.value" count=$articleStatistics.submissionsDecline percentage=$articleStatistics.submissionsDeclinePercent}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">&nbsp;&nbsp;{translate key="manager.statistics.statistics.count.revise"}</td>
		<td width="80%" colspan="2" class="value">{translate key="manager.statistics.statistics.count.value" count=$articleStatistics.submissionsRevise percentage=$articleStatistics.submissionsRevisePercent}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">&nbsp;&nbsp;{translate key="manager.statistics.statistics.count.undecided"}</td>
		<td width="80%" colspan="2" class="value">{translate key="manager.statistics.statistics.count.value" count=$articleStatistics.submissionsUndecided percentage=$articleStatistics.submissionsUndecidedPercent}</td>
	</tr>

	<tr valign="top">
		<td width="20%" class="label">{translate key="user.role.reviewers"}</td>
		<td colspan="2" class="value">{$userStatistics.reviewer}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">&nbsp;&nbsp;{translate key="manager.statistics.statistics.reviewerCount"}</td>
		<td colspan="2" class="value">{$reviewerStatistics.reviewerCount}</td>
	</tr>

	<tr valign="top">
		<td width="20%" class="label">&nbsp;&nbsp;{translate key="manager.statistics.statistics.reviewsPerReviewer"}</td>
		{assign var=reviewerCount value=$userStatistics.reviewer}
		{assign var=reviewCount value=$reviewStatistics.reviewsCount}
		{if $reviewCount != 0}{assign var=reviewsPerReviewer value=$reviewerCount/$reviewCount}{else}{assign var=reviewsPerReviewer value=0}{/if}
		<td colspan="2" class="value">{math equation="round($reviewsPerReviewer)"}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">&nbsp;&nbsp;{translate key="manager.statistics.statistics.reviewerScore"}</td>
		<td colspan="2" class="value">{$reviewerStatistics.reviewerScore}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">&nbsp;&nbsp;{translate key="manager.statistics.statistics.daysPerReview"}</td>
		<td colspan="2" class="value">
			{assign var=daysPerReview value=$reviewerStatistics.daysPerReview}
			{math equation="round($daysPerReview)"}
		</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">{translate key="manager.statistics.statistics.daysToPublication"}</td>
		<td colspan="2" class="value">{$articleStatistics.daysToPublication}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">{translate key="manager.statistics.statistics.registeredUsers"}</td>
		<td colspan="2" class="value">{$userStatistics.totalUsersCount}</td>
	</tr>
	<tr valign="top">
		<td width="20%" class="label">{translate key="manager.statistics.statistics.countryDistribution"}</td>
		<td colspan="2" class="value">
			{foreach from=$countryDistribution item=country}
				{$country} &nbsp;
			{/foreach}
		</td>
	</td>
</table>
