<?php

/**
 * @file Install.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package install
 * @class Install
 *
 * Perform system installation.
 *
 * This script will:
 *  - Create the database (optionally), and install the database tables and initial data.
 *  - Update the config file with installation parameters.
 * It can also be used for a "manual install" to retrieve the SQL statements required for installation.
 *
 * $Id$
 */

// Default installation data
define('INSTALLER_DEFAULT_SITE_TITLE', 'common.openJournalSystems');
define('INSTALLER_DEFAULT_MIN_PASSWORD_LENGTH', 6);

import('install.Installer');

class Install extends Installer {

	/**
	 * Constructor.
	 * @see install.form.InstallForm for the expected parameters
	 * @param $params array installation parameters
	 */
	function Install($params) {
		parent::Installer('install.xml', $params);
	}

	/**
	 * Returns true iff this is an upgrade process.
	 */
	function isUpgrade() {
		return false;
	}

	/**
	 * Pre-installation.
	 * @return boolean
	 */
	function preInstall() {
 		$this->currentVersion = Version::fromString('');

 		$this->locale = $this->getParam('locale');
		$this->installedLocales = $this->getParam('additionalLocales');
		if (!isset($this->installedLocales) || !is_array($this->installedLocales)) {
			$this->installedLocales = array();
		}
		if (!in_array($this->locale, $this->installedLocales) && Locale::isLocaleValid($this->locale)) {
			array_push($this->installedLocales, $this->locale);
		}

		if ($this->getParam('manualInstall')) {
			// Do not perform database installation for manual install
			// Create connection object with the appropriate database driver for adodb-xmlschema
			$conn = &new DBConnection(
				$this->getParam('databaseDriver'),
				null,
				null,
				null,
				null
			);
			$this->dbconn = &$conn->getDBConn();

		} else {
			// Connect to database
			$conn = &new DBConnection(
				$this->getParam('databaseDriver'),
				$this->getParam('databaseHost'),
				$this->getParam('databaseUsername'),
				$this->getParam('databasePassword'),
				$this->getParam('createDatabase') ? null : $this->getParam('databaseName'),
				true,
				$this->getParam('connectionCharset') == '' ? false : $this->getParam('connectionCharset')
			);

			$this->dbconn = &$conn->getDBConn();

			if (!$conn->isConnected()) {
				$this->setError(INSTALLER_ERROR_DB, $this->dbconn->errorMsg());
				return false;
			}
		}

		DBConnection::getInstance($conn);

		return parent::preInstall();
	}


	//
	// Installer actions
	//

