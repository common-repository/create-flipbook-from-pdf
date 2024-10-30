"use strict";
if("undefined"==typeof jQuery)throw new Error("Plugin flipperrr required JQuery");
var c =0;
jQuery(document).ready(function(){
	jQuery('#view_flip').on("hide.bs.modal", function() {
		jQuery('#model_cls').removeAttr('src');
	})
	
	 jQuery(".close").click(function () {
          jQuery(".modal").modal("hide");
       }); 
	var v_frm = jQuery('#v_frm'),err_msg = jQuery('#v_frm #err_msg'),submit_btn=jQuery("#v_frm #v_code");
	v_frm.on("click", function(){
		var purchase_code = jQuery('#purchase_code').val();
		if(purchase_code ==''){
			jQuery("#v_frm #purchase_code").focus();
			jQuery("#v_frm #purchase_code").css("border", "1px solid red");
			return false;
		}else if(purchase_code.length < '12' || purchase_code.length > '12'){
			jQuery("#v_frm #purchase_code").focus();
			jQuery("#v_frm #purchase_code").css("border", "1px solid red");
			if(c == 0){
				c =1;
				jQuery("#v_frm #purchase_code").after("<span style='color:red;'>Maximum number is 12.</span>");
			}
			
			return false;
			
		}
		err_msg.html('Please wait...');
		err_msg.css('color', "green");
		submit_btn.css('opacity', '.5');
		submit_btn.prop('disabled', true);
		
		var formdata = new FormData(v_frm[0]);
		formdata.append('action', 'flip_purchase_code');
		formdata.append('type', 'check_code');
		formdata.append('ip', FAV_COMPILE_ID+'/flipperrr/');
		jQuery.ajax({
			url: ajaxurl,
			method: "POST",
			dataType: "json",
			cache: false,
			contentType: false,
			processData: false,
			data: formdata
		}).always(function (response) {
		submit_btn.prop('disabled', false);
		submit_btn.css('opacity', '1');
			if (response.status == '0') {
				err_msg.css('color', "red");
				err_msg.html(response.msg);
			}else {
				jQuery('#purcahse_code').modal('hide');
			}
		});
	});
	
});

var FlipPlugin = {
	show_loading:function() {
		jQuery("body").append("<div class='flip_ajax_overlay'></div><div class='flip_ajax_load'><img src='"+base_url+"webroot/images/flip_LoaderIcon.gif' /></div>");
		jQuery('.flip_ajax_overlay, .flip_ajax_load').show();
	},
	hide_loading:function() {
		jQuery('.flip_ajax_load, .flip_ajax_overlay').hide();
	}
}


var sendDataObject = {
	'action': 'flip_pdf_file_upload',
	'do_action': 'uploadFile'
};
function check_purchase_code(){
	
	var formdata = "ip="+FAV_COMPILE_ID+'/flipperrr/'+"&type=exit_domain&action=flip_purchase_code";
		jQuery.ajax({
			url: ajaxurl,
			method: "POST",
			dataType: "json",
			data: formdata
		}).always(function (response) {
			if (response.status == '0') {
				jQuery('#purcahse_code').modal({
					backdrop: 'static',
					keyboard: false
				});
			}
		});
	
	
}

jQuery(function() {
  Dropzone.options.DropZoneFiddle = {
  url: ajaxurl,
  params: sendDataObject,
  paramName: "file", //the parameter name containing the uploaded file
  clickable: true,
  maxFilesize: 40, //in mb
  uploadMultiple: false, 
  autoProcessQueue: true,
  maxFiles: 1, // allowing any more than this will stress a basic php/mysql stack
  addRemoveLinks: true,
  acceptedFiles: '.pdf', //allowed filetypes
  dictDefaultMessage: "Drag & Drop file here", //override the default text
  uploadprogress: function(file, progress, bytesSent) {
	  FlipPlugin.show_loading();
	},
  init: function() {
	this.on("sending", function(file, xhr, formData) {
	 
	});
	this.on("success", function(file, responseText) {
		var str_arr = responseText.split('~~~');
		if(str_arr[0]=='1'){
			window.location.href = admin_url+'?page=fliperrrsetting&type='+type_url+'&step=2&fs_id='+str_arr[1];
		}
	});
	this.on("addedfile", function(file){
		
	});
  }
};
});

