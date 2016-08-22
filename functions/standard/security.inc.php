<?php


function updAuditLog($arCtl,$AUDITLOG){

	$mysqli = db_connect(getDBName());
	
	$sql = "insert into AUDITLOG (AU_CPKEY,AU_QUOTEID,AU_QAKEY,AU_DATETIME,AU_USKEY,AU_TEXT)
				values ('" . $AUDITLOG['AU_CPKEY']. "','" . $AUDITLOG['AU_QUOTEID']. "','" . $AUDITLOG['AU_QAKEY']. "',now(),'" . $_SESSION['US_KEY']. "','" . $AUDITLOG['AU_TEXT'] . "')";

// 	echo $sql;
	
	$result2 = mysqli_query ($mysqli, $sql );
	if (! $result2)	{ echo  sql_error (); }
	
}


function UpdAuditLog2($arCtl,$AUDIT_LOG,$arSAFE_REQUEST,$action, $LAST_ACTION){

	$mysqli = db_connect();
	
	// check whitelist
	
	$sql = "select * from IP_WHITELIST where WL_IP = '" . $_SERVER['REMOTE_ADDR'] . "' and WL_ACTIVE = 'Y' LIMIT 1";
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
	$qd = mysqli_fetch_assoc($result);
	if ($qd['WL_KEY'] > 0) {
		// so ip record exists upate count
		$sql = "update IP_WHITELIST set WL_COUNT = WL_COUNT+1 where WL_KEY = " . $qd['WL_KEY'];
		$result = mysqli_query($mysqli,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		
	} //else {
	//	$AUDIT_LOG['AL_STATUS'] = "Not OK";
	//	$arCtl['Message'] = "Your IP address of "  . $_SERVER['REMOTE_ADDR'] .  " is not authorised please contact system admin";
	//	$AUDIT_LOG['AL_STATUS'] = $arCtl['Message'];
	//	createAuditLog($arCtl,$AUDIT_LOG,$arSAFE_REQUEST,$action);
	//	ShowProblemPage($arCtl);
	//	exit();
	//}

	// now check if permission OK - use the session array as the menus are already stored
	$SecurityOK = "N";
	foreach($_SESSION['SecurityMenu'] as $key => $arMenu) {
		foreach ($arMenu['Options'] as $key2 => $arOptions){
			if (trim($arOptions['ScriptName']) == trim($action)){
				$SecurityOK = "Y";
			}
		}
	}
	
	//echo "Session Array";
	//echo "<pre>";
	//print_r ($_SESSION);
	//echo "</pre>";
	
	//echo "Option Array";
	//echo "<pre>";
	//print_r ($arOptions);
	//echo "</pre>";
	
	//echo "Action";
	//echo $action;
	
	
	
	if ($SecurityOK == "Y") {
		// if get here all ok so set AL_STTAUS = OK
		$AUDIT_LOG['AL_STATUS'] = "OK";
		createAuditLog($arCtl,$AUDIT_LOG,$arSAFE_REQUEST,$action);
		// and do nothing so this goes back to index.php

	} else {
		$AUDIT_LOG['AL_STATUS'] = "Not OK";
		$arCtl['Message'] = "No security permission for action  "  . $action .  " please contact system admin";
		$AUDIT_LOG['AL_STATUS'] = $arCtl['Message'];
		createAuditLog($arCtl,$AUDIT_LOG,$arSAFE_REQUEST,$action);
		ShowProblemPage($arCtl);
		exit();
	}

	if ($SecurityOK == "Y") {
		// if get here all ok so set AL_STTAUS = OK
		$AUDIT_LOG['AL_STATUS'] = "OK";
		createAuditLog($arCtl,$AUDIT_LOG,$arSAFE_REQUEST,$action);
		// and do nothing so this goes back to index.php

	} else {
		$AUDIT_LOG['AL_STATUS'] = "Not OK";
		$arCtl['Message'] = "No security permission for action  "  . $action .  " please contact system admin";
		$AUDIT_LOG['AL_STATUS'] = $arCtl['Message'];
		createAuditLog($arCtl,$AUDIT_LOG,$arSAFE_REQUEST,$action);
		ShowProblemPage($arCtl);
		exit();
	}

	
//	So if OK then set up "Back link"
	$sql = "select * from MENU_ITEMS where MI_SCRIPT = '" . $action . "'";
	// 		echo $sql;
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$MENU_ITEM = mysqli_fetch_assoc($result);
	// set in session
	$_SESSION['BackURL'] = getWebRoot() . "?action=" . $MENU_ITEM['MI_BACKSCRIPT'];
	
 
}


function createAuditLog($arCtl,$AUDIT_LOG,$arSAFE_REQUEST,$action){
	ob_start();
		print_r($arSAFE_REQUEST);
		$RequestContents = ob_get_contents();
	ob_end_clean();
	
//	mail("luke@nextventure.co.uk","Auto" . $action . " " . $_SESSION['Name'] ,$RequestContents);

// DB Connection
	$mysqli = db_connect();
	
	$AL_STATUS = "OK"; 
	
		$query = "INSERT AUDIT_LOG (
					AL_DATETIME,
					AL_USER,
					AL_ACTION,
					AL_STATUS,
					AL_DEBUG ,
					AL_IP )
					 VALUES (
					now(),
					'" . $_SESSION['Name'] ."',
					'" . $action . "',
					'" . $AL_STATUS . "',
					'" . $RequestContents . "',					
					'" . $_SERVER['REMOTE_ADDR'] . "')";

		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 
		$AUDIT_LOG['AL_KEY'] = mysqli_insert_id($mysqli);	

//echo $query;
	
}

