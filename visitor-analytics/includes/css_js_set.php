<?php
function wp_view_page_style($hook){
   // echo $hook;
   if($hook == 'toplevel_page_proweaver_visitor_analytics'){
      wp_enqueue_style(
         'proweaver_visitor_analytics',
         WPPLUGIN_URL . 'assets/css/style.css',
         [],
         time()
      );

      wp_enqueue_style(
         'proweaver_visitor_analytics2',
         WPPLUGIN_URL . 'assets/css/mdb.min.css',
         [],
         time()
      );
   }

}

add_action('admin_enqueue_scripts', 'wp_view_page_style');

function wp_admin_script($hook){
   if($hook == 'toplevel_page_proweaver_visitor_analytics'){
      wp_enqueue_script(
         'proweaver_visitor_analytics',
         WPPLUGIN_URL . 'assets/js/mdb.min.js',
         [],
         time()
      );
   }
}

add_action('admin_enqueue_scripts', 'wp_admin_script');