function flip_no_access(){
	jQuery("html, body").animate({scrollTop: jQuery('#msg_perview').offset().top }, 500);
	jQuery("#msg_perview").animate({ scrollTop: 0 }, 500);
	jQuery("#msg_perview").show();
	jQuery("#msg_perview").addClass('alert alert-success');
	jQuery("#msg_perview").css('text-align','center');
	jQuery('#msg_perview').html('In order to create more Fliperrr please <a href="'+CFFP_PRO_LINK+'" target="_blank">upgrade to PRO</a>.');
	jQuery('#msg_perview').fadeOut(20000);
	return false;
  
}

function flip_delete_fliperrr_basic(){
	jQuery("html, body").animate({scrollTop: jQuery('#msg_perview').offset().top }, 500);
	jQuery("#msg_perview").animate({ scrollTop: 0 }, 500);
	jQuery("#msg_perview").show();
	jQuery("#msg_perview").addClass('alert alert-success');
	jQuery("#msg_perview").css('text-align','center');
	jQuery('#msg_perview').html('Please <a href="'+CFFP_PRO_LINK+'" target="_blank">upgrade to PRO</a>');
	jQuery('#msg_perview').fadeOut(20000);
	return false;
  
}

function flip_upload_pdf(input) {
 jQuery('.flip_file-upload-input').trigger( 'click' );
  
	var size_val = (jQuery('#f_upload')[0].files[0].size / 1024);
	var size_val = (Math.round((size_val / 1024) * 100) / 100);
	var compare_size ='40';
  
	if(size_val > compare_size){ //104857600000000
	 jQuery("html, body").animate({scrollTop: jQuery('#msg_perview').offset().top }, 500);
	jQuery("#msg_perview").animate({ scrollTop: 0 }, 500);
		jQuery("#msg_perview").show();
		jQuery("#msg_perview").addClass('alert alert-success');
		jQuery("#msg_perview").css('text-align','center');
		jQuery('#msg_perview').html('Please upload maximum '+compare_size+' file.');
		jQuery('#msg_perview').fadeOut(20000);
	return false;
  }
  var ajaxData = new FormData(jQuery('#pdf_file_upload')[0]);
	ajaxData.append('action', 'flip_pdf_file_upload');
	ajaxData.append('type', 'upload');
	jQuery.ajax({
		beforeSend: function(){ 
			FlipPlugin.show_loading();
		},
		complete: function(){   
			FlipPlugin.hide_loading();
		},
		type: "POST",
		url: ajaxurl, 
		data: ajaxData,
		cache: false,
		contentType: false,
		processData: false,
		success: function( str ) {	
			var str_arr = str.split('~~~');
			//alert(str_arr);
			//console.log(str_arr);
			if(str_arr[0]=='1'){
				window.location.href = admin_url+'?page=fliperrrsetting&step=2&type='+type_url+'&fs_id='+str_arr[1];
			}else if(str_arr[0]=='\n1'){
				window.location.href = admin_url+'?page=fliperrrsetting&step=2&type='+type_url+'&fs_id='+str_arr[1];
			}
			
		}
	});	
	
	
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
		  jQuery('.image-upload-wrap').hide();
		  jQuery('#file-upload-image').attr('src', 'webroot/images/pdf_icon.png');
		  jQuery('.file-upload-content').show();
		  jQuery('.image-title').html(input.files[0].name);
		};
		reader.readAsDataURL(input.files[0]);
	  } else {
		flip_remove_pdf();
	  }
 
}

