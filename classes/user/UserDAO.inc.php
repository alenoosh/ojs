<?php

/**
 * UserDAO.inc.php
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package user
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
define('USER_FIELD_INTERESTS', 'interests');
define('USER_FIELD_INITIAL', 'initial');
define('USER_FIELD_NONE', null);

class UserDAO extends DAO {

	/**
	 * Constructor.
	 */
	function UserDAO() {
		parent::DAO();
	}
	
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
		
		if ($result->RecordCount() == 0) {
			return null;
			
		} else {
			return $this->_returnUserFromRow($result->GetRowAssoc(false));
		}
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
		
		if ($result->RecordCount() == 0) {
			return null;
			
		} else {
			return $this->_returnUserFromRow($result->GetRowAssoc(false));
		}
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
		
		if ($result->RecordCount() == 0) {
			return null;
			
		} else {
			return $this->_returnUserFromRow($result->GetRowAssoc(false));
		}
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
		
		if ($result->RecordCount() == 0) {
			return null;
			
		} else {
			return $this->_returnUserFromRow($result->GetRowAssoc(false));
		}
	}
	
	/**
	 * Internal function to return a User object from a row.
	 * @param $row array
	 * @return User
	 */
	function &_returnUserFromRow(&$row) {
		$user = &new User();
		$user->setUserId($row['user_id']);
		$user->setUsername($row['username']);
		$user->setPassword($row['password']);
		$user->setFirstName($row['first_name']);
		$user->setMiddleName($row['middle_name']);
		$user->setInitials($row['initials']);
		$user->setLastName($row['last_name']);
		$user->setAffiliation($row['affiliation']);
		$user->setEmail($row['email']);
		$user->setPhone($row['phone']);
		$user->setFax($row['fax']);
		$user->setMailingAddress($row['mailing_address']);
		$user->setBiography($row['biography']);
		$user->setInterests($row['interests']);
		$user->setLocales(isset($row['locales']) && !empty($row['locales']) ? explode(':', $row['locales']) : array());
		$user->setDateRegistered($row['date_registered']);
		$user->setDateLastLogin($row['date_last_login']);
		$user->setMustChangePassword($row['must_change_password']);
		$user->setDisabled($row['disabled']);
		
		return $user;
	}
	
	/**
	 * Insert a new user.
	 * @param $user User
	 */
	function insertUser(&$user) {
		$this->update(
			'INSERT INTO users
				(username, password, first_name, middle_name, initials, last_name, affiliation, email, phone, fax, mailing_address, biography, interests, locales, date_registered, date_last_login, must_change_password, disabled)
				VALUES
				(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
			array(
				$user->getUsername(),
				$user->getPassword(),
				$user->getFirstName(),
				$user->getMiddleName(),
				$user->getInitials(),
				$user->getLastName(),
				$user->getAffiliation(),
				$user->getEmail(),
				$user->getPhone(),
				$user->getFax(),
				$user->getMailingAddress(),
				$user->getBiography(),
				$user->getInterests(),
				join(':', $user->getLocales()),
				$user->getDateRegistered() == null ? Core::getCurrentDate() : $user->getDateRegistered(),
				$user->getDateLastLogin() == null ? Core::getCurrentDate() : $user->getDateLastLogin(),
				$user->getMustChangePassword(),
				$user->getDisabled()?1:0
			)
		);
		
		$user->setUserId($this->getInsertUserId());
		return $user->getUserId();
	}
	
	/**
	 * Update an existing user.
	 * @param $user User
	 */
	function updateUser(&$user) {
		return $this->update(
			'UPDATE users
				SET
					username = ?,
					password = ?,
					first_name = ?,
					middle_name = ?,
					initials = ?,
					last_name = ?,
					affiliation = ?,
					email = ?,
					phone = ?,
					fax = ?,
					mailing_address = ?,
					biography = ?,
					interests = ?,
					locales = ?,
					date_last_login = ?,
					must_change_password = ?,
					disabled = ?
				WHERE user_id = ?',
			array(
				$user->getUsername(),
				$user->getPassword(),
				$user->getFirstName(),
				$user->getMiddleName(),
				$user->getInitials(),
				$user->getLastName(),
				$user->getAffiliation(),
				$user->getEmail(),
				$user->getPhone(),
				$user->getFax(),
				$user->getMailingAddress(),
				$user->getBiography(),
				$user->getInterests(),
				join(':', $user->getLocales()),
				$user->getDateLastLogin() == null ? Core::getCurrentDate() : $user->getDateLastLogin(),
				$user->getMustChangePassword(),
				$user->getDisabled()?1:0,
				$user->getUserId()
			)
		);
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
		return $this->update(
			'DELETE FROM users WHERE user_id = ?', $userId
		);
	}
	
	/**
	 * Retrieve a user's name.
	 * @param int $userId
	 * @param $allowDisabled boolean
	 * @return string
	 */
	function getUserFullName($userId, $allowDisabled = true) {
		$result = $this->retrieve(
			'SELECT first_name, middle_name, last_name FROM users WHERE user_id = ?' . ($allowDisabled?'':' AND disabled = 0'),
			$userId
		);
		
		if($result->RecordCount() == 0) {
			return false;
		} else {
			return $result->fields[0] . ' ' . (empty($result->fields[1]) ? '' : $result->fields[1] . ' ') . $result->fields[2];
		}
	}
	
	/**
	 * Retrieve a user's email address.
	 * @param int $userId
	 * @param $allowDisabled boolean
	 * @return string
	 */
	function getUserEmail($userId, $allowDisabled = true) {
		$result = $this->retrieve(
			'SELECT email FROM users WHERE user_id = ?' . ($allowDisabled?'':' AND disabled = 0'),
			$userId
		);
		
		if($result->RecordCount() == 0) {
			return false;
		} else {
			return $result->fields[0];
		}
	}

	/**
	 * Retrieve an array of users.
	 * @param $sort string the field to sort on
	 * @param $order string the sort order (+|-)
	 * @param $allowDisabled boolean
	 * @param $dbResultRange object The desired range of results to return
	 * @return array of Users 
 	 */
	function &getUsers($sort='lastName', $order='+', $allowDisabled = true, $dbResultRange = null) {
		switch ($sort) {
			case 'username':
				break;
			case 'firstName':
				$sort = 'first_name';
				break;
			case 'lastName':
			default:
				$sort = 'last_name';
		}

		if ($order == '-') {
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}
	
		$result = &$this->retrieveRange(
			'SELECT * FROM users' . ($allowDisabled?'':' AND disabled = 0') . ' ORDER BY ' . $sort . ' '. $order,
			false,
			$dbResultRange
		); 

		return new DAOResultFactory(&$result, &$this, '_returnUserFromRow');
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
		$sql = 'SELECT * FROM users';
		switch ($field) {
			case USER_FIELD_USERID:
				$sql .= ' WHERE username = ?';
				$var = $value;
				break;
			case USER_FIELD_USERNAME:
				$sql .= ' WHERE ' . ($match == 'is' ? 'username = ?' : 'LOWER(username) LIKE LOWER(?)');
				$var = $match == 'is' ? $value : "%$value%";
				break;
			case USER_FIELD_INITIAL:
				$sql .= ' WHERE LOWER(last_name) LIKE LOWER(?)';
				$var = "$value%";
				break;
			case USER_FIELD_INTERESTS:
				$sql .= ' WHERE ' . ($match == 'is' ? 'interests = ?' : 'LOWER(interests) LIKE LOWER(?)');
				$var = $match == 'is' ? $value : "%$value%";
				break;
			case USER_FIELD_EMAIL:
				$sql .= ' WHERE ' . ($match == 'is' ? 'email = ?' : 'LOWER(email) LIKE LOWER(?)');
				$var = $match == 'is' ? $value : "%$value%";
				break;
			case USER_FIELD_FIRSTNAME:
				$sql .= ' WHERE ' . ($match == 'is' ? 'first_name = ?' : 'LOWER(first_name) LIKE LOWER(?)');
				$var = $match == 'is' ? $value : "%$value%";
				break;
			case USER_FIELD_LASTNAME:
				$sql .= ' WHERE ' . ($match == 'is' ? 'last_name = ?' : 'LOWER(last_name) LIKE LOWER(?)');
				$var = $match == 'is' ? $value : "%$value%";
				break;
		}
		if ($field != USER_FIELD_NONE) $result = &$this->retrieveRange($sql . ($allowDisabled?'':' AND disabled = 0'), $var, $dbResultRange);
		else $result = &$this->retrieveRange($sql . ($allowDisabled?'':' WHERE disabled = 0'), false, $dbResultRange);
		
		return new DAOResultFactory(&$result, &$this, '_returnUserFromRow');
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
		return isset($result->fields[0]) && $result->fields[0] != 0 ? true : false;
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
		return isset($result->fields[0]) && $result->fields[0] == 1 ? true : false;
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
		return isset($result->fields[0]) && $result->fields[0] == 1 ? true : false;
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
