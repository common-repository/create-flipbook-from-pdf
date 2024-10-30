<?php
/*
Plugin Name: Creates 3D Flipbook, PDF Flipbook in WordPress
Plugin URI: http://saffronsoft.com
Description: Creates 3D Flipbook, PDF Flipbook in WordPress
Version: 1.2
Author: Fliperrr Team
@2023. Unauthorized use and/or duplication of this plugin without express and written permission from this plugin's author and/or owner is strictly prohibited. A Fliperrr plugin can only be used for one wordpress website. If you want to  use it on second website, you have to purchase another license.
*/
 class fliperrr {
	public static $dirPath;
	public static $dirUrl;
	public static $dirInc;
	public static $dirJs;
	public static $dirCss;
	public static $dirImg;
	function __construct() {
		/***********pluginConstants**********/
		$dirPath  = trailingslashit( plugin_dir_path( __FILE__ ) );
		$dirUrl   = trailingslashit( plugins_url( dirname( plugin_basename( __FILE__ ) ) ) );
		$dirInc   = $dirPath  . 'source/';
		$dirCss   = $dirUrl  . 'webroot/css/';
		$dirImg   = $dirUrl  . 'webroot/images/';
		$dirJs    = $dirUrl  . 'webroot/js/';
		define ( 'CFFP_PLUGIN_VERSION', '2.0.0');
		/***********pluginCssJsLoad**********/
		add_action( 'admin_enqueue_scripts', array( $this, 'flip_admin_custom_scripts' ) );
		/***********pluginTable**********/
		function flip_create_flipbook_table() {
			
			global $wpdb;
			$table_name = $wpdb->prefix . 'flip_book_setting';
			
			//if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
	
			//}
			
			$sql =  "CREATE TABLE $table_name (
					fs_id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
					fs_p_id varchar(100) NOT NULL,
					fs_name varchar(100) NOT NULL,
					fs_file_name varchar(100) NOT NULL,
					fs_desgin varchar(100) NOT NULL,
					fs_full_option ENUM( '0', '1' ) NOT NULL,
					fs_sound_option ENUM( '0', '1' ) NOT NULL,
					fs_thumbnail_option ENUM( '0', '1' ) NOT NULL,
					fs_print_option ENUM( '0', '1' ) NOT NULL,
					fs_smart_option ENUM( '0', '1' ) NOT NULL,
					fs_zoom_option ENUM( '0', '1' ) NOT NULL,
					fs_alignment_type ENUM( '0', '1' ) NOT NULL,
					fs_display_type ENUM( '0', '1' ) NOT NULL,
					fs_alignment_postion varchar(100) NOT NULL,
					fs_color varchar(100) NOT NULL,
					fs_display_color varchar(100) NOT NULL,
					fs_display_background varchar(100) NOT NULL,
					fs_bg_option varchar(100) NOT NULL,
					fs_template varchar(100) NOT NULL,
					fs_date varchar(100) NOT NULL,
					fs_screen varchar(100) NOT NULL,
					fs_status  ENUM( '1', '0' ) NOT NULL,
					PRIMARY KEY  (fs_id)
				);";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			
			
			
		}
		register_activation_hook( __FILE__, 'flip_create_flipbook_table' );
		
		/***********pluginDropTable**********/
		function flip_drop_create_flipbook_table(){
			global $wpdb;	
			$table_name = $wpdb->prefix . 'flip_book_setting';
			$sql =  "DROP TABLE ". $table_name;
			$wpdb->query($sql);
		}
		register_deactivation_hook(__FILE__, 'flip_drop_create_flipbook_table' );
		require_once( $dirInc . 'flipbook_admin_setting.php' );
		
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
			define('FAV_COMPILE_ID','https://geetsoft.com/');
            
		}else{
			define('FAV_COMPILE_ID','http://saffronsoft.com/');
		}
		
		
		 
	

		define('CFFP_ERROR_MSG','Error Occured, please try later.');
		define('CFFP_ERROR_COUNT_MSG','You have access to create only PDF.');
		define('CFFP_ACCESS_COUNT','1');
		define('CFFP_SUCCESS_MSG','Your document has been deleted successfully.');
		define('CFFP_SELECT_MSG','Please select background desgin.');
		define('CFFP_FLIP_TITLE','flipBook - My Flipbooks');
		define('CFFP_PRO_LINK','https://codecanyon.net/item/fliperrr-creates-flipbook-from-any-pdf-in-just-1-click-and-1-minute/23800969');
		define('CFFP_P_TYPE',false);
	}
	
	public function flip_admin_custom_scripts(){
		
		wp_enqueue_style( 'flip_bootstrap', plugins_url( 'webroot/css/flip_bootstrap.css?v='.rand(),  __FILE__ ) );
		//wp_enqueue_style( 'flip_jquery-ui', plugins_url( 'webroot/css/flip_jquery-ui.css?v='.rand(),  __FILE__ ) );
		wp_enqueue_style( 'flip_style', plugins_url( 'webroot/css/flip_style.css?v='.rand(),  __FILE__ ) );
		wp_enqueue_style( 'flip_style_custom', plugins_url( 'webroot/css/flip_style_custom.css?v='.rand(),  __FILE__ ) );
		wp_enqueue_style( 'flip_effect', plugins_url( 'webroot/css/flip_effect.css?v='.rand(),  __FILE__ ) );
		wp_enqueue_style( 'flip_responsive', plugins_url( 'webroot/css/flip_responsive.css?v='.rand(),  __FILE__ ) );
		wp_enqueue_style( 'flip_font-awesome', plugins_url( 'webroot/css/flip_font-awesome.css?v='.rand(),  __FILE__ ) );
		wp_enqueue_style( 'flip_font-custom', plugins_url( 'webroot/css/flip_font-custom.css?v='.rand(),  __FILE__ ) );
		wp_enqueue_style( 'flip_basic', plugins_url( 'webroot/css/flip_basic.css?v='.rand(),  __FILE__ ) );
		wp_enqueue_style( 'flip_dropzone', plugins_url( 'webroot/css/flip_dropzone.css?v='.rand(),  __FILE__ ) );
		
		wp_enqueue_script( 'flip_bootstrap', plugins_url( 'webroot/js/flip_bootstrap.js?v='.rand(),  __FILE__ ), array('jquery'));
		wp_enqueue_script( 'flip_admin-flipbook', plugins_url( 'webroot/js/flip_admin-flipbook.js?v='.rand(),  __FILE__ ), array('jquery'));
		wp_enqueue_script( 'flip_dropzone', plugins_url( 'webroot/js/flip_dropzone.js?v='.rand(),  __FILE__ ), array('jquery'));
		wp_enqueue_script( 'flip_jquery-ui', plugins_url( 'webroot/js/flip_jquery-ui.js?v='.rand(),  __FILE__ ), array('jquery'));
		
	}
}
new fliperrr();