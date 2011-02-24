<?php
	
	/*
		Function: wf_widget();
		Variables,
			- locationID: Integer which is the ID of the destination,
	*/
	function wf_template_widget($retData=false) {
		global $post;
		
		$error = 'Error while adding the widget, please report to <a href="http://blog.wanderfly.com/wordpress-plugin/">http://blog.wanderfly.com/wordpress-plugin/</a>.';
		$widgetHTML = false;
		
		if($post) {
			//Get the Destination ID from the post's metas,
			$wf_meta = get_post_meta($post->ID, "wf_destination", true);
			
			//Display the wanderfly widget,
			if($wf_meta) {
				$wf_metas = explode(':', $wf_meta);
				$widgetHTML = wf_widget($wf_metas[0]);
			}
			
		}
		
		//Echo the  widget,
		if($widgetHTML !== false) {
			if($retData) { return $widgetHTML; }
			else { echo $widgetHTML; }
		}
		//Return the widget's HTML,
		else {
			if($retData) { return $widgetHTML; }
			else { echo $error; }
		}
		
	}
	
	/*
		Function: wf_widget();
		Variables,
			- locationID: Integer which is the ID of the destination,
	*/
	function wf_widget($destinationID=null) {
		
		if($destinationID === null) {
			print('You need to provide the destination ID from <a href="http://wanderfly.com">Wanderfly</a>.');
			return false;
		}
		
		//Connect to the OSCommerce database
		$apikey = get_option('wf_apikey');
		
		return '<iframe srcolling="no" frameborder="0" border="0" width="596" height="130" name="wf-widget-checkitout-'.$destinationID.'" id="wf-widget-checkitout-'.$destinationID.'" src="http://partners.wanderfly.com/widgets/checkitout?apiKey='.$apikey.'&destinationID='.$destinationID.'" style="border:none; overflow:hidden; width:596px; height:130px;" allowTransparency="true"></iframe>';
	}
	
?>