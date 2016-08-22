<?php

/**
 * List Users
 */
function ListUsers($arCtl, $REQUEST){
	/************************************************************************************************
	Get Data
	***********************************************************************************************/
	// DB Connection
	$mysqli = db_connect(getConfigDBName());

	$sql  = "SELECT * FROM USERS where US_KEY is not null";	

	if ($arCtl['US_LOGON'] != ""){
		$where .= " AND US_LOGON like '%" . $arCtl['US_LOGON'] . "%'";
	}
	
	if ($arCtl['US_NAME'] != ""){
		$where .= " AND US_NAME like '%" . $arCtl['US_NAME'] . "%'";
	}
	
	$sql = $sql . $where . " ORDER BY US_NAME";
	//echo $sql;
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
	$count=0;
	while($qd = mysqli_fetch_assoc($result)){
		$arUSERS[$count] = $qd;
		// get menu security
		$sql = "select * from USER_SECURITY where UT_USKEY=" . $qd['US_KEY'];
		$result2 = mysqli_query($mysqli,$sql);
		if (!$result2) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
		while($qd2 = mysqli_fetch_assoc($result2)){
			$arUSERS[$count]['USERSECURITY'][] = $qd2;
		}
		// get menu security
		$sql = "select * from USER_ROLES where UO_USKEY=" . $qd['US_KEY'];
		$result2 = mysqli_query($mysqli,$sql);
		if (!$result2) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		while($qd2 = mysqli_fetch_assoc($result2)){
			$arUSERS[$count]['USERROLES'][] = $qd2;
		}
		
		$sql = " select * from USERS_WORKFLOWS, KD_WORKFLOWS where WF_KEY = USF_WFKEY AND USF_USKEY = " . $qd['US_KEY'];
		$result2 = mysqli_query ($mysqli, $sql );
		if (! $result2) error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
		$count2=0;
		while ( $qd2 = mysqli_fetch_assoc ( $result2 ) ) {
			$arUSERS[$count]['WORKFLOWS'][$count2] = $qd2;
			// get list of Wf steps
			$sql = "select * from KD_WORKFLOWS_STEPS where WFS_WFKEY = " . $qd2['WF_KEY'] . " order by WFS_SEQ";
			//echo $sql;
			$result3 = mysqli_query ($mysqli, $sql );
			if (! $result3) error_message ( sql_error, $query, NULL, __LINE__, __FILE__ );
			$arUSERS[$usercount]['WORKFLOWS'][$count2]['WFStepsList'][''] = "All";
			while ( $qd3 = mysqli_fetch_assoc ( $result3 ) ) {
				$arUSERS[$count]['WORKFLOWS'][$count2]['WFStepsList'][$qd3['WFS_KEY']] = $qd3['WFS_DESCRIPTION'];
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
		
		
		$count++;
	}

// 	if ($_SESSION['US_INTERMEDIARY'] == ""){
// 		$sql = "select * from INTERMEDIARY order by IN_NAME";
// 		//echo $sql;
// 		$result = mysqli_query ($mysqli, $sql );
// 		if (! $result)	{ echo  sql_error (); }
// 		$arIntermediary[''] = "Select Intermediary";
// 		while($qd = mysqli_fetch_assoc($result)){
// 			$arIntermediary[$qd['IN_NAME']] = $qd['IN_NAME'];
// 		}
// 	} else {
// 		$arIntermediary[$_SESSION['US_INTERMEDIARY']] = $_SESSION['US_INTERMEDIARY'];
// 	}
	

	// now create drop down on menu grousp not configiured for user
	$sql = "select * from MENU_GROUP";
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$arMENU_GROUP[''] = "Select Menu Group";
	while ($qd = mysqli_fetch_assoc($result)){
		$arMENU_GROUP[$qd['MG_KEY']] = $qd['MG_NAME'];
	}
	
	$arRestrictionTypes[''] = "";
	$arRestrictionTypes['CUSTOMER'] = "Limit to Customer Account";
	$arRestrictionTypes['INSTRUCTOR'] = "Limit to User";
	$arRestrictionTypes['DIVISION'] = "Limit to Division";
	
	
	$arRoleList[''] = "";
	$arRoleList['1'] = "Consultant";
	$arRoleList['2'] = "Design";
	$arRoleList['3'] = "Office Admin";
	
	
	$sql = "select * from KD_WORKFLOWS";
	$result = mysqli_query ( $mysqli, $sql);
	if (! $result)	error_message ( sql_error () );
	$arWorkflowList [''] = "Select Workflow";
	while ($qd = mysqli_fetch_assoc($result)){
		$arWorkflowList [$qd['WF_KEY']] = $qd['WF_DESCRIPTION'];
	}
	
	//$arUserGroups = getUserGroups($arCtl);
	
	
	$sql = "select * from KD_DOCDEF";
	$result = mysqli_query ( $mysqli, $sql);
	if (! $result)	error_message ( sql_error () );
	while ($qd = mysqli_fetch_assoc($result)){
		$arWorkflowList [$qd['WF_KEY']] = $qd['WF_DESCRIPTION'];
	}
		
	$sql = "select * from USERS_HOME";
	$result = mysqli_query ( $mysqli, $sql);
	if (! $result)	error_message ( sql_error () );
	$arHome [''] = "Select Workflow";
	while ($qd = mysqli_fetch_assoc($result)){
		$arHome [$qd['UH_KEY']] = $qd['UH_NAME'];
	}
	
	
	$arWFStepOperatorsList['EQ'] = "Only";
	$arWFStepOperatorsList['FROM'] = "From";
	$arWFStepOperatorsList['TO'] = "To";
	
	/************************************************************************************************
	Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();

// 	print_r($arUSERS);
	
	// results info
	$smarty->assign('arUSERS',  $arUSERS);
	$smarty->assign('arMENU_GROUP',  $arMENU_GROUP);
	$smarty->assign('arRestrictionList',  $arRestrictionTypes);
	$smarty->assign('arRoleList',  $arRoleList);
	$smarty->assign('arWFStepOperatorsList',  $arWFStepOperatorsList);
	$smarty->assign ( 'DocTypeList', $arDocTypes );
	$smarty->assign ( 'WorkflowList', $arWorkflowList );
	$smarty->assign('arHomeList',  $arHome);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('admin/ListUsers.tpl');

}


/**
 * Update Users
 */

function UpdUsers($arCtl, $arSAFE_REQUEST){


	print_r($arSAFE_REQUEST['USERS']);
	
	$arSQL['USERS'][0] = $arSAFE_REQUEST['USERS'];
	$arSQL = saveToDB($arSQL);

	// now redirect (action in session)
	header("location:" . getAdminCommand());

} 


function UpdUserSecurity($arCtl,$REQUEST){
	// DB Connection
	$mysqli = db_connect(getConfigDBName());;

	$USER_SECURITY = $REQUEST['USER_SECURITY'];
	
	
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

	// now redirect (action in session)
	header("location:" . getAdminCommand());
}

function UpdUserRoles($arCtl,$arSAFE_REQUEST){

	$arSQL['USER_ROLES'][0] = $arSAFE_REQUEST['USER_ROLES'];
	$arSQL = saveToDB($arSQL);
	// now redirect (action in session)
	header("location:" . getAdminCommand());
}

function UpdUsersDocs($arCtl, $arSAFE_REQUEST) {

	$arSQL['USERS_DOCUMENTS'][0] = $arSAFE_REQUEST['USERS_DOCUMENTS'];
	$arSQL = saveToDB($arSQL);
	
	
	header("location:" . getAdminCommand());
}
function UpdUsersWorkflows($arCtl, $USERS, $USERS_WORKFLOWS) {
	// DB Connection

	$arSQL['USERS_WORKFLOWS'][0] = $arSAFE_REQUEST['USERS_WORKFLOWS'];
	$arSQL = saveToDB($arSQL);
		// echo $query;
	header("location:" . getAdminCommand());
}

/**
 * Delete Users
 */

function DelUsers($arCtl, $USERS, $USER_SECURITY){
	// DB Connection
	$mysqli = db_connect(getConfigDBName());;

	$query = "DELETE FROM USERS WHERE US_KEY = '" . $USERS['US_KEY'] . "'";
//	echo $query;
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 
	

	$query = "DELETE FROM USER_SECURITY WHERE UT_USKEY = '" . $USERS['US_KEY'] . "'";
//	echo $query;
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 	
	
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());
} 


function DelUserSecurity($arCtl,$REQUEST){
	// DB Connection
	$mysqli = db_connect(getConfigDBName());;

	$USER_SECURITY = $REQUEST['USER_SECURITY'];

	$query = "DELETE FROM USER_SECURITY WHERE UT_KEY = '" . $USER_SECURITY['UT_KEY'] . "'";
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	// now redirect (action in session)
	header("location:" . getAdminCommand());
}

function DelUserRoles($arCtl,$REQUEST){
	// DB Connection
	$mysqli = db_connect(getConfigDBName());;

	$USER_ROLES = $REQUEST['USER_ROLES'];

	$query = "DELETE FROM USER_ROLES WHERE UO_KEY = '" . $USER_ROLES['UO_KEY'] . "'";
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);

	// now redirect (action in session)
	header("location:" . getAdminCommand());
}
function DelUserDivisions($arCtl,$USERS,$USER_DIVISIONS){
	// DB Connection
	$mysqli = db_connect(getConfigDBName());;

	$query = "DELETE FROM USER_DIVISIONS WHERE UD_KEY = '" . $USER_DIVISIONS['UD_KEY'] . "'";
	//echo $query;
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	ShowUsers($arCtl,$USERS);
} 


function DelUserCustomers($arCtl, $USERS,$USER_CUSTOMERS){
	// DB Connection
	$mysqli = db_connect(getConfigDBName());;

	$query = "DELETE FROM USER_CUSTOMERS WHERE UC_KEY = '" . $USER_CUSTOMERS['UC_KEY'] . "'";
	//echo $query;
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	ShowUsers($arCtl,$USERS);
} 


?>


