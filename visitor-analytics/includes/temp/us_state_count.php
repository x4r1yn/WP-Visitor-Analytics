<?php
session_start();
//GET INCLUDES
require_once(dirname(__FILE__) . '/includes.php');

global $wpdb;

$query = $_SESSION['query'];
$result = $wpdb->get_results($query);
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Visitor Count by State / Region</title>
    <link rel="stylesheet" href="<?php echo plugins_url().'/visitor-analytics/assets/css/style.css' ?>">
  </head>
  <style media="screen">
      .state_region_count_tbl th{
         padding: 10px !important;
      }
  </style>
  <body>
    <?php
    $arr_state = array();
    $count_state = array();
    $country = $_GET['country'];

    foreach ($result as $value) {
      if($country == $value->count_country){
        array_push($arr_state, $value->count_state);
      }
    }

    $count_state = array_count_values($arr_state);?>

    <table class="state_region_count_tbl" style="width:100%">
    <tr><th>State / Region</th><th>Visitor Count</th></tr>
    <?php foreach($count_state as $state => $count): ?>
      <tr><td><?php echo $state; ?></td><td><?php echo $count; ?></td></tr>
    <?php endforeach; ?>
    </table>

  </body>
</html>
