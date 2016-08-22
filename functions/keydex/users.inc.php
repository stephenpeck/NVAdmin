<?php

/**
 * List Users
 */
function ListUsers($arCtl, $USERS) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * *********************************************************************************************
	 */
	// DB Connection
	$mysqli = db_connect ( getXSDDBName () );
	
	
	$query = "SELECT * FROM USERS ORDER BY US_NAME";
	$result = mysqli_query ($mysqli, $query );
	if (! $result)
		error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
	$usercount=0;
	while ( $qd = mysqli_fetch_array ( $result ) ) {
		$arUSERS[$usercount] = $qd;
		// get workflwos
		$sql = " select * from USERS_WORKFLOWS, KD_WORKFLOWS where WF_KEY = USF_WFKEY AND USF_USKEY = " . $qd['US_KEY'];
		$result2 = mysqli_query ($mysqli, $sql );
		if (! $result2) error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
		$count2=0;
		while ( $qd2 = mysqli_fetch_assoc ( $result2 ) ) {
			$arUSERS[$usercount]['WORKFLOWS'][$count2] = $qd2;
			
			// get list of Wf steps
			$sql = "select * from KD_WORKFLOWS_STEPS where WFS_WFKEY = " . $qd2['WF_KEY'] . " order by WFS_SEQ";
			//echo $sql;
			$result3 = mysqli_query ($mysqli, $sql );
			if (! $result3) error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
			$arUSERS[$usercount]['WORKFLOWS'][$count2]['WFStepsList'][''] = "All";
			while ( $qd3 = mysqli_fetch_assoc ( $result3 ) ) {
				$arUSERS[$usercount]['WORKFLOWS'][$count2]['WFStepsList'][$qd3['WFS_KEY']] = $qd3['WFS_DESCRIPTION'];
			}
			$count2++;
			
		}
		// get docs
		$sql = " select * from USERS_DOCUMENTS, KD_DOCDEF where DD_KEY = USD_DDKEY AND USD_USKEY = " . $qd['US_KEY'];
		//echo $sql;
		$result2 = mysqli_query ($mysqli, $sql );
		if (! $result2) error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
		while ( $qd2 = mysqli_fetch_assoc ( $result2 ) ) {
			$arUSERS[$usercount]['DOCUMENTS'][] = $qd2;
		}

		$usercount++;
		
	}
	
	
	$query = "SELECT MN_MENU FROM MENU GROUP BY MN_MENU";
	$result2 = mysqli_query ($mysqli, $query );
	if (! $result2)
		error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
	
	while ( $qd2 = mysqli_fetch_assoc ( $result2 ) ) {
		$arMenus [$qd2 ['MN_MENU']] = $qd2 ['MN_MENU'];
	}

	
	/**
	 * **********************************************************************************************
	 * Drop Downs
	 * *********************************************************************************************
	 */
	
	$arType['ShowHome.tpl'] = "Standard Dashboard";
	$arType['ShowHomeAdmin.tpl'] = "Admin";
	
	$sql = "select * from KD_DOCDEF";
	$result = mysqli_query ( $mysqli, $sql);
	if (! $result)	error_message ( sql_error () );
	$arDocTypes [''] = "Select Doc Type";
	while ($qd = mysqli_fetch_assoc($result)){
		foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
			if ($arDocTypeDetails ['Name'] == $qd['DD_KEYSTORE_DOCTYPE']){
				// dropdown options
				$arDocTypes [$qd['DD_KEY']] = $qd['DD_DESCRIPTION'];
			}
		}
	}

	$sql = "select * from KD_WORKFLOWS";
	$result = mysqli_query ( $mysqli, $sql);
	if (! $result)	error_message ( sql_error () );
	$arWorkflowList [''] = "Select Workflow";
	while ($qd = mysqli_fetch_assoc($result)){
		$arWorkflowList [$qd['WF_KEY']] = $qd['WF_DESCRIPTION'];
	}
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * *********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'arUSERS', $arUSERS );
	$smarty->assign ( 'TypeList', $arType );
	$smarty->assign ( 'DocTypeList', $arDocTypes );
	$smarty->assign ( 'WorkflowList', $arWorkflowList );
	$smarty->assign ( 'MenuList', $arMenus );
	$smarty->assign ( 'arCtl', $arCtl );
	$smarty->assign ( 'SESSION', $_SESSION );
	
	$smarty->display ( 'admin/ListUsers.tpl' );
}


