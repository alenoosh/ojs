{**
 * reviewProcess.tpl
 *
 * Copyright (c) 2008-2009 Opatan Inc.
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Subtemplate defining the review process table.
 *
 * $Id$
 *}

<a name="reviewProcess"></a>
<form name="setupForm" method="post" action="{url op="submissionReview" path=$submission->getArticleId()}">

<h3>{translate key="manager.setup.reviewProcess"}</h3>

<p>{translate key="manager.setup.reviewProcessDescription"}</p>

<table width="100%" class="data">
	<tr valign="top">
		<td width="5%" class="label" align="right">
			<input type="radio" name="mailSubmissionsToReviewers" id="mailSubmissionsToReviewers-0" value="0"{if not $mailSubmissionsToReviewers} checked="checked"{/if} />
		</td>
		<td width="95%" class="value">
			<label for="mailSubmissionsToReviewers-0"><strong>{translate key="manager.setup.reviewProcessStandard"}</strong></label>
			<br />
			<span class="instruct">{translate key="manager.setup.reviewProcessStandardDescription"}</span>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="separator">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td width="5%" class="label" align="right">
			<input type="radio" name="mailSubmissionsToReviewers" id="mailSubmissionsToReviewers-1" value="1"{if $mailSubmissionsToReviewers} checked="checked"{/if} />
		</td>
		<td width="95%" class="value">
			<label for="mailSubmissionsToReviewers-1"><strong>{translate key="manager.setup.reviewProcessEmail"}</strong></label>
			<br />
			<span class="instruct">{translate key="manager.setup.reviewProcessEmailDescription"}</span>
		</td>
	</tr>
</table>
<p><input type="submit" value="{translate key="common.record"}" class="button" /></p>

</form>


