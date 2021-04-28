<?php
$a = explode('/',$_SERVER['SCRIPT_FILENAME']);
$b = array_slice($a,0,array_search('wp-content',$a));
$url = implode('/', $b);
// $domain = $_SERVER['HTTP_HOST'];
include_once $url.'/wp-config.php';
include_once $url.'/wp-load.php';
include_once $url.'/wp-includes/wp-db.php';

/*******************************************************************************
*  Title: PHP hit counter (PHPcount)
*  Version: 1.2 @ October 26, 2007
*  Author: Klemen Stirn
*  Website: http://www.phpjunkyard.com
********************************************************************************
*  COPYRIGHT NOTICE
*  Copyright 2004-2007 Klemen Stirn. All Rights Reserved.
*
*  This script may be used and modified free of charge by anyone
*  AS LONG AS COPYRIGHT NOTICES AND ALL THE COMMENTS REMAIN INTACT.
*  By using this code you agree to indemnify Klemen Stirn from any
*  liability that might arise from it's use.
*
*  Selling the code for this program, in part or full, without prior
*  written consent is expressly forbidden.
*
*  Obtain permission before redistributing this software over the Internet
*  or in any other medium. In all cases copyright and header must remain
*  intact. This Copyright is in full effect in any country that has
*  International Trade Agreements with the United States of America or
*  with the European Union.
*******************************************************************************/

// SETUP YOUR COUNTER
// Detailed information found in the readme.htm file

// Count UNIQUE visitors ONLY? 1 = YES, 0 = NO
$count_unique = 1;
// Number of hours a visitor is considered as "unique"
//$unique_hours = 24; //using $unique_hours
// Minimum number of digits shown (zero-padding). Set to 0 to disable.
$min_digits = 1;

//if user ip is in array, do not record
// $ip_restrict = array('115.85.4.123');

#############################
#     DO NOT EDIT BELOW     #
#############################

/* Turn error notices off */
error_reporting(E_ALL ^ E_NOTICE);

/* Get page and log file names */
$page = input($_GET['page']) or die('ERROR: Missing page ID');
$logfile = 'logs/' . $page . '.txt';

$file_name = date('Y_m').'.txt';
$logfile = 'logs/'.$file_name;

if(!file_exists($logfile)){
  fopen($logfile, "w");
  fwrite($logfile, 1);
}

/* Does the log exist? */
if (file_exists($logfile)) {

    /* Get current count */
    $count = trim(file_get_contents($logfile)) or $count = 0;

    if ($count_unique==0 || $_COOKIE['counter_unique']!=$page) {
        /* Increase the count by 1 */
        $count = $count + 1;
        $fp = @fopen($logfile,'w+') or die('ERROR: Can\'t write to the log file ('.$logfile.'), please make sure this file exists and is CHMOD to 666 (rw-rw-rw-)!');
        flock($fp, LOCK_EX);
        fputs($fp, $count);
        flock($fp, LOCK_UN);
        fclose($fp);

        /* Print the Cookie and P3P compact privacy policy */
        header('P3P: CP="NOI NID"');
        //setcookie('counter_unique', $page, time()+60*60*$unique_hours); //using $unique_hours
		setcookie('counter_unique', $page); //cookies get deleted after browser closes
    }

    /* Is zero-padding enabled? */
    if ($min_digits > 0) {
        $count = sprintf('%0'.$min_digits.'s',$count);
    }

    /* Print out Javascript code and exit */
    //echo 'document.write(\''.$count.'\');';
    exit();

} else {
    die('ERROR: Invalid log file!');
}

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

