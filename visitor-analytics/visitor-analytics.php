<?php
@session_start();
/*
* Plugin Name: WP Visitor Analytics
* Version: 1.2
* Plugin URI: http://www.proweaver.com/
* Description: A plugin that counts website visitors and displays statistical data over a specified time period.
* Author: SP Team
*
*/

if (!defined ('WPINC')){
   die;
}

define( 'WPPLUGIN_URL' , plugin_dir_url(__FILE__) );
define( 'WPPLUGIN_PATH' , plugin_dir_path(__FILE__) );

include ( plugin_dir_path( __FILE__ ) . 'includes/css_js_set.php' );
// include ( plugin_dir_path( __FILE__ ) . 'includes/counter.php' );

$data = NULL;

/* create mysql tables */
function create_data_tables(){
     global $wpdb;

     $table_name = $wpdb->prefix . 'visitor_analytics';
     $charset_collate = $wpdb->get_charset_collate();

     $query = "CREATE TABLE $table_name (
          count_id int(6) NOT NULL AUTO_INCREMENT,
          count_date date NULL,
          count_time_in varchar(100) NULL,
          count_time_out varchar(100) NULL,
          count_ip_address varchar(15) NULL,
          count_state varchar(100) NULL,
          count_country varchar(100) NULL,
          count_latitude varchar(100) NULL,
          count_longitude varchar(100) NULL,
          PRIMARY KEY (count_id)
     ) $charset_collate";

     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	  dbDelta( $query );
}

function wp_visitor_setting_page(){
   add_menu_page(
      'WP Visitor Analytics',
      'WP Visitor Analytics',
      'manage_options',
      'proweaver_visitor_analytics',
      'analytics_page_mark_up',
      'dashicons-welcome-view-site',
      100
   );
}

function analytics_page_mark_up(){
   if( !current_user_can('manage_options')){
      return;
   }
   $year = date('Y');
   if(isset($_GET['year']) AND $_GET['year']){
      $year = $_GET['year'];
   }

   $starting_year = 2017;
   $root_folder_name = pathinfo(site_url(), PATHINFO_BASENAME);
   include (plugin_dir_path(__FILE__) . 'includes/temp/view-page.php');
}

function add_head_snippet(){
   echo "<link rel=\"stylesheet\" href=\"https://unpkg.com/leaflet@1.3.4/dist/leaflet.css\" />";
     echo "<link rel=\"stylesheet\" href=\"".site_url('wp-content/plugins/visitor-analytics/assets/css/leaflet-routing-machine.css') ."\" />";
     echo "<script type=\"text/javascript\" src=\"".site_url('wp-content/plugins/visitor-analytics/assets/js/jquery.min.js') ."\"></script>\n";
     echo "<script type=\"text/javascript\" src=\"".site_url('wp-content/plugins/visitor-analytics/assets/js/js.cookie.min.js') ."\"></script>\n";
     echo "<script type=\"text/javascript\" src=\"".site_url('wp-content/plugins/visitor-analytics/assets/js/va_plugins.js') ."\"></script>\n";
     echo "<script type=\"text/javascript\">var va_plugin_url = '".site_url('wp-content/plugins')."'</script>\n";
}

function add_footer_snippet(){
     echo "<script type=\"text/javascript\" src=\"https://unpkg.com/leaflet@1.3.4/dist/leaflet.js\"></script>\n";
     echo "<script type=\"text/javascript\" src=\"".site_url('wp-content/plugins/visitor-analytics/assets/js/leaflet-routing-machine.min.js') ."\"></script>\n";
     echo "<script type=\"text/javascript\" src=\"http://www.liedman.net/leaflet-routing-machine/lib/Control.Geocoder.js\"></script>\n";
     echo "<script type=\"text/javascript\" src=\"".site_url('wp-content/plugins/visitor-analytics/assets/js/frontend.js') ."\"></script>\n";
}


function my_custom_admin_head() {
   global $pagenow;
   if(($pagenow === 'admin.php') && ($_GET['page'] === 'proweaver_visitor_analytics')){
	     echo '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>';
        echo '<script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js" integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA==" crossorigin=""></script>';
   }
}
add_action( 'admin_head', 'my_custom_admin_head' );

function my_custom_admin_footer() {
  global $wpdb;
  global $pagenow;

if(($pagenow === 'admin.php') && ($_GET['page'] === 'proweaver_visitor_analytics')){
   $query = "SELECT * FROM ".$wpdb->prefix ."visitor_analytics";
     $res = $wpdb->get_results($query);

        echo "<script>
             var x = document.getElementById(\"mapid\");

             if (navigator.geolocation) {
                  navigator.geolocation.getCurrentPosition(showPos, showError);
             } else {
                  x.display = \"none\";
             }

             function showPos(position){
                  var latitude = position.coords.latitude;
                  var longitude = position.coords.longitude;

                  myMap(latitude, longitude);
             }

             function showError(error) {

                var latitude = 0;
                var longitude = 0;

                switch(error.code) {
                 case error.PERMISSION_DENIED:
                   x.style.display = \"none\";
                   myNavMap(latitude, longitude);
                   break;
                 case error.POSITION_UNAVAILABLE:
                   x.style.display = \"none\";
                   myNavMap(latitude, longitude);
                   break;
                 case error.TIMEOUT:
                   x.style.display = \"none\";
                   myNavMap(latitude, longitude);
                   break;
                 case error.UNKNOWN_ERROR:
                   x.style.display = \"none\";
                   myNavMap(latitude, longitude);
                   break;
                }
             }

             function myMap(latitude, longitude){
                  var mymap = L.map('mapid',{minZoom: 0}).setView([latitude, longitude], 2);";

                  foreach ($res as $val) {
                       if($val->count_latitude != NULL || $val->count_longitude != NULL){
                          echo "var circle = new L.circle([".$val->count_latitude.",".$val->count_longitude."], {
                                color: 'red',
                                     fillColor: '#f03',
                                     fillOpacity: 0.3,
                                     radius: 1500
                                 }).addTo(mymap);
                            ";
                       }
                  }
                  echo "
                  L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoicHJvc3B0ZWFtMjAxOCIsImEiOiJjanBkZWlyMnoybHo0M3dxczh4ZGxtZG5kIn0.ycZHWnaVd_Lb2ceqYjYAqw', {
                      attribution: 'Map data &copy; 2018',
                      maxZoom: 18,
                      id: 'mapbox.streets',
                      accessToken: 'pk.eyJ1IjoicHJvc3B0ZWFtMjAxOCIsImEiOiJjanBkZWlyMnoybHo0M3dxczh4ZGxtZG5kIn0.ycZHWnaVd_Lb2ceqYjYAqw'
                  }).addTo(mymap);
             }
         </script>";
      }
}
add_action( 'admin_footer', 'my_custom_admin_footer' );