/**
 * Update Users
 */

function UpdUsers($arCtl, $USERS){
	// DB Connection
	$mysqli = db_connect ( getXSDDBName () );
	
	if ($USERS['US_KEY']!= "") {

		$query = "UPDATE USERS SET
					US_LOGON = '" . $USERS['US_LOGON'] . "',
					US_NAME = '" . $USERS['US_NAME'] . "',
					US_TELNO = '" . $USERS['US_TELNO'] . "',
					US_EMAIL = '" . $USERS['US_EMAIL'] . "',
					US_PASSWORD = '" . $USERS['US_PASSWORD'] . "',
					US_HOME = '" . $USERS['US_HOME'] . "'
					WHERE US_KEY = '" . $USERS['US_KEY'] . "'";
		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	} else {

		$query = "INSERT USERS (
					US_LOGON,
					US_NAME,
					US_TELNO,
					US_EMAIL,
					US_PASSWORD,
					US_HOME )
					 VALUES (
					'" . $USERS['US_LOGON'] . "',
					'" . $USERS['US_NAME'] . "',
					'" . $USERS['US_TELNO'] . "',
					'" . $USERS['US_EMAIL'] . "',
					'" . $USERS['US_PASSWORD'] . "',
					'" . $USERS['US_HOME'] . "')";

		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 
		$USERS['US_KEY'] = mysqli_insert_id($mysqli);	
	}

	//echo $query;
	// echo $query;
	header("location:" . getAdminCommand());
} 


function UpdUserSecurity($arCtl,$USERS,$USER_SECURITY){
	// DB Connection
		$mysqli = db_connect ( getXSDDBName () );
	
	if ($USER_SECURITY['UT_KEY'] != "") {

		$query = "UPDATE USER_SECURITY SET
					UT_LEVEL = '" . $USER_SECURITY['UT_LEVEL'] . "'
					WHERE UT_KEY = '" . $USER_SECURITY['UT_KEY'] . "'";
		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	} else {

		$query = "INSERT USER_SECURITY (
					UT_USKEY,
					UT_MGKEY,
					UT_LEVEL )
					 VALUES (
					'" . $USER_SECURITY['UT_USKEY'] . "',
					'" . $USER_SECURITY['UT_MGKEY'] . "',
					'" . $USER_SECURITY['UT_LEVEL'] . "')";

		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 
		$USER_SECURITY['UT_KEY'] = mysqli_insert_id($mysqli);	
	}

//	echo $query;
	// echo $query;
	header("location:" . getAdminCommand());
}


function UpdUsersDocs($arCtl, $USERS, $USERS_DOCUMENTS) {
	// DB Connection
	$mysqli = db_connect ( getDBName () );

	if ($USERS_DOCUMENTS ['USD_KEY'] != "") {

		$query = "UPDATE USERS_DOCUMENTS SET
					USD_USERDOCSONLY = '" . $USERS_DOCUMENTS ['USD_USERDOCSONLY'] . "'
					WHERE USD_KEY = '" . $USERS_DOCUMENTS ['USD_KEY'] . "'";
		// echo $query;
		$result = mysqli_query ($mysqli,  $query );
		if (! $result)
			error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
	} else {

		$query = "INSERT into USERS_DOCUMENTS (
					USD_USKEY,
					USD_DDKEY,
					USD_USERDOCSONLY )
					 VALUES (
					'" . $USERS_DOCUMENTS ['USD_USKEY'] . "',
					'" . $USERS_DOCUMENTS ['USD_DDKEY'] . "',
					'" . $USERS_DOCUMENTS ['USD_USERDOCSONLY'] . "')";
		// echo $query;
		$result = mysqli_query ($mysqli,  $query );
		if (! $result)
			error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
		$USERS_DOCUMENTS ['USD_KEY'] = mysqli_insert_id ( $mysqli );
	}

	// echo $query;
	// echo $query;
	header("location:" . getAdminCommand());
}
function UpdUsersWorkflows($arCtl, $USERS, $USERS_WORKFLOWS) {
	// DB Connection
		$mysqli = db_connect ( getXSDDBName () );
	
	if ($USERS_WORKFLOWS ['USF_KEY'] != "") {

		$query = "UPDATE USERS_WORKFLOWS SET
					USF_DOCOWNER = '" . $USERS_WORKFLOWS ['USF_DOCOWNER'] . "',
					USF_PROCESSOWNER = '" . $USERS_WORKFLOWS ['USF_PROCESSOWNER'] . "',
					USF_WFSKEY = '" . $USERS_WORKFLOWS ['USF_WFSKEY'] . "',
							USF_STATUSUPDATE = '" . $USERS_WORKFLOWS ['USF_STATUSUPDATE'] . "'
						WHERE USF_KEY = '" . $USERS_WORKFLOWS ['USF_KEY'] . "'";
		// echo $query;
		$result = mysqli_query ($mysqli,  $query );
		if (! $result)
			error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
	} else {

		$query = "INSERT into USERS_WORKFLOWS (
					USF_USKEY,
					USF_WFKEY,
					USF_DOCOWNER, 
					USF_PROCESSOWNER, 
					USF_STATUSUPDATE )
				VALUES (
					'" . $USERS_WORKFLOWS ['USF_USKEY'] . "',
					'" . $USERS_WORKFLOWS ['USF_WFKEY'] . "',
					'" . $USERS_WORKFLOWS ['USF_DOCOWNER'] . "',
					'" . $USERS_WORKFLOWS ['USF_PROCESSOWNER'] . "',
					'" . $USERS_WORKFLOWS ['USF_STATUSUPDATE'] . "')";
				// echo $query;
		$result = mysqli_query ($mysqli,  $query );
		if (! $result)
			error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
		$USERS_WORKFLOWS ['USF_KEY'] = mysqli_insert_id ( $mysqli );
	}

	// echo $query;
	// echo $query;
	header("location:" . getAdminCommand());
}
/**
 * Delete Users
 */
