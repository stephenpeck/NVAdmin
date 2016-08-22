<?php
function checkAccess($action){

	// now check if permission OK - use the session array as the menus are already stored
	$SecurityOK = "N";
	foreach($_SESSION['SecurityMenu'] as $key => $arMenu) {
		if (trim($arMenu['MI_SCRIPT']) == trim($action)){
			$SecurityOK = "Y";
			$MENU_ITEM = $arMenu;
		}
	}

	if ($SecurityOK == "N"){
		echo "No access  to " . $action;
		exit();
	} else {
		return $MENU_ITEM;
	}

}


function LogOff($arCtl) {

	$arCtl['message'] = $_SESSION['Name'] . " has logged off";
	$_SESSION = array();
	session_destroy();

	// 	exit();
	header("location:" . getAdminCommand());
	
}

function Login($arCtl, $arSAFE_REQUEST){

	// DB Connection
	$mysqli = db_connect(getConfigDBName());

	$USERS = $arSAFE_REQUEST['USERS'];
	
	$sql = "SELECT * from USERS
				LEFT OUTER JOIN USERS_HOME on UH_KEY = US_HOME			
				where upper(US_LOGON) = '" . strtoupper($USERS['US_LOGON']) . "'
				and upper(US_PASSWORD) = '" . strtoupper($USERS['US_PASSWORD']) . "' LIMIT 1";
// echo $sql;
	$result = mysqli_query($mysqli,$sql);
	if (!$result) mysqli_error($mysqli);
	$USERS = mysqli_fetch_assoc($result);

	//print_r($USERS);
	
	if (trim($USERS['US_KEY']) != ""){
		// user record found and password match
		$_SESSION["LoggedOn"] = true;

		// set Names to be used on header
		$_SESSION["Name"] = $USERS['US_LOGON'] ;
		$_SESSION["US_KEY"] = $USERS['US_KEY'] ;
		$_SESSION["US_UGKEY"] = $USERS['US_UGKEY'] ;
		$_SESSION["US_LEVEL"] = $USERS['US_LEVEL'] ;
		if ($USERS['US_INTERMEDIARY'] != ""){
			$_SESSION["US_INTERMEDIARY"] = $USERS['US_INTERMEDIARY'] ;
		}
		
		// update last login time
		$sql = "update USERS set US_LASTLOGIN = now() where US_KEY = '" . $USERS['US_KEY'] . "'";
		$result = mysqli_query($mysqli,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);

		// load up menu
		$arMenu = getMenu($arCtl,$USERS) ;
		$_SESSION["Menu"] = $arMenu['Menu'] ;
		$_SESSION["SecurityMenu"] = $arMenu['SecurityMenu'] ;
		
		// now get othe rvars
		if ($ENVIRONMENT['PARAMETERS']['KeyDexCompany'] == "Y"){
		
			unset($_SESSION ['USERS_WORKFLOWS']);
			unset($_SESSION ['USERS_DOCUMENTS']);
		
			// get docs and workflow details
			$sql = " select * from USERS_WORKFLOWS, KD_WORKFLOWS where WF_KEY = USF_WFKEY AND USF_USKEY = " . $USERS['US_KEY'];
			// 			echo $sql;
			$result2 = mysqli_query ($db, $sql );
			if (! $result2) sql_error();
			while ( $qd2 = mysqli_fetch_assoc ( $result2 ) ) {
				$_SESSION ['USERS_WORKFLOWS'][] = $qd2;
			}
			// get docs
			$sql = " select * from USERS_DOCUMENTS, KD_DOCDEF where DD_KEY = USD_DDKEY AND USD_USKEY = " . $USERS['US_KEY'];
			// 			echo $sql;
			$result2 = mysqli_query ($db, $sql );
			if (! $result2) error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
			while ( $qd2 = mysqli_fetch_assoc ( $result2 ) ) {
				$_SESSION ['USERS_DOCUMENTS'][] = $qd2;
			}

			// load up doc type sin session
			$arCtl ['Company'] = $ENVIRONMENT['PARAMETERS']['KeyDexCompanyName'];
			$_SESSION ['Company'] = $ENVIRONMENT['PARAMETERS']['KeyDexCompanyName'];
			$_SESSION ['arDocTypes'] = getDocDetails ( $arCtl );
				
		}
		
		
		
		
		
		if ($USERS['US_HOME'] != ""){
			$_SESSION['USERS_HOME_FUNCTION'] = $USERS['UH_FUNCTION'];
			$_SESSION['USERS_HOME_TEMPLATE'] = $USERS['UH_TEMPLATE'];
			$USERS['UH_FUNCTION']($arCtl,$USERS);
		} else {
			ShowHome($arCtl,$arSAFE_REQUEST);
		}
		
	} else {
		$arCtl['message'] = "User Details Incorrect";
		//	LoginScreen($arCtl,$USERS);
		$arCtl['NextAction'] = "LoginScreen";
		
		ShowLogin($arCtl,$arSAFE_REQUEST);
	}


}
function getDocDetails($arCtl) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	$wsdl = "http://www.tposervice.net/keydex/services/TPODocumentService.php?wsdl";

	$client = new SoapClient($wsdl, array(‘features’ => SOAP_SINGLE_ELEMENT_ARRAYS));

	// 	print_r($client->__getFunctions());
	// 	print_r($client->__getTypes());

	$arGetDocDetails = array (
			'Company' => $arCtl ['Company'],
			'DocGroup' => $arCtl ['DocGroup'],
			'DocType' => $arCtl ['DocType']
	);

	//print_r ( $arGetDocDetails );

	/**
	 * **************************************************************************************
	 * Creating SOAP Connections and Displaying Results
	 * ***************************************************************************************
	*/

	$result = $client->DocDetails($arGetDocDetails);

	// 	print_r($result);

	// now rejigg

	$doccount = 0;
	foreach ( $result as $DocDetails ) {
		$arKeys [$doccount] ['Name'] = $DocDetails->DocType;
		$arKeys [$doccount] ['Id'] = $DocDetails->DocId;
		$keycount = 0;
		foreach ( $DocDetails->Keys as $key2 => $KeyDetails ) {
			$arKeys [$doccount] ['Keys'] [$keycount] ['Name'] = $KeyDetails->Name;
			$optioncount = 0;
			if (isset($KeyDetails->Options)){
				foreach ( $KeyDetails->Options as $key3 => $OptionDetails ) {
					$arKeys [$doccount] ['Keys'] [$keycount] ['Options'] [$arOptionDetails ['OptionValue']] = $OptionDetails->OptionValue;
					$optioncount ++;
				}
			}
			$keycount ++;
		}
		$doccount ++;
	}


	return $arKeys;
}
function getMenu($arCtl,$USERS) {
	// DB Connection
	$mysqli = db_connect(getConfigDBName());
	
	// get menu group security
	$sql = "select * from USER_SECURITY, MENU_GROUP where UT_MGKEY = MG_KEY and UT_USKEY = '" . $USERS['US_KEY'] . "' ORDER BY MG_SEQ, MG_NAME";
	//echo $sql;
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $query, NULL, __LINE__, __FILE__);
	$MenuCount=0;
	while ($USER_SECURITY = mysqli_fetch_assoc($result)){

		//now go and get possible menu items
		$sql = "select * from MENU_ITEMS where MI_MGKEY = '" . $USER_SECURITY['UT_MGKEY'] . "' and MI_LEVEL <= '" . $USER_SECURITY['UT_LEVEL'] . "' order by MI_ORDER ASC";
		//echo $sql;
		$result2 = mysqli_query($mysqli,$sql);
		if (!$result2) errormessage(sql_error, $query, NULL, __LINE__, __FILE__);
		$SubMenuCount2=0;
		$Options = array();
		while ($MENU_ITEMS = mysqli_fetch_assoc($result2)){
			if ($MENU_ITEMS['MI_DISPLAY'] == "Y"){
				$Options[$SubMenuCount2]['OptionName'] = $MENU_ITEMS['MI_NAME'];
				$Options[$SubMenuCount2]['ScriptName'] = $MENU_ITEMS['MI_SCRIPT'];
				$Options[$SubMenuCount2]['MI_KEY'] = $MENU_ITEMS['MI_KEY'];
				$SubMenuCount2++;
			}

			$arMenu['SecurityMenu'][] = $MENU_ITEMS;
		}

		if ($USER_SECURITY['MG_DISPLAY'] == "Y" and $SubMenuCount2 > 0){
			$arMenu['Menu'][$MenuCount]['Name'] = $USER_SECURITY['MG_NAME'];
			$arMenu['Menu'][$MenuCount]['Options'] = $Options;
			$MenuCount++;
		}
		
	}
	return $arMenu;
}

/*****************************************************************************************
 Show Login Screen
 ******************************************************************************************/
function ShowLogin($arCtl,$arSAFE_REQUEST) {

	// set up smarty get DB connections
	$smarty = getSmarty();
	
	$smarty->assign('DateTime', date("d/m/Y"));
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',  $_SESSION);
	$smarty->assign('USERS',    $USERS);

	// Now show
	$smarty->display('standard/LoginScreen.tpl');

}

function ShowHome($arCtl) {

	// check to see if a dash is set up
	if ($_SESSION['USERS_HOME_FUNCTION'] != "" and $_SESSION['USERS_HOME_FUNCTION'] != "ShowHome"){
		$_SESSION['USERS_HOME_FUNCTION']($arCtl,$USERS);
	} else {
		// if not just show blanks
		// set up smarty get DB connections
		$smarty = getSmarty();
		
		$smarty->assign('arCtl',    $arCtl);
		$smarty->assign('SESSION',    $_SESSION);
		$smarty->assign('USERS',    $USERS);
		// Now show
		$smarty->display('standard/ShowHome.tpl');
		
	}
	
	

}

?>