	/**
	 * Create required files directories
	 * FIXME No longer needed since FileManager will auto-create?
	 * @return boolean
	 */
	function createDirectories() {
		if ($this->getParam('skipFilesDir')) {
			return true;
		}

		// Check if files directory exists and is writeable
		if (!(file_exists($this->getParam('filesDir')) &&  is_writeable($this->getParam('filesDir')))) {
			// Files upload directory unusable
			$this->setError(INSTALLER_ERROR_GENERAL, 'installer.installFilesDirError');
			return false;
		} else {
			// Create required subdirectories
			$dirsToCreate = array('site', 'journals');
			foreach ($dirsToCreate as $dirName) {
				$dirToCreate = $this->getParam('filesDir') . '/' . $dirName;
				if (!file_exists($dirToCreate)) {
					import('file.FileManager');
					if (!FileManager::mkdir($dirToCreate)) {
						$this->setError(INSTALLER_ERROR_GENERAL, 'installer.installFilesDirError');
						return false;
					}
				}
			}
		}

		// Check if public files directory exists and is writeable
		$publicFilesDir = Config::getVar('files', 'public_files_dir');
		if (!(file_exists($publicFilesDir) &&  is_writeable($publicFilesDir))) {
			// Public files upload directory unusable
			$this->setError(INSTALLER_ERROR_GENERAL, 'installer.publicFilesDirError');
			return false;
		} else {
			// Create required subdirectories
			$dirsToCreate = array('site', 'journals');
			foreach ($dirsToCreate as $dirName) {
				$dirToCreate = $publicFilesDir . '/' . $dirName;
				if (!file_exists($dirToCreate)) {
					import('file.FileManager');
					if (!FileManager::mkdir($dirToCreate)) {
						$this->setError(INSTALLER_ERROR_GENERAL, 'installer.publicFilesDirError');
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Create a new database if required.
	 * @return boolean
	 */
	function createDatabase() {
		if (!$this->getParam('createDatabase')) {
			return true;
		}

		// Get database creation sql
		$dbdict = &NewDataDictionary($this->dbconn);

		if ($this->getParam('databaseCharset')) {
				$dbdict->SetCharSet($this->getParam('databaseCharset'));
		}

		list($sql) = $dbdict->CreateDatabase($this->getParam('databaseName'));
		unset($dbdict);

		if (!$this->executeSQL($sql)) {
			return false;
		}

		if (!$this->getParam('manualInstall')) {
			// Re-connect to the created database
			$this->dbconn->disconnect();

			$conn = &new DBConnection(
				$this->getParam('databaseDriver'),
				$this->getParam('databaseHost'),
				$this->getParam('databaseUsername'),
				$this->getParam('databasePassword'),
				$this->getParam('databaseName'),
				true,
				$this->getParam('connectionCharset') == '' ? false : $this->getParam('connectionCharset')
			);

			DBConnection::getInstance($conn);

			$this->dbconn = &$conn->getDBConn();

			if (!$conn->isConnected()) {
				$this->setError(INSTALLER_ERROR_DB, $this->dbconn->errorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 * Create initial required data.
	 * @return boolean
	 */
	function createData() {
		if ($this->getParam('manualInstall')) {
			// Add insert statements for default data
			// FIXME use ADODB data dictionary?
			$this->executeSQL(sprintf('INSERT INTO site (primary_locale, installed_locales) VALUES (\'%s\', \'%s\')', $this->getParam('locale'), join(':', $this->installedLocales)));
			$this->executeSQL(sprintf('INSERT INTO site_settings (setting_name, setting_type, setting_value, locale) VALUES (\'%s\', \'%s\', \'%s\', \'%s\')', 'title', 'string', addslashes(Locale::translate(INSTALLER_DEFAULT_SITE_TITLE)), $this->getParam('locale')));
			$this->executeSQL(sprintf('INSERT INTO site_settings (setting_name, setting_type, setting_value, locale) VALUES (\'%s\', \'%s\', \'%s\', \'%s\')', 'contactName', 'string', addslashes(Locale::translate(INSTALLER_DEFAULT_SITE_TITLE)), $this->getParam('locale')));
			$this->executeSQL(sprintf('INSERT INTO site_settings (setting_name, setting_type, setting_value, locale) VALUES (\'%s\', \'%s\', \'%s\', \'%s\')', 'contactEmail', 'string', addslashes(strtolower($this->getParam('adminUsername'))), $this->getParam('locale'))); // Opatan Inc. : 'adminEmail' changed to 'adminUsername'  
		        $lowerAdminUsername = strtolower($this->getParam('adminUsername')); // Opatan Inc.
			$this->executeSQL(sprintf('INSERT INTO users (user_id, username, first_name, last_name, password, email, date_registered, date_last_login) VALUES (%d, \'%s\', \'%s\', \'%s\',  \'%s\', \'%s\', \'%s\', \'%s\')', 1, strtolower($this->getParam('adminUsername')), substr($lowerAdminUsername, 0, strpos($lowerAdminUsername, '@')), substr($lowerAdminUsername, 0, strpos($lowerAdminUsername, '@')), Validation::encryptCredentials(strtolower($this->getParam('adminUsername')), $this->getParam('adminPassword'), $this->getParam('encryption')), strtolower($this->getParam('adminUsername')), Core::getCurrentDate(), Core::getCurrentDate())); // Opatan Inc. : 'adminEmail' changed to 'adminUsername' && first_name and last_name is not considered
			$this->executeSQL(sprintf('INSERT INTO roles (journal_id, user_id, role_id) VALUES (%d, %d, %d)', 0, 1, ROLE_ID_SITE_ADMIN));

		} else {
			// Add initial site data
			$locale = $this->getParam('locale');
			$siteDao = &DAORegistry::getDAO('SiteDAO', $this->dbconn);
			$site = &new Site();
			$site->setTitle(Locale::translate(INSTALLER_DEFAULT_SITE_TITLE), $locale);
			$site->setJournalRedirect(0);
			$site->setMinPasswordLength(INSTALLER_DEFAULT_MIN_PASSWORD_LENGTH);
			$site->setPrimaryLocale($locale);
			$site->setInstalledLocales($this->installedLocales);
			$site->setSupportedLocales($this->installedLocales);
			$site->setContactName($site->getTitle($locale), $locale);
            		// Opatan Inc. : 'adminEmail' changed to 'adminUsername'           
			$site->setContactEmail(strtolower($this->getParam('adminUsername')), $locale);
			if (!$siteDao->insertSite($site)) {
				$this->setError(INSTALLER_ERROR_DB, $this->dbconn->errorMsg());
				return false;
			}

			// Add initial site administrator user
			$userDao = &DAORegistry::getDAO('UserDAO', $this->dbconn);
			$user = &new User();
            		// Opatan Inc. : strtolower is added to lowercase the username
			$user->setUsername(strtolower($this->getParam('adminUsername')));
			$user->setPassword(Validation::encryptCredentials(strtolower($this->getParam('adminUsername')), $this->getParam('adminPassword'), $this->getParam('encryption')));
            		// Opatan Inc. : we set the username part of email address as first name and as last name
			$user->setFirstName(substr($user->getUsername(), 0, strpos($user->getUsername(), '@'))); 
			$user->setLastName(substr($user->getUsername(), 0, strpos($user->getUsername(), '@')));
            		// Opatan Inc. : 'adminEmail' changed to 'adminUsername'           
			$user->setEmail(strtolower($this->getParam('adminUsername')));
			if (!$userDao->insertUser($user)) {
				$this->setError(INSTALLER_ERROR_DB, $this->dbconn->errorMsg());
				return false;
			}

			$roleDao = &DAORegistry::getDao('RoleDAO', $this->dbconn);
			$role = &new Role();
			$role->setJournalId(0);
			$role->setUserId($user->getUserId());
			$role->setRoleId(ROLE_ID_SITE_ADMIN);
			if (!$roleDao->insertRole($role)) {
				$this->setError(INSTALLER_ERROR_DB, $this->dbconn->errorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 * Write the configuration file.
	 * @return boolean
	 */
	function createConfig() {
		return $this->updateConfig(
			array(
				'general' => array(
					'installed' => 'On',
					'base_url' => Request::getBaseUrl()
				),
				'database' => array(
					'driver' => $this->getParam('databaseDriver'),
					'host' => $this->getParam('databaseHost'),
					'username' => $this->getParam('databaseUsername'),
					'password' => $this->getParam('databasePassword'),
					'name' => $this->getParam('databaseName')
				),
				'i18n' => array(
					'locale' => $this->getParam('locale'),
					'client_charset' => $this->getParam('clientCharset'),
					'connection_charset' => $this->getParam('connectionCharset') == '' ? 'Off' : $this->getParam('connectionCharset'),
					'database_charset' => $this->getParam('databaseCharset') == '' ? 'Off' : $this->getParam('databaseCharset')
				),
				'files' => array(
					'files_dir' => $this->getParam('filesDir')
				),
				'security' => array(
					'encryption' => $this->getParam('encryption')
				),
				'oai' => array(
					'repository_id' => $this->getParam('oaiRepositoryId')
				)
			)
		);
	}

}

?>
