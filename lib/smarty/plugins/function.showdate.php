<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {showdate} function plugin
 *
 * Type:     function<br>
 * Name:     showdate<br>
 * Purpose:  handle math computations in template<br>
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_showdate($params, &$smarty)
{
	$dateFormatTrunc = "%m-%d";
	$dateFormatShort = "%Y-%m-%d";
	$dateFormatLong = "%B %e, %Y";
	$datetimeFormatShort = "%Y-%m-%d %I:%M %p";
	$datetimeFormatLong = "%B %e, %Y - %I:%M %p";
	$dateFormatWithSpace = "%e %m %Y";
	$yearFormat = "%Y";
	$yearAlphaMonthDay = "%Y %b %e";
	$dayAlphaMonthYear = "%e %b %Y";
	$dayLongAlphaMonthYear = "%e %B %Y";
	$completeDateTimeFormat = "%Y-%m-%dT%T%z";
	$alphaMonthYear = "%B, %Y";

	$type = $params["type"];
	$format = $params["format"];
	$value = $params["value"];	
	if (isset($params["assign"])) {
		$assign = $params["assign"];
	} else {
		$assign = 0;
	}

	if ($value == null) {
		if (isset($params["default"])) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], $params["default"]);
			} else {
				return $params["default"];
			}				
		} else {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], "&mdash;");
			} else {
				return "&mdash;";
			}				
		}			
	}

	// 1 equals jalali and 0 equals gregorian
	if ($type == 1) {
		$value = $params["value"];
		$timestamp = Core::convertDateTimeToTimestamp($value);
		if (!$timestamp) {
			$timestamp = $value;
		}
		$hour = date('h', $timestamp);
		$minute = date('i', $timestamp);
		$second = date('s', $timestamp);
		$amPm = date('A', $timestamp);
		$gmt = date('O', $timestamp);
		$jalali = Core::gregorianToJalali($timestamp);
		
		$month = ($jalali["month"] < 10) ? "0".(string)$jalali["month"] : $jalali["month"];
		$day = ($jalali["day"] < 10) ? "0".(string)$jalali["day"] : $jalali["day"];

		if (strcmp($format, $dateFormatTrunc) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], $month."-".$day);
			} else {
				return $month."-".$day;
			}				
		} else if (strcmp($format, $dateFormatShort) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], $jalali["year"]."-".$month."-".$day);
			} else {
				return $jalali["year"]."-".$month."-".$day;
			}
		} else if (strcmp($format, $dateFormatLong) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], $jalali["monthName"]." ".$day.", ".$jalali["year"]);
			} else {
				return $jalali["monthName"]." ".$day.", ".$jalali["year"];
			}
		} else if (strcmp($format, $datetimeFormatShort) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], $jalali["year"]."-".$month."-".$day." ".$hour.":".$minute." ".$amPm);
			} else {
				return $jalali["year"]."-".$month."-".$day." ".$hour.":".$minute." ".$amPm;
			}
		} else if (strcmp($format, $datetimeFormatLong) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], $jalali["monthName"]." ".$day.", ".$jalali["year"]." - ".$hour.":".$minute." ".$amPm);
			} else {
				return $jalali["monthName"]." ".$day.", ".$jalali["year"]." - ".$hour.":".$minute." ".$amPm;
			}
		} else if (strcmp($format, $dateFormatWithSpace) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], $day." ".$month." ".$jalali["year"]);
			} else {
				return $day." ".$month." ".$jalali["year"];
			}		
		} else if (strcmp($format, $yearFormat) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], $jalali["year"]);
			} else {
				return $jalali["year"];
			}		
		} else if (strcmp($format, $yearAlphaMonthDay) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], $jalali["year"]." ".$jalali["monthName"]." ".$day);
			} else {
				return $jalali["year"]." ".$jalali["monthName"]." ".$day;
			}
		} else if (strcmp($format, $dayAlphaMonthYear) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], $day." ".$jalali["monthName"]." ".$jalali["year"]);
			} else {
				return $day." ".$jalali["monthName"]." ".$jalali["year"];
			}
		} else if (strcmp($format, $dayLongAlphaMonthYear) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], $day." ".$jalali["monthName"]." ".$jalali["year"]);
			} else {
				return $day." ".$jalali["monthName"]." ".$jalali["year"];
			}
		} else if (strcmp($format, $completeDateTimeFormat) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], $jalali["year"]."-".$month."-".$day."T".$hour.":".$minute.":".$second.$gmt);
			} else {
				return $jalali["year"]."-".$month."-".$day."T".$hour.":".$minute.":".$second.$gmt;
			}
		} else if (strcmp($format, $alphaMonthYear) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], $jalali["monthName"].", ".$jalali["year"]);
			} else {
				return $jalali["monthName"].", ".$jalali["year"];
			}
		} else {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], $jalali["year"]."-".$month."-".$day." ".$hour.":".$minute.":".$second);
			} else {
				return $jalali["year"]."-".$month."-".$day." ".$hour.":".$minute.":".$second;
			}
		}
	} else {
		$timestamp = Core::convertDateTimeToTimestamp($value);
		if (!$timestamp) {
			$timestamp = $value;
		}
		if (strcmp($format, $dateFormatTrunc) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], date('m-d', $timestamp));
			} else {
				return date('m-d', $timestamp);
			}
		} else if (strcmp($format, $dateFormatShort) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], date('Y-m-d', $timestamp));
			} else {
				return date('Y-m-d', $timestamp);
			}
		} else if (strcmp($format, $dateFormatLong) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], date('F d, Y', $timestamp));
			} else {
				return date('F d, Y', $timestamp);
			}
		} else if (strcmp($format, $datetimeFormatShort) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], date('Y-m-d h:i A', $timestamp));
			} else {
				return date('Y-m-d h:i A', $timestamp);
			}
		} else if (strcmp($format, $datetimeFormatLong) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], date('F d, Y - h:i A', $timestamp));
			} else {
				return date('F d, Y - h:i A', $timestamp);
			}
		} else if (strcmp($format, $dateFormatWithSpace) == 0) {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], date('d m Y', $timestamp));
			} else {
				return date('d m Y', $timestamp);
			}
		} else if (strcmp($format, $yearFormat) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], date('Y', $timestamp));
			} else {
				return date('Y', $timestamp);
			}
		} else if (strcmp($format, $yearAlphaMonthDay) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], date('Y M d', $timestamp));
			} else {
				return date('Y M d', $timestamp);
			}
		} else if (strcmp($format, $dayAlphaMonthYear) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], date('d M Y', $timestamp));
			} else {
				return date('d M Y', $timestamp);
			}		
		} else if (strcmp($format, $dayLongAlphaMonthYear) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], date('d F Y', $timestamp));
			} else {
				return date('d F Y', $timestamp);
			}
		} else if (strcmp($format, $completeDateTimeFormat) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], date('c', $timestamp));
			} else {
				return date('c', $timestamp);
			}
		} else if (strcmp($format, $alphaMonthYear) == 0) {
			if ($assign == 1) {
				$smarty->assign($params['item'], date('F, Y', $timestamp));
			} else {
				return date('F, Y', $timestamp);
			}
		} else {
			if ($assign == 1) {
	                        $smarty->assign($params['item'], date('Y-m-d h:i:s', $timestamp));
			} else {
				return date('Y-m-d h:i:s', $timestamp);
			}
		}
	}

}

/* vim: set expandtab: */

?>