function flip_remove_pdf() {
  jQuery('.flip_file-upload-input').replaceWith(jQuery('.flip_file-upload-input').clone());
  jQuery('.flip_file-upload-content').hide();
  jQuery('.flip_image-upload-wrap').show();
}
jQuery('.flip_image-upload-wrap').bind('dragover', function () {
	jQuery('.flip_image-upload-wrap').addClass('flip_image-dropping');
});
jQuery('.flip_image-upload-wrap').bind('dragleave', function () {
	jQuery('.flip_image-upload-wrap').removeClass('flip_image-dropping');
});

function flip_book_background(fs_id){
		var d =    jQuery("input[name='radioBg']:checked").val();
		if(d=='' || typeof(d) =='undefined' ){
			jQuery("html, body").animate({scrollTop: jQuery('.flip_main_box').offset().top }, 500);
			jQuery( ".flip_main_box").slideDown( 100, function() {
				jQuery("#error_dialog_msg5").animate({ scrollTop: 0 }, 500);
				jQuery("#error_dialog_msg5").show();
				jQuery("#error_dialog_msg5").addClass('alert alert-success');
				jQuery('#error_dialog_msg5').html(CFFP_SELECT_MSG);
				jQuery('#error_dialog_msg').fadeOut(15000);
			});
			return false;
		}
		
		var params = "d="+d+"&fs_id="+fs_id+"&action=flip_book_background";
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: params,
			beforeSend: function(){
				FlipPlugin.show_loading();
			},
			complete: function(){ 
				FlipPlugin.hide_loading();
			},
			success: function(str){
				jQuery('#error_dialog_msg').show();
				var str_arr = str.split('~~~');
				if(str_arr[0]==1){
					window.location.href = admin_url+'?page=fliperrrsetting&step=3&type='+type_url+'&fs_id='+str_arr[1];
				}else{
					jQuery("html, body").animate({scrollTop: jQuery('#error_dialog_msg').offset().top }, 500);
					 jQuery("#error_dialog_msg").addClass('alert alert-success');
					 jQuery("#error_dialog_msg").html(CFFP_ERROR_MSG);
					 setTimeout(function(){jQuery('#error_dialog_msg').fadeOut("slow", function () {});}, 2500);
					 setTimeout(function(){ location.reload(); },3000);
				}
				
			}
		 });	
	}

function flip_get_option(id){
	if(id == '0'){
		jQuery('#bgimageOptions').hide();
		jQuery('#solidcolorOptions').show();
	}else{
		jQuery('#solidcolorOptions').hide();
		jQuery('#bgimageOptions').show();
	}
}

function flip_get_current_option(id){
	if(id == '0'){
		jQuery('#verticalOptions').hide();
		jQuery('#horizontalOptions').show();
	}else{
		jQuery('#horizontalOptions').hide();
		jQuery('#verticalOptions').show();
	}
}

jQuery(document).ready(function () {
	jQuery('#radioHorizontal').change(function() {
		jQuery('#horizontalOptions').toggle();
	});
	jQuery('#radioVertical').change(function() {
		jQuery('#verticalOptions').toggle();
	});
});



function flip_view_widget(fs_id,val){
	setTimeout(function(){ jQuery('#view_widget'+fs_id).modal('show'); }, 50);
	
}

