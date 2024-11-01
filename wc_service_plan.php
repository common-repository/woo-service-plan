<?php 
/*
Plugin Name: Wc  Service Plan 
Plugin URI: http://99plugin.com
Description:  Advance Gift packing  Wrap plugin and Service Plan
Author: kirtikanani
Version: 1.0
Author URI: http://kananikirti.wordpress.com/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/********

Global

********/
global $agw_option_setting ,$agw_service_plain_setting,$plugin_url ,$plugin_dir;
$agw_option_setting=get_option("agw_option_setting");
$agw_service_plain_setting=get_option("agw_service_plain_setting");


$plugin_url = plugins_url()."/wc_service_plan";
$plugin_dir = plugin_dir_path( __FILE__ );

//***** define ***//
define('GIFTS_WRAP_OPTION','Gifts Wrap');
define('GIFT_MESSAGE','Message');
define('MESSAGE_FONT_STYLE','Font Style');


define('SERVICE_PLAIN_OPTION','Service Plan');
//**orignal article

//admin setting
//include('admin/admin-interface.php');
include(plugin_dir_path( __FILE__ ).'admin/uploader/class-gift-wrap-uploader.php');
include(plugin_dir_path( __FILE__ ).'admin/class/wc-admin-service-plain-meta.php');



include(plugin_dir_path( __FILE__ ).'class/wc-frontend-advance-gift-wrap.php');
include(plugin_dir_path( __FILE__ ).'class/class-product-addon-cart.php');

if(!function_exists('gwp_output_buffer')){
	//set default  Admin option 
	add_action('init', 'gwp_output_buffer');
	function gwp_output_buffer() {
			ob_start();
	}
}
register_activation_hook( __FILE__, 'gwp_install' ); 
function gwp_install() {
    
	 $agw_option_setting =array( 
	                           'service_plain_status'=>'0',
	                           'service_plain_type'=>'1',	
							   'service_popup_title'=>'Service Plan'                            
							);
	update_option('agw_service_plain_setting',$agw_option_setting);
}
if(!class_exists('WC_GIFTS_WRAP_OPTION')){
  class WC_GIFTS_WRAP_OPTION{
	   		private $_templates = array();
			function __construct(){
				$this->_templates = array(
				   'cart/cart.php',				
				);
				add_action('plugins_loaded', array(&$this, 'hooks') ); 
				
			}
			public function hooks(){
				if( !class_exists( 'Woocommerce' ) )
			     return;				 
				 add_filter('wc_get_template', array( $this, 'custom_template') , 999, 5 );
				 add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
				 
				 add_action( 'woocommerce_update_options_settings_gift_wrap_tab', __CLASS__ . '::update_settings' );
				 add_action( 'woocommerce_settings_tabs_settings_gift_wrap_tab',__CLASS__ .'::settings_tab');
				 				 
				 
				 
				 add_action('add_meta_boxes',array($this,'product_sevice_plain_meta_box'));
				 add_action("save_post", array($this,'save_product_sevice_plain'), 10, 3);				 
				
				 
				  
				 
		   }
		   public function custom_template($located, $template_name, $args, $template_path, $default_path){
						if( in_array( $template_name, $this->_templates ) )
						return plugin_dir_path( __FILE__ ).$template_name;			
						return $located;					
		   }			  
		   public static function update_settings(){	
		     if($_POST['action'] == "service_plain"){
				 				 
				 $sanitize_agw_option_setting=sanitize_text_array($_POST['agw_option_setting']);
				 update_option('agw_service_plain_setting',$sanitize_agw_option_setting);		
				 
			  }
   		     if(is_array($_POST['agw_option_setting']) && isset($_POST['agw_option_setting'])){
			      wp_redirect($_POST['_wp_http_referer'], 301 ); exit;
			  }	 
		   }
		   public static function add_settings_tab( $settings_tabs ) {
			$settings_tabs['settings_gift_wrap_tab'] = __('Service Plan', 'woocommerce-settings-tab-demo' );
			return $settings_tabs;
           }
		   public function settings_tab(){			   		       
               include(plugin_dir_path( __FILE__ ).'admin/admin-interface.php');			   			  
          }	   
		 
		  public function product_sevice_plain_meta_box(){	
		    global $agw_service_plain_setting;			
			if($agw_service_plain_setting['service_plain_type']== '2'){		
					add_meta_box(
								'Service Plan Setting',
								 __( 'Service Plan Setting', 'service_plain_type' ),
								 array($this,'product_service_plain_box_callback'),
								 'product'
								);	
						
			 }
         }
		 function product_service_plain_box_callback($post){
		    global $WC_ADMIN_SERVICE_PLAIN;
			do_action('service_plain_html');
		 }
		 public function save_product_sevice_plain($post_id,  $post, $update)
		 {
		   if(isset($_REQUEST['agw_option_service_setting'])){
			   $sanitize_agw_option_service_setting=sanitize_text_array($_REQUEST['agw_option_service_setting']);
		    update_post_meta($post_id,'service_plain_meta',$sanitize_agw_option_service_setting);
		   }
		 } 
		 public function product_gift_wrap_box_callback($post){		    
			do_action('gift_wrap_html');
		 }
		 
 	  
  } 
  
 $load = new WC_GIFTS_WRAP_OPTION();
}
function sanitize_text_array($arrays_orig)
{ 
  
  foreach($arrays_orig as $key =>$arrayvalues){
	  if(is_array($arrayvalues)){
		  foreach($arrayvalues as $key2 => $array_sub){
	          $arraysdata[$key][$key2]=sanitize_text_field($array_sub);
		  }
	  }else{
		      $arraysdata[$key]= sanitize_text_field($arrayvalues);	  
	  }
	  
  }
  return $arraysdata;
}