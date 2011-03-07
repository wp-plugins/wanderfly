<?php
	
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	$term = urlencode($_GET['q']);
	$limit = $_GET['limit'];
	$apikey = $_GET['apikey'];
	
	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, "http://www.wanderfly.com/server/services/cxf/dataService/getOriginDestinationsByName/?limit=10&q=".$term."&limit=".$limit);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);

	$output = curl_exec($c);
	
	echo $output;
?>