register_activation_hook( __FILE__, 'create_data_tables');
add_action('admin_menu', 'wp_visitor_setting_page');
add_action('wp_head', 'add_head_snippet');
add_action('wp_footer', 'add_footer_snippet');


function wpdocs_enqueue_custom_admin_script() {
   global $pagenow;

   if(($pagenow === 'admin.php') && ($_GET['page'] === 'proweaver_visitor_analytics')){
       wp_register_script( 'jquery_js', site_url('wp-content/plugins/visitor-analytics/assets/js/jquery.min.js'), false, '1.0.0' );
       wp_register_script( 'datatables_js', site_url('wp-content/plugins/visitor-analytics/assets/js/jquery.dataTables.min.js'), false, '1.0.0' );
       wp_register_script( 'datetimepicker_js', site_url('wp-content/plugins/visitor-analytics/assets/js/datepicker.min.js'), false, '1.0.0' );
       wp_register_script( 'datetimepicker_lang_js', site_url('wp-content/plugins/visitor-analytics/assets/js/datepicker.en.js'), false, '1.0.0' );
       wp_register_script( 'admin_js', site_url('wp-content/plugins/visitor-analytics/assets/js/admin.js'), false, '1.0.0' );
       wp_register_script( 'jspdf_js', site_url('wp-content/plugins/visitor-analytics/assets/js/jspdf.min.js'), false, '1.0.0' );
       wp_register_script( 'jspdf_autotable_js', site_url('wp-content/plugins/visitor-analytics/assets/js/jspdf.plugin.autotable.js'), false, '1.0.0' );

       wp_enqueue_script( 'jquery_js' );
       wp_enqueue_script( 'datatables_js' );
       wp_enqueue_script( 'datetimepicker_js' );
       wp_enqueue_script( 'datetimepicker_lang_js' );
       wp_enqueue_script( 'admin_js' );
       wp_enqueue_script( 'jspdf_js' );
       wp_enqueue_script( 'jspdf_autotable_js' );
   }
}
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_script' );

function wpdocs_enqueue_custom_admin_style() {
   global $pagenow;

   if(($pagenow === 'admin.php') && ($_GET['page'] === 'proweaver_visitor_analytics')){
       wp_register_style( 'datatables_css', site_url('wp-content/plugins/visitor-analytics/assets/css/jquery.dataTables.min.css'), false, '1.0.0' );
       wp_register_style( 'datepicker_css', site_url('wp-content/plugins/visitor-analytics/assets/css/datepicker.min.css'), false, '1.0.0' );
       wp_enqueue_style( 'datatables_css' );
       wp_enqueue_style( 'datepicker_css' );
   }
}
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );

function map_nav_shortcode($atts, $content = NULL){
   $args = shortcode_atts(
      array(
      "width" => NULL,
      "height" => NULL,
      "address" => "United States"
   ), $atts);

   if(!empty($args['width'])){
      $width = $args['width'];
   }
   if(!empty($args['height'])){
      $height = $args['height'];
   }
   if(empty($args['width'])){
      $width = "100%";
   }
   if(empty($args['height'])){
      $height = "500px";
   }

   ob_start();

   echo " <div id=\"map_nav\" class=\"map\" style=\"position: relative; width: $width; height: $height; z-index: 999; margin: 0 auto; \"></div>";

   return ob_get_clean();

   $_SESSION['address'] = $args['address'];
}

add_shortcode("va_map_nav", "map_nav_shortcode");

function visitor_counter_shortcode(){

   global $wpdb;

   ob_start();

   $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."visitor_analytics");

   $count_row = $wpdb->num_rows;

   echo "<span class='va_counter'><span>Visitor Counter:</span> <b>".$count_row."</b></span>";

   return ob_get_clean();
}

add_shortcode("va_visitor_counter", "visitor_counter_shortcode");

wp_enqueue_script( 'my_js_library', plugin_dir_url( __FILE__ ) . '/assets/js/frontend.js' );

$dataToBePassed = array(
    'address' => __( $_SESSION['address'], 'default' )
);
wp_localize_script( 'my_js_library', 'php_vars', $dataToBePassed );

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'va_add_plugin_page_settings_link');
function va_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .plugins_url('visitor-analytics/attachments/').'documentation.pdf" target="_blank">' . __('Documentation') . '</a>';
	return $links;
}

 ?>
