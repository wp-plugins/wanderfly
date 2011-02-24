<?php
	/*
		Plugin Name: Wanderfly
		Plugin URI: http://partners.wanderfly.com
		Description: Plugin for displaying trips results from Wanderfly's engine
		Author: Keven Bouchard, Wanderfly Programmer
		Version: 1.2
		Author URI: http://www.wanderfly.com
	*/
	
	//Init plugin stuff,
	add_action('init', 'wf_init');
	
	function wf_init() {
		wp_enqueue_style('jquery-ui-autocomplete', plugins_url('css/ui-lightness/jquery-ui-1.8.7.custom.css', __FILE__));
		wp_enqueue_script('jquery-1.4.4', plugins_url('js/jquery-1.4.4.min.js', __FILE__));
		wp_enqueue_script('jquery-ui', plugins_url('js/jquery-ui-1.8.7.custom.min.js', __FILE__));
		wp_enqueue_script('wf-script', plugins_url('js/wf-script.js', __FILE__));
	}
	
	//Init wp vars,
	add_action('wp_head', 'wf_wp_head');
	add_action('admin_head', 'wf_wp_head');
	
	function wf_wp_head() {
		echo '<script type="text/javascript">';
			echo 'var wf_plugin_url = "'.plugins_url('', __FILE__).'";';
			echo 'var wf_api_key = "'.get_option('wf_apikey').'";';
		echo '</script>';
	}
	
	//Include usefull funcs, settings,
	require_once('functions.php');
	
	//Add settings page to admin menu,
	function wf_admin() {
		include('wf_admin_page.php');
	}
	
	function wf_admin_actions() {
		global $wf_admin_menu;
		$wf_admin_menu = add_options_page("Wanderfly", "Wanderfly", 1, "Wanderfly", "wf_admin");
	}
	
	add_action('admin_menu', 'wf_admin_actions');
	
	//Help section,
	function wf_admin_help($contextual_help, $screen_id, $screen) {

		global $wf_admin_menu;
		if ($screen_id == $wf_admin_menu) {

			$contextual_help = '
				<h4>How to get an API Key</h4>
				<ol style="list-style-type: decimal;">
					<li>Go on wanderfly.com</li>
					<li>Create an account if you don\'t have one already.</li>
					<li>Once you\'re logged in, go on the "My Settings" page.</li>
					<li>There, scroll to the "Wanderfly Widgets" section and enter your domain name in the text field (eg: http://wanderfly.com) then click "Get Key" button.</li>
					<li>After the success message has been displayed, copy your API Key that is displayed right after the domain name you just provided.</li>
					<li>Paste the API Key into Wanderfly\'s plugin section of your Wordpress admin panel.</li>
				</ol>
			';
		}
		return $contextual_help;
	}

	add_filter('contextual_help', 'wf_admin_help', 10, 3);
	
	
	//Add post editing + page editing sidebar box,
	add_action('admin_init', 'wf_add_custom_box', 1);

	/* Adds a box to the main column on the Post and Page edit screens */
	function wf_add_custom_box() {
	    add_meta_box('wf_sectionid', __('Wanderfly widget', 'wf_admin_posttitle' ), 'wf_inner_custom_box', 'post', 'side');
	}
	
	
	/* Prints the box content */
	function wf_inner_custom_box($post, $metabox) {
		
		$wf_apikey = get_option('wf_apikey');
		
		if($wf_apikey) {
			
			// Use nonce for verification
			wp_nonce_field(plugin_basename(__FILE__), 'wf_noncename');
			
			//Check if there's already a provided destination,
			$wf_meta = get_post_meta($post->ID, "wf_destination", true);
			
			$value = "";
			$search_display = "";
			$item_display = "display: none;";
			if($wf_meta) {
				$metas = explode(':', $wf_meta);
				$value = $metas[1];
				
				$search_display = "display: none;";
				$item_display = "";
			}
			
			// The actual fields for data entry
			echo '<div id="wf_destination_search_wrapper" style="'.$search_display.'">';
				echo '<input type="text" id="wf_destination_autocomplete" name="wf_destination" value="" size="30" />';
				echo '<p class="howto"><label for="wf_destination_autocomplete">'.__("Search for a destination using the autocomplete above", 'wf_admin_search').'</label></p>';
			echo '</div>';
			echo '<div id="wf_destination_item_wrapper" style="'.$item_display.'">';
				echo '<a class="btn-delete"></a>';
				echo '<p class="destination-name">'.$value.'</p>';
			echo '</div>';
			echo '<input type="hidden" id="wf_destination_val" name="wf_destination_val" value="" />';
			
		}
		else { // User must provide the API Key first,
			
			echo '<p class="howto">You need to provide an API KEY in order to use the Wanderfly Widget Plugin.</p>';
			echo '<p><a href="options-general.php?page=Wanderfly">Set your API KEY</a></p>';
			
		}
		
	}
	
	/* Do something with the data entered */
	add_action('save_post', 'wf_save_postdata');
	
	/* When the post is saved, saves our custom data */
	function wf_save_postdata($post_id) {
		
		//Verify if the user added a destination or not,
		if(!isset($_POST['wf_destination_val']) || $_POST['wf_destination_val'] == "" ) { return $post_id; }
		
		// Verify this came from the our screen and with proper authorization,
		if(!wp_verify_nonce($_POST['wf_noncename'], plugin_basename(__FILE__))) { return $post_id; }

		// Verify if this is an auto save routine. If it is our form has not been submitted, so we don't want to do anything,
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }

		// Check permissions
		if('page' == $_POST['post_type']) {
		  if(!current_user_can('edit_page', $post_id)) { return $post_id; }
		} else {
		  if(!current_user_can('edit_post', $post_id)) { return $post_id; }
		}
		
		// OK, we're authenticated: we need to find and save the data
		$destinationID = $_POST['wf_destination_val'];
		
		//Look if the user wants to delete the destination on that post,
		if($destinationID === "delete") {
			delete_post_meta($post_id, "wf_destination");
			return $post;
		}

		// Do something with $mydata 
		update_post_meta($post_id, "wf_destination", $destinationID);

		return $mydata;
	}
	
	// Automatic widget integration,
	if(get_option('wf_display') !== 'manual') {
		
		function insertContentWidget($content) {
	        if(is_single()) {
				$widget = wf_template_widget(true);
				if($widget !== false) {
					$content .= '<div style="padding: 10px 0; clear: both;">'.$widget.'</div>';
				}
	        }
	        return $content;
		}

		add_filter('the_content', 'insertContentWidget');
	
	}	
	
?>