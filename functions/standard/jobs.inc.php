<?php 
function ListJobs($arCtl,$arSAFE_REQUEST){
	
	$mysqli = db_connect(getDBName());
	
	
	$sql = "select * from JOBS, CUSTOMERS, JOBTYPES
			WHERE CU_KEY = JO_CUKEY
			AND JT_KEY = JO_JTKEY";
	
	
	if ($arCtl['CU_KEY'] != ""){
		$sql .= " AND CU_KEY = " . $arCtl['CU_KEY'];
	}
	if ($arCtl['CU_KEY'] != ""){
		$sql .= " AND CU_KEY = " . $arCtl['CU_KEY'];
	}
	
	$sql .= " order by JO_KEY";
	
	//echo $sql;
	
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$count=0;
	while($qd = mysqli_fetch_assoc($result)){
		$arJOBS[$count] = $qd;
		
		// now get latest step
		$sql = "select * from JOBTYPESTEPS,JOBSTEPS  
				LEFT OUTER JOIN " . getConfigDBName() . ".USERS ON JOS_OWNER_USKEY = US_KEY
				where JOS_JTSKEY = JTS_KEY 
				and JOS_JOKEY = " . $qd['JO_KEY'] . " 
				AND JOS_ENDDATE is null";  

		if ($arCtl['US_KEY'] != ""){
			$sql .= " AND JOS_OWNER_USKEY = " . $arCtl['US_KEY'];
		}
		
		$sql .= " order by JTS_SEQ LIMIT 1";
		//echo $sql;
		
		$result2 = mysqli_query($mysqli,$sql);
		if (!$result2) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		$LATESTSTEP = mysqli_fetch_assoc($result2);
		
		if ($LATESTSTEP['JOS_KEY'] == ""){
			$arJOBS[$count]['STATUS'] = "All Steps Completed";
		} else {
			$arJOBS[$count]['STATUS'] = "Open";
			$arJOBS[$count]['US_NAME'] = $LATESTSTEP['US_NAME'];
			$arJOBS[$count]['NEXTSTEP'] = $LATESTSTEP['JTS_PHASENAME'] . " " . $LATESTSTEP['JTS_PHASENAME'];
			$arJOBS[$count]['NEXTSTEPDATE'] =  $LATESTSTEP['JTS_ENDDATE_FORECAST'];
		}
		$count++;
		
	}

	
	$sql = "select * from CUSTOMERS order by CU_NAME";
	$result = mysqli_query($mysqli,$sql);
	$arCustomers[''] = "Select Customer";
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	while($qd = mysqli_fetch_assoc($result)){
		$arCustomers[$qd['CU_KEY']] = $qd['CU_NAME'];
	}
	$sql = "select * from JOBTYPES order by JT_NAME";
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$arJobTypes[''] = "Select Job Type";
	while($qd = mysqli_fetch_assoc($result)){
		$arJobTypes[$qd['JT_KEY']] = $qd['JT_NAME'];
	}
	
	$mysqli2 = db_connect(getConfigDBName());
	$sql = "select * from USERS";
	$result = mysqli_query($mysqli2,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$arOwnerList[""]	= "Select Owner";
	while ($qd = mysqli_fetch_assoc($result)){
		$arOwnerList[$qd['US_KEY']]	= $qd['US_NAME'];
	}
	
	/************************************************************************************************
	 Assign Template Variables
	 ***********************************************************************************************/
	$smarty = getSmarty();
	
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arCustomerList',    $arCustomers);
	$smarty->assign('arJobTypeList',    $arJobTypes);
	$smarty->assign('arOwnerList',    $arOwnerList);
	$smarty->assign('arJOBS',    $arJOBS);
	$smarty->assign('SESSION',   $_SESSION);
	
	
	$smarty->display('standard/ListJobs.tpl');
	
}

function ShowNewJob($arCtl,$arSAFE_REQUEST){

	/************************************************************************************************
	 Get Data
	***********************************************************************************************/
	// DB Connection
	$mysqli = db_connect(getDBName());

	$JOBS = $arSAFE_REQUEST['JOBS'];
	unset($_SESSION['JOBS']);
	
	// get custoemrs etc
	
	$sql = "select * from CUSTOMERS order by CU_NAME";
	$result = mysqli_query($mysqli,$sql);
	$arCustomers[''] = "Select Customer";
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	while($qd = mysqli_fetch_assoc($result)){
		$arCustomers[$qd['CU_KEY']] = $qd['CU_NAME'];
	}

	if ($JOBS['JO_JTKEY'] != ""){
		$sql = "select * from JOBTYPES where JT_KEY = " . $JOBS['JO_JTKEY'];
		$result = mysqli_query($mysqli,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		$JOBTYPES = mysqli_fetch_assoc($result);
		$JOBS = array_merge($JOBS,$JOBTYPES);
		
		
// 		$arRoleList[''] = "";
// 		$arRoleList['1'] = "Consultant";
// 		$arRoleList['2'] = "Design";
// 		$arRoleList['3'] = "Office Admin";

		// get staff lists
		$mysqli2 = db_connect(getConfigDBName());
		$sql = "select * from USERS, USER_ROLES where UO_USKEY = US_KEY and UO_ROKEY = '1'";
		echo $sql;
		$result = mysqli_query($mysqli2,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		$arOwnerList[""]	= "Select Consultant";	
		while ($qd = mysqli_fetch_assoc($result)){
			$arOwnerList[$qd['US_KEY']]	= $qd['US_NAME'];	
		}
		$sql = "select * from USERS, USER_ROLES where UO_USKEY = US_KEY and UO_ROKEY = '3'";
		$result = mysqli_query($mysqli2,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		$arOwner2List[""]	= "Select Admin";	
		while ($qd = mysqli_fetch_assoc($result)){
			$arOwner2List[$qd['US_KEY']]	= $qd['US_NAME'];
		}
		
	}

	
	if ($JOBS['JO_CUKEY'] != ""){
		$sql = "select * from LOCATIONS where LO_CUKEY = " . $JOBS['JO_CUKEY'] . " order by LO_NAME";
		$result = mysqli_query($mysqli,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		$arLocations[''] = "Select Location";
		while($qd = mysqli_fetch_assoc($result)){
			$arLocations[$qd['LO_KEY']] = $qd['LO_NAME'];
		}
	}

	$sql = "select * from JOBTYPES order by JT_NAME";
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$arJobTypes[''] = "Select Job Type";
	while($qd = mysqli_fetch_assoc($result)){
		$arJobTypes[$qd['JT_KEY']] = $qd['JT_NAME'];
	}
	
	/************************************************************************************************
	 Assign Template Variables
	 ***********************************************************************************************/
	$smarty = getSmarty();
	
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arCustomerList',    $arCustomers);
	$smarty->assign('arLocationList',    $arLocations);
	$smarty->assign('arJobTypeList',    $arJobTypes);
	$smarty->assign('arOwnerList',    $arOwnerList);
	$smarty->assign('arOwner2List',    $arOwner2List);
	$smarty->assign('JOBS',    $JOBS);
	$smarty->assign('SESSION',   $_SESSION);
	
	
	$smarty->display('standard/ShowNewJob.tpl');
	
}

function ShowJob($arCtl,$arSAFE_REQUEST){

	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$mysqli = db_connect(getDBName());

	$JOBS = $arSAFE_REQUEST['JOBS'];
	
	$sql = "select * from JOBS, JOBTYPES where JO_JTKEY = JT_KEY AND JO_KEY = " . $JOBS['JO_KEY'];
	//echo $sql;
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$JOBS = mysqli_fetch_assoc($result);
	
	$sql = "select * from JOBTYPESTEPS, JOBSTEPS 
			LEFT OUTER JOIN " . getConfigDBName() . ".USERS ON JOS_OWNER_USKEY = US_KEY
			where JOS_JTSKEY = JTS_KEY AND JOS_JOKEY = " . $JOBS['JO_KEY'];
	//echo $sql;
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$count=0;
	while($qd = mysqli_fetch_assoc($result)) {
		$arJOBSTEPS[$count] = $qd;
		
		// make model
		
		$sql = "select * from JOBTYPESTEPFIELDS 
				LEFT OUTER JOIN JOBSTEPFIELDS ON JTSF_KEY = JOSF_JTSFKEY
				WHERE JTSF_JTSKEY = " . $qd['JOS_JTSKEY']; 
 		//echo $sql;
 		$result2 = mysqli_query($mysqli,$sql);
 		if (!$result2) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
 		while($qd2 = mysqli_fetch_assoc($result2)) {
 			$arJOBSTEPS[$count]['FIELDS'][] = $qd2;
 		}
		$count++;
	}
	
	
	$sql = "select * from CUSTOMERS order by CU_NAME";
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	while($qd = mysqli_fetch_assoc($result)){
		$arCustomers[$qd['CU_KEY']] = $qd['CU_NAME'];
	}
	
	if ($JOBS['JO_CUKEY'] != ""){
		$sql = "select * from LOCATIONS where LO_CUKEY = " . $JOBS['JO_CUKEY'] . " order by LO_NAME";
		$result = mysqli_query($mysqli,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		while($qd = mysqli_fetch_assoc($result)){
			$arLocations[$qd['LO_KEY']] = $qd['LO_NAME'];
		}
	}
	
	$sql = "select * from JOBTYPES order by JT_NAME";
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	while($qd = mysqli_fetch_assoc($result)){
		$arJobTypes[$qd['JT_KEY']] = $qd['JT_NAME'];
	}
	
	// get staff lists
	$mysqli2 = db_connect(getConfigDBName());
	$sql = "select * from USERS, USER_ROLES where UO_USKEY = US_KEY and UO_ROKEY = '1'";
	//echo $sql;
	$result = mysqli_query($mysqli2,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$arOwnerList[""]	= "Select Consultant";
	while ($qd = mysqli_fetch_assoc($result)){
		$arOwnerList[$qd['US_KEY']]	= $qd['US_NAME'];
	}
	$sql = "select * from USERS, USER_ROLES where UO_USKEY = US_KEY and UO_ROKEY = '3'";
	$result = mysqli_query($mysqli2,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$arOwner2List[""]	= "Select Admin";
	while ($qd = mysqli_fetch_assoc($result)){
		$arOwner2List[$qd['US_KEY']]	= $qd['US_NAME'];
	}

	$sql = "select * from USERS";
	$result = mysqli_query($mysqli2,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$arFullOwnerList[""]	= "Select Owner";
	while ($qd = mysqli_fetch_assoc($result)){
		$arFullOwnerList[$qd['US_KEY']]	= $qd['US_NAME'];
	}
	
	/************************************************************************************************
	 Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();

	
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arCustomerList',    $arCustomers);
	$smarty->assign('arJobTypeList',    $arJobTypes);
	$smarty->assign('arLocationList',    $arLocations);
	$smarty->assign('arFullOwnerList',    $arFullOwnerList);
	$smarty->assign('arOwnerList',    $arOwnerList);
	$smarty->assign('arOwner2List',    $arOwner2List);
	$smarty->assign('JOBS',    $JOBS);
	$smarty->assign('arJOBSTEPS',    $arJOBSTEPS);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('standard/ShowJob.tpl');

}


function ListJobTypes($arCtl,$arSAFE_REQUEST){

	/************************************************************************************************
	 Get Data
	***********************************************************************************************/
	// DB Connection
	$mysqli = db_connect(getDBName());
	
	if ($arCtl['JT_KEY'] != ""){
		
		$sql  = "SELECT * FROM JOBTYPES where JT_KEY = " . $arCtl['JT_KEY'];
		
		$result = mysqli_query($mysqli,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
		$count = 0;
		while($qd = mysqli_fetch_assoc($result)){
			$arJOBTYPES[$count] = $qd;
			$arJOBTYPES[$count]['STEPOWNER'][''] = "";
			$arJOBTYPES[$count]['STEPOWNER']['JO_OWNER_USKEY'] = $qd['JT_OWNERTYPE'];
			$arJOBTYPES[$count]['STEPOWNER']['JO_OWNER_USKEY2'] = $qd['JT_OWNERTYPE2'];;
			
			$sql = "select * from JOBTYPESTEPS where JTS_JTKEY = " .$qd['JT_KEY'] . " ORDER by JTS_SEQ";
			$result2 = mysqli_query($mysqli,$sql);
			if (!$result2) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
			$count2 = 0;
			while($qd2 = mysqli_fetch_assoc($result2)){
				$arJOBTYPES[$count]['JOBTYPESTEPS'][$count2] = $qd2;
				$sql = "select * from JOBTYPESTEPFIELDS where JTSF_JTSKEY = " .$qd2['JTS_KEY'];
				$result3 = mysqli_query($mysqli,$sql);
				if (!$result3) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
				while($qd3 = mysqli_fetch_assoc($result3)){
					$arJOBTYPES[$count]['JOBTYPESTEPS'][$count2]['JOBTYPESTEPFIELDS'][] = $qd3;
				}
				$count2++;
			}
		}
	}
	
	// get DD
	
	$arYesNoList[''] = ""; 
	$arYesNoList['Y'] = "Yes";
	$arYesNoList['N'] = "No";

	
	$arFieldTypeList[''] = "";
	$arFieldTypeList['TEXT'] = "Text Field";
	$arFieldTypeList['FILE'] = "Document Upload";

	
	$arJobTypes[''] = "Select Job Types";
	$sql = "select * from JOBTYPES order by JT_NAME";
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$count = 0;
	while($qd = mysqli_fetch_assoc($result)){
		$arJobTypes[$qd['JT_KEY']] = $qd['JT_NAME'];
	}
	
	// get enumeration
	
	/************************************************************************************************
	 Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();
	
	// results info
	$smarty->assign('arJOBTYPES',  $arJOBTYPES);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arYesNoList',    $arYesNoList);
	$smarty->assign('arFieldTypeList',    $arFieldTypeList);
	$smarty->assign('arJobTypeList',    $arJobTypes);
	$smarty->assign('SESSION',   $_SESSION);
	
	$smarty->display('standard/ListJobTypes.tpl');

}

function UpdNewJob($arCtl,$arSAFE_REQUEST){

	$mysqli = db_connect(getDBName());

// 	print_r($arSAFE_REQUEST['JOBS']);
	
	$arSQL['JOBS'][0] = $arSAFE_REQUEST['JOBS'];
	$arSQL = saveToDB($arSQL);

// 	print_r($arSQL);
	
	if ($arSAFE_REQUEST['JOBS']['JO_KEY'] == ""){
		// new job
		$_SESSION['JOBS']['JO_KEY'] = $arSQL['JOBS'][0]['JO_KEY'];
		
	} else {
		// already created override and go to main job
		$_SESSION['NextAction'] = "ShowJob";
	}

// 	exit();
	
	header("location:" . getAdminCommand());

}

function UpdNewJobSteps($arCtl,$arSAFE_REQUEST){

	$mysqli = db_connect(getDBName());

// 	print_r($arSAFE_REQUEST['JOBS']);

	$arSQL['JOBS'][0] = $arSAFE_REQUEST['JOBS'];
	$arSQL = saveToDB($arSQL);

	// now get steps
	$sql = "select * from JOBTYPESTEPS where JTS_JTKEY = " . $arSAFE_REQUEST['JOBS']['JO_JTKEY'] . " order by JTS_SEQ";
//	echo $sql;
	$result = mysqli_query($mysqli,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$count = 0;
	while($qd = mysqli_fetch_assoc($result)){
			
		$arSQL['JOBSTEPS'][0]['JOS_JOKEY'] = $arSAFE_REQUEST['JOBS']['JO_KEY'] ;
		$arSQL['JOBSTEPS'][0]['JOS_JTSKEY'] = $qd['JTS_KEY'] ;
		$arSQL['JOBSTEPS'][0]['JOS_STARTDATE_FORECAST'] = $arSAFE_REQUEST['JOBS']['JO_TARGETDATE'] ;
		if ($qd['JTS_DURATION'] != 0){
			$arSQL['JOBSTEPS'][0]['JOS_ENDDATE_FORECAST'] = $arSQL['JOBSTEPS'][0]['JOS_STARTDATE_FORECAST'] ;
		}
		// check step record for which user
		$arSQL['JOBSTEPS'][0]['JOS_OWNER_USKEY'] = $arSAFE_REQUEST['JOBS'][$qd['JTS_OWNER']];
		$arSQL = saveToDB($arSQL);
		//print_r($arSQL);
		unset($arSQL);	
	}
	
	header("location:" . getAdminCommand());

}

function UpdJob($arCtl,$arSAFE_REQUEST){

	$mysqli = db_connect(getDBName());

	$arSQL['JOBS'][0] = $arSAFE_REQUEST['JOBS'];
	$arSQL = saveToDB($arSQL);

	header("location:" . getAdminCommand());

}

function UpdJobTypes($arCtl,$arSAFE_REQUEST){

	$mysqli = db_connect(getDBName());

	$arSQL['JOBTYPES'][0] = $arSAFE_REQUEST['JOBTYPES'];
	$arSQL = saveToDB($arSQL);
	
	$_SESSION['arCtl']['JT_KEY'] = $arSQL['JOBTYPES'][0]['JT_KEY'];

	header("location:" . getAdminCommand());

}

function UpdJobTypeSteps($arCtl,$arSAFE_REQUEST){
	
// 	echo "<pre>";
// 	print_r($arSAFE_REQUEST['JOBTYPESTEPS']);
// 	print_r($arSAFE_REQUEST);
// 	echo "</pre>";
// 	echo "HHHHH";
	
	$mysqli = db_connect(getDBName());
	
	$arSQL['JOBTYPESTEPS'][0] = $arSAFE_REQUEST['JOBTYPESTEPS'];
	saveToDB($arSQL);

	header("location:" . getAdminCommand());

}

function UpdJobTypeStepFields($arCtl,$arSAFE_REQUEST){

	$mysqli = db_connect(getDBName());

	$arSQL['JOBTYPESTEPFIELDS'][0] = $arSAFE_REQUEST['JOBTYPESTEPFIELDS'];
	saveToDB($arSQL);

	header("location:" . getAdminCommand());

}

function DelJobTypes($arCtl,$arSAFE_REQUEST){
	// DB Connection
	$mysqli = db_connect(getDBName());

	$arSQL['JOBTYPES'][0] = $arSAFE_REQUEST['JOBTYPES'];

	deleteFromDB($arSQL);

	header("location:" . getAdminCommand());

}

function DelJobTypeSteps($arCtl,$arSAFE_REQUEST){
	// DB Connection
	$mysqli = db_connect(getDBName());

	
	$arSQL['JOBTYPESTEPS'][0] = $arSAFE_REQUEST['JOBTYPESTEPS'];

	deleteFromDB($arSQL);

	
	header("location:" . getAdminCommand());

}

function DelJobTypeStepFields($arCtl,$arSAFE_REQUEST){
	// DB Connection
	$mysqli = db_connect(getDBName());

	$arSQL['JOBTYPESTEPFIELDS'][0] = $arSAFE_REQUEST['JOBTYPESTEPFIELDS'];

	deleteFromDB($arSQL);

	header("location:" . getAdminCommand());

}
?>
