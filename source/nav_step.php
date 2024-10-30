<ul>
	<li <?php if(empty($_GET['step'])){ ?> class="active" <?php } ?>><a href="javascript:void(0);"><?php echo esc_html__('File Upload'); ?></a></li>
	<li <?php if(isset($_GET['step']) && $_GET['step'] =='2'){ ?> class="active" <?php } ?>><a href="javascript:void(0);"><?php echo esc_html__('Choose Background'); ?></a></li>
	<li <?php if(isset($_GET['step']) && $_GET['step'] =='3'){ ?> class="active" <?php } ?>><a href="javascript:void(0);"><?php echo esc_html__('Settings'); ?></a></li>
	<li <?php if(isset($_GET['step']) && $_GET['step'] =='4'){ ?> class="active" <?php } ?>><a href="javascript:void(0);"><?php echo esc_html__('Finished'); ?></a></li>
</ul>