<?php
session_start();
//GET INCLUDES
require_once(dirname(__FILE__) . '/includes.php');

global $wpdb;

$query = "SELECT * FROM ".$wpdb->prefix ."visitor_analytics WHERE ";

if (!isset($_POST['From_date'],$_POST['To_date'])) {
  if (isset($_POST['months'])) {
    $months = $_POST['months'];
  }else {
    $months = array(1,2,3,4,5,6,7,8,9,10,11,12);
  }
  foreach ($months as $value) {
    if ($value < 10) {
      $value = '0'.$value;
    }
    $query .= "count_date like '".$_POST['year']."-".$value."%'";
    if ($value != end($months)) {
      $query .= " OR ";
    }
  }
}else {
  $from = $_POST['From_date'];
  $to = $_POST['To_date'];
  $query .= "count_date BETWEEN '$from' AND '$to'";
}

$_SESSION['query'] = $query;

  $results = $wpdb->get_results($query);
  $count_time = array(0,0,0,0,0,0,0);
  $arr_country = array();
  $country_key = array();
  $arr_state = array();
  $us_state = array();
  $months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
 ?>

 <h4 style="font-weight: 600">Site visitors: <?php echo sizeof($results); ?></h4>
 <?php if (sizeof($results) != 0): ?>
  <table id="table_visitors" class="table-fill">
    <thead>
      <tr>
        <th class="text-left" style="max-width:30px;">IP Address</th>
        <th class="text-left">State / Region</th>
        <th class="text-left">Country</th>
        <th class="text-left">Date</th>
        <th class="text-left" style="max-width:110px;">Time Duration</th>
      </tr>
    </thead>
  <tbody class="table-hover">
    <?php foreach ($results as $value): ?>
      <?php $time_duration = floor((strtotime($value->count_time_out) - strtotime($value->count_time_in))/60); ?>
      <?php
          if ($time_duration > 59) {

              $hours = floor($time_duration / 60);
              $minutes = ($time_duration % 60);
      }

      ?>
      <tr>
        <td class="text-left"><?php echo $value->count_ip_address; ?></td>
        <td class="text-left"><?php if($value->count_latitude == NULL || $value->count_longitude == NULL){ echo $value->count_state; }else{
          echo $value->count_state." [<a href='javascript:;' class='view_map' data-location='".$value->count_state.", ".$value->count_country."' data-url='".plugin_dir_url(__FILE__)."visitor-map.php?lati=".$value->count_latitude."&longi=".$value->count_longitude."'>VIEW MAP</a>]";
        } ?>
          </td>

        <td class="text-left"><?php echo $value->count_country; ?></td>
        <td class="text-left"><?php echo $value->count_date; ?></td>
        <td class="text-left"><?php if($time_duration > 59){ echo $hours." hr ".$minutes." min"; }else{ echo $time_duration." minutes"; } ?></td>
      </tr>
      <?php
        //counts time duration
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

        //counts country
        array_push($arr_country, $value->count_country);

        $state_list = array('AL'=>"Alabama", 'AK'=>"Alaska", 'AZ'=>"Arizona", 'AR'=>"Arkansas", 'CA'=>"California", 'CO'=>"Colorado", 'CT'=>"Connecticut", 'DE'=>"Delaware", 'DC'=>"District Of Columbia", 'FL'=>"Florida", 'GA'=>"Georgia", 'HI'=>"Hawaii", 'ID'=>"Idaho", 'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa", 'KS'=>"Kansas", 'KY'=>"Kentucky", 'LA'=>"Louisiana", 'ME'=>"Maine", 'MD'=>"Maryland", 'MA'=>"Massachusetts", 'MI'=>"Michigan", 'MN'=>"Minnesota", 'MS'=>"Mississippi", 'MO'=>"Missouri", 'MT'=>"Montana", 'NE'=>"Nebraska", 'NV'=>"Nevada", 'NH'=>"New Hampshire", 'NJ'=>"New Jersey", 'NM'=>"New Mexico", 'NY'=>"New York", 'NC'=>"North Carolina", 'ND'=>"North Dakota", 'OH'=>"Ohio", 'OK'=>"Oklahoma", 'OR'=>"Oregon", 'PA'=>"Pennsylvania", 'RI'=>"Rhode Island", 'SC'=>"South Carolina", 'SD'=>"South Dakota", 'TN'=>"Tennessee", 'TX'=>"Texas", 'UT'=>"Utah", 'VT'=>"Vermont", 'VA'=>"Virginia", 'WA'=>"Washington", 'WV'=>"West Virginia", 'WI'=>"Wisconsin", 'WY'=>"Wyoming");

          //gets the states
         if($value->count_country == "United States"){
              if (array_search($value->count_state, $state_list) != '') {
                array_push($arr_state, $value->count_state);
              }
         }


       ?>
    <?php endforeach; ?>

    <?php $country_count = array_count_values($arr_country);
    $state_count = array_count_values($arr_state);

      //gets the country name
      foreach($country_count as $key => $value){
        array_push($country_key,$key);
      }

      //gets the US states
      foreach($state_count as $key => $value){
        array_push($us_state,$key);
      }
    ?>
  </tbody>
  </table>
  <br />
  <hr />
  <br />
  <div class="visitor_container">
    <table id="table_visitor_time" class="table-fill">
      <?php $time_range = array("0 - 10 minutes", "11 - 20 minutes", "21 - 30 minutes", "31 - 40 minutes", "41 - 50 minutes", "51 minutes - 1 hour", "more than 1 hour");

      $time_count = array_combine($time_range,$count_time);

      ?>
      <thead>
        <tr>
          <th class="text-left">Time Duration</th>
          <th class="text-left">Visitor Count</th>
        </tr>
      </thead>
      <tbody class="table-hover">
        <?php foreach($time_count as $time => $count): ?>
          <tr>
            <td class="text-left"><?php echo $time; ?></td>
            <td class="text-left"><?php echo $count; ?></td>
          </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
  </div>
  <div class="country_container">
    <table id="table_visitor_country" class="table-fill">
      <thead>
        <tr>
          <th class="text-left">Country</th>
          <th class="text-left">Visitor Count</th>
        </tr>
      </thead>
      <tbody class="table-hover">
        <?php foreach($country_count as $key => $value): ?>
          <tr>
            <td class="text-left"><a class="view_state_table" href="javascript:;" data-country="<?php echo $key; ?>" data-url="<?php echo plugin_dir_url(__FILE__).'us_state_count.php?country='.$key.'&month='.$_POST['months'].'&year='.$_POST['year']; ?>" title="Click here to view visitor count per state / region"><?php echo $key; ?></a></td>
            <td class="text-left"><?php echo $value; ?></td>
          </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
  </div>

  <br />
  <hr />
  <br />
  <div class="charts_container">
  <div class="time_chart">
    <canvas id="myChartTime"></canvas>
  </div>
<script type="text/javascript">
Chart.defaults.global.defaultFontFamily = "'Roboto', helvetica, arial, sans-serif";
var ctx = document.getElementById("myChartTime").getContext('2d');
var myChart = new Chart(ctx, {
type: 'line',
data: {
   labels: ["0-10 mins", "11-20 mins", "21-30 mins", "31 - 40 mins", "41 - 50 mins", "51 mins - 1 hr", "> 1 hr"],
   datasets: [{
    label: 'Number of Visitors',
    fill: false,
    pointRadius: 5,
    data: [<?php echo $count_time[0] ?>, <?php echo $count_time[1] ?>, <?php echo $count_time[2] ?>, <?php echo $count_time[3] ?>, <?php echo $count_time[4] ?>, <?php echo $count_time[5] ?>, <?php echo $count_time[6] ?>],

    borderColor: [
     'rgba(54, 162, 235, 1)'
    ],
    backgroundColor: [
     'rgba(54, 162, 235, 1)'
     ],
    borderWidth: 1,
   }]
},
options: {
  layout: {
        padding: {
            left: 20,
            right: 20,
            top: 0,
            bottom: 20
        }
    },
  title: {
    display: true,
    text: "Site Visit Average Time Duration",
    fontSize: 16,
    fontColor: '#1a4d86',
    fontStyle: 'bold',
    padding: 25
  },
   scales: {
    yAxes: [{
     ticks: {
      beginAtZero:true,
      callback: function (value) { if (Number.isInteger(value)) { return value; } },
      stepSize: <?php echo round(max($count_time) / 10) ?>,
      fontStyle: 'bold'
    },
    scaleLabel: {
      display: true,
      labelString: "Visitor Count"
    }
  }],
  xAxes: [{
    scaleLabel: {
      display: true,
      labelString: "Time Duration",
    },
    ticks: {
      fontColor: '#862d1a',
      fontStyle: 'bold'
    }
  }]
   }
}
});
</script>
<div class="country_chart">
  <canvas id="myChartCountry"></canvas>
</div>
<script type="text/javascript">
var ctx = document.getElementById("myChartCountry").getContext('2d');
var myChart = new Chart(ctx, {
type: 'bar',
data: {
   labels: [<?php echo '"'.implode('","',$country_key).'"'; ?>],
   datasets: [{
    label: "Number of Visitors",
    data: [<?php echo implode(",",$country_count); ?>],
    backgroundColor: [
     'rgba(255, 99, 132, 0.5)',
     'rgba(54, 162, 235, 0.5)',
     'rgba(255, 206, 86, 0.5)',
     'rgba(75, 192, 192, 0.5)',
     'rgba(153, 102, 255, 0.5)',
     'rgba(255, 159, 64, 0.5)',
     'rgba(255, 206, 64, 0.5)'
    ],
    borderColor: [
     'rgba(255,99,132,1)',
     'rgba(54, 162, 235, 1)',
     'rgba(255, 206, 86, 1)',
     'rgba(75, 192, 192, 1)',
     'rgba(153, 102, 255, 1)',
     'rgba(255, 159, 64, 1)',
     'rgba(255, 206, 64, 1)'
    ],
    borderWidth: 1
   }]
},
options: {
  layout: {
        padding: {
            left: 20,
            right: 20,
            top: 0,
            bottom: 20
        }
    },
  legend: {
    display: false
  },
  title: {
    display: true,
    text: "Site Visitors Per Country",
    fontSize: 16,
    fontColor: '#1a4d86',
    fontStyle: 'bold',
    padding: 25
  },
   scales: {
    yAxes: [{
     ticks: {
      beginAtZero:true,
      callback: function (value) { if (Number.isInteger(value)) { return value; } },
      stepSize: <?php echo round(max($country_count) / 10) ?>,
      fontStyle: 'bold'
    },
    scaleLabel: {
      display: true,
      labelString: "Visitor Count"
    }
  }],
  xAxes: [{
     maxBarThickness: 100,
    scaleLabel: {
      display: true,
      labelString: "Country"
    },
    ticks: {
      fontColor: '#862d1a',
      fontStyle: 'bold',
    }
  }]
   }
}
});
</script>
<?php if($us_state != NULL): ?>
<div class="state_chart">
  <canvas id="myChartState"></canvas>
</div>
</div> <!-- end charts_container  -->
<script type="text/javascript">
function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
      color += letters[Math.floor(Math.random() * 16)];
    }
    // color += '1a';
    return color;
}

