<?php

function ListOrderSkills($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getAdminDBName());
	
	$sql = "select  * from ORDERS, ACTIVITIES, COURSES, TRAINERS, CUSTOMER 
			where CO_KEY = AC_COKEY
			AND OD_KEY = AC_ODKEY
			AND AC_TRKEY = TR_KEY
			AND OD_CUKEY = CU_KEY  ";

	if ($arCtl['AC_DATE'] != ""){
		$sql .= " AND AC_DATE >= '" . dateToDB($arCtl['AC_DATE']) . "'";
	}
	if ($arCtl['CO_KEY'] != ""){
		$sql .= " AND CO_KEY ='" . $arCtl['CO_KEY'] . "'";
	}
	
	$sql .= " order by AC_DATE, OD_REF";
	
 	//echo $sql; 
// 	print_r($arCtl);
	if ($arCtl['Run'] != ""){
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)	{ echo  sql_error (); }
		$count=0;
		while ($qd = mysqli_fetch_assoc($result)){
			$arORDERS[$count] = $qd;
			// get trainer
			$sql = "select CT_TRKEY, if(CT_OPERATOR_EXPIRY > CT_INSTRUCTOR_EXPIRY,CT_OPERATOR_EXPIRY,CT_INSTRUCTOR_EXPIRY) EXPIRYDATE  from COURSETRAINERS where CT_COKEY = " . $qd['CO_KEY'];
			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	{ echo  sql_error (); }
			$arORDERS[$count]['SKILLDESC'] = "Not qualified!";
			while($qd2 = mysqli_fetch_assoc($result2)){
				if ($qd2['CT_TRKEY'] == $qd['TR_KEY']){

					$arORDERS[$count]['EXPIRYDATE'] = $qd2['EXPIRYDATE'];
						
					// now check the expiry dates
					if(strtotime($qd2['EXPIRYDATE']) < strtotime($qd['AC_DATE'])){
						$arORDERS[$count]['SKILLDESC'] = "Not qualified - Date Expired!";
					} else {
						$arORDERS[$count]['SKILLDESC'] = "OK";
					}
				}
			}
			
			if ($arCtl['QUALIFIED'] == "N"){
				if ($arORDERS[$count]['SKILLDESC'] == "OK"){
					unset($arORDERS[$count]);
				} else {
					$count++;
				}
			} elseif ($arCtl['QUALIFIED'] == "Y"){
				if ($arORDERS[$count]['SKILLDESC'] == "Not qualified!" or $arORDERS[$count]['SKILLDESC'] == "Not qualified - Date Expired!"){
					unset($arORDERS[$count]);
				} else {
					$count++;
				}
			} else {
				$count++;
			}
			
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
	

	$arYesNo[''] = "All Orders";
	$arYesNo['Y'] = "Orders with qualified instructors";
	$arYesNo['N'] = "Orders without qualified instructors";
		
	if ($arCtl['AC_DATE'] == ""){
		$arCtl['AC_DATE'] = date('d/m/Y');
	}
	
	// set up smarty
	$smarty = getSmarty();
	
	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arYesNoList',    $arYesNo);
	$smarty->assign('arCourseList',    $arCourseList);
	$smarty->assign('arORDERS',    $arORDERS);
	$smarty->assign('SESSION',   $_SESSION);
	
	// display using template name provided
	$smarty->display("custom/ListOrderSkills.tpl");
	
}




?>