function DelUsers($arCtl, $USERS) {
	// DB Connection
	$mysqli = db_connect ( getXSDDBName () );
	
	$query = "DELETE FROM USERS WHERE US_KEY = '" . $USERS ['US_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ($mysqli, $query );
	if (! $result)
		error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
	// echo $query;
	header("location:" . getAdminCommand());
}
function DelUsersWorkflows($arCtl,$USERS, $USERS_WORKFLOWS) {
	// DB Connection
	$mysqli = db_connect ( getXSDDBName () );
	
	$query = "DELETE FROM USERS_WORKFLOWS WHERE USF_KEY = '" . $USERS_WORKFLOWS ['USF_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ($mysqli, $query );
	if (! $result)
		error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
	// echo $query;
	header("location:" . getAdminCommand());
}

function DelUsersDocs($arCtl,$USERS, $USERS_DOCUMENTS) {
	// DB Connection
	$mysqli = db_connect ( getXSDDBName () );
	
	$query = "DELETE FROM USERS_WORKFLOWS WHERE USD_KEY = '" . $USERS_DOCUMENTS ['USD_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ($mysqli, $query );
	if (! $result)
		error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );

	// echo $query;
	header("location:" . getAdminCommand());
}

/*****************************************************************************************************

Function to check if logged on

****************************************************************************************************/
function LoggedOn($us_name) {

	if (!$_SESSION["LoggedOn"]) {
		$arCtl['message'] = "Please Log On";
		LoginScreen($arCtl,"");
		exit;
	}

}


function LogOff($arCtl) {

	$arCtl['message'] = $_SESSION['Name'] . " has logged off";
	$_SESSION = array();
	session_destroy();
	
	// set up sessions
	if (session_id()){
		// checking if a session has already been started if so stop it
		session_destroy();
		unset($_SESSION);
	}
	$previous_name = session_name("TPOWeb");
	session_set_cookie_params(0, '/', getDomain());
	session_start();
	
	// get confoig info
	// update last login time
	$mysqli = db_connect(getXSDDBName());
	$sql = "select * from KX_COMPANY where KC_URL = '" . $_SERVER ['HTTP_HOST'] . "'";
	$result = mysqli_query($mysqli,$sql);
	if (!$result) {sql_error($mysqli);}
	$_SESSION['KX_COMPANY'] = mysqli_fetch_assoc($result);
	
	ShowLogin($arCtl,"");
	exit;

}

function Login($arCtl, $USERS){

	// DB Connection
	$db = db_connect(getDBName());

	$sql = "SELECT * from USERS
				where upper(US_LOGON) = '" . strtoupper($USERS['US_LOGON']) . "'
				and upper(US_PASSWORD) = '" . strtoupper($USERS['US_PASSWORD']) . "' LIMIT 1";

//	echo $sql;
	
	$result = mysqli_query($db,$sql);
	if (!$result) mysqli_error($db);
	$USERS = mysqli_fetch_assoc($result);

	if (trim($USERS['US_KEY']) != ""){
		// user record found and password match
		$_SESSION["LoggedOn"] = true;

		// set Names to be used on header
		$_SESSION["Name"] = $USERS['US_LOGON'] ;
		$_SESSION["US_NAME"] = $USERS['US_NAME'] ;
		$_SESSION["US_KEY"] = $USERS['US_KEY'] ;
		$_SESSION["US_LEVEL"] = $USERS['US_LEVEL'] ;
		$_SESSION["US_HOME"] = $USERS['US_HOME'] ;
		
		// update last login time
		$sql = "update USERS set US_LASTLOGIN = now() where US_KEY = '" . $USERS['US_KEY'] . "'";
		$result = mysqli_query($db,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		
		// load up menu
		$arMenu = getMenu($arCtl,$USERS) ;
		$_SESSION["Menu"] = $arMenu['Menu'] ;
		$_SESSION["SecurityMenu"] = $arMenu['SecurityMenu'] ;
		
		
		if ($USERS['US_TYPE'] == "U"){
		
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
		}
		
		
		// load up doc type sin session
		$arCtl ['Company'] = getCompany ();
		$_SESSION ['Company'] = $arCtl ['Company'];
		$_SESSION ['arDocTypes'] = getDocDetails ( $arCtl );
		
		ShowHome($arCtl);
		
	} else {
		$arCtl['message'] = "User Details Incorrect";
		//	LoginScreen($arCtl,$USERS);
		$arCtl['NextAction'] = "LoginScreen";
		
		ShowLogin($arCtl,$USERS);
		
	}


}

function getMenu($arCtl,$USERS) {
	// DB Connection
	$db = db_connect(getXSDDBName());
	
	// get menu group security
	$sql = "select * from USER_SECURITY, MENU_GROUP where UT_MGKEY = MG_KEY and UT_USKEY = '" . $USERS['US_KEY'] . "' ORDER BY MG_SEQ, MG_NAME";
//  	echo $sql;
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $query, NULL, __LINE__, __FILE__);
	$MenuCount=0;
	while ($USER_SECURITY = mysqli_fetch_assoc($result)){

		//now go and get possible menu items
		$sql = "select * from MENU_ITEMS where MI_MGKEY = '" . $USER_SECURITY['UT_MGKEY'] . "' and MI_LEVEL <= '" . $USER_SECURITY['UT_LEVEL'] . "' order by MI_ORDER ASC";
//  		echo $sql;
		$result2 = mysqli_query($db,$sql);
		if (!$result2) errormessage(sql_error, $query, NULL, __LINE__, __FILE__);
		$SubMenuCount2=0;
		$Options = array();
		while ($MENU_ITEMS = mysqli_fetch_assoc($result2)){
			if ($MENU_ITEMS['MI_DISPLAY'] == "Y"){
				$Options[$SubMenuCount2]['OptionName'] = $MENU_ITEMS['MI_NAME'];
				$Options[$SubMenuCount2]['OptionFAIcon'] = $MENU_ITEMS['MI_ICON'];
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
function ShowLogin($arCtl,$USERS) {

	// set up smarty get DB connections
	$smarty = getSmarty();

	$smarty->assign('DateTime', date("d/m/Y"));
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',  $_SESSION);
	$smarty->assign('USERS',    $USERS);

	// Now show
	$smarty->display('admin/LoginScreen.tpl');

}

function ShowHome($arCtl) {

	// this shows the dashboard
	
	// so find all workflows and co status
	$arWorkFlows = getWorkFlows($arCtl);
	
	// find latest set of notifcations
	$arDocMessages = getDocMessages($arCtl);
	
	
	// set up smarty get DB connections
	$smarty = getSmarty();

	$smarty->assign('arWorkFlows',    $arWorkFlows);
	$smarty->assign('arDocMessages',    $arDocMessages);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',    $_SESSION);
	$smarty->assign('USERS',    $USERS);

	// Now show
	$smarty->display('standard/' . $_SESSION["US_HOME"]);

}

?>


