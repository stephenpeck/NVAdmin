<?php
function StartPhases($arCtl,$JOBS,$PHASES, $STORES){
	
	$db = db_connect();
	
	$sql = "update PHASES set PH_STATUS = 'Started', PH_ACTUAL_START_DATE = curdate(), PH_USKEY = '" . $_SESSION['US_KEY'] . "' where PH_KEY = " . $PHASES['PH_KEY'];
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	
	if ($arCtl['JPT_FUNCTION'] != ""){
		$arCtl['JPT_FUNCTION']($arCtl,$JOBS,$PHASES,$STORES);
	} else {
		ShowPhases($arCtl,$JOBS,$PHASES);
	}
}

function ShowPORequest($arCtl,$JOBS,$PHASES){

	/************************************************************************************************
	 Get Data
	*******************************************PH_STATUS****************************************************/
	// DB Connection
	$db = db_connect();

	$sql = "select PH_KEY, PH_SEQUENCE, PH_STATUS, PH_JTPKEY,date_format(PH_ACTIONDATE1,'%d/%m/%y') PH_ACTIONDATE1, PH_ACTIONSTATUS1, date_format(PH_ACTIONDATE2,'%d/%m/%y') PH_ACTIONDATE2, PH_ACTIONSTATUS2, date_format(PH_ACTIONDATE3,'%d/%m/%y') PH_ACTIONDATE3, PH_ACTIONSTATUS3, PH_COMMENT from PHASES where PH_KEY = '" . $PHASES['PH_KEY'] . "'";
	//	echo $sql;
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$PHASES = mysqli_fetch_assoc($result);
	
	
	$sql = "select PH_KEY, PH_JOKEY, PH_SEQUENCE, date_format(PH_START_DATE,'%d/%m/%y') PH_START_DATE from PHASES where PH_JOKEY = '" . $JOBS['JO_KEY'] . "' and PH_SEQUENCE = '4' ";
	//	echo $sql;
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$PHASESELEC = mysqli_fetch_assoc($result);
	
	
	

	$sql = "select * from JOBS, STORES where JO_KEY = '" . $JOBS['JO_KEY'] . "' and JO_STKEY = ST_KEY";
	//	echo $sql;
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$JOBS = mysqli_fetch_assoc($result);

	// get no compatibles
	$sql = "select * from ASSETS where AS_JOKEY = '" . $JOBS['JO_KEY'] . "' and AS_COMPATIBLE_CONTROLLER != 'Y'";
	//	echo $sql;
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	while($ASSETS = mysqli_fetch_assoc($result)){
		
		$arASSETS[] = $ASSETS;
		
	}


	// 	echo $sql;

	 	//echo "<pre>";
	 	//print_r ($PHASES);
	 	//echo "</pre>";
	 	
	 	//echo "<pre>";
	 	//print_r ($JOBS);
	 	//echo "</pre>";

	 	//echo "<pre>";
	 	//print_r ($arASSETS);
	 	//echo "</pre>";

	/************************************************************************************************
	 Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();

	// results info

	$smarty->assign('JOBS',  $JOBS);
	$smarty->assign('PHASES',  $PHASES);
	$smarty->assign('PHASESELEC',  $PHASESELEC);	
	$smarty->assign('arASSETS',  $arASSETS);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('ShowPORequest.tpl');

}

function ShowPhases($arCtl,$JOBS,$PHASES){
	
	/************************************************************************************************
	 Get Data
	*******************************************PH_STATUS****************************************************/
	// DB Connection
	$db = db_connect();
	
	$sql = "select PH_KEY, PH_SEQUENCE, PH_STATUS, PH_JTPKEY,date_format(PH_ACTIONDATE1,'%d/%m/%y') PH_ACTIONDATE1, PH_ACTIONSTATUS1, date_format(PH_ACTIONDATE2,'%d/%m/%y') PH_ACTIONDATE2, PH_ACTIONSTATUS2, date_format(PH_ACTIONDATE3,'%d/%m/%y') PH_ACTIONDATE3, PH_ACTIONSTATUS3, PH_COMMENT from PHASES where PH_KEY = '" . $PHASES['PH_KEY'] . "'";
	//	echo $sql;
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$PHASES = mysqli_fetch_assoc($result);	
	
	$sql = "select * from JOBS where JO_KEY = '" . $JOBS['JO_KEY'] . "'";
	//	echo $sql;
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$JOBS = mysqli_fetch_assoc($result);	
	
	$sql = "select * from JOB_TYPE_PHASES where JTP_KEY = '" . $PHASES['PH_JTPKEY'] . "'";
	//	echo $sql;
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$JOB_TYPE_PHASES = mysqli_fetch_assoc($result);		

	
// 	echo $sql;
	
// 	print_r ($JOB_TYPE_PHASES);
	
	
	
	//Drop Down Menus
	
	$arActionStatusList['N'] = "Not Complete";
	$arActionStatusList['C'] = "Complete";
	
// 	echo "<pre>";
// 	print_r ($arActionStatusList);
// 	echo "</pre>";
	
// 	echo "<pre>";
// 	print_r ($PHASES);
// 	echo "</pre>";	
	
	/************************************************************************************************
	 Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();
	
	// results info

	$smarty->assign('JOBS',  $JOBS);
	$smarty->assign('PHASES',  $PHASES);
	$smarty->assign('JOB_TYPE_PHASES',  $JOB_TYPE_PHASES);	
	$smarty->assign('ActionStatusList',  $arActionStatusList);		
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);	
	
	$smarty->display('ShowPhases.tpl');

}


function UpdPhases($arCtl,$JOBS,$PHASES,$STORES){
	
	//print_r ($PHASES);

	$PHASES['PH_ACTIONDATE1'] = convertdate($PHASES['PH_ACTIONDATE1']);
	$PHASES['PH_ACTIONDATE2'] = convertdate($PHASES['PH_ACTIONDATE2']);
	$PHASES['PH_ACTIONDATE3'] = convertdate($PHASES['PH_ACTIONDATE3']);	
	$PHASES['PH_ACTIONDATE4'] = convertdate($PHASES['PH_ACTIONDATE4']);	
	
	//print_r ($PHASES);	
	
	/************************************************************************************************
	 Get Data
	***********************************************************************************************/
	// DB Connection
	$mysqli = db_connect();
	
	if ($PHASES['PH_ACTIONSTATUS1'] != ""){
		if ($PHASES['PH_ACTIONSTATUS1'] == "Complete"){
			$query = "UPDATE PHASES SET
				PH_ACTIONDATE1 = '" . $PHASES['PH_ACTIONDATE1'] . "',
				PH_ACTIONSTATUS1 = '" . $PHASES['PH_ACTIONSTATUS1'] . "',
				PH_ACTIONDATEACTUAL1 = curdate()
				WHERE PH_KEY = '" . $PHASES['PH_KEY'] . "'";
		} else {
			$query = "UPDATE PHASES SET
				PH_ACTIONDATE1 = '" . $PHASES['PH_ACTIONDATE1'] . "',
				PH_ACTIONSTATUS1 = '" . $PHASES['PH_ACTIONSTATUS1'] . "'
				WHERE PH_KEY = '" . $PHASES['PH_KEY'] . "'";
		}
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);
	}


	if ($PHASES['PH_ACTIONSTATUS2'] != ""){
		if ($PHASES['PH_ACTIONSTATUS2'] == "Complete"){
			$query = "UPDATE PHASES SET
				PH_ACTIONDATE2 = '" . $PHASES['PH_ACTIONDATE2'] . "',
				PH_ACTIONSTATUS2 = '" . $PHASES['PH_ACTIONSTATUS2'] . "',
				PH_ACTIONDATEACTUAL2 = curdate()
				WHERE PH_KEY = '" . $PHASES['PH_KEY'] . "'";
		} else {
			$query = "UPDATE PHASES SET
				PH_ACTIONDATE2 = '" . $PHASES['PH_ACTIONDATE2'] . "',
				PH_ACTIONSTATUS2 = '" . $PHASES['PH_ACTIONSTATUS2'] . "'
				WHERE PH_KEY = '" . $PHASES['PH_KEY'] . "'";
		}
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);
	}

	
	if ($PHASES['PH_ACTIONSTATUS3'] != ""){
		if ($PHASES['PH_ACTIONSTATUS3'] == "Complete"){
			$query = "UPDATE PHASES SET
				PH_ACTIONDATE3 = '" . $PHASES['PH_ACTIONDATE3'] . "',
				PH_ACTIONSTATUS3 = '" . $PHASES['PH_ACTIONSTATUS3'] . "',
				PH_ACTIONDATEACTUAL3 = curdate()
				WHERE PH_KEY = '" . $PHASES['PH_KEY'] . "'";
		} else {
			$query = "UPDATE PHASES SET
				PH_ACTIONDATE3 = '" . $PHASES['PH_ACTIONDATE3'] . "',
				PH_ACTIONSTATUS3 = '" . $PHASES['PH_ACTIONSTATUS3'] . "'
				WHERE PH_KEY = '" . $PHASES['PH_KEY'] . "'";
		}
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);
	}
	

	if ($PHASES['PH_ACTIONSTATUS4'] != ""){
		if ($PHASES['PH_ACTIONSTATUS4'] == "Complete"){
			$query = "UPDATE PHASES SET
				PH_ACTIONDATE4 = '" . $PHASES['PH_ACTIONDATE4'] . "',
				PH_ACTIONSTATUS4 = '" . $PHASES['PH_ACTIONSTATUS4'] . "',
				PH_ACTIONDATEACTUAL4 = curdate()
				WHERE PH_KEY = '" . $PHASES['PH_KEY'] . "'";
		} else {
			$query = "UPDATE PHASES SET
				PH_ACTIONDATE4 = '" . $PHASES['PH_ACTIONDATE4'] . "',
				PH_ACTIONSTATUS4 = '" . $PHASES['PH_ACTIONSTATUS4'] . "'
				WHERE PH_KEY = '" . $PHASES['PH_KEY'] . "'";
		}
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);
	}
	
	//always update comment
	
	$query = "UPDATE PHASES SET
				PH_COMMENT = '" . $PHASES['PH_COMMENT'] . "'
				WHERE PH_KEY = '" . $PHASES['PH_KEY'] . "'";
	
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	//print_r ($arCtl);
	

	if ($arCtl['Screen'] == "Y"){		

		if ($arCtl['Install'] == "Y"){	
		
		ShowInstall($arCtl,$JOBS,$PHASES,$STORES);
		
		}else	
			
		ShowJobs($arCtl,$JOBS);
	
	}
	
	
}

function UpdPhaseDates($arCtl,$JOBS,$PHASES){
	

	$PHASES['PH_START_DATE'] = convertdate($PHASES['PH_START_DATE']);
	$PHASES['PH_END_DATE'] = convertdate($PHASES['PH_END_DATE']);	
	
	
	/************************************************************************************************
	 Get Data
	***********************************************************************************************/
	// DB Connection
	$mysqli = db_connect();

		$query = "UPDATE PHASES SET
			PH_START_DATE = '" . $PHASES['PH_START_DATE'] . "',
			PH_END_DATE = '" . $PHASES['PH_END_DATE'] . "'
			WHERE PH_KEY = '" . $PHASES['PH_KEY'] . "'";
				
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);
		
	ShowJobs($arCtl,$JOBS);

}


function CompletePhases($arCtl,$JOBS,$PHASES, $STORES){

	$db = db_connect();

	$sql = "update PHASES set PH_STATUS = 'Complete', PH_ACTUAL_END_DATE = curdate() where PH_KEY = " . $PHASES['PH_KEY'];
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);

	ShowJobs($arCtl,$JOBS,$PHASES);

}

?>