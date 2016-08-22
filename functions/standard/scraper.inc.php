<?php
function ShowScraperLog($arCtl,$arSAFE_REQUEST) {

	$mysqli = db_connect(getDBName());
	
	$sql = "select * from SCRAPERLOG where SL_KEY = " . $arSAFE_REQUEST['SCRAPERLOG']['SL_KEY'];
	echo $sql;
	$result2 = mysqli_query ($mysqli, $sql );
	if (! $result2)	{ echo  sql_error (); }
	$SCRAPERLOG = mysqli_fetch_assoc($result2);

	// set up smarty
	$smarty = getSmarty();

	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SCRAPERLOG',    $SCRAPERLOG);
	$smarty->assign('SESSION',   $_SESSION);

	// display using template name provided
	$smarty->display("admin/ShowScraperLog.tpl");

}


function doCurlPull($arCtl) {
	
	require_once (getAppRoot() . "libs/simple_html_dom.inc.php");
	
	$mysqli = db_connect(getDBName());
	global $cookiejar;
	
	
	print_r($arCtl);
	

	if (!$cookiejar){
    	preg_match("/([a-zA-Z0-9-_]*)\.(com|co\.uk|org|net)/", $arCtl['url'], $regex);
        $cookiejar = tempnam("./cookies", $regex[1]);
	}
	
	$ch = curl_init($arCtl['url']);
	
	/** Set the cURL options **/
	curl_setopt($ch, CURLOPT_URL, $arCtl['url']);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
//	curl_setopt($ch, CURLOPT_COOKIESESSION, true );
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiejar);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	
	if ($arCtl['referer'] != ""){
		curl_setopt($ch, CURLOPT_REFERER,$arCtl['referer']);
	}
	
	$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)";
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);


	if (count($arCtl['POST']) > 0){
		
		foreach($arCtl['POST'] as $key=>$value) { 
			$fields_string .= $key.'='.urlencode($value).'&'; 
		}
		rtrim($fields_string, '&');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	}
	
	$output = curl_exec($ch);
	
// 	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
// 	$header = substr($output, 0, $header_size);
// 	$body = substr($output, $header_size);

	if($output === FALSE) {
		echo "cURL Error: ".curl_error($ch); // do something here if we couldn't scrape the page
		$info = curl_getinfo($ch);
		$arReturn['Status'] = "Error";
		$arReturn['Info'] = $info;
		$arReturn['Error'] = curl_error($ch);

		$sql = "insert into SCRAPERLOG (SL_DATETIME, SL_TYPE,SL_STATUS, SL_CUSTOMERKEY, SL_PAGENAME, SL_REQUEST) values (
				now(),
				'" . $arCtl['SL_TYPE'] . "',
				'" . $arReturn['Status'] . "',
				'" . $arCtl['SL_CUSTOMERKEY'] . "',
				'" . $arCtl['SL_PAGENAME'] . "',
				'" . mysqli_real_escape_string($mysqli,$arReturn['Info']['request_header']) . "')";
//		echo $sql;
		$result2 = mysqli_query ($mysqli, $sql );
		if (!$result2)	{ echo  sql_error (); }
	
	}
	else {
		$info = curl_getinfo($ch);
// 		echo "Took ".$info['total_time']." seconds for url: ".$info['url'];
// 		print_r($info);
// 		echo $output;
 		//$html= str_get_html($output); // Transfer CURL to SIMPLE HTML DOM
		$arReturn['Status'] = "Response";
		$arReturn['Info'] = $info;
 		$arReturn['Output'] = $output;
// 		$arReturn['DOM'] = $html;
		
 		
//  		$doc = new DOMDocument();
//  		$doc->loadHTML($output);
//  		$titles = $doc->getElementsByTagName("title");
//  		echo "HHHHHHHHHHHHHHHHH" . $titles->item[0]->nodeValue;
		// see if expected title
// 	 echo $output;
// 		foreach($html->find('title') as $title) {
// //			print_r($title);

// 			echo trim($title->plaintext); 
// 			echo $arCtl['ExpectedTitle'];
				
// 			if (trim($title->plaintext) == $arCtl['ExpectedTitle']){
// 				$arReturn['Status'] = "Matched";
// 			}
// 		}
		
 		preg_match("/<title[^>]*>(.*?)<\/title>/ims",$output,$title);
//  		print_r($title);
 		
 		if (trim($title[1]) == $arCtl['ExpectedTitle']){
				$arReturn['Status'] = "Matched";
		}
		
		$sql = "insert into SCRAPERLOG (SL_DATETIME, SL_TYPE,SL_STATUS, SL_CUSTOMERKEY, SL_PAGENAME, SL_REQUEST, SL_RESPONSE) values (
				now(),
				'" . $arCtl['SL_TYPE'] . "',
				'" . $arReturn['Status'] . "',
				'" . $arCtl['SL_CUSTOMERKEY'] . "',
				'" . $arCtl['SL_PAGENAME'] . "',
				'" .  mysqli_real_escape_string($mysqli,$arReturn['Output']) . "',
				'" .  mysqli_real_escape_string($mysqli,$arReturn['Info']['request_header']) . "')";
//		echo $sql;
		$result2 = mysqli_query ($mysqli, $sql );
		if (!$result2)	{ echo  sql_error (); }
				
		
	}
	
	/** Free up cURL **/
	curl_close($ch);
	
	return $arReturn;
	
}


?>
