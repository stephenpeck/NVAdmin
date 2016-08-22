<?php

function getCodes($COD_TYPE){
	
	$mysqli = db_connect(getAdminDBName());
	
	$sql = "select  * from CODES where COD_TYPE = '" . $COD_TYPE . "' order by COD_SORT";
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	{ echo  sql_error (); }
	$arDropDown[''] = "";
	while ($qd = mysqli_fetch_assoc($result)){
		$arDropDown[$qd['COD_CODE']] = $qd['COD_DESC'];
	}
	
	return $arDropDown;
}

function ShowErrorPage($arCtl){

	// set up smarty

	$smarty = getSmarty();

	$arCtl['Intermediary'] = getIntermediaryName();
	$arCtl['WebRoot'] = getWebRoot();
	
	$_SESSION['Header'] = "header/" . getCSS() . "_header_q.tpl";
	$_SESSION['Footer'] = "header/" . getCSS() . "_footer_q.tpl";
	$arCtl['WebCMSRoot'] = getCMSDomain();
	
	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);


	// display using template name provided
	$smarty->display('ShowErrorPage.tpl');

}
 
function mergeAssoc($Arr1, $Arr2){
	if (is_array($Arr1)){

		foreach($Arr2 as $key => $Value)
		{
			if(array_key_exists($key, $Arr1) && is_array($Value))
				$Arr1[$key] = mergeAssoc($Arr1[$key], $Arr2[$key]);

			else
				$Arr1[$key] = $Value;

		}
	} else {
		$Arr1 = $Arr2;
	}
	return $Arr1;
}






function DatetoDB($date){
	$arDate = explode("/",$date);
	return $arDate[2] . "-" . $arDate[1] . "-" . $arDate[0];
}

function DatefromDB($date){
	$arDate = explode("-",$date);
	return $arDate[2] . "/" . $arDate[1] . "/" . $arDate[0];
}


function dateRange( $first, $last, $step = '+1 day', $format = 'Y/m/d' ) {

	$dates = array();
	$current = strtotime( $first );
	$last = strtotime( $last );

	while( $current <= $last ) {

		$dates[] = date( $format, $current );
		$current = strtotime( $step, $current );
	}

	return $dates;
}
?>