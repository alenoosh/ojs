<?php

/**
 * @file Core.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package core
 * @class Core
 *
 * Class containing system-wide functions.
 *
 * $Id$
 */

class Core {
	/**
	 * Get the path to the base OJS directory.
	 * @return string
	 */
	function getBaseDir() {
		static $baseDir;

		if (!isset($baseDir)) {
			// Need to change if this file moves from classes/core
			$baseDir = dirname(dirname(dirname(__FILE__)));
		}

		return $baseDir;
	}

	/**
	 * Sanitize a variable.
	 * Removes leading and trailing whitespace, normalizes all characters to UTF-8.
	 * @param $var string
	 * @return string
	 */
	function cleanVar($var) {
		// only process strings that are not UTF-8 already
		if ( !String::isUTF8($var) && Config::getVar('i18n', 'charset_normalization') == 'On' ) {
			import('core.Transcoder');
			$tinyMCE = PluginRegistry::getPlugin('generic', 'TinyMCEPlugin');

			if (empty($tinyMCE) || !$tinyMCE->getEnabled()) {
				$var = strtr($var, array("&amp;" => "&", "&quot" => '"', "&lt;" => "<", "&gt;" => ">"));

				// convert string to HTML entities (numeric and named)
				$trans =& new Transcoder('CP1252', 'HTML-ENTITIES');
				$var = $trans->trans($var);

				// convert UTF-8 entities back to UTF-8 characters
				$trans =& new Transcoder('HTML-ENTITIES', 'UTF-8');
				$var = $trans->trans($var);
			} else {
				// convert characters to UTF-8
				$trans =& new Transcoder('CP1252', 'UTF-8');
				$var = $trans->trans($var);
			}
		}		

		return trim($var);
	}

	/**
	 * Sanitize a value to be used in a file path.
	 * Removes any characters except alphanumeric characters, underscores, and dashes.
	 * @param $var string
	 * @return string
	 */
	function cleanFileVar($var) {
		return String::regexp_replace('/[^\w\-]/', '', $var);
	}

	/**
	 * Return the current date in ISO (YYYY-MM-DD HH:MM:SS) format.
	 * @param $ts int optional, use specified timestamp instead of current time
	 * @return string
	 */
	function getCurrentDate($ts = null) {
		return date('Y-m-d H:i:s', isset($ts) ? $ts : time());
	}
	
	/**
	 * Opatan Inc. :
	 * coverts date/time to timestamp.
	 * @return int
	 */
	function convertDateTimeToTimestamp($dateTime) {
		$val = explode(" ", $dateTime);
	        $date = explode("-", $val[0]);
	        $time = explode(":", $val[1]);
		
		if ($time[0] == null) {
			$hour = $minute = $second = 0;
		} else {
			$hour = $time[0];
			$minute = $time[1];
			$second = $time[2];
		}

		if ($val[0] != $date[0]) {
		        $timestamp = mktime($hour, $minute, $second, $date[1], $date[2], $date[0]);
		} else {
			return null;
		}

		return $timestamp;
	}
	
