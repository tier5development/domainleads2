<?php
//use DateTime;

/**
 * -- These functions are available to the entire platform --
 */



/**
 *  This function adjusts date range and used in search requests 
 *  @param
 *  start_date and end_date are 2 dates as input params
 *  @return 
 *  array [0] -> datewise start date
 *  array [1] -> datewise end date
 */ 
function generateDateRange($start_date,$end_date) {
    $dates = [];
    if($start_date == null && $end_date == null) {
    	return null;
    }
    else if($start_date == null) {
    	$dates[0] = $end_date;
        return $dates;
    }
    else if($end_date == null) {
    	$dates[0] = $start_date;
    	return $dates;
    }
    else {
    	$day = 3600*24;
    	if(strtotime($start_date) > strtotime($end_date) )
		{
			$temp = $start_date;
			$start_date = $end_date;
			$end_date = $temp;
		}
        $dates[0] = $start_date;
        $dates[1] = $end_date;
        return $dates;
    }
}

/**
 *  This mask domain name 
 *  @param
 *  domain_name
 *  @return 
 *  masked_domain_name
 */ 
function customMaskDomain($domain) {
    $revDomains = array_reverse(explode('.', $domain));
    return '************.'.$revDomains[0];
}

/**
 *  This generates query string from request array.
 *  @param
 *  array
 *  @return 
 *  query string
 */
function getQueryParamsCustom($arr) {
    $str = '';
    if(count($arr) == 0) {
        return $str;
    }
    foreach($arr as $key => $each) {
        $str .= strlen($str) > 0 ? '&'.$key.'='.$each : '?'.$key.'='.$each;
    }
    return $str;
}

/**
 *  This converts any date to m-d-Y date format
 *  @param
 *  date
 *  @return 
 *  date in m-d-Y
 */
function convertToMDY($date) {
    if(DateTime::createFromFormat('d/m/Y', $date)) {
        return DateTime::createFromFormat('d/m/Y', $date)->format('m-d-Y');
    } else if(DateTime::createFromFormat('m/d/Y', $date)) {
        return DateTime::createFromFormat('m/d/Y', $date)->format('m-d-Y');
    } else if(DateTime::createFromFormat('Y/m/d', $date)) {
        return DateTime::createFromFormat('Y/m/d', $date)->format('m-d-Y');
    } else if(DateTime::createFromFormat('Y-m-d', $date)) {
        return DateTime::createFromFormat('Y-m-d', $date)->format('m-d-Y');
    } else if(DateTime::createFromFormat('d-m-Y', $date)) {
        return DateTime::createFromFormat('d-m-Y', $date)->format('m-d-Y');
    } else {
        return $date;
    }
}

function getPlanName($id) {
    return config('settings.PLAN.'.$id)[2]['name'];
}

function getPlanNumber($name) {
    return config('settings.PLAN.NAMEMAP.'.$name)[0];
}

function getPlanFriendlyName($id) {
    // $planId = getPlanName($id);
    return ucwords(config('settings.PLAN.PUBLISHABLE.'.$id)[3]);
}
/**
 * Gets array of reasons for leaving our platform
 */
function getCancelMembershipReasons() {
    return [
        'Bad onboarding',
        'Buggy product',
        'Bad support',
        'Not a right fit',
        'Price is too high',
        'Others'
    ];
}

/**
 * Get 2 digit representation of card month
 */
function getCardMonth($month) {
    return sprintf("%02d", $month);
}

/**
 * Get 2 digit representation of card year
 */
function getCardYear($year) {
    return substr($year, -2);
}

/**
 * Get pure country name from inpure country names
 * @param : countryName
 * @return : pureCountryName
 */
function getCountryName($countryName) {
    $smallCountryName = strtolower(trim($countryName));
    return isset(custom_country_aliases()[$smallCountryName]) ? ucwords(custom_country_aliases()[$smallCountryName]) : ucwords($smallCountryName);
}
?>