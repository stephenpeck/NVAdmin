<?php

function ListCustomers($arCtl,$arSAFE_REQUEST){

	/************************************************************************************************
	 Get Data
	***********************************************************************************************/
	// DB Connection
	$mysqli = db_connect(getDBName());
	
	$sql  = "SELECT * FROM CUSTOMERS ORDER BY CU_NAME";
	
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	
	while($qd = mysqli_fetch_assoc($result)){
		$arCUSTOMERS[] = $qd;
	}
	
	/************************************************************************************************
	 Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();
	
	// results info
	$smarty->assign('arCUSTOMERS',  $arCUSTOMERS);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);
	
	$smarty->display('standard/ListCustomers.tpl');

}

function ListCustomerLocations($arCtl,$arSAFE_REQUEST){

	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$mysqli = db_connect(getDBName());

	$sql  = "SELECT * FROM LOCATIONS 
			where LO_CUKEY = '" . $arCtl['CU_KEY'] . "' 
			ORDER BY LO_CODE, LO_NAME";

	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);

	while($qd = mysqli_fetch_assoc($result)){
		$arLOCATIONS[] = $qd;
	}


	// create drop downs
	$sql = "select * from CUSTOMERS order by CU_NAME";
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	
	$arCustomerList[''] = 'Select Customer';
	while($qd = mysqli_fetch_assoc($result)){
		$arCustomerList[$qd['CU_KEY']] = $qd['CU_NAME'];
	}

	$arCustomerGroups[''] = "";
	$arCustomerGroups['1'] = "London";
	$arCustomerGroups['2'] = "Central & Eastern";
	$arCustomerGroups['3'] = "etc";
	
	/************************************************************************************************
	 Assign Template Variables
	 ***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arLOCATIONS',  $arLOCATIONS);
	$smarty->assign('arCustomerList',  $arCustomerList);
	$smarty->assign('arCustomerGroups',  $arCustomerGroups);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('standard/ListCustomerLocations.tpl');

}

function ShowCustomers($arCtl,$arSAFE_REQUEST){

	/************************************************************************************************
	 Get Data
	***********************************************************************************************/
	// DB Connection
	$mysqli = db_connect(getDBName());
	
	$CUSTOMERS = $arSAFE_REQUEST['CUSTOMERS'];
	
	if ($CUSTOMERS['CU_KEY'] != ""){
		$sql  = "SELECT * FROM CUSTOMERS where CU_KEY = '" . $CUSTOMERS['CU_KEY'] . "'";
		
		$result = mysqli_query($mysqli,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		
		$CUSTOMERS = mysqli_fetch_assoc($result);
		
	} 

	// do drop downs here
	
	// create dropdown for Course Groups

	/************************************************************************************************
	 Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();
	
	// results info
	$smarty->assign('CUSTOMERS',  $CUSTOMERS);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);
	
	$smarty->display('standard/ShowCustomers.tpl');

}

function UpdCustomers($arCtl,$arSAFE_REQUEST){

	$mysqli = db_connect(getDBName());
	
	$arSQL['CUSTOMERS'][0] = $arSAFE_REQUEST['CUSTOMERS'];
	saveToDB($arSQL);
	
	header("location:" . getAdminCommand());

}


function DelCustomers($arCtl,$arSAFE_REQUEST){
	// DB Connection
	$mysqli = db_connect(getDBName());
	
	$arSQL['CUSTOMERS'][0] = $arSAFE_REQUEST['CUSTOMERS'];

	deleteFromDB($arSQL);
	
	header("location:" . getAdminCommand());
	
} 
function UpdCustomerLocations($arCtl,$arSAFE_REQUEST){

	$mysqli = db_connect(getDBName());

	$arSQL['LOCATIONS'][0] = $arSAFE_REQUEST['LOCATIONS'];
	saveToDB($arSQL);

	header("location:" . getAdminCommand());

}


function DelCustomerLocationss($arCtl,$arSAFE_REQUEST){
	// DB Connection
	$mysqli = db_connect(getDBName());

	$arSQL['LOCATIONS'][0] = $arSAFE_REQUEST['LOCATIONS'];

	deleteFromDB($arSQL);

	header("location:" . getAdminCommand());

}



?>