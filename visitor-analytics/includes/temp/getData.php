<?php
session_start();
//GET INCLUDES
require_once(dirname(__FILE__) . '/includes.php');

global $wpdb;

$results = $wpdb->get_results($_SESSION['query']);

$data = array(
   'v_data'=> array(),
   't_data'=> array(),
   'c_data'=> array(),
   'c_states'=> array()
);

$count_time = array(0,0,0,0,0,0,0);
$countries_v = array();

//VISITOR INFO
foreach ($results as $key => $value) {
   $time_duration = floor((strtotime($value->count_time_out) - strtotime($value->count_time_in))/60);

   if ($time_duration > 59) {
      $hours = floor($time_duration / 60);
      $minutes = ($time_duration % 60);
   }

   if($time_duration > 59){
      $time = $hours." hr(s) ".$minutes." min(s)";
   }else{
      $time = $time_duration." minutes";
   }

   array_push($data['v_data'],
      array(
         $value->count_ip_address,
         $value->count_state,
         $value->count_country,
         $value->count_date,
         $time
      )
   );

   //get countries
   array_push($countries_v, $value->count_country);

   //count time durations
   if($time_duration <= 10){
        $count_time[0] ++;
     }elseif ($time_duration <= 20) {
        $count_time[1]++;
     }elseif ($time_duration <= 30) {
        $count_time[2]++;
     }elseif ($time_duration <= 40) {
        $count_time[3]++;
     }elseif ($time_duration <= 50) {
        $count_time[4]++;
     }elseif ($time_duration <= 60) {
        $count_time[5]++;
     }else{
        $count_time[6]++;
     }
}

//TIME DURATION INFO
$time_range = array("0 - 10 minutes", "11 - 20 minutes", "21 - 30 minutes", "31 - 40 minutes", "41 - 50 minutes", "51 minutes - 1 hour", "more than 1 hour");
$time_count = array_combine($time_range,$count_time);

foreach ($time_count as $key => $value) {
   array_push($data['t_data'],
      array(
         $key,
         $value
      )
   );
}


//COUNTRY INFO
$count_v = array();

foreach ($countries_v as $key => $value) {
   if (!array_key_exists($value, $count_v)) {
      $count_v[$value] = 1;
   }else {
      $count_v[$value] += 1;
   }
}

foreach ($count_v as $key => $value) {
   array_push($data['c_data'],
      array(
         $key,
         $value
      )
   );
}

$countries_v = array_unique($countries_v);
$states = array();

foreach ($countries_v as $key => $value) {
  $query = "SELECT * FROM ".$wpdb->prefix ."visitor_analytics WHERE count_country like '$value'";
  $results = $wpdb->get_results($query);

  foreach ($results as $key => $value) {
    if (!array_key_exists($value->count_country, $states)) {
      $states[$value->count_country][$value->count_state] = 1;
    }else {
      $states[$value->count_country][$value->count_state] += 1;
    }
  }
}

foreach ($states as $key => $value) {
   foreach ($value as $key2 => $val) {
      if (empty($data['c_states'][$key])) {
         $data['c_states'][$key] = array(array($key2,$val));
      }else {
         array_push($data['c_states'][$key], array($key2,$val));
      }
   }
}

echo json_encode($data);
