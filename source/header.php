<?php
 header("Access-Control-Allow-Origin: https://geetsoft.com");
 header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html__(CFFP_FLIP_TITLE); ?></title>	
<?php
if(isset($_GET['type']) && $_GET['type'] =='edit'){ 
	$type='edit';
	global $wpdb;	
	$fs_id = preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['fs_id']);
	$tablename = $wpdb->prefix.'flip_book_setting';
	$flipbook_setting = $wpdb->get_row( 'SELECT * FROM '.$tablename.' where fs_id ="'.$fs_id.'" ');
	
	if(isset($flipbook_setting->fs_alignment_type) && $flipbook_setting->fs_alignment_type == '1'){ 
		$val='1';
	}else{
		$val='0';
	} 
	if(isset($flipbook_setting->fs_display_type) && $flipbook_setting->fs_display_type == '1'){ 
		$val1='1';
	}else{
		$val1='0';
	} 
}else{
	$type='add';
	$val='0';
	$val1='0';
}
?>	
<script type="text/javascript">
var base_url ="<?php echo esc_html__(trailingslashit(plugins_url('create-flipbook-from-pdf'))); ?>";
var admin_url ="<?php echo esc_html__(admin_url('admin.php')); ?>";
var FAV_COMPILE_ID ='<?php echo esc_html__(FAV_COMPILE_ID); ?>';
var CFFP_ERROR_MSG ='<?php echo esc_html__(CFFP_ERROR_MSG); ?>';
var CFFP_SUCCESS_MSG ='<?php echo esc_html__(CFFP_SUCCESS_MSG); ?>';
var CFFP_SELECT_MSG ='<?php echo esc_html__(CFFP_SELECT_MSG); ?>';
var CFFP_ACCESS_COUNT ='<?php echo esc_html__(CFFP_ACCESS_COUNT); ?>';
var CFFP_PRO_LINK ='<?php echo esc_html__(CFFP_PRO_LINK); ?>';

var type_url ='<?php echo esc_html__($type); ?>';
var val ='<?php echo esc_html__($val); ?>';
var val1 ='<?php echo esc_html__($val1); ?>';

jQuery(document).ready(function () {
	flip_get_option(val1);
	flip_get_current_option(val);
});	
</script>	
<?php if(CFFP_P_TYPE == true){ ?>
<script type="text/javascript">
jQuery(document).ready(function () {
	check_purchase_code();
});	
</script>
<?php } ?>
</head>
<body>
<!-- The Modal -->
<div id="purcahse_code" class="flip_modal" >

  <!-- Modal content -->
  <div class="flip_modal-content">
		<form name="v_frm" id="v_frm" method="POST">
		  <div class="flip_modal-body mx-3">
			<div class="md-form mb-5">
			  <input type="text" required="required" minlength="12" maxlength="12" id="purchase_code" name="purchase_code" placeholder="Purchase code" class="form-control validate">
			</div>
		  </div>
		  <div class="flip_modal-footer d-flex justify-content-center">
			<input type="submit" name="v_code" id="v_code" value="Verify Code">
		  </div>
		  <div id="err_msg"></div>
	  </form>
	</div>
  </div>