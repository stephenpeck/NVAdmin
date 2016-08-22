<?php

function ListCourses($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getAdminDBName());
	
	$sql = "select  * from COURSES where CO_KEY is not null ";

	if ($arCtl['CO_SHOW'] != ""){
		$sql .= " AND CO_SHOW ='" . $arCtl['CO_SHOW'] . "'";
	}
	if ($arCtl['CO_KEY'] != ""){
		$sql .= " AND CO_KEY ='" . $arCtl['CO_KEY'] . "'";
	}
	
	$sql .= " order by CO_NAME";
	
// 	echo $sql; 
// 	print_r($arCtl);
	if ($arCtl['Run'] != ""){
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)	{ echo  sql_error (); }
		$count=0;
		while ($qd = mysqli_fetch_assoc($result)){
			$arCOURSES[$count] = $qd;
			
			//echo $qd['IN_TOPSREF'] . "\n";
			// get trainer
			$sql = "select * from TRAINERS order by TR_NAME ";
			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	{ echo  sql_error (); }
			$arCOURSES[$count]['InstructorList'][''] = "Select Instructor";
			while($qd2 = mysqli_fetch_assoc($result2)){
				$arCOURSES[$count]['InstructorList'][$qd2['TR_KEY']] = $qd2['TR_NAME'] . " " . $qd2['TR_CODE'];
			}

			// get trainer
			$sql = "select * from COURSETRAINERS, TRAINERS where CT_TRKEY = TR_KEY AND CT_COKEY = '" . $qd['CO_KEY'] . "'";
			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	{ echo  sql_error (); }
			$count2=0;
			while($qd2 = mysqli_fetch_assoc($result2)) {
				//unset($arCOURSES[$count]['InstructorList'][$qd2['TR_KEY']]);
				$arCOURSES[$count]['COURSETRAINERS'][$count2] = $qd2;
				$arCOURSES[$count]['COURSETRAINERS'][$count2]['CT_OPERATOR_EXPIRY'] = DatefromDB($qd2['CT_OPERATOR_EXPIRY']);
				$arCOURSES[$count]['COURSETRAINERS'][$count2]['CT_INSTRUCTOR_EXPIRY'] = DatefromDB($qd2['CT_INSTRUCTOR_EXPIRY']);
				$count2++;
			}

			$count++;
		}
	}

//	print_r($arCOURSES);
	
	$sql = "select  * from COURSES order by CO_NAME";
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	{ echo  sql_error (); }
	$arCourseList[""] = "Select course";
	while ($qd = mysqli_fetch_assoc($result)){
		$arCourseList[$qd['CO_KEY']] = $qd['CO_NAME'] . " " . $qd['CO_CODE'];
	}
	
	$arYesNo[''] = "";
	$arYesNo['Y'] = "Y";
	$arYesNo['N'] = "N";
	
	$arAccreditation[''] = "Select Accreditation";
	$arAccreditation['ITSSAR'] = "ITSSAR";
	$arAccreditation['RTITB'] = "RTITB";
	$arAccreditation['ALLMI'] = "ALLMI";
	$arAccreditation['JAUPT'] = "JAUPT";
	$arAccreditation['TRG'] = "TRG";
	
		
	
	// set up smarty
	$smarty = getSmarty();
	
	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arAccreditationList',    $arAccreditation);
	$smarty->assign('arYesNoList',    $arYesNo);
	$smarty->assign('arCourseList',    $arCourseList);
	$smarty->assign('arCOURSES',    $arCOURSES);
	$smarty->assign('SESSION',   $_SESSION);
	
	// display using template name provided
	$smarty->display("custom/ListCourses.tpl");
	
}


function UpdCourseTrainers($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getAdminDBName());