	/**
	 * Opatan Inc. :
	 * convert gregorian to jalali.
	 */
	function &gregorianToJalali($timestamp) {
		$gMonths = Array (31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$jMonths = Array (31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
		$jMonthName = Array ("", "Farvardin", "Ordibehesht", "Khordad", "Tir",
 	                             "Mordad", "Shahrivar", "Mehr", "Aban", "Azar",
 	                             "Dey", "Bahman", "Esfand");

       		$date = getdate($timestamp);
 	                
 	        $gDay   = $date['mday'];
 	        $gWDay  = $date['wday'];
 	        $gMonth = $date['mon'];
 	        $gYear  = $date['year'];

 		$gy = $gYear - 1600;
 	        $gm = $gMonth - 1;
 	        $gd = $gDay - 1;
 	
 	        $gDayNo = 365 * $gy + (int) (($gy + 3) / 4) - (int) (($gy + 99) / 100) + (int) (($gy + 399) / 400);
 	
 	        for ($i = 0; $i < $gm; $i++) {
 	            $gDayNo += $gMonths[$i];
 	        }
 	  
 	        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) {
 	            // leap and after Feb
 	            ++$gDayNo;
 	        }
 	  
 	        $gDayNo += $gd;
 	        $jDayNo = $gDayNo-79;
 	
 	        $j_np = (int) ($jDayNo / 12053);
 	        $jDayNo %= 12053;
 	
 	        $jy = 979 + 33 * $j_np + 4 * (int) ($jDayNo / 1461);
 	
 	        $jDayNo %= 1461;
 	
 	        if ($jDayNo >= 366) {
	            $jy += (int) (($jDayNo - 1) / 365);
	            $jDayNo = ($jDayNo - 1) % 365;
 	        }
 	
 	        for ($i = 0; $i < 11 && $jDayNo >= $jMonths[$i]; $i++) {
 	            $jDayNo -= $jMonths[$i];
 	        }
 	  
 	        $jm = $i + 1;
 	        $jd = $jDayNo + 1;
 	
 	        $jalali = Array("year" => $jy, "month" => $jm, "day" => $jd, "monthName" => $jMonthName[$jm]);

 	        return $jalali;
	}
	
	/**
	 * Opatan Inc. :
	 * Convert jalali to gregorian.
	 */
	function jalaliToGregorian($year, $month, $day) {
		$gMonths = Array (31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$jMonths = Array (31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

 	 	$jy = $year - 979;
		$jm = $month - 1;
		$jd = $day - 1;
		$gm = 0;
		$gd = 0;
		$gy = 0;
		$jDayNo = 365 * $jy + ((int)($jy/33))*8 + (int)((($jy%33) + 3)/4);
		for ($i = 0; $i < $jm; ++$i) {
  			$jDayNo += $jMonths[$i];
		}
		$jDayNo += $jd;
		$gDayNo = $jDayNo + 79;
		$gy = 1600 + 400 * (int)($gDayNo/146097);
		$gDayNo = $gDayNo % 146097;
		$leap = 1;
		if ($gDayNo >= 36525) {
			$gDayNo = $gDayNo - 1;
			$gy += 100 * (int)($gDayNo/36524);
			$gDayNo = $gDayNo % 36524;
			if ($gDayNo >= 365) {
				$gDayNo = $gDayNo + 1;
			} else {
				$leap = 0;
			}
		}
		$gy += 4 * (int)($gDayNo/1461);
		$gDayNo %= 1461;
		if ($gDayNo >= 366) {
			$leap = 0;
			$gDayNo = $gDayNo - 1;
			$gy += (int)($gDayNo / 365);
			$gDayNo = $gDayNo % 365;
		}
		$i = 0;
		$tmp = 0;
		while ($gDayNo >= ($gMonths[$i] + $tmp)) {
			if ($i == 1 && $leap == 1) {
				$tmp = 1;
			} else {
				$tmp = 0;
			}
			$gDayNo -= $gMonths[$i] + $tmp;
			$i = $i + 1;
		}
		$gm = $i + 1;
	  	$gd = $gDayNo + 1;

		return array("month" => $gm, "day" => $gd, "year" => $gy);
	}
	
	/** 
	 * Get the current jalali year.
	 * @return int
	 */
	function getCurrentJalaliYear() {
		$date = Core::getCurrentDate();
		$timestamp = Core::convertDateTimeToTimestamp($date);
		$jDate = &Core::gregorianToJalali($timestamp);

		return $jDate["year"];
	}

	/**
	 * Return *nix timestamp with microseconds (in units of seconds).
	 * @return float
	 */
	function microtime() {
		list($usec, $sec) = explode(' ', microtime());
		return (float)$sec + (float)$usec;
	}

	/**
	 * Get the operating system of the server.
	 * @return string
	 */
	function serverPHPOS() {
		return PHP_OS;
	}

	/**
	 * Get the version of PHP running on the server.
	 * @return string
	 */
	function serverPHPVersion() {
		return phpversion();
	}

	/**
	 * Check if the server platform is Windows.
	 * @return boolean
	 */
	function isWindows() {
		return strtolower(substr(Core::serverPHPOS(), 0, 3)) == 'win';
	}
}

?>
