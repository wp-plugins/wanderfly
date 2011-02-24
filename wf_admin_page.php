<?php
	//Handling options page saving,
	if($_POST['wf_hidden'] == 'Y') {
		//Form data sent,
		$wf_apikey = $_POST['wf_apikey'];
		$wf_display = $_POST['wf_display'];
		
		update_option('wf_apikey', $wf_apikey);
		update_option('wf_display', $wf_display);
	?>
		<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
	<?php
	} else {
		//Normal page display,
		$wf_apikey = get_option('wf_apikey');
		$wf_display = get_option('wf_display');
	}
?>

<div class="wrap">
	<?php echo "<h2>" . __( 'Wanderfly options', 'wf_opts_title' ) . "</h2>"; ?>

	<form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="wf_hidden" value="Y">
		<fieldset>
			<?php echo "<h4>" . __( 'Your account info', 'wf_opts_accountinfo' ) . "</h4>"; ?>
			<p><?php _e("API key: " ); ?><input type="text" name="wf_apikey" value="<?php echo $wf_apikey; ?>" size="50"></p>
		</fieldset>
		
		<fieldset>
			<?php echo "<h4>" . __( 'Widget display', 'wf_opts_widgetdisplay' ) . "</h4>"; ?>
			<p>Where do you want to display the widget ?</p>
			<?php if($wf_display === "manual") { ?>
				<p><?php _e("End of posts: " ); ?><input type="radio" name="wf_display" value="end" /></p>
				<p><?php _e("Manual: " ); ?><input type="radio" name="wf_display" value="manual" checked="checked" /></p>
			<?php } else { ?>
				<p><?php _e("End of posts: " ); ?><input type="radio" name="wf_display" value="end" checked="checked" /></p>
				<p><?php _e("Manual: " ); ?><input type="radio" name="wf_display" value="manual" /></p>
			<?php } ?>
			<p><em>If you choose manual, you will have to modify your single.php file and add this line of code : "<code>&lt;?php wf_template_widget(); ?&gt;</code>" where you want the widget to be displayed.
				<br />
				Note: This will only work for posts pages!
			</em></p>
		</fieldset>
		
		<p class="submit">
		<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Changes', 'wf_opts_save' ) ?>" />
		</p>
	</form>
</div>