//  	print_r($arSAFE_REQUEST);
	
	$COURSETRAINERS = $arSAFE_REQUEST['COURSETRAINERS'];
	
	// check if duplicate
	$sql = "select * from COURSETRAINERS 
			where CT_ACCREDITATION = '" . $COURSETRAINERS['CT_ACCREDITATION'] . "' 
			and CT_TRKEY = '" . $COURSETRAINERS['CT_TRKEY'] . "'
			and CT_COKEY = '" . $COURSETRAINERS['CT_COKEY'] . "'";
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	$Exists = false;
	while($qd = mysqli_fetch_assoc($result2)){
		if ($COURSETRAINERS['CT_KEY'] != ""){
			// check not same 
			if ($COURSETRAINERS['CT_KEY'] != $qd['CT_KEY']){
				$Exists = true;
				$_SESSION['Error'] = "Instructor is already set up for this course and accreditation";
			} 
		} else {
			$Exists = true;
			$_SESSION['Error'] = "Instructor is already set up for this course and accreditation";
		}
	
	}

	$arErrCheck['CT_OPERATOR_EXPIRY'] = "MustExist";
	$arErrCheck['CT_INSTRUCTOR_EXPIRY'] = "MustExist";
	$arErrCheck['CT_ACCREDITATION'] = "MustExist";
	
	if ($COURSETRAINERS['CT_ACCREDITATION'] == "" or $COURSETRAINERS['CT_OPERATOR_EXPIRY'] == "" or $COURSETRAINERS['CT_INSTRUCTOR_EXPIRY'] == ""){
		$Exists = true;
		$_SESSION['Error'] = "All data must be entered to add new instructor";
	}
	
			
	if (!$Exists){
	
		$COURSETRAINERS['CT_OPERATOR_EXPIRY'] = DatetoDB($COURSETRAINERS['CT_OPERATOR_EXPIRY']);
		$COURSETRAINERS['CT_INSTRUCTOR_EXPIRY'] = DatetoDB($COURSETRAINERS['CT_INSTRUCTOR_EXPIRY']);
		
		if ($COURSETRAINERS['CT_KEY'] != ""){
			
			$sql = "UPDATE COURSETRAINERS SET
					CT_ACCREDITATION = '" . $COURSETRAINERS['CT_ACCREDITATION'] . "',
					CT_OPERATOR_EXPIRY = '" . $COURSETRAINERS['CT_OPERATOR_EXPIRY'] . "',
					CT_INSTRUCTOR_EXPIRY = '" . $COURSETRAINERS['CT_INSTRUCTOR_EXPIRY'] . "'
					WHERE CT_KEY = " . $COURSETRAINERS['CT_KEY'];
			
		} else {
			
			$sql = "INSERT INTO COURSETRAINERS (
					CT_ACCREDITATION,
					CT_OPERATOR_EXPIRY,
					CT_INSTRUCTOR_EXPIRY,
					CT_TRKEY,
					CT_COKEY
			) values (
					'" . $COURSETRAINERS['CT_ACCREDITATION'] . "',
					'" . $COURSETRAINERS['CT_OPERATOR_EXPIRY'] . "',
					'" . $COURSETRAINERS['CT_INSTRUCTOR_EXPIRY'] . "',
					'" . $COURSETRAINERS['CT_TRKEY'] . "',
					'" . $COURSETRAINERS['CT_COKEY'] . "'
					)";
		}
	//  	echo $sql;
	
	//  	exit();
	 	
		$result2 = mysqli_query ($mysqli, $sql );
		if (!$result2)	{ echo  sql_error (); }

	} 
		
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
}

function DelCourseTrainers($arCtl,$arSAFE_REQUEST) {

	$mysqli = db_connect(getAdminDBName());

	//  	print_r($arSAFE_REQUEST);

	$COURSETRAINERS = $arSAFE_REQUEST['COURSETRAINERS'];

	if ($COURSETRAINERS['CT_KEY'] != ""){

		$sql = "DELETE FROM COURSETRAINERS WHERE CT_KEY = " . $COURSETRAINERS['CT_KEY'];

		$result2 = mysqli_query ($mysqli, $sql );
		if (!$result2)	{ echo  sql_error (); }
		
		
	} 
	// 	echo $sql;


	// now redirect (action in session)
	header("location:" . getAdminCommand());

}


?>