var ctx = document.getElementById("myChartState").getContext('2d');
var myChart = new Chart(ctx, {
type: 'bar',
data: {
   labels: [<?php echo '"'.implode('","',$us_state).'"'; ?>],
   datasets: [{
    label: 'Number of Visitors',
    data: [<?php echo implode(",",$state_count); ?>],
    backgroundColor: [
    <?php for($i = 0; $i < sizeof($state_count); $i++){ ?>
      getRandomColor(),
    <?php } ?>
  ],
  borderColor: [
    <?php for($i = 0; $i < sizeof($state_count); $i++){ ?>
      '#a1a1a1',
    <?php } ?>
  ],
  borderWidth: 1
  }],
},
options: {
  layout: {
        padding: {
            left: 20,
            right: 20,
            top: 0,
            bottom: 20
        }
    },
  legend: {
    display: false
  },
  title: {
    display: true,
    text: "(FOR UNITED STATES) Site Visitors Per State",
    fontSize: 16,
    fontColor: '#1a4d86',
    fontStyle: 'bold',
    padding: 25
  },
  scales: {
   yAxes: [{
    ticks: {
     beginAtZero:true,
     callback: function (value) { if (Number.isInteger(value)) { return value; } },
      stepSize: <?php echo round(max($state_count) / 10) ?>,
      fontStyle: 'bold'
   },
   scaleLabel: {
     display: true,
     labelString: "Visitor Count"
   }
 }],
 xAxes: [{
    maxBarThickness: 100,
   scaleLabel: {
     display: true,
     labelString: "US State"
   },
   ticks: {
     fontColor: '#862d1a',
     fontStyle: 'bold',
   }
 }]
}
}
});
</script>
<?php endif; ?>
<?php else: ?>
  <p class="d-block bg-danger text-white no-data">No data found.</p>
<?php endif; ?>

<!-- The Modal -->
<div id="view_map_modal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close-modal">&times;</span>
    </div>
    <div class="modal-body">
    </div>
  </div>

</div>

<div id="view_state_modal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="close-modal">&times;</span>
    </div>
    <div class="modal-body">
    </div>
  </div>
</div>
