<?php

/**
 * Display appropriate menu

/**
 * List Menu Groups
*/

function ListFunctions($arCtl){
	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$db = db_connect(getControlDBName());
	
	$sql  = "SELECT * FROM FUNCTIONS";

	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);

	while($qd = mysqli_fetch_assoc($result)){
		$arFUNCTIONS[] = $qd;
	}


	/************************************************************************************************
	 Assign Template Variables
	 ***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arFUNCTIONS',  $arFUNCTIONS);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('admin/ListFunctions.tpl');

}
function ListParameters($arCtl){
	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$db = db_connect(getControlDBName());

	$sql  = "SELECT * FROM PARAMETERS";

	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);

	while($qd = mysqli_fetch_assoc($result)){
		$arPARAMETERS[] = $qd;
	}


	/************************************************************************************************
	 Assign Template Variables
	 ***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arPARAMETERS',  $arPARAMETERS);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('admin/ListParameters.tpl');

}

function ListTables($arCtl){
	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$db = db_connect(getControlDBName());
	
	$sql  = "SELECT * FROM TABLES";

	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);

	while($qd = mysqli_fetch_assoc($result)){
		$arTABLES[] = $qd;
	}


	/************************************************************************************************
	 Assign Template Variables
	 ***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arTABLES',  $arTABLES);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('admin/ListTables.tpl');

}

function ListCompany($arCtl){
	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$db = db_connect(getControlDBName());

	$sql  = "SELECT * FROM COMPANY";

	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$count=0;
	while($qd = mysqli_fetch_assoc($result)){
		$arCOMPANY[$count] = $qd;
		$sql  = "SELECT * FROM " . getConfigDBName(). ".COMPANY_FUNCTIONS, FUNCTIONS where CF_FUKEY = FU_KEY AND CF_COMPANY = '" . $qd['CO_COMPANY'] . "'";
		//echo $sql;
		$result2 = mysqli_query($db,$sql);
		if (!$result2) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		while($qd2 = mysqli_fetch_assoc($result2)){
			$arCOMPANY[$count]['COMPANY_FUNCTIONS'][] = $qd2;
		}
		$count++;
	}

	$sql  = "SELECT * FROM FUNCTIONS";
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$arFunctionList[''] = "Select Functions";
	while($qd = mysqli_fetch_assoc($result)){
		$arFunctionList[$qd['FU_KEY']] = $qd['FU_NAME'] . " (" . $qd['FU_DESCRIPTION'] . ")";
	}
	
	/************************************************************************************************
	 Assign Template Variables
	 ***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arCOMPANY',  $arCOMPANY);
	$smarty->assign('arFunctionList',  $arFunctionList);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('admin/ListCompany.tpl');

}

function ListEnvironments($arCtl){
	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$db = db_connect(getControlDBName());

	$sql  = "SELECT * FROM ENVIRONMENTS";

	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$count=0;
	while($qd = mysqli_fetch_assoc($result)){
		$arENVIRONMENTS[$count] = $qd;
		
		$sql = "select * from ENVIRONMENT_PARAMETERS, PARAMETERS where EP_PAKEY = PA_KEY AND EP_ENKEY = ". $qd['EN_KEY'];
		$result2 = mysqli_query($db,$sql);
		if (!$result2) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		while($qd2 = mysqli_fetch_assoc($result2)){
			$arENVIRONMENTS[$count]['ENVIRONMENTPARAMETERS'][] = $qd2;
		}
		$count++;
	}

	$sql  = "SELECT * FROM PARAMETERS";
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$arParameterList[''] = "";
	while($qd = mysqli_fetch_assoc($result)){
		$arParameterList[$qd['PA_KEY']] = $qd['PA_NAME'];
	}

	$sql  = "SELECT * FROM COMPANY";
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$arCompanyList[''] = "";
	while($qd = mysqli_fetch_assoc($result)){
		$arCompanyList[$qd['CO_KEY']] = $qd['CO_NAME'];
	}
	
	/************************************************************************************************
	 Assign Template Variables
	 ***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arENVIRONMENTS',  $arENVIRONMENTS);
	$smarty->assign('arCompanyList',  $arCompanyList);
	$smarty->assign('arParameterList',  $arParameterList);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('admin/ListEnvironments.tpl');

}


function UpdTables($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['TABLES'][0] = $arSAFE_REQUEST['TABLES'];
	$arSQL = saveToDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function UpdFunctions($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['FUNCTIONS'][0] = $arSAFE_REQUEST['FUNCTIONS'];
	$arSQL = saveToDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function UpdEnvironments($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['ENVIRONMENTS'][0] = $arSAFE_REQUEST['ENVIRONMENTS'];
	$arSQL = saveToDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function UpdCompany($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['COMPANY'][0] = $arSAFE_REQUEST['COMPANY'];
	$arSQL = saveToDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function UpdParameters($arCtl,$arSAFE_REQUEST){

	print_r($arSAFE_REQUEST['PARAMETERS']);
	$arSQL['PARAMETERS'][0] = $arSAFE_REQUEST['PARAMETERS'];
	$arSQL = saveToDB($arSQL);
	
	header("location:" . getAdminCommand());

}
function UpdEnvironmentParameters($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['ENVIRONMENT_PARAMETERS'][0] = $arSAFE_REQUEST['ENVIRONMENT_PARAMETERS'];
	$arSQL = saveToDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function UpdCompanyFunctions($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['COMPANY_FUNCTIONS'][0] = $arSAFE_REQUEST['COMPANY_FUNCTIONS'];
	$arSQL = saveToDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function DelEnvironmentParameters($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['ENVIRONMENT_PARAMETERS'][0] = $arSAFE_REQUEST['ENVIRONMENT_PARAMETERS'];
	$arSQL = deleteFromDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function DelParameters($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['PARAMETERS'][0] = $arSAFE_REQUEST['PARAMETERS'];
	$arSQL = deleteFromDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function DelTables($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['TABLES'][0] = $arSAFE_REQUEST['TABLES'];
	$arSQL = deleteFromDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function DelCompanyFunctions($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['COMPANY_FUNCTIONS'][0] = $arSAFE_REQUEST['COMPANY_FUNCTIONS'];
	$arSQL = deleteFromDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function DelEnvironments($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['ENVIRONMENT'][0] = $arSAFE_REQUEST['ENVIRONMENT'];
	$arSQL = deleteFromDB($arSQL);

	// 	exit();
	header("location:" . getAdminCommand());

}
function DelFunctions($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['FUNCTIONS'][0] = $arSAFE_REQUEST['FUNCTIONS'];
	$arSQL = deleteFromDB($arSQL);
	
	// 	exit();
	header("location:" . getAdminCommand());

}
function DelCompany($arCtl,$arSAFE_REQUEST){

	// 	print_r($arSAFE_REQUEST['JOBS']);
	$arSQL['COMPANY'][0] = $arSAFE_REQUEST['COMPANY'];
	$arSQL = deleteFromDB($arSQL);
	// 	exit();
	header("location:" . getAdminCommand());

}


?>
