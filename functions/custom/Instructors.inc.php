<?php

function ListInstructors($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getAdminDBName());
	
	
	
	$sql = "select  * from TRAINERS where TR_KEY is not null ";

	if ($arCtl['TR_AVAILABLE'] == "All"){
		$sql .= " AND TR_AVAILABLE <> 'X'";
	}elseif ($arCtl['TR_AVAILABLE'] != ""){
		$sql .= " AND TR_AVAILABLE ='" . $arCtl['TR_AVAILABLE'] . "'";
	}
	if ($arCtl['TR_CLASS'] != ""){
		$sql .= " AND TR_CLASS ='" . $arCtl['TR_CLASS'] . "'";
	}

	if ($arCtl['TR_NAME'] != ""){
		$sql .= " AND TR_NAME like '%" . $arCtl['TR_NAME'] . "%'";
	}
	$sql .= " order by TR_NAME";
	
 //	echo $sql; 
// 	print_r($arCtl);
	if ($arCtl['Run'] != ""){
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)	{ echo  sql_error (); }
		$count=0;
		while ($qd = mysqli_fetch_assoc($result)){
			$arTRAINERS[$count] = $qd;
			
			// get trainer
			$sql = "select * from COURSETRAINERS, COURSES where CT_COKEY = CO_KEY AND CT_TRKEY = '" . $qd['TR_KEY'] . "'";
			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	{ echo  sql_error (); }
			$count2=0;
			while($qd2 = mysqli_fetch_assoc($result2)) {
				//unset($arCOURSES[$count]['InstructorList'][$qd2['TR_KEY']]);
				$arTRAINERS[$count]['COURSETRAINERS'][$count2] = $qd2;
				$arTRAINERS[$count]['COURSETRAINERS'][$count2]['CT_OPERATOR_EXPIRY'] = DatefromDB($qd2['CT_OPERATOR_EXPIRY']);
				$arTRAINERS[$count]['COURSETRAINERS'][$count2]['CT_INSTRUCTOR_EXPIRY'] = DatefromDB($qd2['CT_INSTRUCTOR_EXPIRY']);
				$count2++;
			}

			$count++;
		}
	}

	$arAvailableList['All'] = "Show All";
	$arAvailableList = $arAvailableList + getCodes("TS");
	
//	print_r($arCOURSES);

	if ($arCtl['TR_AVAILABLE'] == ""){
		$arCtl['TR_AVAILABLE'] = "All";
	}
	
	// set up smarty
	$smarty = getSmarty();
	
	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arEmpStatusList',    getCodes("TE"));
	$smarty->assign('arAvailableList2',    $arAvailableList);
	$smarty->assign('arAvailableList',    getCodes("TS"));
	$smarty->assign('arTRAINERS',    $arTRAINERS);
	$smarty->assign('SESSION',   $_SESSION);
	
	// display using template name provided
	$smarty->display("custom/ListInstructors.tpl");
	
}


function UpdInstructors($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getAdminDBName());

//  	print_r($arSAFE_REQUEST);
	
	$TRAINERS = $arSAFE_REQUEST['TRAINERS'];
	

		
	if ($TRAINERS['TR_KEY'] != ""){
		
		$sql = "UPDATE TRAINERS SET
				TR_AVAILABLE = '" . $TRAINERS['TR_AVAILABLE'] . "',
				TR_REF = '" . $TRAINERS['TR_REF'] . "',
				TR_CLASS = '" . $TRAINERS['TR_CLASS'] . "'
				WHERE TR_KEY = " . $TRAINERS['TR_KEY'];
		
	} 
// 	else {
		
// 		$sql = "INSERT INTO COURSETRAINERS (
// 				CT_ACCREDITATION,
// 				CT_OPERATOR_EXPIRY,
// 				CT_INSTRUCTOR_EXPIRY,
// 				CT_TRKEY,
// 				CT_COKEY
// 		) values (
// 				'" . $COURSETRAINERS['CT_ACCREDITATION'] . "',
// 				'" . $COURSETRAINERS['CT_OPERATOR_EXPIRY'] . "',
// 				'" . $COURSETRAINERS['CT_INSTRUCTOR_EXPIRY'] . "',
// 				'" . $COURSETRAINERS['CT_TRKEY'] . "',
// 				'" . $COURSETRAINERS['CT_COKEY'] . "'
// 				)";
// 	}
	  //	echo $sql;
	
	 	
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }

	//exit();
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
}



?>