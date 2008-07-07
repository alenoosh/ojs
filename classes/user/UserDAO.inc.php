<?php

/**
 * @file UserDAO.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package user
 * @class UserDAO
 *
 * Class for User DAO.
 * Operations for retrieving and modifying User objects.
 *
 * $Id$
 */

import('user.User');

/* These constants are used user-selectable search fields. */
define('USER_FIELD_USERID', 'user_id');
define('USER_FIELD_FIRSTNAME', 'first_name');
define('USER_FIELD_LASTNAME', 'last_name');
define('USER_FIELD_USERNAME', 'username');
define('USER_FIELD_EMAIL', 'email');
define('USER_FIELD_URL', 'url');
define('USER_FIELD_INTERESTS', 'interests');
define('USER_FIELD_INITIAL', 'initial');
define('USER_FIELD_NONE', null);

class UserDAO extends DAO {
	/**
	 * Retrieve a user by ID.
	 * @param $userId int
	 * @param $allowDisabled boolean
	 * @return User
	 */
	function &getUser($userId, $allowDisabled = true) {
		$result = &$this->retrieve(
			'SELECT * FROM users WHERE user_id = ?' . ($allowDisabled?'':' AND disabled = 0'), $userId
		);

		$user = null;
		if ($result->RecordCount() != 0) {
			$user = &$this->_returnUserFromRowWithData($result->GetRowAssoc(false));
		}
		$result->Close();
		unset($result);
		return $user;
	}