function listAuditLog($arCtl,$AUDIT_LOG){

	
	
	$db = db_connect(); 

	
	// first set yp check special arCtl[H] var to see what values to be set
	if ($arCtl['H'] == "Y"){
		$arCtl['AL_USER'] = $_SESSION['Name'];
		$arCtl['AL_DATETIME_FROM'] = date('Y-m-d');
		$arCtl['Run'] = "Do run";
	}
	
	
	
	$sql  = "SELECT * FROM AUDIT_LOG where AL_KEY is not null ";	

	// check selection critera
	
	if ($arCtl['AL_USER'] != ""){
		$sql = $sql . " and AL_USER = '" . $arCtl['AL_USER'] . "' ";
	}
	
	if ($arCtl['AL_ACTION'] != ""){
		$sql = $sql . " and AL_ACTION = '" . $arCtl['AL_ACTION'] . "' ";
	}	
	
	if ($arCtl['AL_STATUS'] != ""){
		$sql = $sql . " and AL_STATUS = '" . $arCtl['AL_STATUS'] . "' ";
	}	

	if ($arCtl['AL_DATETIME_FROM'] != ""){
		$sql = $sql . " and AL_DATETIME >= '" . $arCtl['AL_DATETIME_FROM'] . "' ";
	}
	if ($arCtl['AL_DATETIME_TO'] != ""){
		$sql = $sql . " and AL_DATETIME <= '" . $arCtl['AL_DATETIME_TO'] . "' ";
	}
	

	$sql = $sql . " order by AL_DATETIME DESC";

// 	echo $sql;
	
	if (trim($arCtl['Run']) != ""){
	
		$result = mysqli_query($db,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
	
		while($qd = mysqli_fetch_assoc($result)){
			$arAUDIT_LOG[] = $qd;	
		}

	}
	
	//echo "<pre>";
	//print_r ($arAUDIT_LOG);
	//echo "</pre>";	

	// set default values
	//set from to be today
	if ($arCtl['AL_DATETIME_FROM'] == ""){
		$arCtl['AL_DATETIME_FROM'] = date('Y-m-d');
	}
	
	
	// create drop downs
	
	$arStatusList[''] = '..';
	$arStatusList['OK'] = 'OK';
	$arStatusList['NOT OK'] = 'NOT OK';
	
	
	
	// do query to get user list
	$sql = "SELECT * from USERS ORDER BY US_NAME";
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
	$arUserList[''] = "Select User";
	while($qd = mysqli_fetch_assoc($result)){
		$arUserList[$qd['US_NAME']] = $qd['US_NAME'];
	}
	
	
	// do query to get list of actions	
	$sql = "SELECT * from MENU_ITEMS order by MI_SCRIPT";
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
	$arActionList[''] = "Select Action";
	while($qd = mysqli_fetch_assoc($result)){
		$arActionList[$qd['MI_SCRIPT']] = $qd['MI_SCRIPT'];
	}	
	
	//echo "<pre>";
	//print_r ($arActionList);
	//echo "</pre>";	


	/************************************************************************************************
	Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arAUDIT_LOG',  $arAUDIT_LOG);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arStatusList',    $arStatusList);
	$smarty->assign('arUserList',    $arUserList);	
	$smarty->assign('arActionList',    $arActionList);		
	$smarty->assign('DateTime', date("d/m/Y"));
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('ListAuditLog.tpl');

}

function ShowDebug($arCtl,$AUDIT_LOG){
	$db = db_connect(); 

	$sql  = "SELECT * FROM AUDIT_LOG where AL_KEY = '" . $AUDIT_LOG['AL_KEY'] . "'";
	
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
	$AUDIT_LOG = mysqli_fetch_assoc($result);
	
	while($qd = mysqli_fetch_assoc($result)){
		$arAUDIT_LOG[] = $qd;	

	echo "Test 1 <br>";	
	echo "<per>";
	print_r ($arAUDIT_LOG);
	echo "</per>";

	echo "Test 2 <br>";	
	echo $AUDIT_LOG;

	}
	
//	echo "Test 1 <br>";	
//	echo "<per>";
//	print_r ($AUDIT_LOG);
//	echo "</per>";

	/************************************************************************************************
	Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arAUDIT_LOG',  $arAUDIT_LOG);
	$smarty->assign('AUDIT_LOG',  $AUDIT_LOG);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('DateTime', date("d/m/Y"));
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('ShowDebug.tpl');
}

function listIPWhitelist($arCtl,$IP_WHITELIST){
	/************************************************************************************************
	Get Data
	***********************************************************************************************/
	// DB Connection
	$db = db_connect(); 

	$sql  = "SELECT * FROM IP_WHITELIST";	
	
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
	
	while($qd = mysqli_fetch_assoc($result)){
		$arIP_WHITELIST[] = $qd;	
	}

	//echo $sql;
	
	//echo "<pre>";
	//print_r ($arIP_WHITELIST);
	//echo "</pre>";
	
	// yes no drop down
	$arYesNo['Y'] = "Y";
	$arYesNo['N'] = "N";
			
	/************************************************************************************************
	Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arIP_WHITELIST',  $arIP_WHITELIST);
	$smarty->assign('arYesNo',    $arYesNo);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('ListIPWhitelist.tpl');	

}


function UpdIPWhitelist($arCtl,$IP_WHITELIST){
	// DB Connection
	$mysqli = db_connect();

	if ($IP_WHITELIST['WL_KEY']!= "") {

		$query = "UPDATE IP_WHITELIST SET
					WL_IP = '" . $IP_WHITELIST['WL_IP'] . "',
					WL_DESCRIPTION = '" . $IP_WHITELIST['WL_DESCRIPTION'] . "',
					WL_ACTIVE = '" . $IP_WHITELIST['WL_ACTIVE'] . "'
					WHERE WL_KEY = '" . $IP_WHITELIST['WL_KEY'] . "'";
		
		
		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	} else {

		$query = "INSERT IP_WHITELIST (
					WL_IP,
					WL_DESCRIPTION,
					WL_ACTIVE )
					VALUES (
					'" . $IP_WHITELIST['WL_IP'] . "',		
					'" . $IP_WHITELIST['WL_DESCRIPTION'] . "',
					'" . $IP_WHITELIST['WL_ACTIVE'] . "')";

		//echo $query;
		
		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 
		$IP_WHITELIST['WL_KEY'] = mysqli_insert_id($mysqli);	
	}

	//echo $query;
	
	ListIPWhitelist($arCtl,$IP_WHITELIST);	
}

function DelIPWhitelist($arCtl,$IP_WHITELIST){
		// DB Connection
	$mysqli = db_connect();

	$query  = "DELETE FROM IP_WHITELIST WHERE WL_KEY = '" . $IP_WHITELIST['WL_KEY'] . "'";

	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	ListIPWhiteList($arCtl, $IP_WHITELIST);

}

function protect_against_sql_attack($arDataToCheck){

	$mysqli = db_connect(getDBName());


	$safe_data = array();
	foreach($arDataToCheck as $suspect_key => $suspect_data){

		// 				echo "suspect_key" . $suspect_key . "<BR>";
		// 				echo "suspect_data" . $suspect_data . "<BR>";

		if(is_array($arDataToCheck[$suspect_key])){
			// if array then loop around array
			//			echo "doing array<BR>";
			$safe_data[$suspect_key] = protect_against_sql_attack($arDataToCheck[$suspect_key]);
		}else{
			// check for certain special chars
			//			echo "not doing array<BR>";
			if ($suspect_key == "k"){
				$result = false;
			} else {
				$result = check($suspect_data, array("=", "*/", "/*", "\"", "%", "--", "`", ";", "#"));
			}
			//cookie used by google analytics, needs to be allowed
			if($suspect_key != "__utmz" && $suspect_key != "__utma" && $suspect_key != "__utmb" && $suspect_key != "__utmc" && $suspect_key != "s_sq" && $suspect_key != "s_cc"){
				// possibly need these as well ( ) @ '
				if($result!==false){
					$message = "Failed validation on '".$suspect_key."' with value '".$suspect_data."' at '".$result."'";
					//					echo $message . "<BR>";
					mail("stephen@nextventure.co.uk", $message,"");
					// need to decide if to create error screen - current we just ignore the data
				}else{
					// no special chars found so do normal mysql escape
					//					echo "so using mysqli - " . $suspect_key . "-" . $suspect_data . "<BR>";
					$safe_data[$suspect_key] = mysqli_real_escape_string($mysqli,trim($suspect_data));
					//					echo "so using mysqli - " . $suspect_key . "-" . $safe_data[$suspect_key]. "<BR>";
				}
			}
		}
	}
	return $safe_data;
}

function check($haystack, $arr_needle) {
	if(!is_array($arr_needle)){
		$arr_needle = array($arr_needle);
	}
	foreach($arr_needle as $what) {
		if(($pos = stripos($haystack, $what))!==false){
			return $what;
		}
	}
	return false;
}

function doHash($arCtl){

	$textToEncrypt = date('U') . "-" . $arCtl['RQ_QUOTEID'] . "-" . "contactemail";
	//$textToEncrypt = "231405140566" . "-" . $arCtl['RQ_QUOTEID'] . "-" . "contactemail";
	//To encrypt
	$k = openssl_encrypt($textToEncrypt, getEMethod(), getHash(),true,getIV());
	$e = urlencode(trim(base64_encode($k)));


	return $e;
}

function undoHash($arCtl){

	// 	echo $arCtl['k'] . "<BR>\n";
	// 	echo urldecode($arCtl['k']) . "<BR>\n";;
	// 	echo  base64_decode(urldecode($arCtl['k'])) . "<BR>\n";;


	$k = base64_decode(urldecode($arCtl['k']));
	$decryptedMessage = openssl_decrypt($k, getEMethod(), getHash(),true,getIV());

	if ($decryptedMessage == ""){
		$k = base64_decode($arCtl['k']);
		$decryptedMessage = openssl_decrypt($k, getEMethod(), getHash(),true,getIV());
	}

	//  	echo $decryptedMessage . "HHH";

	// 	echo $decryptedMessage;
	$arMessage = explode("-",$decryptedMessage);

	$mysqli = db_connect(getDBName());
	$arMessage[1] = mysqli_real_escape_string($mysqli,$arMessage[1]);

	return $arMessage[1];
}

?>