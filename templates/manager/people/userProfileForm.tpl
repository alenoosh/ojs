{**
 * userProfileForm.tpl
 *
 * Copyright (c) 2003-2005 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * User profile form under journal management.
 *
 * $Id$
 *}

{assign var="pageTitle" value="manager.people"}
{include file="common/header.tpl"}

{if not $userId}
{assign var="passwordRequired" value="true"}
{/if}

{if $userCreated}
<p>{translate key="manager.people.userCreatedSuccessfully"}</p>
{/if}

<h3>{if $userId}{translate key="manager.people.editProfile"}{else}{translate key="manager.people.createUser"}{/if}</h3>

<form method="post" action="{$pageUrl}/manager/updateUser">
{if $userId}
<input type="hidden" name="userId" value="{$userId}" />
{/if}

{include file="common/formErrors.tpl"}

<table width="100%" class="data">
	{if not $userId}
	<tr valign="top">	
		<td class="label">{fieldLabel name="enrollAs" key="manager.people.enrollUserAs"}</td>
		<td class="value"><select name="enrollAs[]" id="enrollAs" multiple="multiple" size="10" class="selectMenu">
			{html_options_translate options=$roleOptions selected=$enrollAs}
		</select></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="username" required="true" key="user.username"}</td>
		<td class="value">
			<input type="text" name="username" id="username" value="{$username|escape}" size="20" maxlength="32" class="textField" />
			<br />
			<span class="instruct">{translate key="user.register.usernameRestriction"}</span>
		</td>
	</tr>
	{else}
	<tr valign="top">
		<td class="label">{fieldLabel name="username" key="user.username"}</td>
		<td class="value"><strong>{$username|escape}</strong></td>
	</tr>
	{/if}
	<tr valign="top">
		<td class="label">{fieldLabel name="password" required=$passwordRequired key="user.password"}</td>
		<td class="value">
			<input type="password" name="password" id="password" value="{$password|escape}" size="20" maxlength="32" class="textField" />
			<br />
			<span class="instruct">{translate key="user.register.passwordLengthRestriction" length=$minPasswordLength}</span>
		</td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="password2" required=$passwordRequired key="user.register.repeatPassword"}</td>
		<td class="value"><input type="password" name="password2"  id="password2" value="{$password2|escape}" size="20" maxlength="32" class="textField" /></td>
	</tr>
	{if $userId}
	<tr valign="top">
		<td>&nbsp;</td>
		<td class="value">{translate key="user.register.passwordLengthRestriction" length=$minPasswordLength}<br />{translate key="user.profile.leavePasswordBlank"}</td>
	</tr>
	{else}
	<tr valign="top">
		<td class="label">&nbsp;</td>
		<td class="value"><input type="checkbox" name="sendNotify" id="sendNotify" value="1"{if $sendNotify} checked="checked"{/if} /> <label for="sendNotify">{translate key="manager.people.createUserSendNotify"}</label></td>
	</tr>
	{/if}
	<tr valign="top">
		<td class="label">&nbsp;</td>
		<td class="value"><input type="checkbox" name="mustChangePassword" id="mustChangePassword" value="1"{if $mustChangePassword} checked="checked"{/if} /> <label for="mustChangePassword">{translate key="manager.people.userMustChangePassword"}</label></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="firstName" required="true" key="user.firstName"}</td>
		<td class="value"><input type="text" name="firstName" id="firstName" value="{$firstName|escape}" size="20" maxlength="40" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="middleName" key="user.middleName"}</td>
		<td class="value"><input type="text" name="middleName" id="middleName" value="{$middleName|escape}" size="20" maxlength="40" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="lastName" required="true" key="user.lastName"}</td>
		<td class="value"><input type="text" name="lastName" id="lastName" value="{$lastName|escape}" size="20" maxlength="60" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="affiliation" key="user.affiliation"}</td>
		<td class="value"><input type="text" name="affiliation" id="affiliation value="{$affiliation|escape}" size="30" maxlength="90" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="email" required="true" key="user.email"}</td>
		<td class="value"><input type="text" name="email" id="email" value="{$email|escape}" size="30" maxlength="90" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="phone" key="user.phone"}</td>
		<td class="value"><input type="text" name="phone" id="phone" value="{$phone|escape}" size="15" maxlength="24" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="fax" key="user.fax"}</td>
		<td class="value"><input type="text" name="fax" id="fax" value="{$fax|escape}" size="15" maxlength="24" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="interests" key="user.interests"}</td>
		<td class="value"><input type="text" name="interests" id="interests" value="{$interests|escape}" size="30" maxlength="255" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="mailingAddress" key="common.mailingAddress"}</td>
		<td class="value"><textarea name="mailingAddress" id="mailingAddress" rows="3" cols="40" class="textArea">{$mailingAddress|escape}</textarea></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="biography" key="user.biography"}</td>
		<td class="value"><textarea name="biography" id="biography" rows="5" cols="40" class="textArea">{$biography|escape}</textarea></td>
	</tr>
	{if $profileLocalesEnabled && count($availableLocales) > 1}
	<tr valign="top">
		<td class="label">{translate key="user.workingLanguages"}</td>
		<td>{foreach from=$availableLocales key=localeKey item=localeName}
			<input type="checkbox" name="userLocales[]" id="userLocales[{$localeKey}]" value="{$localeKey}"{if $userLocales && in_array($localeKey, $userLocales)} checked="checked"{/if}> <label for="userLocales[{$localeKey}]">{$localeName}</label><br />
		{/foreach}</td>
	</tr>
	{/if}
</table>

<p><input type="submit" value="{translate key="common.save"}" class="button defaultButton" /> {if not $userId}<input type="submit" name="createAnother" value="{translate key="manager.people.saveAndCreateAnotherUser"}" class="button" /> {/if}<input type="button" value="{translate key="common.cancel"}" class="button" onclick="document.location.href='{$pageUrl}/manager/people/all'" /></p>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>

</form>

{include file="common/footer.tpl"}