	/**
	 * Retrieve a user by username.
	 * @param $username string
	 * @param $allowDisabled boolean
	 * @return User
	 */
	function &getUserByUsername($username, $allowDisabled = true) {
		$result = &$this->retrieve(
			'SELECT * FROM users WHERE username = ?' . ($allowDisabled?'':' AND disabled = 0'), $username
		);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = &$this->_returnUserFromRowWithData($result->GetRowAssoc(false));
		}
		$result->Close();
		unset($result);
		return $returner;
	}

	/**
	 * Retrieve a user by email address.
	 * @param $email string
	 * @param $allowDisabled boolean
	 * @return User
	 */
	function &getUserByEmail($email, $allowDisabled = true) {
		$result = &$this->retrieve(
			'SELECT * FROM users WHERE email = ?' . ($allowDisabled?'':' AND disabled = 0'), $email
		);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = &$this->_returnUserFromRowWithData($result->GetRowAssoc(false));
		}
		$result->Close();
		unset($result);
		return $returner;
	}

	/**
	 * Retrieve a user by username and (encrypted) password.
	 * @param $username string
	 * @param $password string encrypted password
	 * @param $allowDisabled boolean
	 * @return User
	 */
	function &getUserByCredentials($username, $password, $allowDisabled = true) {
		$result = &$this->retrieve(
			'SELECT * FROM users WHERE username = ? AND password = ?' . ($allowDisabled?'':' AND disabled = 0'), array($username, $password)
		);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = &$this->_returnUserFromRowWithData($result->GetRowAssoc(false));
		}
		$result->Close();
		unset($result);
		return $returner;
	}

	function &_returnUserFromRowWithData(&$row) {
		$user =& $this->_returnUserFromRow($row, false);
		$this->getDataObjectSettings('user_settings', 'user_id', $row['user_id'], $user);
		
		HookRegistry::call('UserDAO::_returnUserFromRowWithData', array(&$user, &$row));

		return $user;
	}

	/**
	 * Internal function to return a User object from a row.
	 * @param $row array
	 * @param $callHook boolean
	 * @return User
	 */
	function &_returnUserFromRow(&$row, $callHook = true) {
		$user = &new User();
		$userSettingsDao = &DAORegistry::getDAO('UserSettingsDAO');	
		$user->setUserId($row['user_id']);
		$user->setUsername($row['username']);
		$user->setPassword($row['password']);
		// Opatan Inc. : $user->setSalutation is removed
		// Opatan Inc. : $user->setFirstName is removed
		// Opatan Inc. : $user->setMiddleName is removed
		$user->setInitials($row['initials']);
		// Opatan Inc. : $user->setLastName is removed
		$user->setGender($row['gender']);
		$user->setDiscipline($row['discipline']);
		// Opatan Inc. : $user->setAffiliation is removed
		$user->setEmail($row['email']);
		$user->setUrl($row['url']);
		$user->setPhone($row['phone']);
		$user->setFax($row['fax']);
		$user->setMailingAddress($row['mailing_address']);
		$user->setCountry($row['country']);
		$user->setLocales(isset($row['locales']) && !empty($row['locales']) ? explode(':', $row['locales']) : array());
		$user->setDateLastEmail($this->datetimeFromDB($row['date_last_email']));
		$user->setDateRegistered($this->datetimeFromDB($row['date_registered']));
		$user->setDateValidated($this->datetimeFromDB($row['date_validated']));
		$user->setDateLastLogin($this->datetimeFromDB($row['date_last_login']));
		$user->setDateEndMembership($this->datetimeFromDB($row['date_end_membership']));
		$user->setMustChangePassword($row['must_change_password']);
		$user->setDisabled($row['disabled']);
		$user->setDisabledReason($row['disabled_reason']);
		$user->setAuthId($row['auth_id']);

		if ($callHook) HookRegistry::call('UserDAO::_returnUserFromRow', array(&$user, &$row));

		return $user;
	}

	/**
	 * Insert a new user.
	 * @param $user User
	 */
	function insertUser(&$user) {
		if ($user->getDateRegistered() == null) {
			$user->setDateRegistered(Core::getCurrentDate());
		}
		if ($user->getDateLastLogin() == null) {
			$user->setDateLastLogin(Core::getCurrentDate());
		}
		$this->update(
			// Opatan Inc. : first_name, last_name, middle_name, affiliation and salutation are removed
			sprintf('INSERT INTO users
				(username, password, initials, gender, discipline, email, url, phone, fax, mailing_address, country, locales, date_last_email, date_registered, date_validated, date_last_login, date_end_membership, must_change_password, disabled, disabled_reason, auth_id)
				VALUES
				(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, %s, %s, %s, %s, %s, ?, ?, ?, ?)',
				$this->datetimeToDB($user->getDateLastEmail()), $this->datetimeToDB($user->getDateRegistered()), $this->datetimeToDB($user->getDateValidated()), $this->datetimeToDB($user->getDateLastLogin()), $this->datetimeToDB($user->getDateEndMembership())),
			array(
				$user->getUsername(),
				$user->getPassword(),
				$user->getInitials(),
				$user->getGender(),
				$user->getDiscipline(),
				$user->getEmail(),
				$user->getUrl(),
				$user->getPhone(),
				$user->getFax(),
				$user->getMailingAddress(),
				$user->getCountry(),
				join(':', $user->getLocales()),
				$user->getMustChangePassword(),
				$user->getDisabled() ? 1 : 0,
				$user->getDisabledReason(),
				$user->getAuthId()
			)
		);

		$user->setUserId($this->getInsertUserId());
		$this->updateLocaleFields($user);
		return $user->getUserId();
	}

	function getLocaleFieldNames() {
		return array('firstName', 'middleName', 'lastName', 'affiliation', 'salutation', 'biography', 'signature', 'interests');
	}

	function updateLocaleFields(&$user) {
		$this->updateDataObjectSettings('user_settings', $user, array(
			'user_id' => $user->getUserId()
		));
	}

	/**
	 * Update an existing user.
	 * @param $user User
	 */
	function updateUser(&$user) {
		if ($user->getDateLastLogin() == null) {
			$user->setDateLastLogin(Core::getCurrentDate());
		}

		$this->updateLocaleFields($user);
		// Opatan Inc. : first_name, last_name, middle_name, affiliation and salutation are removed
		return $this->update(
			sprintf('UPDATE users
				SET
					username = ?,
					password = ?,
					initials = ?,
					gender = ?,
					discipline = ?,
					email = ?,
					url = ?,
					phone = ?,
					fax = ?,
					mailing_address = ?,
					country = ?,
					locales = ?,
					date_last_email = %s,
					date_validated = %s,
					date_last_login = %s,
					date_end_membership = %s,		
					must_change_password = ?,
					disabled = ?,
					disabled_reason = ?,
					auth_id = ?
				WHERE user_id = ?',
				$this->datetimeToDB($user->getDateLastEmail()), $this->datetimeToDB($user->getDateValidated()), $this->datetimeToDB($user->getDateLastLogin()), $this->dateToDB($user->getDateEndMembership())),
			array(
				$user->getUsername(),
				$user->getPassword(),
				$user->getInitials(),
				$user->getGender(),
				$user->getDiscipline(),
				$user->getEmail(),
				$user->getUrl(),
				$user->getPhone(),
				$user->getFax(),
				$user->getMailingAddress(),
				$user->getCountry(),
				join(':', $user->getLocales()),
				$user->getMustChangePassword(),
				$user->getDisabled()?1:0,
				$user->getDisabledReason(),
				$user->getAuthId(),
				$user->getUserId()
			)
		);
	}

	/**
	 * Renew a membership to dateEnd + 1 year
	 * if the was expired, renew to current date + 1 year  
	 * @param $user User
	 */	
	function renewMembership(&$user){
		$dateEnd = ($user->getDateEndMembership())?strtotime($user->getDateEndMembership()):0;
		
		// if the membership is expired, extend it to today + 1 year
		$time = time();
		if ($dateEnd < $time ) $dateEnd = $time;

		$user->setDateEndMembership(mktime(23, 59, 59, date("m", $dateEnd), date("d", $dateEnd), date("Y", $dateEnd)+1));
		$this->updateUser($user);
	}

	/**
	 * Delete a user.
	 * @param $user User
	 */
	function deleteUser(&$user) {
		return $this->deleteUserById($user->getUserId());
	}

	/**
	 * Delete a user by ID.
	 * @param $userId int
	 */
	function deleteUserById($userId) {
		$this->update('DELETE FROM user_settings WHERE user_id = ?', $userId);
		return $this->update('DELETE FROM users WHERE user_id = ?', $userId);
	}

	/**
	 * Retrieve a user's name.
	 * @param int $userId
	 * @param $allowDisabled boolean
	 * @return string
	 */
	function getUserFullName($userId, $allowDisabled = true) {
		// Opatan Inc. : first_name , last_name and middle_name is removed from column lists
		$result = &$this->retrieve(
			'SELECT * FROM users WHERE user_id = ?' . ($allowDisabled?'':' AND disabled = 0'), 
			$userId
		);

		// Opatan Inc. : firstName, lastName and middleName are gotten from user_settings
		$user = null;
		if ($result->RecordCount() != 0) {
			$user = &$this->_returnUserFromRowWithData($result->GetRowAssoc(false));
		}        

        	if ($result->recordCount() == 0) {
	            $returner = false;
	        } else {            
	            $locale = Locale::getLocale();
	            $returner = $user->_data['firstName'][$locale] . ' ' . 
        	    (empty($user->_data['middleName'][$locale]) ? '' : $user->_data['middleName'][$locale] . ' ') . $user->_data['lastName'][$locale];
	        }        
 
 		$result->Close();
 		unset($result);

		return $returner;
	}

	/**
	 * Retrieve a user's email address.
	 * @param int $userId
	 * @param $allowDisabled boolean
	 * @return string
	 */
	function getUserEmail($userId, $allowDisabled = true) {
		$result = &$this->retrieve(
			'SELECT email FROM users WHERE user_id = ?' . ($allowDisabled?'':' AND disabled = 0'),
			$userId
		);

		if($result->RecordCount() == 0) {
			$returner = false;
		} else {
			$returner = $result->fields[0];
		}

		$result->Close();
		unset($result);

		return $returner;
	}

	/**
	 * Retrieve an array of users matching a particular field value.
	 * @param $field string the field to match on
	 * @param $match string "is" for exact match, otherwise assume "like" match
	 * @param $value mixed the value to match
	 * @param $allowDisabled boolean
	 * @param $dbResultRange object The desired range of results to return
	 * @return array matching Users
	 */

	function &getUsersByField($field = USER_FIELD_NONE, $match = null, $value = null, $allowDisabled = true, $dbResultRange = null) {
		$var = false;
		$locale = Locale::getLocale();

		// Opatan Inc. : the users table is joined with user_settings three times to provide setting_value
		// of first_name , last_name and interests
		$sql = 'SELECT *, uf.setting_value, ul.setting_value, ui.setting_value FROM users u LEFT JOIN user_settings ui ON (u.user_id = ui.user_id AND ui.setting_name = \'interests\' AND ui.locale = \''.$locale.'\') LEFT JOIN user_settings uf ON (u.user_id = uf.user_id AND uf.setting_name = \'firstName\' AND uf.locale = \''.$locale.'\') LEFT JOIN user_settings ul ON (u.user_id = ul.user_id AND ul.setting_name = \'lastName\' AND ul.locale = \''.$locale.'\')';

		if ($value != null) {
			switch ($field) {
				case USER_FIELD_USERID:
					$sql .= ' WHERE u.user_id = ?';
					$var = $value;
					break;
				case USER_FIELD_USERNAME:
					$sql .= ' WHERE LOWER(u.username) ' . ($match == 'is' ? '=' : 'LIKE') . ' LOWER(?)';
					$var = $match == 'is' ? $value : "%$value%";
					break;
				case USER_FIELD_INITIAL:
					$sql .= ' WHERE LOWER(ul.setting_value) LIKE LOWER(?)';
					$var = "$value%";
					break;
				case USER_FIELD_INTERESTS:
					$sql .= ' WHERE LOWER(ui.setting_value) ' . ($match == 'is' ? '=' : 'LIKE') . ' LOWER(?)';
					$var = $match == 'is' ? $value : "%$value%";
					break;
				case USER_FIELD_EMAIL:
					$sql .= ' WHERE LOWER(u.email) ' . ($match == 'is' ? '=' : 'LIKE') . ' LOWER(?)';
					$var = $match == 'is' ? $value : "%$value%";
					break;
				case USER_FIELD_URL:
					$sql .= ' WHERE LOWER(u.url) ' . ($match == 'is' ? '=' : 'LIKE') . ' LOWER(?)';
					$var = $match == 'is' ? $value : "%$value%";
					break;
				case USER_FIELD_FIRSTNAME:
					$sql .= ' WHERE LOWER(uf.setting_value) ' . ($match == 'is' ? '=' : 'LIKE') . ' LOWER(?)';
					$var = $match == 'is' ? $value : "%$value%";
					break;
				case USER_FIELD_LASTNAME:
					$sql .= ' WHERE LOWER(ul.setting_value) ' . ($match == 'is' ? '=' : 'LIKE') . ' LOWER(?)';
					$var = $match == 'is' ? $value : "%$value%";
					break;
			}
		}

		// Opatan Inc. : the query results are ordered by setting_value of firstName and setting_value of lastName
		$orderSql = ' ORDER BY ul.setting_value, uf.setting_value'; // FIXME Add "sort field" parameter?

		if ($field != USER_FIELD_NONE) $result = &$this->retrieveRange($sql . ($allowDisabled?'':' AND u.disabled = 0') . $orderSql, $var, $dbResultRange);
		else $result = &$this->retrieveRange($sql . ($allowDisabled?'':' WHERE u.disabled = 0') . $orderSql, false, $dbResultRange);

                $returner = &new DAOResultFactory($result, $this, '_returnUserFromRowWithData');
	
		return $returner;
	}

	/**
	 * Check if a user exists with the specified user ID.
	 * @param $userId int
	 * @param $allowDisabled boolean
	 * @return boolean
	 */
	function userExistsById($userId, $allowDisabled = true) {
		$result = &$this->retrieve(
			'SELECT COUNT(*) FROM users WHERE user_id = ?' . ($allowDisabled?'':' AND disabled = 0'), $userId
		);
		$returner = isset($result->fields[0]) && $result->fields[0] != 0 ? true : false;

		$result->Close();
		unset($result);

		return $returner;
	}

	/**
	 * Check if a user exists with the specified username.
	 * @param $username string
	 * @param $userId int optional, ignore matches with this user ID
	 * @param $allowDisabled boolean
	 * @return boolean
	 */
	function userExistsByUsername($username, $userId = null, $allowDisabled = true) {
		$result = &$this->retrieve(
			'SELECT COUNT(*) FROM users WHERE username = ?' . (isset($userId) ? ' AND user_id != ?' : '') . ($allowDisabled?'':' AND disabled = 0'),
			isset($userId) ? array($username, $userId) : $username
		);
		$returner = isset($result->fields[0]) && $result->fields[0] == 1 ? true : false;

		$result->Close();
		unset($result);

		return $returner;
	}

	/**
	 * Check if a user exists with the specified email address.
	 * @param $email string
	 * @param $userId int optional, ignore matches with this user ID
	 * @param $allowDisabled boolean
	 * @return boolean
	 */
	function userExistsByEmail($email, $userId = null, $allowDisabled = true) {
		$result = &$this->retrieve(
			'SELECT COUNT(*) FROM users WHERE email = ?' . (isset($userId) ? ' AND user_id != ?' : '') . ($allowDisabled?'':' AND disabled = 0'),
			isset($userId) ? array($email, $userId) : $email
		);
		$returner = isset($result->fields[0]) && $result->fields[0] == 1 ? true : false;

		$result->Close();
		unset($result);

		return $returner;
	}

	/**
	 * Get the ID of the last inserted user.
	 * @return int
	 */
	function getInsertUserId() {
		return $this->getInsertId('users', 'user_id');
	}
}

?>
