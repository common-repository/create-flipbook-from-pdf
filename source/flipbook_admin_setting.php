<?php
class fliperrr_admin_setting extends fliperrr {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'flip_maker_menu' ), 7);
		add_action( 'wp_ajax_flip_book_background', array( $this, 'flip_book_background' ) );	
		add_action( 'wp_ajax_flip_pdf_file_upload', array( $this, 'flip_pdf_file_upload' ) );	
		add_action( 'wp_ajax_flip_purchase_code', array( $this, 'flip_purchase_code' ) );	
		add_action( 'wp_ajax_flip_save_fliperrr_setting', array( $this, 'flip_save_fliperrr_setting' ) );	
		add_action( 'wp_ajax_flip_delete_fliperrr', array( $this, 'flip_delete_fliperrr' ) );	
		add_action( 'wp_ajax_flip_view_fliperrr', array( $this, 'flip_view_fliperrr' ) );	
		add_action( 'wp_ajax_flip_no_access', array( $this, 'flip_no_access' ) );	
	}

	public function flip_maker_menu() {
		$dirPlgUrl  = trailingslashit( plugins_url('create-flipbook-from-pdf') );
		$pageTitle = __( esc_html__('Fliperrr'), esc_html__('Fliperrr') );
		$fliperrrlisting = 'fliperrrlisting';
		$fliperrrsetting = 'fliperrrsetting';
		$plgIcon  = $dirPlgUrl  . 'webroot/images/flip_view.png';
		$dirInc  = $dirPlgUrl  . 'source/';
		
		$mainMenu = add_menu_page( $pageTitle, $pageTitle, 'manage_options', $fliperrrlisting, array( $this, 'flip_book_listing' ),$plgIcon  );

		global $submenu;
		/***********subMenu**********/
		$subSettingMenu = add_submenu_page($fliperrrlisting, __( esc_html__('Create'), esc_html__('fliperrr') ), __( esc_html__('Create'), esc_html__('fliperrr') ),  'manage_options', $fliperrrsetting, array( $this, 'flip_book_setting' ));
	}
	
	/***********optionSource**********/
	public function flip_book_listing(){
		require_once(  'header.php' );
		$dirIncImg  = trailingslashit(plugins_url('create-flipbook-from-pdf'));
		$dirPlgUrl  = trailingslashit( plugins_url('create-flipbook-from-pdf') );
		global $wpdb;
		$tablename = $wpdb->prefix.'flip_book_setting';
		$limit = 12;
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
		$offset = ( $pagenum - 1 ) * $limit;
		if($pagenum>1){
			$i = ((($pagenum-1)*$limit)+1);
		}else{
			$i = 1;
		}
		if(isset($_GET['sortby']) && $_GET['sortby'] !=''){ 
			$sortby = preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['sortby']);
			if($sortby =='dateDESC'){
				$where = "ORDER BY fs_date DESC";
			}elseif($sortby =='dateASC'){
				$where = "ORDER BY fs_date ASC";
			}elseif($sortby =='nameASC'){
				$where = "ORDER BY fs_name ASC";
			}elseif($sortby =='nameDESC'){
				$where = "ORDER BY fs_name DESC";
			}
			$total = $wpdb->get_var( $wpdb->prepare("SELECT count('fs_id') FROM ".$tablename." where  fs_status ='%s' ".$where."", 1) );
			$num_of_pages = ceil( $total / $limit );
			$flip_data = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$tablename." where  fs_status ='%s' AND fs_p_id!='' ".$where." LIMIT $offset, $limit ",1) );
		}else{
			$total = $wpdb->get_var($wpdb->prepare("SELECT count('fs_id') FROM ".$tablename." where  fs_status='%s'", 1) );

			$num_of_pages = ceil( $total / $limit );
			$flip_data = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$tablename." where  fs_status ='%s' AND fs_p_id!='' ORDER BY fs_id DESC LIMIT $offset, $limit ",1) );
		}
		$type = 'add';
	?>	
	<script>
	function frm_submit(){
		jQuery('#frm_sortby').submit();
	}
	</script>
	
	
		<div class="flip_wrap">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="flip_main_box">
							<div class="flip_main_bg">
								<?php require_once( 'nav.php' ); ?>
								<div class="flip_m20">
								</div>
								<div class="flip_box">
									<h1><?php echo esc_attr('my flipbook');  ?></h1>
									
									<form action='<?php echo admin_url('admin.php'); ?>' method='get' id='frm_sortby' name='frm_sortby'>
									<input type='hidden' name='page' id='page' value='fliperrrlisting'>
									<div class="flip_filter">
										<i class="fa fa-filter" aria-hidden="true"></i>
											<select id='sortby' name='sortby' onchange='return frm_submit();'>
												<option <?php if(isset($_GET['sortby']) && $_GET['sortby'] =='dateDESC'){ echo 'selected="selected"'; } ?> value='dateDESC'><?php echo esc_attr('Date : Latest - Oldest'); ?></option>
												<option <?php if(isset($_GET['sortby']) && $_GET['sortby'] =='dateASC'){ echo 'selected="selected"'; } ?> value='dateASC'><?php echo esc_attr('Date : Oldest - Latest'); ?></option>
												<option <?php if(isset($_GET['sortby']) && $_GET['sortby'] =='nameASC'){ echo 'selected="selected"'; } ?> value='nameASC'><?php echo esc_attr('Sort : A - Z'); ?></option>
												<option <?php if(isset($_GET['sortby']) && $_GET['sortby'] =='nameDESC'){ echo 'selected="selected"'; } ?> value='nameDESC'><?php echo esc_attr('Sort : Z - A'); ?></option>
											</select>
									</div>
									</form>
									<div class="flip_create_flip">
										<?php
											if(isset($_SESSION['pc_count']) && $_SESSION['pc_count'] !=''){
												$ACCESS_COUNT = (int)$_SESSION['pc_count'];
											}else{
												$ACCESS_COUNT = (int)CFFP_ACCESS_COUNT;
											}
											if(count($flip_data)<$ACCESS_COUNT){
												
										?>
										<a href='<?php echo admin_url('admin.php?page=fliperrrsetting&type='.$type); ?>'><button type="button" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span><?php echo esc_attr('Create flipbook'); ?></button></a>
										<?php }else{ ?>
										<a href='javascript:void(0);' onclick="return flip_no_access();"><button type="button" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span><?php echo esc_attr('Create flipbook'); ?></button></a>
										<?php } ?>
									</div>
								</div>
								<div id='msg_perview' class="alert alert-success" role="alert"></div>
								<div id='error_dialog_msg'></div>
									<div id='msg_id'></div>
								<div class="flip_listing">
									<ul>
										<?php 
										// echo "<pre>";
										// print_r($_SERVER);
										// echo "</pre>";
										$i=1;
										if(!empty($flip_data)){
											foreach($flip_data as $flip){
												$name = strip_tags($flip->fs_name);
												if(strlen($name)>16){
													$name = substr($name,0,16).'...';
												}
												$fs_id = base64_encode( serialize(esc_attr($flip->fs_p_id)));
												$site_name = FAV_COMPILE_ID.'/flipperrr/';
												$url = $site_name.'view/?v='.$fs_id.'&r='. rand();
									
												
										?>
										<!-- View Modal start -->
										<div class="modal fade flip_view" id="view_widget<?php  echo esc_attr($flip->fs_id); ?>"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
										  <div class="modal-dialog modal-lg" role="document">
											<div class="modal-content">
												<div id='error_dialog_msg1' ></div>
											  <div class="modal-body">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<div class="flip_main_box">
													<div class="flip_main_bg">
														<div class="flip_box">
															<h1><?php echo esc_attr('Widgets'); ?></h1>
														</div>
														<div class="flip_create_flip_box">
															<div class="flip_widget_box">
																<div id="accordion<?php  echo esc_attr($flip->fs_id); ?>">
																		<h3><?php echo esc_attr('Popup Widget'); ?> <small><?php echo esc_attr('Use this widget to open Flipbook in pop up.'); ?></small></h3>
																		<div>
																			<h6><?php echo esc_attr('Place the code anywhere in your webpage.'); ?></h6>
																			<div id='dialog1<?php  echo esc_attr($flip->fs_id); ?>'></div>
																			<textarea onclick='return copy_code<?php  echo esc_attr($flip->fs_id); ?>("1");' readonly='readonly' id='copy_code1<?php  echo esc_attr($flip->fs_id); ?>' name='copy_code1' ><script id='sq-widget-script-id'  sq-iframe-src='<?php echo esc_url($url); ?>' sq-popup-delay='0' src='<?php echo esc_attr($site_name); ?>lib/js/doc-web.js?v=<?php echo rand(); ?>'></script></textarea>
																		</div>
																</div>		
																<?php if(CFFP_P_TYPE==false){ ?>
																	
																	<div class="flip_widget_pro flip_widget_pro1">
																		<div class="flip_pro_overlay flip_pro_overlay_small">
																			<div class="flip_pro_tbl">
																			  <div class="flip_pro_tblcell">
																				<p><i class="fa fa-lock" aria-hidden="true"></i>Upgrade to</p>
																				<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank">Fliperrr Pro</a> </div>
																			</div>
																		 </div>
																		<h3><?php echo esc_html__('Iframe Widget '); ?><small><?php echo esc_html__('Use this widget to open Flipbook in iFrame.'); ?></small></h3>
																	</div>	
																	<div class="flip_widget_pro flip_widget_pro1">
																		<div class="flip_pro_overlay flip_pro_overlay_small">
																			<div class="flip_pro_tbl">
																			  <div class="flip_pro_tblcell">
																				<p><i class="fa fa-lock" aria-hidden="true"></i>Upgrade to</p>
																				<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank">Fliperrr Pro</a> </div>
																			</div>
																		 </div>
																			<h3><?php echo esc_attr('Button Widget '); ?><small><?php echo esc_attr('To generate Button Embed code for your Web page.'); ?></small></h3>
																	</div>	
																	<?php }else{ ?>
																<div id="accordion<?php  echo esc_attr($flip->fs_id); ?>">
																	<h3><?php echo esc_attr('Popup Widget'); ?> <small><?php echo esc_attr('Use this widget to open Flipbook in pop up.'); ?></small></h3>
																	<div>
																		<h6><?php echo esc_attr('Place the code anywhere in your webpage.'); ?></h6>
																		<div id='dialog1<?php  echo esc_attr($flip->fs_id); ?>'></div>
																		<textarea onclick='return copy_code<?php  echo esc_attr($flip->fs_id); ?>("1");' readonly='readonly' id='copy_code1<?php  echo esc_attr($flip->fs_id); ?>' name='copy_code1' ><script id='sq-widget-script-id'  sq-iframe-src='<?php echo esc_url($url); ?>' sq-popup-delay='0' src='<?php echo esc_attr($site_name); ?>lib/js/doc-web.js?v=<?php echo rand(); ?>'></script></textarea>
																	</div>
																
																	<h3><?php echo esc_attr('Iframe Widget '); ?><small><?php echo esc_attr('Use this widget to open Flipbook in iFrame.'); ?></small></h3>
																	<div>
																		<h6><?php echo esc_attr('Place the code anywhere in your webpage.'); ?></h6>
																		<div id='dialog<?php  echo esc_attr($flip->fs_id); ?>'></div>
																		<textarea readonly='readonly' id='copy_code<?php  echo esc_attr($flip->fs_id); ?>' name='copy_code' onclick='return copy_code<?php  echo esc_attr($flip->fs_id); ?>("2");'><iframe style='overflow: hidden;width:100%;height:100vh' class='flip_perview'  src="<?php echo esc_url($url); ?>" frameborder="0"></iframe></textarea>
																	</div>
																	



																	<h3><?php echo esc_attr('Button Widget '); ?><small><?php echo esc_attr('To generate Button Embed code for your Web page.'); ?></small></h3>
																	<div>
																		<h6><?php echo esc_attr('Place the code anywhere in your webpage.'); ?></h6>
																		<div id='dialog3<?php  echo esc_attr($flip->fs_id); ?>'></div>
																		<textarea readonly='readonly' id='copy_code3<?php  echo esc_attr($flip->fs_id); ?>' name='copy_code3' onclick='return copy_code<?php  echo esc_attr($flip->fs_id); ?>("3");'><script id='sq-widget-script-id'  sq-iframe-src='<?php echo esc_url($url); ?>' sq-popup-delay='0' src='<?php echo esc_attr($site_name); ?>lib/js/doc-web-button.js?v=<?php echo rand(); ?>'></script><button style="background: #fdcc00;text-align: center;padding: 8px 22px;font-size: 18px;text-transform: uppercase;color: #7a038f; border-radius: 40px; font-weight: 600; display: block;margin: 0px auto;" id="sq-widget-element-id" type="button" class='flip_perview1' ><p  id="sq-widget-element-id_text"><?php echo esc_attr('Show Flip Book'); ?></p></button></textarea>
																	</div>
																		
																</div>
																<?php } ?>
															</div>
														</div>
													</div>
												</div>
											  </div>
											</div>
										  </div>
										</div>
										<!-- View Modal end -->
										<style>
										#view_widget<?php  echo esc_attr($flip->fs_id); ?> .modal-body {
											position: relative;
											padding: 0;
										}
										#view_widget<?php  echo esc_attr($flip->fs_id); ?> button.close{
											color:#000;
											opacity:1;
										}
										#view_widget<?php  echo esc_attr($flip->fs_id); ?> button.close span{
											font-size:35px;
											color:#000;
											opacity:1;
										}
										</style>
										<li>
										
										
										<div class="flip_hover flip_ehover6">
											<img class="img-responsive" src="<?php echo esc_url($dirIncImg); ?>webroot/flip/cover/flip_design<?php echo esc_attr($i); ?>.jpg" alt=""/>
											<div class="flip_overlay flip_point">
												<small><?php echo date('j M y',strtotime(esc_attr($flip->fs_date))); ?></small>
												<div class="flip_rotate">
													<p class="flip_group1">
														<a href="<?php echo admin_url('admin.php?page=fliperrrsetting&step=2&type=edit&fs_id='.esc_attr($flip->fs_id)); ?>" data-toggle="tooltip" title="Settings"><i class="fa fa-cog" aria-hidden="true"></i></a><a href="javascript:void(0);" onclick="return flip_view_fliperrr('<?php echo esc_attr($flip->fs_p_id); ?>','<?php echo rand(); ?>');" data-toggle="tooltip" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>
													</p>
													<hr>
													<hr>
													<p class="flip_group2">
														<?php if(CFFP_P_TYPE==true){ ?>
														<a href="javascrpt:void(0);" onclick='return flip_delete_fliperrr("<?php echo esc_attr($flip->fs_id); ?>","1");' data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
														<a  href="javascript:void(0);" onclick="return flip_view_widget('<?php echo esc_attr($flip->fs_id); ?>','<?php echo rand(); ?>');" data-toggle="tooltip" title="Widget"><i class="fa fa-code" aria-hidden="true"></i></a>
														<?php }else{ ?>
														<a href="javascrpt:void(0);" onclick='return flip_delete_fliperrr_basic();' data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
														<a  href="javascript:void(0);" onclick="return flip_view_widget('<?php echo esc_attr($flip->fs_id); ?>','<?php echo rand(); ?>');" data-toggle="tooltip" title="Widget"><i class="fa fa-code" aria-hidden="true"></i></a>
														<?php } ?>
													</p>
												</div>
											</div>
										</div>
										<h2><?php echo ucfirst(esc_attr($name)); ?></h2>
										</li>
										<script>



										function copy_code<?php  echo esc_attr($flip->fs_id); ?>(id) {
											
										if(id=='1'){
											var copy_code = document.getElementById("copy_code1<?php  echo esc_attr($flip->fs_id); ?>");
											copy_code.select();
											document.execCommand("copy");
											jQuery( "#dialog1<?php  echo esc_attr($flip->fs_id); ?>").slideDown( 100, function() {
												jQuery("#dialog1<?php  echo esc_attr($flip->fs_id); ?>").animate({ scrollTop: 0 }, 500);
												jQuery("#dialog1<?php  echo esc_attr($flip->fs_id); ?>").show();
												jQuery('#dialog1<?php  echo esc_attr($flip->fs_id); ?>').html('<div class="alert alert-success" role="alert"><?php echo esc_attr('Your text has been copied to Clipboard.'); ?><button type="button" class="close_btn" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
												jQuery( '#dialog1<?php  echo esc_attr($flip->fs_id); ?>').fadeOut( 20000);
											});
										}else if(id=='2'){
											var copy_code = document.getElementById("copy_code<?php  echo esc_attr($flip->fs_id); ?>");
											copy_code.select();
											document.execCommand("copy");
											jQuery( "#dialog<?php  echo esc_attr($flip->fs_id); ?>").slideDown( 100, function() {
												jQuery("#dialog<?php  echo esc_attr($flip->fs_id); ?>").animate({ scrollTop: 0 }, 500);
												jQuery("#dialog<?php  echo esc_attr($flip->fs_id); ?>").show();
												jQuery('#dialog<?php  echo esc_attr($flip->fs_id); ?>').html('<div class="alert alert-success" role="alert"><?php echo esc_attr('Your text has been copied to Clipboard.'); ?><button type="button" class="close_btn" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
												jQuery( '#dialog<?php  echo esc_attr($flip->fs_id); ?>').fadeOut( 20000);
											});
										}else if(id=='3'){
											var copy_code = document.getElementById("copy_code3<?php  echo esc_attr($flip->fs_id); ?>");
											copy_code.select();
											document.execCommand("copy");
											jQuery( "#dialog3<?php  echo esc_attr($flip->fs_id); ?>").slideDown( 100, function() {
												jQuery("#dialog3<?php  echo esc_attr($flip->fs_id); ?>").animate({ scrollTop: 0 }, 500);
												jQuery("#dialog3<?php  echo esc_attr($flip->fs_id); ?>").show();
												jQuery('#dialog3<?php  echo esc_attr($flip->fs_id); ?>').html('<div class="alert alert-success" role="alert"><?php echo esc_attr('Your text has been copied to Clipboard.'); ?><button type="button" class="close_btn" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
												jQuery( '#dialog3<?php  echo esc_attr($flip->fs_id); ?>').fadeOut( 20000);
											});
										}
										}
										</script>
										<script>
										  jQuery( function() {
											jQuery( "#accordion<?php  echo esc_attr($flip->fs_id); ?>" ).accordion({
											  heightStyle: "content"
											});
										  } );
										 </script> 
										<?php $i++;}} else{ ?>
										<p class='flip_norow'><?php echo esc_attr(' No record.'); ?></p>
										<?php } ?>
									</ul>
								</div>
								<?php $page_links = paginate_links( array(
									'base' => add_query_arg( 'pagenum', '%#%' ),
									'format' => '',
									'prev_text' => __( '<span aria-hidden="true">&larr;</span>', 'aag' ),
									'next_text' => __( '<span aria-hidden="true">&rarr;</span>', 'aag' ),
									'total' => esc_attr($num_of_pages),
									'current' => esc_attr($pagenum)
								) );

								if ( $page_links ) { ?>
									
									<div class="flip_tablenav" id="flip_tablenav"><div class="flip_tablenav-pages"><?php echo $page_links; ?></div></div>
									
									
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		
		<!-- View Modal start -->
		<div class="modal fade" id="view_flip"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
			<div id='error_dialog_msg1' ></div>
			
			  <div class="modal-body">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				
				<iframe id='model_cls'  src="" frameborder="0"></iframe>
			  </div>
			</div>
		  </div>
		</div>
		
		<!-- View Modal end -->
		
	
		
		<!-- main_box end -->
	<?php
		require_once( 'footer.php' );
	}
	
	/***********createoptionSource**********/
	public function flip_book_setting(){
		require_once(  'header.php' );
		$dirIncImg  = trailingslashit(plugins_url('create-flipbook-from-pdf'));
		
		
	?>	
		<!-- main_box start -->
		<div class="flip_wrap">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="flip_main_box">
							<div class="flip_main_bg">
								<?php require_once( 'nav.php' ); 
								if(isset($_GET['type']) && $_GET['type'] =='edit'){ 
									$type='edit';
								}else{
									$type='add';
								}
								
								if(isset($_GET['type']) && $_GET['type'] =='edit'){ 
								
									global $wpdb;	
									$tablename = $wpdb->prefix.'flip_book_setting';
									$fs_id = preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['page']);
									$fliperrr_setting = $wpdb->get_row( $wpdb->prepare('SELECT * FROM '.$tablename.' where fs_id ="%s" ',$fs_id));
									
									if(isset($_GET['step']) && $_GET['step'] =='2'){
										
									
								?>
								<!--Step2-->
									<div class="flip_box">
										<h1><?php echo esc_attr('Choose Background'); ?></h1>
									</div>
									<div class="flip_tabs">
										<?php require_once( 'nav_step.php' ); ?>
									</div>
									
									<div id='error_dialog_msg5' ></div>
									<div id='msg_id' ></div>
									<div class="flip_create_flip_box">
										<div class="flip_next_page flip_top_btn_page">
											<a href="<?php echo admin_url('admin.php?page=fliperrrlisting'); ?>"  ><?php echo esc_attr('Cancel'); ?> &raquo;</a>&nbsp;
											<a href="javascript:void(0)" onclick='return flip_book_background("<?php echo esc_attr($_GET['fs_id']); ?>");'><?php echo esc_attr('next'); ?> &raquo;</a>
										</div>
										<div class="flip_choose_bg">
											<ul>
												<?php 
													for($i=1;$i<=40;$i++){
												?>
												<li>
													
													<?php 
													if(CFFP_P_TYPE == false){
													if($i>10){ ?>
													<div class="flip_pro_overlay">
														<div class="flip_pro_tbl">
															<div class="flip_pro_tblcell">
																<p>
																	<i class="fa fa-lock" aria-hidden="true"></i>Upgrade to
																</p>
																<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank">Fliperrr Pro</a>
															</div>
														</div>
													</div>
													<?php }} ?>
													<div class="flip_hover flip_ehover">
													  <input type="radio"  id="radioBg<?php echo esc_attr($i); ?>" name="radioBg" value="bg<?php echo esc_attr($i); ?>" <?php if(!empty($fliperrr_setting) && $fliperrr_setting->fs_desgin == 'bg'.$i ){ echo 'checked="checked"';  } ?>>
													  <label <?php if(!empty($fliperrr_setting) && $fliperrr_setting->fs_desgin == 'bg'.$i ){ echo 'class="active"';  } ?> for="radioBg<?php echo esc_attr($i); ?>"><img src="<?php echo esc_url($dirIncImg); ?>webroot/flip/background/small/flip_bg<?php echo esc_attr($i); ?>.jpg" alt="" /></label>
													 
													</div>
												 </li>
												<?php } ?>
											</ul>
										</div>
										<div class="flip_next_page">
											<a href="<?php echo admin_url('admin.php?page=fliperrrlisting'); ?>"  ><?php echo esc_attr('Cancel'); ?> &raquo;</a>&nbsp;
											<a href="javascript:void(0)" onclick='return flip_book_background("<?php echo esc_attr($_GET['fs_id']); ?>");'><?php echo esc_attr('next'); ?> &raquo;</a>
										</div>
									</div>
								
								<?php }elseif(isset($_GET['step']) && $_GET['step'] =='3'){ ?>
								<!--Step3--->
								<div class="flip_box">
										<h1><?php echo esc_attr('Settings'); ?></h1>
									</div>
									
									<div class="flip_tabs">
										<?php require_once( 'nav_step.php' ); ?>
									</div>
									<div id='error_dialog_msg'></div>
									<div id='msg_id' ></div>
									<form id='frm_setting' name='frm_setting' action='' method='POST' enctype="multipart/form-data">
									<input type="hidden" name='fs_id' id='fs_id' value='<?php echo esc_attr($_GET['fs_id']); ?>'> 
									<div class="flip_create_flip_box">
									<div class="flip_next_page flip_top_btn_page">
											<a href="<?php echo admin_url('admin.php?page=fliperrrlisting'); ?>"  ><?php echo esc_attr('Cancel'); ?> &raquo;</a>&nbsp;
											<a href="javascript:void(0)" onclick='return flip_save_fliperrr_setting("<?php echo esc_attr('edit'); ?>");'><?php echo esc_attr('next'); ?> &raquo;</a>
										</div>
									
										<div class="flip_setting_box flip_setting_box1">
								<h1><?php echo esc_attr('Toolbar Options'); ?></h1>
								<div class="flip_tool_opt">
									<ul>
										<li>
										<h6><?php echo esc_attr('Show Full View Option'); ?></h6>
										<label class='flip_toggle-label'>
										<input type='checkbox' name='fs_full_option' id='fs_full_option' value='1' <?php if(isset($fliperrr_setting->fs_full_option) && $fliperrr_setting->fs_full_option == '1'){ echo "checked='checked'"; } ?> />
										<span class='flip_back'>
										<span class='flip_toggle'></span>
										<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
										<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
										</span>
										</label>
										</li>
										<li>
										<h6><?php echo esc_attr('Show Sound Option'); ?></h6>
										<label class='flip_toggle-label'>
										<input type='checkbox' name='fs_sound_option' id='fs_sound_option' value='1' <?php if(isset($fliperrr_setting->fs_sound_option) && $fliperrr_setting->fs_sound_option == '1'){ echo "checked='checked'"; } ?> />
										<span class='flip_back'>
										<span class='flip_toggle'></span>
										<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
										<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
										</span>
										</label>
										</li>
										<li>
										<?php if(CFFP_P_TYPE==false){ ?>
										<div class="flip_pro_overlay flip_pro_overlay_small">
										<div class="flip_pro_tbl">
												<div class="flip_pro_tblcell">
													<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank"><i class="fa fa-lock" aria-hidden="true"></i>Upgrade</a>
												</div>
											</div>
										</div>
										<?php } ?>
										<h6><?php echo esc_attr('Show Thumbnail Panel'); ?></h6>
										<label class='flip_toggle-label'>
										<input type='checkbox'  name='fs_thumbnail_option' id='fs_thumbnail_option' value='1' <?php if(isset($fliperrr_setting->fs_thumbnail_option) && $fliperrr_setting->fs_thumbnail_option == '1'){ echo "checked='checked'"; } ?>/>
										<span class='flip_back'>
										<span class='flip_toggle'></span>
										<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
										<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
										</span>
										</label>
										</li>
										<li>
										
										<h6><?php echo esc_attr('Show Print PDF Option'); ?></h6>
										<label class='flip_toggle-label'>
										<input type='checkbox' name='fs_print_option' id='fs_print_option' value='1' <?php if(isset($fliperrr_setting->fs_print_option) && $fliperrr_setting->fs_print_option == '1'){ echo "checked='checked'"; } ?>/>
										<span class='flip_back'>
										<span class='flip_toggle'></span>
										<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
										<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
										</span>
										</label>
										</li>
										<li>
										<?php if(CFFP_P_TYPE==false){ ?>
										<div class="flip_pro_overlay flip_pro_overlay_small">
										<div class="flip_pro_tbl">
												<div class="flip_pro_tblcell">
													<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank"><i class="fa fa-lock" aria-hidden="true"></i>Upgrade</a>
												</div>
											</div>
										</div>
										<?php } ?>
										<h6><?php echo esc_attr('Show Smart Pan Option'); ?></h6>
										<label class='flip_toggle-label'>
										<input type='checkbox'  name='fs_smart_option' id='fs_smart_option' value='1'  <?php if(isset($fliperrr_setting->fs_smart_option) && $fliperrr_setting->fs_smart_option == '1'){ echo "checked='checked'"; } ?>/>
										<span class='flip_back'>
										<span class='flip_toggle'></span>
										<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
										<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
										</span>
										</label>
										</li>
										<li>
										<h6><?php echo esc_attr('Show Zoom Options'); ?></h6>
										<label class='flip_toggle-label'>
										<input type='checkbox'  name='fs_zoom_option' id='fs_zoom_option' value='1' <?php if(isset($fliperrr_setting->fs_zoom_option) && $fliperrr_setting->fs_zoom_option == '1'){ echo "checked='checked'"; } ?>/>
										<span class='flip_back'>
										<span class='flip_toggle'></span>
										<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
										<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
										</span>
										</label>
										</li>
									</ul>
								</div>
							</div>
							<div class="flip_setting_box flip_setting_box2">
								<h1><?php echo esc_attr('Toolbar Display Settings'); ?></h1>
								<div class="flip_tool_opt">
									<div class="flip_tool_color">
										<?php if(CFFP_P_TYPE==false){ ?>
										<div class="flip_pro_overlay flip_pro_overlay_small">
										<div class="flip_pro_tbl">
												<div class="flip_pro_tblcell">
													<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank"><i class="fa fa-lock" aria-hidden="true"></i>Upgrade</a>
												</div>
											</div>
										</div>
										<?php } ?>
										<h3><?php echo esc_attr('Color'); ?> </h3>
										<input type="color" value="<?php if(isset($fliperrr_setting->fs_color) && $fliperrr_setting->fs_color != ''){ echo esc_attr($fliperrr_setting->fs_color); }else{ echo "#000000"; } ?>" name='fs_color' id='fs_color' >
									</div>
									<div class="flip_tool_align flip_funkyradio">
										<h3><?php echo esc_attr('Alignment'); ?></h3>
										<ul>
											<li>
											<div class="flip_funkyradio-default">
												<input  onclick='return flip_get_current_option(0);' type="radio" id="fs_alignment_type1" name='fs_alignment_type' value='0' <?php if(isset($fliperrr_setting->fs_alignment_type) && $fliperrr_setting->fs_alignment_type == '0'){ echo "checked='checked'"; } ?>>
												<label for="fs_alignment_type1"><?php echo esc_attr('Horizontal'); ?></label>
											</div>
											<div class="flip_horizontalOptions" id="horizontalOptions">
												<div>
												<input type="radio" class="alg_cls" id="fs_alignment_postion1"   name='fs_alignment_postion' value="top" <?php if(isset($fliperrr_setting->fs_alignment_postion) && $fliperrr_setting->fs_alignment_postion == 'top'){ echo "checked='checked'"; } ?> />
												<label for="fs_alignment_postion1"><?php echo esc_attr('Top'); ?></label>
												</div>
												<br />
												<div>
												<input type="radio"   class="alg_cls" id="fs_alignment_postion2" name='fs_alignment_postion' value="bottom"  <?php if(isset($fliperrr_setting->fs_alignment_postion) && $fliperrr_setting->fs_alignment_postion == 'bottom'){ echo "checked='checked'"; } ?> />
												<label for="fs_alignment_postion2"><?php echo esc_attr('Bottom'); ?></label>
												</div>
											</div>
											</li>
											<li>
											<div class="flip_funkyradio-default">
												<input type="radio"   onclick='return flip_get_current_option(1);' type="radio" id="fs_alignment_type2" name='fs_alignment_type' value='1' <?php if(isset($fliperrr_setting->fs_alignment_type) && $fliperrr_setting->fs_alignment_type == '1'){ echo "checked='checked'"; } ?>>
												<label for="fs_alignment_type2"><?php echo esc_attr('Vertical'); ?></label>
											</div>
											<div class="flip_verticalOptions" id="verticalOptions">
												<div>
												<input type="radio" class="alg_cls" id="fs_alignment_postion3"  name='fs_alignment_postion' value="left" <?php if(isset($fliperrr_setting->fs_alignment_postion) && $fliperrr_setting->fs_alignment_postion == 'left'){ echo "checked='checked'"; } ?>/>
												<label for="fs_alignment_postion3"><?php echo esc_attr('Left'); ?></label>
												</div>
												<br />
												<div>
												<input type="radio" class="alg_cls" id="fs_alignment_postion4" name='fs_alignment_postion' value="right" <?php if(isset($fliperrr_setting->fs_alignment_postion) && $fliperrr_setting->fs_alignment_postion == 'right'){ echo "checked='checked'"; } ?>/>
												<label for="fs_alignment_postion4"><?php echo esc_attr('Right'); ?></label>
												</div>
											</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="flip_setting_box flip_setting_box3">
								<?php if(CFFP_P_TYPE==false){ ?>
								<div class="flip_pro_overlay flip_pro_overlay_small">
								<div class="flip_pro_tbl">
										<div class="flip_pro_tblcell">
											<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank"><i class="fa fa-lock" aria-hidden="true"></i>Upgrade</a>
										</div>
									</div>
								</div>
								<?php } ?>
								<h1><?php echo esc_attr('Theme Display Settings'); ?></h1>
								<div class="flip_tool_opt">
									<h3><?php echo esc_attr('Background'); ?></h3>
									<ul class="flip_funkyradio">
										<li>
										<div class="flip_funkyradio-default">
											<input type="radio" onclick="return flip_get_option(0);" id="fs_display_type"  name='fs_display_type' value='0' <?php if(isset($fliperrr_setting->fs_display_type) && $fliperrr_setting->fs_display_type == '0'){ echo "checked='checked'"; } ?> />
											<label for="fs_display_type"><?php echo esc_attr('Solid Color'); ?></label>
										</div>
										<div class="flip_selectColor">
											<input type="color" id="fs_display_color"  name='fs_display_color' value="<?php if(isset($fliperrr_setting->fs_display_color) && $fliperrr_setting->fs_display_color != ''){ echo esc_attr($fliperrr_setting->fs_display_color); }else{ echo "#000000"; } ?>">
										</div>
										</li>
										<li>
										<div class="flip_funkyradio-default">
											<input type="radio" onclick="return flip_get_option(1);" id="fs_display_type1" name='fs_display_type' value='1' <?php if(isset($fliperrr_setting->fs_display_type) && $fliperrr_setting->fs_display_type == '1'){ echo "checked='checked'"; } ?>>
											<label for="fs_display_type1"><?php echo esc_attr('Background Image'); ?></label>
										</div>
										<div id="bgimageOptions">
											<h4><?php echo esc_attr('Template Default'); ?></h4>
											<label class='flip_toggle-label'>
											<input type='checkbox' id="fs_template" name='fs_template' value='1' <?php if(isset($fliperrr_setting->fs_template) && $fliperrr_setting->fs_template != ''){ echo 'checked="checked"'; } ?>/>
											<span class='flip_back'>
											<span class='flip_toggle'></span>
											<span class='flip_label flip_on'><?php echo esc_attr('YES'); ?></span>
											<span class='flip_label flip_off'><?php echo esc_attr('NO'); ?></span>
											</span>
											</label>
										</div>
										</li>
									</ul>
									<div class="flip_set_opac">
										<h3 class="flip_set_head"><?php echo esc_attr('Default Screen Lighting'); ?><span id="trans_val"></span></h3>
										<input  ng-model="project.appConfig.styling.fs_screen" type="range" id="fs_screen" name='fs_screen' min="0"  max="100" step="10" class="ng-valid ng-dirty ng-valid-parse ng-touched" value='<?php if(isset($fliperrr_setting->fs_screen) && $fliperrr_setting->fs_screen != ''){ echo esc_attr($fliperrr_setting->fs_screen); }else{ echo"10"; } ?>'>
									</div>
								</div>
							</div>
										<div class="flip_next_page">
											<a href="<?php echo admin_url('admin.php?page=fliperrrlisting'); ?>"  ><?php echo esc_attr('Cancel'); ?> &raquo;</a>&nbsp;
											<a href="javascript:void(0)" onclick='return flip_save_fliperrr_setting("<?php echo esc_attr('edit'); ?>");'><?php echo esc_attr('next'); ?> &raquo;</a>
										</div>
									</div>
									</form>
								<?php }elseif(isset($_GET['step']) && $_GET['step'] =='4'){ ?>
									<div class="flip_box">
										<h1><?php echo esc_attr('Finished'); ?></h1>
									</div>
									<div class="flip_tabs">
										<?php require_once( 'nav_step.php' ); ?>
									</div>
									<div id='error_dialog_msg'></div>
									<div id='msg_id'></div>
									<div class="flip_create_flip_box">
										<div class="flip_finish_box">
											<h4><?php echo esc_attr('Yay!!'); ?></h4>
											<p>
											<?php if(isset($_GET['type']) && $_GET['type'] =='edit'){ ?>
												<strong><?php echo esc_attr('Your marvelous fliperrr has been updated succesfully.'); ?></strong>
											<?php }else{ ?>
												<strong><?php echo esc_attr('Your marvelous fliperrr has been created succesfully.'); ?></strong>
											<?php } ?>
											</p>
											<a href="<?php echo admin_url('admin.php?page=fliperrrlisting'); ?>"><i class="fa fa-book" aria-hidden="true"></i>&nbsp; &nbsp;<?php echo esc_attr('Go to my fliperrrs'); ?></a>
										</div>
									</div>
								<?php } ?>
								
								<?php }elseif(isset($_GET['fs_id']) && $_GET['fs_id'] !=''){ 
								
								if(isset($_GET['step']) && $_GET['step'] =='2'){
									$pluginList = get_option( 'active_plugins' );
									$plugin = 'fliperrr/fliperrr.php'; 
									if ( in_array( $plugin , $pluginList ) ) {
										global $wpdb;
										$tablename = $wpdb->prefix.'flip_book_setting';
										$total = $wpdb->get_var( $wpdb->prepare("SELECT count('*') FROM ".$tablename) );
										if($total==1){
											if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
												$sRoot = $_SERVER['DOCUMENT_ROOT'];
												$fp = fopen($sRoot.'/.htaccess','a+');
												if($fp)
												{
							
													fwrite($fp,'
													Header add Access-Control-Allow-Origin "https://geetsoft.com"
													Header add Access-Control-Allow-Methods: "GET,POST,OPTIONS,DELETE,PUT"');
													fclose($fp);
													//echo "First time";
												}
											}
										}
									}
								?>
							<!--Step2-->
									<div class="flip_box">
										<h1><?php echo esc_attr('Choose Background'); ?></h1>
									</div>
									<div class="flip_tabs">
										<?php require_once( 'nav_step.php' ); ?>
									</div>
									
									<div id='error_dialog_msg5' ></div>
									<div id='msg_id'></div>
									
									<div class="flip_create_flip_box">
										<div class="flip_next_page flip_top_btn_page">
											<a href="javascript:void(0)" onclick='return flip_delete_fliperrr("<?php echo esc_attr($_GET['fs_id']); ?>","0");' ><?php echo esc_attr('Cancel'); ?> &raquo;</a>&nbsp;
											<a href="javascript:void(0)" onclick='return flip_book_background("<?php echo esc_attr($_GET['fs_id']); ?>");'><?php echo esc_attr('next'); ?> &raquo;</a>
										</div>
										<div class="flip_choose_bg">
											<ul>
												<?php 
													for($i=1;$i<=40;$i++){
												?>
												<li>
													<?php 
													if(CFFP_P_TYPE == false){
													if($i>10){ ?>
													<div class="flip_pro_overlay">
														<div class="flip_pro_tbl">
															<div class="flip_pro_tblcell">
																<p>
																	<i class="fa fa-lock" aria-hidden="true"></i>Upgrade to
																</p>
																<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank">Fliperrr Pro</a>
															</div>
														</div>
													</div>
													<?php }} ?>
													<div class="flip_hover flip_ehover">
													  <input type="radio"  id="radioBg<?php echo esc_attr($i); ?>" name="radioBg" value="bg<?php echo esc_attr($i); ?>" <?php if(!empty($fliperrr_setting) && $fliperrr_setting->fs_desgin == 'bg'.$i ){ echo 'checked="checked"';  } ?>>
													  <label <?php if(!empty($fliperrr_setting) && $fliperrr_setting->fs_desgin == 'bg'.$i ){ echo 'class="active"';  } ?> for="radioBg<?php echo esc_attr($i); ?>"><img src="<?php echo esc_url($dirIncImg); ?>webroot/flip/background/small/flip_bg<?php echo esc_attr($i); ?>.jpg" alt="" /></label>
													 
													</div>
												 </li>
												<?php } ?>
											</ul>
										</div>
										<div class="flip_next_page">
										<a href="javascript:void(0)" onclick='return flip_delete_fliperrr("<?php echo esc_attr($_GET['fs_id']); ?>","0");' ><?php echo esc_attr('Cancel'); ?> &raquo;</a>&nbsp;
											<a href="javascript:void(0)" onclick='return flip_book_background("<?php echo esc_attr($_GET['fs_id']); ?>");'><?php echo esc_attr('next'); ?> &raquo;</a>
										</div>
									</div>
								
								<?php }elseif(isset($_GET['step']) && $_GET['step'] =='3'){ ?>
								<!--Step3--->
								<div class="flip_box">
										<h1><?php echo esc_attr('Settings'); ?></h1>
									</div>
									
								<div class="flip_tabs">
										<?php require_once( 'nav_step.php' ); ?>
									</div>
									<div id='error_dialog_msg'></div>
									<div id='msg_id'></div>
									<form id='frm_setting' name='frm_setting' action='' method='POST' enctype="multipart/form-data">
									<input type="hidden" name='fs_id' id='fs_id' value='<?php echo esc_attr($_GET['fs_id']); ?>'> 
									<div class="flip_create_flip_box">
										<div class="flip_next_page flip_top_btn_page">
											<a href="javascript:void(0)" onclick='return flip_delete_fliperrr("<?php echo esc_attr($_GET['fs_id']); ?>","0");' ><?php echo esc_attr('Cancel'); ?> &raquo;</a>&nbsp;<a href="javascript:void(0)" onclick='return flip_save_fliperrr_setting("<?php echo esc_attr('add'); ?>");'><?php echo esc_attr('next'); ?> &raquo;</a>
										</div>
										<div class="flip_setting_box flip_setting_box1">
											<h1><?php echo esc_attr('Toolbar Options'); ?></h1>
											<div class="flip_tool_opt">
												<ul>
													<li>
													<h6><?php echo esc_attr('Show Full View Option'); ?></h6>
													<label class='flip_toggle-label'>
													<input type='checkbox' name='fs_full_option' id='fs_full_option' value='1' />
													<span class='flip_back'>
													<span class='flip_toggle'></span>
													<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
													<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
													</span>
													</label>
													</li>
													<li>
													<h6><?php echo esc_attr('Show Sound Option'); ?></h6>
													<label class='flip_toggle-label'>
													<input type='checkbox' name='fs_sound_option' id='fs_sound_option' value='1' />
													<span class='flip_back'>
													<span class='flip_toggle'></span>
													<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
													<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
													</span>
													</label>
													</li>
													<li>
													<?php if(CFFP_P_TYPE==false){ ?>
													<div class="flip_pro_overlay flip_pro_overlay_small">
													<div class="flip_pro_tbl">
															<div class="flip_pro_tblcell">
																<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank"><i class="fa fa-lock" aria-hidden="true"></i>Upgrade</a>
															</div>
														</div>
													</div>
													<?php } ?>
													<h6><?php echo esc_attr('Show Thumbnail Panel'); ?></h6>
													<label class='flip_toggle-label'>
													<input type='checkbox'  name='fs_thumbnail_option' id='fs_thumbnail_option' value='1'/>
													<span class='flip_back'>
													<span class='flip_toggle'></span>
													<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
													<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
													</span>
													</label>
													</li>
													<li>
													<h6><?php echo esc_attr('Show Print PDF Option'); ?></h6>
													<label class='flip_toggle-label'>
													<input type='checkbox' name='fs_print_option' id='fs_print_option' value='1' />
													<span class='flip_back'>
													<span class='flip_toggle'></span>
													<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
													<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
													</span>
													</label>
													</li>
													
													<li>
													<?php if(CFFP_P_TYPE==false){ ?>
													<div class="flip_pro_overlay flip_pro_overlay_small">
													<div class="flip_pro_tbl">
															<div class="flip_pro_tblcell">
																<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank"><i class="fa fa-lock" aria-hidden="true"></i>Upgrade</a>
															</div>
														</div>
													</div>
													<?php } ?>
													<h6><?php echo esc_attr('Show Smart Pan Option'); ?></h6>
													<label class='flip_toggle-label'>
													<input type='checkbox'  name='fs_smart_option' id='fs_smart_option' value='1'  />
													<span class='flip_back'>
													<span class='flip_toggle'></span>
													<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
													<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
													</span>
													</label>
													</li>
													<li>
													<h6><?php echo esc_attr('Show Zoom Options'); ?></h6>
													<label class='flip_toggle-label'>
													<input type='checkbox'  name='fs_zoom_option' id='fs_zoom_option' value='1' />
													<span class='flip_back'>
													<span class='flip_toggle'></span>
													<span class='flip_label flip_on'><?php echo esc_attr('ON'); ?></span>
													<span class='flip_label flip_off'><?php echo esc_attr('OFF'); ?></span>
													</span>
													</label>
													</li>
												</ul>
											</div>
										</div>
										<div class="flip_setting_box flip_setting_box2">
											<h1><?php echo esc_attr('Toolbar Display Settings'); ?></h1>
											<div class="flip_tool_opt">
												<div class="flip_tool_color">
													<?php if(CFFP_P_TYPE==false){ ?>
													<div class="flip_pro_overlay flip_pro_overlay_small">
													<div class="flip_pro_tbl">
															<div class="flip_pro_tblcell">
																<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank"><i class="fa fa-lock" aria-hidden="true"></i>Upgrade</a>
															</div>
														</div>
													</div>
													<?php } ?>
													<h3><?php echo esc_attr('Color'); ?></h3>
													<input type="color" value="#000000" name='fs_color' id='fs_color' >
												</div>
												<div class="flip_tool_align flip_funkyradio">
													<h3><?php echo esc_attr('Alignment'); ?></h3>
													<ul>
														<li>
														<div class="flip_funkyradio-default">
															<input  onclick='return flip_get_current_option(0);' type="radio" id="fs_alignment_type1" name='fs_alignment_type' value='0' checked='checked'>
															<label for="fs_alignment_type1"><?php echo esc_attr('Horizontal'); ?></label>
														</div>
														<div class="flip_horizontalOptions" id="horizontalOptions">
															<div>
															<input type="radio" class="alg_cls" id="fs_alignment_postion1"   name='fs_alignment_postion' value="top" />
															<label for="fs_alignment_postion1"><?php echo esc_attr('Top'); ?></label>
															</div>
															<br />
															<div>
															<input type="radio"   class="alg_cls" id="fs_alignment_postion2" name='fs_alignment_postion' value="bottom"  checked='checked' />
															<label for="fs_alignment_postion2"><?php echo esc_attr('Bottom'); ?></label>
															</div>
														</div>
														</li>
														<li>
														<div class="flip_funkyradio-default">
															<input type="radio"   onclick='return flip_get_current_option(1);' type="radio" id="fs_alignment_type2" name='fs_alignment_type' value='1'>
															<label for="fs_alignment_type2"><?php echo esc_attr('Vertical'); ?></label>
														</div>
														<div class="flip_verticalOptions" id="verticalOptions">
															<div>
															<input type="radio" class="alg_cls" id="fs_alignment_postion3"  name='fs_alignment_postion' value="left" />
															<label for="fs_alignment_postion3"><?php echo esc_attr('Left'); ?></label>
															</div>
															<br />
															<div>
															<input type="radio" class="alg_cls" id="fs_alignment_postion4" name='fs_alignment_postion' value="right" />
															<label for="fs_alignment_postion4"><?php echo esc_attr('Right'); ?></label>
															</div>
														</div>
														</li>
													</ul>
												</div>
											</div>
										</div>
										<div class="flip_setting_box flip_setting_box3">
											<?php if(CFFP_P_TYPE==false){ ?>
													<div class="flip_pro_overlay flip_pro_overlay_small">
													<div class="flip_pro_tbl">
															<div class="flip_pro_tblcell">
																<a href="<?php echo CFFP_PRO_LINK; ?>" target="_blank"><i class="fa fa-lock" aria-hidden="true"></i>Upgrade</a>
															</div>
														</div>
													</div>
													<?php } ?>
											<h1><?php echo esc_attr('Theme Display Settings'); ?></h1>
											<div class="flip_tool_opt">
												<h3><?php echo esc_attr('Background'); ?></h3>
												<ul class="flip_funkyradio">
													<li>
													<div class="flip_funkyradio-default">
														<input type="radio" onclick="return flip_get_option(0);" id="fs_display_type"  name='fs_display_type' value='0' checked='checked' />
														<label for="fs_display_type"><?php echo esc_attr('Solid Color'); ?></label>
													</div>
													<div class="flip_selectColor">
														<input type="color" id="fs_display_color"  name='fs_display_color' value="#000000">
													</div>
													</li>
													<li>
													<div class="flip_funkyradio-default">
														<input type="radio" onclick="return flip_get_option(1);" id="fs_display_type1" name='fs_display_type' value='1'checked="checked">
														<label for="fs_display_type1"><?php echo esc_attr('Background Image'); ?></label>
													</div>
													<div id="bgimageOptions">
														<h4><?php echo esc_attr('Template Default'); ?></h4>
														<label class='flip_toggle-label'>
														<input type='checkbox' id="fs_template" name='fs_template' value='1' checked="checked"/>
														<span class='flip_back'>
														<span class='flip_toggle'></span>
														<span class='flip_label flip_on'><?php echo esc_attr('YES'); ?></span>
														<span class='flip_label flip_off'><?php echo esc_attr('NO'); ?></span>
														</span>
														</label>
													</div>
													</li>
												</ul>
												<div class="flip_set_opac">
													<h3 class="flip_set_head"><?php echo esc_attr('Default Screen Lighting'); ?><span id="trans_val"></span></h3>
													<input  ng-model="project.appConfig.styling.fs_screen" type="range" id="fs_screen" name='fs_screen' min="0"  max="100" step="10" class="ng-valid ng-dirty ng-valid-parse ng-touched" value='10'>
												</div>
											</div>
										</div>
										<div class="flip_next_page">
											<a href="javascript:void(0)" onclick='return flip_delete_fliperrr("<?php echo esc_attr($_GET['fs_id']); ?>","0");' ><?php echo esc_attr('Cancel'); ?> &raquo;</a>&nbsp;<a href="javascript:void(0)" onclick='return flip_save_fliperrr_setting("<?php echo esc_attr('add'); ?>");'><?php echo esc_attr('next'); ?> &raquo;</a>
										</div>
										
									</div>
									</form>
								<?php }elseif(isset($_GET['step']) && $_GET['step'] =='4'){ ?>
									<div class="flip_box">
										<h1><?php echo esc_attr('Finished'); ?></h1>
									</div>
									<div class="flip_tabs">
										<?php require_once( 'nav_step.php' ); ?>
									</div>
									<div id='error_dialog_msg'></div>
									<div id='msg_id'></div>
									<div class="flip_create_flip_box">
										<div class="flip_finish_box">
											<h4><?php echo esc_attr('Yay!!!'); ?></h4>
											<p>
												<strong><?php echo esc_attr('Your marvelous fliperrr has been created succesfully.'); ?></strong>
											</p>
											<a href="<?php echo admin_url('admin.php?page=fliperrrlisting'); ?>"><i class="fa fa-book" aria-hidden="true"></i>&nbsp; &nbsp;<?php echo esc_attr('Go to my fliperrrs'); ?></a>
										</div>
									</div>
								<?php } ?>
						<?php }else{ ?>
							<!--Step1-->
								<div class="flip_m20">
								</div>
								<div class="flip_box">
									<h1><?php echo esc_attr('Upload File'); ?></h1>
								</div>
								<div class="flip_tabs">
									<?php require_once( 'nav_step.php' ); ?>
								</div>
								<div class="flip_create_flip_box">
								<div id='msg_perview' class="alert alert-success" role="alert"></div>
								<div class="flip_file-upload">
									<form action="" class="dropzone" id="DropZoneFiddle" enctype="multipart/form-data">
										<div class="flip_image-upload-wrap">
											<div class="flip_drag-text">
												<i class="fa fa-file-text-o" aria-hidden="true"></i>
											</div>
										</div>
									</form>
									<div class="flip_or1">
										<small><?php echo esc_attr('OR'); ?></small>
									</div>
									<form class="flip_pdf_file_upload" id='pdf_file_upload' method='post' action='' enctype="multipart/form-data">
									<input name='type' id='type' type='hidden' value='upload'/>
									<div class="flip_upl_btn">
										<button class="flip_up_btn" type="button" onclick="jQuery('.flip_file-upload-input').trigger( 'click' )"><i class="fa fa-upload" aria-hidden="true"></i> &nbsp; <?php echo esc_attr('Upload a file...'); ?></button>
										<div class="flip_image-upload-wrap">
										<input class="flip_file-upload-input" name='f_upload' id='f_upload' type='file' onchange="flip_upload_pdf(this);" accept="application/pdf, application/vnd.ms-excel"/>
										</div>
									</div>
									</form>
								</div>
							</div>
							<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- main_box end -->
		<script type="text/javascript">
		var trans_slider = document.getElementById("fs_screen");
		var trans_output = document.getElementById("trans_val");
		trans_output.innerHTML = trans_slider.value;

		trans_slider.oninput = function() {
		  trans_output.innerHTML = this.value;
		}
		</script>
		
	<?php
		require_once( 'footer.php' );
	}	
	
	/***********settingSource**********/
	public function flip_book_settings(){
		require_once(  'header.php' );
		$dirIncImg  = trailingslashit(plugins_url('create-flipbook-from-pdf'));
		echo 'gsdgsdg';
		require_once( 'footer.php' );

	}
	/***********AjaxRequest**********/
	public function  flip_purchase_code(){
			global $wpdb;
			$post = preg_replace('/[^-a-zA-Z0-9_]/', '', $_REQUEST);
			$ip = filter_var($_POST['ip'], FILTER_SANITIZE_STRING);
			
			$client_website = site_url();
			function get_domain($url){
			  $pieces = parse_url($url);
			  $domain = isset($pieces['host']) ? $pieces['host'] : '';
			  if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,10})$/i', $domain, $regs)) {
				function isLetter($domain_name) {
				  return preg_match('/^\s*[a-z,A-Z]/', $domain_name) > 0;
				}
				if(isLetter($regs['domain'])){
					 return strtolower($regs['domain']);
				}else{
					 return strtolower("com_".$regs['domain']);			
				}
			  }
			  return false;
			}
			$client_domain_name = get_domain($client_website);
			if($post['type'] == 'exit_domain' ){
				
				$data =array(
					"val"=>filter_var($_POST['type'], FILTER_SANITIZE_STRING),
					"website"=>$client_domain_name
				);
				function eflipRequestCode($data,$ip){
					set_time_limit(300);
					$fields = '';
					foreach ($data as $key => $value) {
						$fields .= $key . '=' . $value . '&';
					}
					$fields = rtrim($fields, '&');
					// $post = curl_init();
					// curl_setopt($post, CURLOPT_URL,$ip);
					// curl_setopt($post, CURLOPT_VERBOSE, 0);  
					// curl_setopt($post, CURLOPT_RETURNTRANSFER, true);
					// curl_setopt($post, CURLOPT_SSL_VERIFYHOST, false);
					// curl_setopt($post, CURLOPT_SSL_VERIFYPEER, false);
					// curl_setopt($post, CURLOPT_CONNECTTIMEOUT, 10);
					// curl_setopt($post, CURLOPT_TIMEOUT, 300);
					// $agent = 'Mozilla/5.0 (X11; U; Linux x86_64; pl-PL; rv:1.9.2.22) Gecko/20110905 Ubuntu/10.04 (lucid) Firefox/3.6.22';
					// if(!empty($_SERVER['HTTP_USER_AGENT'])){
						// $agent = $_SERVER['HTTP_USER_AGENT'];
					// }
					// curl_setopt($post, CURLOPT_USERAGENT, $agent);
					// curl_setopt($post, CURLOPT_FAILONERROR, 1);
					// curl_setopt($post, CURLOPT_POST, count($data));
					// curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
					// $res = curl_exec($post);
					// $result = json_decode($res, true);
					// $code = curl_getinfo($post, CURLINFO_HTTP_CODE);
					// $success = ($code == 200);
					// curl_close($post);
					
					if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
						$response = wp_remote_get( $ip.'?'.$fields , array('sslverify' => TRUE));
					}else{
						$response = wp_remote_get( $ip.'?'.$fields , array('sslverify' => FALSE));
					}
					$result = wp_remote_retrieve_body( $response );
					$result = json_decode($result);
					
					if ($result->msg=='notexit') {
						if (!session_id()) {
							session_start();
						}
						$_SESSION['pc_count']=$result->pc_count;
						$response = array('status' => '0', 'msg' => 'open');
					}elseif ($result->msg=='exit' ) {
						global $wpdb;
						if (!session_id()) {
							session_start();
						}
						$_SESSION['pc_count']=$result->pc_count;
						$response = array('status' => '1', 'msg' => 'close');
					}
					echo json_encode($response);
					return;
				}
				eflipRequestCode($data,$ip);
				exit();
			}else{
				$data =array(
					"val"=>filter_var($_POST['type'], FILTER_SANITIZE_STRING),
					"purchase_code"=>filter_var($_POST['purchase_code'], FILTER_SANITIZE_STRING),
					"website"=>$client_domain_name
				);
				function eflipRequestCode($data,$ip){
					set_time_limit(300);
					$fields = '';
					foreach ($data as $key => $value) {
						$fields .= $key . '=' . $value . '&';
					}
					$fields = rtrim($fields, '&');
					// $post = curl_init();
					// curl_setopt($post, CURLOPT_URL,$ip);
					// curl_setopt($post, CURLOPT_VERBOSE, 0);  
					// curl_setopt($post, CURLOPT_RETURNTRANSFER, true);
					// curl_setopt($post, CURLOPT_SSL_VERIFYHOST, false);
					// curl_setopt($post, CURLOPT_SSL_VERIFYPEER, false);
					// curl_setopt($post, CURLOPT_CONNECTTIMEOUT, 10);
					// curl_setopt($post, CURLOPT_TIMEOUT, 300);
					// $agent = 'Mozilla/5.0 (X11; U; Linux x86_64; pl-PL; rv:1.9.2.22) Gecko/20110905 Ubuntu/10.04 (lucid) Firefox/3.6.22';
					// if(!empty($_SERVER['HTTP_USER_AGENT'])){
						// $agent = $_SERVER['HTTP_USER_AGENT'];
					// }
					// curl_setopt($post, CURLOPT_USERAGENT, $agent);
					// curl_setopt($post, CURLOPT_FAILONERROR, 1);
					// curl_setopt($post, CURLOPT_POST, count($data));
					// curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
					// $res = curl_exec($post);
					// $result = json_decode($res, true);
					// $code = curl_getinfo($post, CURLINFO_HTTP_CODE);
					// $success = ($code == 200);
					// curl_close($post);
					if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
						$response = wp_remote_get( $ip.'?'.$fields , array('sslverify' => TRUE));
					}else{
						$response = wp_remote_get( $ip.'?'.$fields , array('sslverify' => FALSE));
					}
					
					$result = wp_remote_retrieve_body( $response );
					$result = json_decode($result);
					if ($result->msg=='notvalid') {
						if (!session_id()) {
							session_start();
						}
						$_SESSION['pc_count']=$result->pc_count;
						$response = array('status' => '0', 'msg' => 'Your purchase code not valid. Please try again');
					}elseif ($result->msg=='valid' ) {
						if (!session_id()) {
							session_start();
						}
						$_SESSION['pc_count']=$result->pc_count;
						$response = array('status' => '1', 'msg' => 'Your purchase code valid.');
					}
					echo json_encode($response);
					return;
				}
				eflipRequestCode($data,$ip);
				exit();
			}
			
			
	}
	public function  flip_pdf_file_upload(){
			global $wpdb;
			$p  = trailingslashit( plugin_dir_path( __FILE__ ) );
			$plugin_path = str_replace('source/', '', $p);
			
			$type = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
			
			if(isset($type) && $type=='upload'){
			//	echo $f_name = filter_var($_FILES['f_upload'], FILTER_SANITIZE_STRING);
				$f_name=$_FILES['f_upload'];
				$file_name = $f_name['name'];
				$file_arr = explode('.pdf',$file_name);
			}else{
				$f_name = filter_var($_FILES['file'], FILTER_SANITIZE_STRING);
				$file_name = $f_name['name'];
				$file_arr = explode('.pdf',$file_name);
			}	
			$tablename = $wpdb->prefix.'flip_book_setting';
			$data = array(
				'fs_file_name'=>$file_name,
				'fs_name'=>$file_arr[0],
				'fs_date'=>date('Y-m-d'),
				'fs_display_type'=>'1',
				'fs_status'=>'0'
			);	
			$date = date('Y-m-d');
			$f_name_val = $file_arr[0];
			
			$wpdb->query( $wpdb->prepare( 
				"INSERT INTO ".$wpdb->prefix."flip_book_setting
					( fs_file_name, fs_name, fs_date ,fs_display_type,fs_status )
					VALUES ( %s, %s, %s,%s, %s )
				", 
				$file_name, 
				$file_arr[0], 
				date('Y-m-d'), 
				'1', 
				'1' 
			) );
			
			// print_r($f_name['error']);
			// print_r($_FILES);
			// echo "<br/>";
			  $sid = $wpdb->insert_id;
			if($sid>0){
				//echo $f_name['error'];
				 if ( $f_name['error'] === UPLOAD_ERR_OK ) {
					// echo "hello";
					if ( !file_exists($plugin_path."/upload") ) {
					mkdir($plugin_path."/upload", 0777);
					}
					@chmod($plugin_path."/upload", 0777);

					if ( !file_exists($plugin_path."/upload/".$sid) ) {
					mkdir($plugin_path."/upload/".$sid, 0777);
					}
					@chmod($plugin_path."/upload/".$sid, 0777);
					 $pdf_path = $plugin_path."/upload/".$sid.'/'.$file_name;
					move_uploaded_file($f_name["tmp_name"], $pdf_path );
				}
				echo esc_attr('1~~~'.$sid);
			}else{
				echo esc_attr('0~~~0');
			}
			exit();
	}
	public function  flip_book_background(){
			global $wpdb;
			$p  = trailingslashit( plugin_dir_path( __FILE__ ) );
			$fs_id = filter_var($_POST['fs_id'], FILTER_SANITIZE_STRING);
			$d = filter_var($_POST['d'], FILTER_SANITIZE_STRING);
			$tablename = $wpdb->prefix.'flip_book_setting';
			$data = array(
				'fs_desgin'=>$d
			);
			$where = array(
				'fs_id'=>$fs_id
			);
			
			$wpdb->query($wpdb->prepare("UPDATE $tablename SET fs_desgin='$d' WHERE fs_id= %d", $fs_id));
			
			echo esc_attr("1~~~".$fs_id);
			exit();
	}
	
	function flip_save_fliperrr_setting(){
		global $wpdb;
		$p  = trailingslashit( plugin_dir_path( __FILE__ ) );
		$fs_id = filter_var($_POST['fs_id'], FILTER_SANITIZE_STRING);
		$val =  filter_var($_POST['val'], FILTER_SANITIZE_STRING);
		$tablename = $wpdb->prefix.'flip_book_setting';
		
		$where = array(
			'fs_id'=>$fs_id
		);
		
		$fs_full_option = filter_var($_POST['fs_full_option'], FILTER_SANITIZE_STRING);
		$fs_sound_option = filter_var($_POST['fs_sound_option'], FILTER_SANITIZE_STRING);
		$fs_thumbnail_option = filter_var($_POST['fs_thumbnail_option'], FILTER_SANITIZE_STRING);
		$fs_print_option =filter_var($_POST['fs_print_option'], FILTER_SANITIZE_STRING);
		$fs_smart_option = filter_var($_POST['fs_smart_option'], FILTER_SANITIZE_STRING);
		$fs_zoom_option = filter_var($_POST['fs_zoom_option'], FILTER_SANITIZE_STRING);
		$fs_color = filter_var($_POST['fs_color'], FILTER_SANITIZE_STRING);
		$fs_screen = filter_var($_POST['fs_screen'], FILTER_SANITIZE_STRING);
		$fs_alignment_type = filter_var($_POST['fs_alignment_type'], FILTER_SANITIZE_STRING);
		$fs_alignment_postion= filter_var($_POST['fs_alignment_postion'], FILTER_SANITIZE_STRING);
		$fs_display_type = filter_var($_POST['fs_display_type'], FILTER_SANITIZE_STRING);
		
		$wpdb->query($wpdb->prepare("UPDATE $tablename SET fs_full_option='$fs_full_option',fs_sound_option='$fs_sound_option',fs_thumbnail_option='$fs_thumbnail_option',fs_print_option='$fs_print_option',fs_smart_option='$fs_smart_option',fs_zoom_option='$fs_zoom_option',fs_color='$fs_color',fs_screen='$fs_screen',fs_alignment_type='$fs_alignment_type',fs_alignment_postion='$fs_alignment_postion',fs_display_type='$fs_display_type' WHERE fs_id= %d", $fs_id));
		
		
		
		
		if($fs_display_type=='0'){
			$fs_display_color = filter_var($_POST['fs_display_color'], FILTER_SANITIZE_STRING);
			$wpdb->query($wpdb->prepare("UPDATE $tablename SET fs_display_color='$fs_display_color' WHERE fs_id= %d", $fs_id));
		}else{
			$wpdb->query($wpdb->prepare("UPDATE $tablename SET fs_template='$fs_template' WHERE fs_id= %d", $fs_id));
		}
		
		//cURL Data
		$get_data = $wpdb->get_row( $wpdb->prepare('SELECT * FROM '.$tablename.' where fs_id ="%s" ',$fs_id));
		
		$countthem = $wpdb->get_var("SELECT COUNT(*) FROM $tablename");


		$dirPlgUrl  = trailingslashit( plugins_url('create-flipbook-from-pdf') );
		
		$admin_email = get_option('admin_email');
		
		$client_website = site_url();
		$pieces = parse_url($client_website);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		
		$server_info = array(
			's_host' => urlencode($_SERVER['HTTP_HOST']),
			's_ssl' => urlencode($_SERVER['HTTPS']),
			's_addr' => urlencode($_SERVER['SERVER_ADDR']),
			's_url' => urlencode($_SERVER['REQUEST_URI']),
		
		);
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {  
			$url = "https://";   
		}
		else  {
			$url = "http://";  
		}		 
		$url.= $_SERVER['HTTP_HOST']; 
		$dirPlgUrl = str_replace($url,"",$dirPlgUrl);
		$curl_data = array(
			'fs_u_id'=>$fs_id,
			'fs_p_id'=>$get_data->fs_p_id,
			'fs_file_name'=>$get_data->fs_file_name,
			'fs_name'=>$get_data->fs_name,
			'fs_full_option'=>$get_data->fs_full_option,
			'fs_sound_option'=>$get_data->fs_sound_option,
			'fs_thumbnail_option'=>$get_data->fs_thumbnail_option,
			'fs_print_option'=>$get_data->fs_print_option,
			'fs_smart_option'=>$get_data->fs_smart_option,
			'fs_zoom_option'=>$get_data->fs_zoom_option,
			'fs_color'=>urlencode($get_data->fs_color),
			'fs_screen'=>$get_data->fs_screen,
			'fs_date'=>$get_data->fs_date,
			'fs_desgin'=>$get_data->fs_desgin,
			'fs_display_type'=>$get_data->fs_display_type,
			'fs_template'=>$get_data->fs_template,
			'fs_alignment_postion'=>$get_data->fs_alignment_postion,
			'fs_display_color'=>$get_data->fs_display_color,
			'fs_display_background'=>$get_data->fs_display_background,
			'fs_bg_option'=>$get_data->fs_bg_option,
			'fs_alignment_type'=>$fs_alignment_type,
			'server_info'=>json_encode($server_info),
			'plugin_path'=>urlencode($dirPlgUrl),
			'val'=>filter_var($_POST['val'], FILTER_SANITIZE_STRING),
			'admin_email'=>$admin_email
		);
		
			// echo "<pre>";
			// print_r($server_info);
			 // echo "</pre>";
		$ip = filter_var($_POST['ip'], FILTER_SANITIZE_STRING);
	
		function eflipRequest($data,$fs_id,$ip){
			set_time_limit(300);
			$fields = '';
			foreach ($data as $key => $value) {
				$fields .= $key . '=' . $value . '&';
			}
			 $fields = rtrim($fields, '&');
			//echo  $ip.'?'.$fields ;
			if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
				//echo "ssl";
				$response = wp_remote_get( $ip.'?'.$fields);
			}else{
				$response = wp_remote_get( $ip.'?'.$fields , array('sslverify' => FALSE));
			}
		
			$result = wp_remote_retrieve_body( $response );
			$result = json_decode($result, true);
			// echo "<pre>";
			// print_r($response);
			 // echo "</pre>";
		
			 // echo $result->msg;
			if ($result['msg']=='error') {
				 $str = "0~~~".$fs_id;			
			}elseif ($result['msg']=='add' || $result['msg']=='edit') {
				global $wpdb;
				$tablename = $wpdb->prefix.'flip_book_setting';
				$id = $result['id'];
				//echo "UPDATE $tablename SET fs_p_id='$id',fs_status='1' WHERE fs_id= '$fs_id'";
				$wpdb->query($wpdb->prepare("UPDATE $tablename SET fs_p_id='$id',fs_status='1' WHERE fs_id= %d", $fs_id));

				$str = "1~~~".$fs_id;	
			
				
			}
			
			echo esc_attr($str);
			return;
		}
		eflipRequest($curl_data,$fs_id,$ip);
		exit();
		
	}
	
	public function flip_delete_fliperrr(){ 
		global $wpdb;
		$tablename = $wpdb->prefix.'flip_book_setting';
		$fs_id =  filter_var($_POST['fs_id'], FILTER_SANITIZE_STRING);
		$val =  filter_var($_POST['val'], FILTER_SANITIZE_STRING);
		$ip = filter_var($_POST['ip'], FILTER_SANITIZE_STRING);
		
		$fliperrr_setting = $wpdb->get_row( $wpdb->prepare('SELECT * FROM '.$tablename.' where fs_id ="%s" ',$fs_id));
		
		$p  = trailingslashit( plugin_dir_path( __FILE__ ) );	
		$plugin_path = str_replace('source/', '', $p);
		@unlink($plugin_path.'upload/'.$fliperrr_setting->fs_id.'/'.$fliperrr_setting->fs_file_name);
		@rmdir($plugin_path.'upload/'.$fliperrr_setting->fs_id);
	
		$curl_data = array(
			'val'=>$val,
			'fs_p_id'=>$fliperrr_setting->fs_p_id
		);
		function flip_eflipRequest($data,$fs_id,$ip){
				set_time_limit(300);
				$fields = '';
				foreach ($data as $key => $value) {
					$fields .= $key . '=' . $value . '&';
				}
				$fields = rtrim($fields, '&');
				// $post = curl_init();
				// curl_setopt($post, CURLOPT_URL,$ip);
				// curl_setopt($post, CURLOPT_VERBOSE, 0);  
				// curl_setopt($post, CURLOPT_RETURNTRANSFER, true);
				// curl_setopt($post, CURLOPT_SSL_VERIFYHOST, false);
				// curl_setopt($post, CURLOPT_SSL_VERIFYPEER, false);
				// curl_setopt($post, CURLOPT_CONNECTTIMEOUT, 10);
				// curl_setopt($post, CURLOPT_TIMEOUT, 300);
				// $agent = 'Mozilla/5.0 (X11; U; Linux x86_64; pl-PL; rv:1.9.2.22) Gecko/20110905 Ubuntu/10.04 (lucid) Firefox/3.6.22';
				// if(!empty($_SERVER['HTTP_USER_AGENT'])){
					// $agent = $_SERVER['HTTP_USER_AGENT'];
				// }
				// curl_setopt($post, CURLOPT_USERAGENT, $agent);
				// curl_setopt($post, CURLOPT_FAILONERROR, 1);
				// curl_setopt($post, CURLOPT_POST, count($data));
				// curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
				// $res = curl_exec($post);
				// $result = json_decode($res, true);
				// $code = curl_getinfo($post, CURLINFO_HTTP_CODE);
				// $success = ($code == 200);
				//curl_close($post);
				if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
					$response = wp_remote_get( $ip.'?'.$fields , array('sslverify' => TRUE));
				}else{
					$response = wp_remote_get( $ip.'?'.$fields , array('sslverify' => FALSE));
				}
				
				$result = wp_remote_retrieve_body( $response );
				$result = json_decode($result);
				if ($result->msg=='error') {
					 $str = "0~~~".$fs_id;			
				}elseif ($result->msg=='delete') {
					$str = "1~~~".$fs_id;
				}
				echo esc_attr($str);
				return;
				// if ($result['msg']=='error') {
					 // $str = "0~~~".$fs_id;			
				// }elseif ($result['msg']=='delete') {
					 // $str = "1~~~".$fs_id;
				// }
				// echo esc_attr($str);
				// return;
			}
			flip_eflipRequest($curl_data,$fs_id,$ip);
		
		$wpdb->query($wpdb->prepare("DELETE FROM $tablename WHERE fs_id = %d",$fs_id));
		exit();
	}
	public function flip_view_fliperrr(){ 
		global $wpdb;
		$tablename = $wpdb->prefix.'flip_book_setting';
		$fs_id =  filter_var($_POST['fs_id'], FILTER_SANITIZE_STRING);
		$val =  filter_var($_POST['val'], FILTER_SANITIZE_STRING);
		$ip = filter_var($_POST['ip'], FILTER_SANITIZE_STRING);
		function flip_vieweflipRequest($data,$fs_id,$ip){
				set_time_limit(300);
				$fields = '';
				foreach ($data as $key => $value) {
					$fields .= $key . '=' . $value . '&';
				}
				$fields = rtrim($fields, '&');
				// $post = curl_init();
				// curl_setopt($post, CURLOPT_URL,$ip);
				// curl_setopt($post, CURLOPT_VERBOSE, 0);  
				// curl_setopt($post, CURLOPT_RETURNTRANSFER, true);
				// curl_setopt($post, CURLOPT_SSL_VERIFYHOST, false);
				// curl_setopt($post, CURLOPT_SSL_VERIFYPEER, false);
				// curl_setopt($post, CURLOPT_CONNECTTIMEOUT, 10);
				// curl_setopt($post, CURLOPT_TIMEOUT, 300);
				// $agent = 'Mozilla/5.0 (X11; U; Linux x86_64; pl-PL; rv:1.9.2.22) Gecko/20110905 Ubuntu/10.04 (lucid) Firefox/3.6.22';
				// if(!empty($_SERVER['HTTP_USER_AGENT'])){
					// $agent = $_SERVER['HTTP_USER_AGENT'];
				// }
				// curl_setopt($post, CURLOPT_USERAGENT, $agent);
				// curl_setopt($post, CURLOPT_FAILONERROR, 1);
				// curl_setopt($post, CURLOPT_POST, count($data));
				// curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
				// $res = curl_exec($post);
				// $result = json_decode($res, true);
				// $code = curl_getinfo($post, CURLINFO_HTTP_CODE);
				// $success = ($code == 200);
				// curl_close($post);
				if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
					$response = wp_remote_get( $ip.'?'.$fields , array('sslverify' => TRUE));
				}else{
					$response = wp_remote_get( $ip.'?'.$fields , array('sslverify' => FALSE));
				}
				$result = wp_remote_retrieve_body( $response );
				$result = json_decode($result);
				// echo "<pre>";
			// print_r($response);
			 // echo "</pre>";
		
				if ($result->msg=='error') {
					 $str = "0~~~".$result->path;			
				}elseif ($result->msg=='view') {
					$fs_id = base64_encode( serialize($data['v']));
					$str = "1~~~".$fs_id;	
				}
				echo esc_attr($str);
				return;

			}
		$dirPlgUrl  = trailingslashit( plugins_url('create-flipbook-from-pdf') );	
		$curl_data = array(
			'v'=>$fs_id,
			'val'=>$val,
			//'plugin_path'=>$dirPlgUrl
		);	
		flip_vieweflipRequest($curl_data,$fs_id,$ip);
		exit();
	}
	
	
}
new fliperrr_admin_setting();