function getUserIP2() {
    if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
            $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

//Gets Private IP Address, but could not detect location :(
function getUserIP3() {
     $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
     $res = socket_connect($sock, '8.8.8.8', 53);

     socket_getsockname($sock, $addr);
     socket_shutdown($sock);
     socket_close($sock);

     return $addr;
}

function input($in) {

     // $realIP = file_get_contents("http://ipecho.net/plain");
     // $details = json_decode(file_get_contents("http://ipinfo.io/json"));
    $country_name = json_decode('{"BD": "Bangladesh", "BE": "Belgium", "BF": "Burkina Faso", "BG": "Bulgaria", "BA": "Bosnia and Herzegovina", "BB": "Barbados", "WF": "Wallis and Futuna", "BL": "Saint Barthelemy", "BM": "Bermuda", "BN": "Brunei", "BO": "Bolivia", "BH": "Bahrain", "BI": "Burundi", "BJ": "Benin", "BT": "Bhutan", "JM": "Jamaica", "BV": "Bouvet Island", "BW": "Botswana", "WS": "Samoa", "BQ": "Bonaire, Saint Eustatius and Saba ", "BR": "Brazil", "BS": "Bahamas", "JE": "Jersey", "BY": "Belarus", "BZ": "Belize", "RU": "Russia", "RW": "Rwanda", "RS": "Serbia", "TL": "East Timor", "RE": "Reunion", "TM": "Turkmenistan", "TJ": "Tajikistan", "RO": "Romania", "TK": "Tokelau", "GW": "Guinea-Bissau", "GU": "Guam", "GT": "Guatemala", "GS": "South Georgia and the South Sandwich Islands", "GR": "Greece", "GQ": "Equatorial Guinea", "GP": "Guadeloupe", "JP": "Japan", "GY": "Guyana", "GG": "Guernsey", "GF": "French Guiana", "GE": "Georgia", "GD": "Grenada", "GB": "United Kingdom", "GA": "Gabon", "SV": "El Salvador", "GN": "Guinea", "GM": "Gambia", "GL": "Greenland", "GI": "Gibraltar", "GH": "Ghana", "OM": "Oman", "TN": "Tunisia", "JO": "Jordan", "HR": "Croatia", "HT": "Haiti", "HU": "Hungary", "HK": "Hong Kong", "HN": "Honduras", "HM": "Heard Island and McDonald Islands", "VE": "Venezuela", "PR": "Puerto Rico", "PS": "Palestinian Territory", "PW": "Palau", "PT": "Portugal", "SJ": "Svalbard and Jan Mayen", "PY": "Paraguay", "IQ": "Iraq", "PA": "Panama", "PF": "French Polynesia", "PG": "Papua New Guinea", "PE": "Peru", "PK": "Pakistan", "PH": "Philippines", "PN": "Pitcairn", "PL": "Poland", "PM": "Saint Pierre and Miquelon", "ZM": "Zambia", "EH": "Western Sahara", "EE": "Estonia", "EG": "Egypt", "ZA": "South Africa", "EC": "Ecuador", "IT": "Italy", "VN": "Vietnam", "SB": "Solomon Islands", "ET": "Ethiopia", "SO": "Somalia", "ZW": "Zimbabwe", "SA": "Saudi Arabia", "ES": "Spain", "ER": "Eritrea", "ME": "Montenegro", "MD": "Moldova", "MG": "Madagascar", "MF": "Saint Martin", "MA": "Morocco", "MC": "Monaco", "UZ": "Uzbekistan", "MM": "Myanmar", "ML": "Mali", "MO": "Macao", "MN": "Mongolia", "MH": "Marshall Islands", "MK": "Macedonia", "MU": "Mauritius", "MT": "Malta", "MW": "Malawi", "MV": "Maldives", "MQ": "Martinique", "MP": "Northern Mariana Islands", "MS": "Montserrat", "MR": "Mauritania", "IM": "Isle of Man", "UG": "Uganda", "TZ": "Tanzania", "MY": "Malaysia", "MX": "Mexico", "IL": "Israel", "FR": "France", "IO": "British Indian Ocean Territory", "SH": "Saint Helena", "FI": "Finland", "FJ": "Fiji", "FK": "Falkland Islands", "FM": "Micronesia", "FO": "Faroe Islands", "NI": "Nicaragua", "NL": "Netherlands", "NO": "Norway", "NA": "Namibia", "VU": "Vanuatu", "NC": "New Caledonia", "NE": "Niger", "NF": "Norfolk Island", "NG": "Nigeria", "NZ": "New Zealand", "NP": "Nepal", "NR": "Nauru", "NU": "Niue", "CK": "Cook Islands", "XK": "Kosovo", "CI": "Ivory Coast", "CH": "Switzerland", "CO": "Colombia", "CN": "China", "CM": "Cameroon", "CL": "Chile", "CC": "Cocos Islands", "CA": "Canada", "CG": "Republic of the Congo", "CF": "Central African Republic", "CD": "Democratic Republic of the Congo", "CZ": "Czech Republic", "CY": "Cyprus", "CX": "Christmas Island", "CR": "Costa Rica", "CW": "Curacao", "CV": "Cape Verde", "CU": "Cuba", "SZ": "Swaziland", "SY": "Syria", "SX": "Sint Maarten", "KG": "Kyrgyzstan", "KE": "Kenya", "SS": "South Sudan", "SR": "Suriname", "KI": "Kiribati", "KH": "Cambodia", "KN": "Saint Kitts and Nevis", "KM": "Comoros", "ST": "Sao Tome and Principe", "SK": "Slovakia", "KR": "South Korea", "SI": "Slovenia", "KP": "North Korea", "KW": "Kuwait", "SN": "Senegal", "SM": "San Marino", "SL": "Sierra Leone", "SC": "Seychelles", "KZ": "Kazakhstan", "KY": "Cayman Islands", "SG": "Singapore", "SE": "Sweden", "SD": "Sudan", "DO": "Dominican Republic", "DM": "Dominica", "DJ": "Djibouti", "DK": "Denmark", "VG": "British Virgin Islands", "DE": "Germany", "YE": "Yemen", "DZ": "Algeria", "US": "United States", "UY": "Uruguay", "YT": "Mayotte", "UM": "United States Minor Outlying Islands", "LB": "Lebanon", "LC": "Saint Lucia", "LA": "Laos", "TV": "Tuvalu", "TW": "Taiwan", "TT": "Trinidad and Tobago", "TR": "Turkey", "LK": "Sri Lanka", "LI": "Liechtenstein", "LV": "Latvia", "TO": "Tonga", "LT": "Lithuania", "LU": "Luxembourg", "LR": "Liberia", "LS": "Lesotho", "TH": "Thailand", "TF": "French Southern Territories", "TG": "Togo", "TD": "Chad", "TC": "Turks and Caicos Islands", "LY": "Libya", "VA": "Vatican", "VC": "Saint Vincent and the Grenadines", "AE": "United Arab Emirates", "AD": "Andorra", "AG": "Antigua and Barbuda", "AF": "Afghanistan", "AI": "Anguilla", "VI": "U.S. Virgin Islands", "IS": "Iceland", "IR": "Iran", "AM": "Armenia", "AL": "Albania", "AO": "Angola", "AQ": "Antarctica", "AS": "American Samoa", "AR": "Argentina", "AU": "Australia", "AT": "Austria", "AW": "Aruba", "IN": "India", "AX": "Aland Islands", "AZ": "Azerbaijan", "IE": "Ireland", "ID": "Indonesia", "UA": "Ukraine", "QA": "Qatar", "MZ": "Mozambique"}');

     $user_ip = $_POST['ip'];
     $user_region = $_POST['region'];
     $user_country = $_POST['country'];

     date_default_timezone_set('Asia/Singapore'); //change to your preferred location
     global $wpdb;

     $table_name = $wpdb->prefix.'visitor_analytics';

     $unique_id = $_COOKIE['unique_count'];
     $visitor_latitude = $_COOKIE['latitude'];
     $visitor_longitude = $_COOKIE['longitude'];

     $db_records = $wpdb->get_results("SELECT * FROM $table_name WHERE count_id LIKE '".$unique_id."'");

     if( isset($_COOKIE['unique_count']) )
     {
        $time_out =  date('H:i:s');
        // $last_visit = $_COOKIE['last_visit'];
        // setcookie('last_visit', date('H:i:s', strtotime($time_out . "+5 minutes", $time_out)),0,'/');

          $wpdb->update($table_name,
               array(
                    'count_time_out'=>date('H:i:s', strtotime($time_out . "+5 minutes", $time_out)),
                    'count_latitude'=>$visitor_latitude,
                    'count_longitude'=>$visitor_longitude
          ),
               array(
                    'count_id'=>$unique_id
          ));

     }
     else
     {
          if ($wpdb->num_rows == 0) {
               $time_in = date('H:i:s');
               $time_out = date('H:i:s');
               $time_out_fin = date('H:i:s', strtotime($time_out . "+5 minutes", $time_out));

               if ($user_ip != '115.85.4.122') {
                  $wpdb->insert($table_name,
                  array(
                     'count_date'=>date('Y/m/d'),
                     'count_time_in' => $time_in,
                     'count_time_out' => $time_out_fin,
                     'count_ip_address'=>$user_ip,
                     'count_state'=>$user_region,
                     'count_country'=>$country_name->$user_country,
                     'count_latitude'=>$visitor_latitude,
                     'count_longitude'=>$visitor_longitude
                  ));
                  setcookie('unique_count', $wpdb->insert_id, time() + (60*60*24), '/');
               }
       }
     }

    return $out;
}

?>