function flip_view_fliperrr(fs_id,val){
	var ip = FAV_COMPILE_ID+'/flipperrr/view/function.php';
	var params = "fs_id="+fs_id+"&val="+val+"&action=flip_view_fliperrr&ip="+ip;
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: params,
		beforeSend: function(){
			FlipPlugin.show_loading();
		},
		complete: function(){ 
			FlipPlugin.hide_loading();
		},
		success: function(str){
          console.log(str) ;
			jQuery('#error_dialog_msg1').show();
			var str_arr = str.split('~~~');
			if(str_arr[0]==1){
				
				var f_url = FAV_COMPILE_ID+"/flipperrr/view/?v="+str_arr[1]+"&r="+val;
                 console.log(f_url) ;
				jQuery("#model_cls").attr("src", f_url);
				setTimeout(function(){ jQuery('#view_flip').modal('show'); }, 50);
			}else{
				jQuery("html, body").animate({scrollTop: jQuery('#error_dialog_msg1').offset().top }, 500);
				 jQuery("#error_dialog_msg1").addClass('alert alert-success');
				 jQuery("#error_dialog_msg1").html(CFFP_ERROR_MSG);
				 setTimeout(function(){jQuery('#error_dialog_msg1').fadeOut("slow", function () {});}, 2500);
			}
		}
	 });
	
}

function flip_view_widget(fs_id){
	jQuery('#view_widget'+fs_id).modal('show');
}
function flip_save_fliperrr_setting(val){
	var ajaxData = new FormData(jQuery('#frm_setting')[0]);
	ajaxData.append('action', 'flip_save_fliperrr_setting');
	ajaxData.append('val', val);
	ajaxData.append('ip', FAV_COMPILE_ID+'/flipperrr/');
	//ajaxData.append('ip', 'https://www.digitekprinting.com/flip.php/');
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: ajaxData,
		contentType: false,
		processData: false,
		beforeSend: function(){
			FlipPlugin.show_loading();
		},
		complete: function(){ 
			FlipPlugin.hide_loading();
		},
		success: function(str){
			jQuery('#error_dialog_msg').show();
			var str_arr = str.split('~~~');
			//alert(str_arr);
			if(str_arr[0]==1){
				window.location.href = admin_url+'?page=fliperrrsetting&step=4&type='+type_url+'&fs_id='+str_arr[1];
			}else{
				 jQuery("html, body").animate({scrollTop: jQuery('#error_dialog_msg').offset().top }, 500);
				jQuery("#error_dialog_msg").addClass('alert alert-success');
				 jQuery("#error_dialog_msg").html('In order to create more Fliperrr please <a href="https://codecanyon.net/item/fliperrr-creates-flipbook-from-any-pdf-in-just-1-click-and-1-minute/23800969" target="_blank">upgrade to PRO</a>');
				// setTimeout(function(){jQuery('#error_dialog_msg').fadeOut("slow", function () {});}, 2500);
				// setTimeout(function(){ location.reload(); },3000);
			}
			
		}
	 });	
}

function flip_delete_fliperrr(fs_id,d){
	var ip = FAV_COMPILE_ID+'/flipperrr/';
	var val = 'delete';
	var params = "fs_id="+fs_id+"&action=flip_delete_fliperrr&ip="+ip+"&val="+val;
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: params,
		beforeSend: function(){
			FlipPlugin.show_loading();
		},
		complete: function(){ 
			FlipPlugin.hide_loading();
		},
		success: function(str){
			var str_arr = str.split('~~~');
			if(d=='0'){
				if(str_arr[0]==1){
					window.location.href = admin_url+'?page=fliperrrlisting';
				}
			}else{
				jQuery('#error_dialog_msg').show();
				jQuery("html, body").animate({scrollTop: jQuery('#error_dialog_msg').offset().top }, 500);
				
				if(str_arr[0]==1){
					alert(str_arr)
					 jQuery("#error_dialog_msg").css('color','green');
					 jQuery("#error_dialog_msg").addClass('alert alert-success');
					 jQuery("#error_dialog_msg").html(CFFP_SUCCESS_MSG);
				}else{
					 jQuery("#error_dialog_msg").css('color','red');
					 jQuery("#error_dialog_msg").addClass('alert alert-success');
					 jQuery("#error_dialog_msg").html(CFFP_ERROR_MSG);
				}
				setTimeout(function(){jQuery('#error_dialog_msg').fadeOut("slow", function () {});}, 2500);
				setTimeout(function(){ 
					location.reload(); 
				},3000);
			}	
		}
     });	
}