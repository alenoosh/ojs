<?php

/**
 * @file PaymentSettingsForm.inc.php
 *
 * Copyright (c) 2006-2007 Gunther Eysenbach, Juan Pablo Alperin, MJ Suhonos
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package manager.form
 * @class PayMethodSettingsForm
 *
 * Form for conference managers to modify Payment Plugin settings
 * 
 */

import('form.Form');

class PayMethodSettingsForm extends Form {
	/** @var $errors string */
	var $errors;

	/** @var $plugins array */
	var $plugins;

	/**
	 * Constructor
	 */
	function PayMethodSettingsForm() {
		parent::Form('manager/payments/payMethodSettingsForm.tpl');

		// Load the plugins.
		$this->plugins =& PluginRegistry::loadCategory('paymethod');

		// Add form checks
		$this->addCheck(new FormValidatorInSet($this, 'paymentMethodPluginName', 'optional', 'manager.payment.paymentPluginInvalid', array_keys($this->plugins)));

	}

	/**
	 * Display the form.
	 */
	function display() {
		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign_by_ref('paymentMethodPlugins', $this->plugins);
		parent::display();
	}
	
	/**
	 * Initialize form data from current group group.
	 */
	function initData() {
		$journal =& Request::getJournal();

		// Allow the current selection to supercede the stored value
		$paymentMethodPluginName = Request::getUserVar('paymentMethodPluginName');
		if (empty($paymentMethodPluginName) || !in_array($paymentMethodPluginName, array_keys($this->plugins))) {
			$paymentMethodPluginName = $journal->getSetting('paymentMethodPluginName');
		}

		if (!isset($this->plugins[$paymentMethodPluginName])) {
			// Choose an arbitrary default if no valid plugin chosen
			$paymentMethodPluginName = array_shift(array_keys($this->plugins));
		}

		// A valid payment method plugin should now be chosen.
		$paymentMethodPlugin =& $this->plugins[$paymentMethodPluginName];

		$this->_data = array(
			'paymentMethodPluginName' => $paymentMethodPluginName
		);

		if (isset($this->plugins[$paymentMethodPluginName])) {
			$plugin =& $this->plugins[$paymentMethodPluginName];
			foreach ($plugin->getSettingsFormFieldNames() as $field) {
				$this->_data[$field] = $plugin->getSetting($journal->getJournalId(), $field);
			}
		}
	}
	
	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array(
			'paymentMethodPluginName'
		));

		$paymentMethodPluginName = $this->getData('paymentMethodPluginName');
		if (isset($this->plugins[$paymentMethodPluginName])) {
			$plugin =& $this->plugins[$paymentMethodPluginName];
			$this->readUserVars($plugin->getSettingsFormFieldNames());
		}

	}
	
	/**
	 * Save settings 
	 */	 
	function execute() {
		$journal =& Request::getJournal();
		// Save the general settings for the form
		foreach (array('paymentMethodPluginName') as $journalSettingName) {
			$journal->updateSetting($journalSettingName, $this->getData($journalSettingName));
		}

		// Save the specific settings for the plugin
		$paymentMethodPluginName = $this->getData('paymentMethodPluginName');
		if (isset($this->plugins[$paymentMethodPluginName])) {
			$plugin =& $this->plugins[$paymentMethodPluginName];
			foreach ($plugin->getSettingsFormFieldNames() as $field) {
				$plugin->updateSetting($journal->getJournalId(), $field, $this->getData($field));
			}
		}
	}
}

?>
