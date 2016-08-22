<?php

function ListTOPS($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getAdminDBName());
	
	$sql = "select  * from ATTENDEES, COURSES, ACTIVITIES, ORDERS, CERTIFICATES, INDIVIDUALS
			where AT_ACKEY = AC_KEY
			AND CO_KEY = AC_COKEY
			AND CE_ACKEY = AC_KEY
			AND IN_KEY = AT_INKEY
			AND AC_ODKEY = OD_KEY
			AND AT_CERTNO <> ''";

	if ($arCtl['AC_DATE'] != ""){
		$sql .= " AND DATE(AC_DATE) >='" . $arCtl['RQ_DATETIME'] . "'";
	}
	
	if ($arCtl['OD_REF'] != ""){
		$sql .= " AND OD_REF ='" . $arCtl['OD_REF'] . "'";
	}
	
	if ($arCtl['CO_KEY'] != ""){
		$sql .= " AND CO_KEY ='" . $arCtl['CO_KEY'] . "'";
	}
	
	if ($arCtl['AT_TOPSLOADED'] != ""){
		$sql .= " AND AT_TOPSLOADED ='" . $arCtl['AT_TOPSLOADED'] . "'";
	}
	
	$sql .= " order by CE_KEY DESC LIMIT 100";
	
//	echo $sql; 
	if ($arCtl['Run'] != ""){
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)	{ echo  sql_error (); }
		$count=0;
		while ($qd = mysqli_fetch_assoc($result)){
			$arACTIVITIES[$count] = $qd;
			
			//echo $qd['IN_TOPSREF'] . "\n";
			
			// get trainer
			$sql = "select * from TRAINERS where TR_KEY = '" . $qd['AC_TRKEY'] . "'";
			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	{ echo  sql_error (); }
			$qd2 = mysqli_fetch_assoc($result2);
			$arACTIVITIES[$count]['TRAINER_NAME'] = $qd2['TR_NAME'];
			$arACTIVITIES[$count]['TO_INSTRUCTOR_TRKEY'] = $qd2['TR_KEY'];
			
			$sql = "select * from TRAINERS where TR_KEY = '" . $qd['CE_TRKEY'] . "'";
			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	{ echo  sql_error (); }
			$qd2 = mysqli_fetch_assoc($result2);
			$arACTIVITIES[$count]['EXAMINER_NAME'] = $qd2['TR_NAME'];
			$arACTIVITIES[$count]['TO_EXAMINER_TRKEY'] = $qd2['TR_KEY'];
				
			
			// get TOPS record
			$sql = "select * from TOPS where TO_ATKEY = " . $qd['AT_KEY'];
			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	{ echo  sql_error (); }
			$qd2 = mysqli_fetch_assoc($result2);
			if ($qd2['TO_KEY'] != ""){
				//use this data
				$arACTIVITIES[$count]['TOPS'] = $qd2;

				$mysqli2 = db_connect(getDBName());
				// now see if there are any uplaod recods
				$arACTIVITIES[$count]['SCRAPERLOG'] = array();
				$sql = "select * from SCRAPERLOG where SL_TYPE = 'TOPS' and SL_CUSTOMERKEY ='" . $qd2['TO_KEY']. "'";
				$result3 = mysqli_query ($mysqli2, $sql );
				if (! $result3)	{ echo  sql_error (); }
				while($qd3 = mysqli_fetch_assoc($result3)){
					$arACTIVITIES[$count]['SCRAPERLOG'][] = $qd3;
				}
				
			} else {
				// use calculated data
				$arACTIVITIES[$count]['TOPS']['TO_ACKEY'] = $qd['AC_KEY'];
				$arACTIVITIES[$count]['TOPS']['TO_TOPSID'] = $qd['IN_TOPSREF'];
				$arACTIVITIES[$count]['TOPS']['TO_TITLE'] = "Mr";
				$arACTIVITIES[$count]['TOPS']['TO_FORENAME'] = $qd['IN_FORENAME'];
				$arACTIVITIES[$count]['TOPS']['TO_SURNAME'] = $qd['IN_SURNAME'];
				$arACTIVITIES[$count]['TOPS']['TO_DOB'] = $qd['IN_DOB'];
				$arACTIVITIES[$count]['TOPS']['TO_COURSETYPE'] = $qd['CO_ITSTAR_COURSE_TYPE'];
				$arACTIVITIES[$count]['TOPS']['TO_MOTIVE'] = $qd['CE_MOTIVE'];
				$arACTIVITIES[$count]['TOPS']['TO_INDTRUCKGROUP'] = $qd['CO_ITSTAR_COURSE'];
				$arACTIVITIES[$count]['TOPS']['TO_RATIO'] = $qd['CE_RATIO'];
				$arACTIVITIES[$count]['TOPS']['TO_RATEDCAPACITY'] = $qd['CE_CAPACITY'];
				$arACTIVITIES[$count]['TOPS']['TO_INSTRUCTOR_TRKEY'] = $arACTIVITIES[$count]['TO_INSTRUCTOR_TRKEY'];
				$arACTIVITIES[$count]['TOPS']['TO_INSTRUCTOR_TRKEY_2'] = $qd['TR_KEY'];
				$arACTIVITIES[$count]['TOPS']['TO_DURATION'] = $qd['CO_DURATION'];
				$arACTIVITIES[$count]['TOPS']['TO_STARTDATE'] = $qd['AC_DATE'];
				// calc $EndDate
				if ($arACTIVITIES[$count]['TOPS']['TO_DURATION'] == "" or $arACTIVITIES[$count]['TOPS']['TO_DURATION'] == 1){
					$EndDate = $arACTIVITIES[$count]['TOPS']['TO_STARTDATE'];
				} else {
					$DurationTime = ($arACTIVITIES[$count]['TOPS']['TO_DURATION'] - 1) * (60*60*24);
					$StartTime = strtotime($arACTIVITIES[$count]['TOPS']['TO_STARTDATE']);
					$EndTime = $StartTime + $DurationTime;
					$EndDate = date('Y-m-d',$EndTime);
				}

				$arACTIVITIES[$count]['TOPS']['TO_DURATION'] = $qd['CO_DURATION'] * 8;
				
				$arACTIVITIES[$count]['TOPS']['TO_ENDDATE'] = $EndDate;
				if ($qd['CE_TESTDATE'] == "0000-00-00"){
					$arACTIVITIES[$count]['TOPS']['TO_TESTDATE'] = $arACTIVITIES[$count]['TOPS']['TO_ENDDATE'];
				} else {
					$arACTIVITIES[$count]['TOPS']['TO_TESTDATE'] = $qd['CE_TESTDATE'];
				}
				$arACTIVITIES[$count]['TOPS']['TO_EXAMINER_TRKEY'] = $arACTIVITIES[$count]['TO_EXAMINER_TRKEY'];
				$arACTIVITIES[$count]['TOPS']['TO_LIFTHEIGHT'] = str_replace("MM","",$qd['CE_LIFTHEIGHT']);
				$arACTIVITIES[$count]['TOPS']['TO_LIFTHEIGHT'] = round($arACTIVITIES[$count]['TOPS']['TO_LIFTHEIGHT'] / 1000,1);
				$arACTIVITIES[$count]['TOPS']['TO_IDREQUIREMENT'] = "ID Number Only";
				
			}
				
			$count++;
		}
	}

	
	// get dropd downs
	
	$sql = "select * from COURSES order by CO_NAME";
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	$arCourses[''] = "Select Course";
	while ($qd2 = mysqli_fetch_assoc($result2)){
		$arCourses[$qd2['CO_KEY']] = $qd2['CO_NAME'];
	}

	$sql = "select CO_ITSTAR_COURSE, CO_ITSTAR_COURSE_TYPE from COURSES group by CO_ITSTAR_COURSE, CO_ITSTAR_COURSE_TYPE";
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	$arITSSARCourseType[''] = "Select Option";
	$arITSSARCourse[''] = "Select Option";
	while ($qd2 = mysqli_fetch_assoc($result2)){
		$arITSSARCourseType[$qd2['CO_ITSTAR_COURSE_TYPE']] = $qd2['CO_ITSTAR_COURSE_TYPE'];
		$arITSSARCourse[$qd2['CO_ITSTAR_COURSE']] = $qd2['CO_ITSTAR_COURSE'];
	}

	$sql = "select * from CODES where COD_TYPE = 'MO'  order by COD_SORT";
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	$arMotive[''] = "Select Motive";
	while ($qd2 = mysqli_fetch_assoc($result2)){
		$arMotive[$qd2['COD_DESC']] = $qd2['COD_DESC'];
	}
	
	$sql = "select * from CODES where COD_TYPE = 'RA' order by COD_SORT";
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	$arRatio[''] = "Select ratio";
	while ($qd2 = mysqli_fetch_assoc($result2)){
		$arRatio[$qd2['COD_DESC']] = $qd2['COD_DESC'];
	}

	$sql = "select * from TRAINERS";
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	$arInstructor[''] = "Select ";
	while ($qd2 = mysqli_fetch_assoc($result2)){
		
// 		print_r($qd2);
		
		if ($qd2['TR_TOPSREF'] == ""){
			$arInstructor[$qd2['TR_KEY']] = $qd2['TR_NAME'] . "- No TOPS Ref!";
		} else {
			$arInstructor[$qd2['TR_KEY']] = $qd2['TR_NAME'] . "- OK Ref " . $qd2['TR_TOPSREF'];
		}
	}
	

	$arID['ID Number Only'] = "ID Number Only";
	$arID['ID Card'] = "ID Card";
	$arID['Update'] = "Update";
	
	$arTitle[''] = "";
	$arTitle['Mr'] = "Mr";
	$arTitle['Mrs'] = "Mrs";
	$arTitle['Miss'] = "Miss";
	
	$arYesNo[''] = "";
	$arYesNo['Y'] = "TOPS Loaded";
	$arYesNo['N'] = "Not Loaded";
	
// 	if ($arCtl['RQ_DATETIME'] == ""){
// 		$arCtl['RQ_DATETIME'] = date('Y-m-d');
// 	}

	
	// set up smarty
	$smarty = getSmarty();
	
	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arTitleList',    $arTitle);
	$smarty->assign('arIDList',    $arID);
	$smarty->assign('arCourseList',    $arCourses);
	$smarty->assign('arInstructorList',    $arInstructor);
	$smarty->assign('arRatioList',    $arRatio);
	$smarty->assign('arMotiveList',    $arMotive);
	$smarty->assign('arITSSARCourseList',    $arITSSARCourse);
	$smarty->assign('arITSSARCourseTypeList',    $arITSSARCourseType);
	$smarty->assign('arYesNoList',    $arYesNo);
	$smarty->assign('arACTIVITIES',    $arACTIVITIES);
	$smarty->assign('SESSION',   $_SESSION);
	
	// display using template name provided
	$smarty->display("custom/ListTOPS.tpl");
	
}

function updTOPSOverride($arCtl,$arSAFE_REQUEST) {
	

	$mysqli = db_connect(getAdminDBName());
	
	// now do updates to adin system
	$sql = "UPDATE INDIVIDUALS set IN_TOPSREF = '" . $arSAFE_REQUEST['INDIVIDUALS']['IN_TOPSREF'] . "' where IN_KEY = " . $arSAFE_REQUEST['INDIVIDUALS']['IN_KEY'];
	echo $sql;
	
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	
	$sql = "UPDATE ATTENDEES set AT_TOPSLOADED = '" . $arSAFE_REQUEST['ATTENDEES']['AT_TOPSLOADED'] . "' where AT_KEY = " . $arSAFE_REQUEST['ATTENDEES']['AT_KEY'];
	echo $sql;
	
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
		

//   	exit();
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
}

function UpdTOPS($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getAdminDBName());

//  	print_r($arSAFE_REQUEST);
	
	$TOPS = $arSAFE_REQUEST['TOPS'];
	
	if ($TOPS['TO_KEY'] != ""){
		
		$sql = "UPDATE TOPS SET
				TO_TITLE = '" . $TOPS['TO_TITLE'] . "',
				TO_FORENAME = '" . $TOPS['TO_FORENAME'] . "',
				TO_SURNAME = '" . $TOPS['TO_SURNAME'] . "',
				TO_DOB = '" . $TOPS['TO_DOB'] . "',
				TO_COURSETYPE = '" . $TOPS['TO_COURSETYPE'] . "',
				TO_MOTIVE = '" . $TOPS['TO_MOTIVE'] . "',
				TO_INDTRUCKGROUP = '" . $TOPS['TO_INDTRUCKGROUP'] . "',
				TO_RATIO = '" . $TOPS['TO_RATIO'] . "',
				TO_RATEDCAPACITY = '" . $TOPS['TO_RATEDCAPACITY'] . "',
				TO_INSTRUCTOR_TRKEY = '" . $TOPS['TO_INSTRUCTOR_TRKEY'] . "',
				TO_INSTRUCTOR_TRKEY_2 = '" . $TOPS['TO_INSTRUCTOR_TRKEY_2'] . "',
				TO_DURATION = '" . $TOPS['TO_DURATION'] . "',
				TO_STARTDATE = '" . $TOPS['TO_STARTDATE'] . "',
				TO_ENDDATE = '" . $TOPS['TO_ENDDATE'] . "',
				TO_TESTDATE = '" . $TOPS['TO_TESTDATE'] . "',
				TO_EXAMINER_TRKEY = '" . $TOPS['TO_EXAMINER_TRKEY'] . "',
				TO_LIFTHEIGHT = '" . $TOPS['TO_LIFTHEIGHT'] . "',
				TO_IDREQUIREMENT = '" . $TOPS['TO_IDREQUIREMENT'] . "'
				WHERE TO_KEY = " . $TOPS['TO_KEY'];
		
	} else {
		
		$sql = "INSERT INTO TOPS (
				TO_ACKEY,
				TO_ATKEY,
				TO_TITLE,
				TO_FORENAME,
				TO_SURNAME,
				TO_DOB,
				TO_COURSETYPE,
				TO_MOTIVE,
				TO_INDTRUCKGROUP,
				TO_RATIO,
				TO_RATEDCAPACITY,
				TO_INSTRUCTOR_TRKEY,
				TO_INSTRUCTOR_TRKEY_2,
				TO_DURATION,
				TO_STARTDATE,
				TO_ENDDATE,
				TO_TESTDATE,
				TO_EXAMINER_TRKEY,
				TO_LIFTHEIGHT,
				TO_IDREQUIREMENT
		) values (
				'" . $TOPS['TO_ACKEY'] . "',
				'" . $TOPS['TO_ATKEY'] . "',
				'" . $TOPS['TO_TITLE'] . "',
				'" . $TOPS['TO_FORENAME'] . "',
				'" . $TOPS['TO_SURNAME'] . "',
				'" . $TOPS['TO_DOB'] . "',
				'" . $TOPS['TO_COURSETYPE'] . "',
				'" . $TOPS['TO_MOTIVE'] . "',
				'" . $TOPS['TO_INDTRUCKGROUP'] . "',
				'" . $TOPS['TO_RATIO'] . "',
				'" . $TOPS['TO_RATEDCAPACITY'] . "',
				'" . $TOPS['TO_INSTRUCTOR_TRKEY'] . "',
				'" . $TOPS['TO_INSTRUCTOR_TRKEY_2'] . "',
				'" . $TOPS['TO_DURATION'] . "',
				'" . $TOPS['TO_STARTDATE'] . "',
				'" . $TOPS['TO_ENDDATE'] . "',
				'" . $TOPS['TO_TESTDATE'] . "',
				'" . $TOPS['TO_EXAMINER_TRKEY'] . "',
				'" . $TOPS['TO_LIFTHEIGHT'] . "',
				'" . $TOPS['TO_IDREQUIREMENT'] . "'
				)";
	}

// 	echo $sql;
	
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }

//  	exit();
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
	
}

function runTOPSUpload($arCtl,$arSAFE_REQUEST){

	//print_r($arSAFE_REQUEST);
	
	$arResult = runTOPSScraper($arCtl,$arSAFE_REQUEST);
	
	//print_r($arResult);
	
	if ($arResult['Status'] == "Complete"){

		$mysqli = db_connect(getAdminDBName());
		
		// now do updates to adin system
		$sql = "UPDATE INDIVIDUALS set IN_TOPSREF = '" . $arResult['TOPSREF']  . "' where IN_KEY = " . $arSAFE_REQUEST['INDIVIDUALS']['IN_KEY'];
		echo $sql;
		
		$result2 = mysqli_query ($mysqli, $sql );
		if (!$result2)	{ echo  sql_error (); }
		
		$sql = "UPDATE ATTENDEES set AT_TOPSLOADED = 'Y' where AT_KEY = " . $arSAFE_REQUEST['ATTENDEES']['AT_KEY'];
		echo $sql;
		
		$result2 = mysqli_query ($mysqli, $sql );
		if (!$result2)	{ echo  sql_error (); }
		
	} else {
		// donothing
	}

// 	exit();
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
}

function runTOPSScraper($arCtl,$arSAFE_REQUEST){
	
	ob_start();
	
//	print_r($arSAFE_REQUEST);
	
	$mysqli = db_connect(getAdminDBName());
	// get the info needed
	$sql = "select * from TOPS where TO_KEY = " . $arSAFE_REQUEST['TOPS']['TO_KEY'];
	echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (!$result)	{ echo  sql_error (); }
	$TOPS = mysqli_fetch_assoc($result);
	
	//TR_TOPSREF
	$sql = "select * from TRAINERS where TR_KEY = " . $TOPS['TO_INSTRUCTOR_TRKEY'];
	$result = mysqli_query ($mysqli, $sql );
	if (!$result)	{ echo  sql_error (); }
	$INSTRUCTOR_1 = mysqli_fetch_assoc($result);
	
	$sql = "select * from TRAINERS where TR_KEY = " . $TOPS['TO_INSTRUCTOR_TRKEY_2'];
	$result = mysqli_query ($mysqli, $sql );
	if (!$result)	{ echo  sql_error (); }
	$INSTRUCTOR_2 = mysqli_fetch_assoc($result);
	if ($INSTRUCTOR_2['TR_KEY'] == ""){
		$INSTRUCTOR_2['TR_TOPSREF'] = 0;
	} 
	
	$sql = "select * from TRAINERS where TR_KEY = " . $TOPS['TO_EXAMINER_TRKEY'];
	$result = mysqli_query ($mysqli, $sql );
	if (!$result)	{ echo  sql_error (); }
	$EXAMINER = mysqli_fetch_assoc($result);
	
	// now create data
	
	$POST['cboopcrstitle'] = $TOPS['TO_TITLE'];
	$POST['txtopcrsforename'] = $TOPS['TO_FORENAME'];
	$POST['txtopcrssurname'] = $TOPS['TO_SURNAME'];
	$POST['txtopcrsdob'] = DatefromDB($TOPS['TO_DOB']);
	
	$POST['cboopcrstype'] = $TOPS['TO_COURSETYPE'];
	$POST['cboopcrsratio'] = $TOPS['TO_RATIO'];

	$arTruckGroup = explode("|",$TOPS['TO_INDTRUCKGROUP']);
	$POST['cboopcrsitg'] = $arTruckGroup[0];
	
	$POST['cboopcrsmotive'] = $TOPS['TO_MOTIVE'];
	$POST['txtopcrsrated'] = $TOPS['TO_RATEDCAPACITY'];
	$POST['cboopcrsins1'] = $INSTRUCTOR_1['TR_TOPSREF'];
	$POST['cboopcrsins2'] = $INSTRUCTOR_2['TR_TOPSREF'];
	$POST['txtopcrsdatestart'] = DatefromDB($TOPS['TO_STARTDATE']);
	$POST['txtopcrsdateend'] = DatefromDB($TOPS['TO_ENDDATE']);
	$POST['txtopcrsdur'] = $TOPS['TO_DURATION'];
	$POST['txtopcrsdatetest'] = DatefromDB($TOPS['TO_TESTDATE']);
	$POST['cboopcrsexam'] = $EXAMINER['TR_TOPSREF'];;
	$POST['txtopcrslift'] = $TOPS['TO_LIFTHEIGHT'];
	$POST['cboopcrsidreq'] = $TOPS['TO_IDREQUIREMENT'];
	

	print_r($POST);
	
	// no start scraper
	
	
	$arResult['Status'] = "NotComplete";
	$arResult['TOPSREF'] = "";
	
	$arCtl['SL_TYPE'] = "TOPS";
	$arCtl['SL_CUSTOMERKEY'] = $arSAFE_REQUEST['TOPS']['TO_KEY'];
	
	//go to log page
	$arCtl['url'] = "https://www.operc.com/ITSSAR/";
	$arCtl['ExpectedTitle'] = "TOP Scheme - Login";
	$arCtl['SL_PAGENAME'] = "Login Page";
	
	$arReturn = doCurlPull($arCtl);
	//print_r($arReturn);
	
	if ($arReturn['Status'] != "Matched"){ return $arResult;}
	
	// post
	$arCtl['url'] = "https://www.operc.com/ITSSAR/tops-login.asp";
	$arCtl['POST']['txtid'] = "alex.byrne@the-resources-group.com";
	$arCtl['POST']['txtpassword'] ="AByrne123";
//	$arCtl['ExpectedTitle'] = "TOP Scheme - Training Provider Menu";
	$arCtl['ExpectedTitle'] = "TOPS Scheme - Administrator Home";
	$arCtl['SL_PAGENAME'] = "Home Page";
	$arReturn = doCurlPull($arCtl);
 	if ($arReturn['Status'] != "Matched"){ return $arResult;}
	unset($arCtl['POST']);

	print_r($arReturn);

	$arCtl['url'] = "https://www.operc.com/ITSSAR/tops-ad-crslist.asp";
	$arCtl['referer'] = "https://www.operc.com/ITSSAR/tops-ad-home.asp";
	$arCtl['ExpectedTitle'] = "TOPS Scheme - Operator Course List";
	$arCtl['SL_PAGENAME'] = "Course List Page";
	$arReturn = doCurlPull($arCtl);
 	if ($arReturn['Status'] != "Matched"){ return $arResult;}

	print_r($arReturn);
	
	$arCtl['url'] = "https://www.operc.com/ITSSAR/tops-ad-crscreate-op.asp";
	$arCtl['ExpectedTitle'] = "TOPS Scheme - Create Operator Course 1";
	$arCtl['SL_PAGENAME'] = "Add New Course";
	$arReturn = doCurlPull($arCtl);
 	if ($arReturn['Status'] != "Matched"){ return $arResult;}

	print_r($arReturn);
	
	$arCtl['POST']['txtoptops'] = "";
	$arCtl['POST']['cboopcrstitle'] = $POST['cboopcrstitle'];
	$arCtl['POST']['txtopcrsforename'] = $POST['txtopcrsforename'];
	$arCtl['POST']['txtopcrssurname'] = $POST['txtopcrssurname'];
	$arCtl['POST']['txtopcrsdob'] = $POST['txtopcrsdob'];
	$arCtl['POST']['txtopcrsni'] = "";
	$arCtl['POST']['Submit'] = "+Submit+";
	$arCtl['POST']['txtdupidno'] = "";
	
	
	$arCtl['url'] = "https://www.operc.com/ITSSAR/tops-ad-crscreate-op.asp";
	$arCtl['ExpectedTitle'] = "TOPS Scheme - Create Operator Course 2";
	$arCtl['SL_PAGENAME'] = "Enter Driver Details";
	$arReturn = doCurlPull($arCtl);
	unset($arCtl['POST']);

	print_r($arReturn);
	
	if ($arReturn['Status'] != "Matched"){ 
		
		// check to see if duplicate
		//Please click <strong>CONFIRM</strong> to continue with this operator
		if (preg_match("/\bCONFIRM\b/i", $arReturn['Output'])) {
			echo "A match was found.";

			// get TOPS Ref
			//<span class="fm_text_bold">TOPS:101209</span>
			preg_match("/TOPS:(.*?) already/ims",$arReturn['Output'],$TOPSRef);
// 			echo "TOPS" ;
// 			print_r($TOPSRef);
			
			$TOPSREF = substr($TOPSRef[0],0,11);

			$arResult['TOPSREF'] = $TOPSREF;
				
// 			echo $TOPSREF . "HKHHJH";
			//<input type="hidden" type="text" name="txtdupidno" id="txtdupidno" value="91334" size="1">
			preg_match("/id=\"txtdupidno\" value=\"(.*?)\" size=\"1\"/ims",$arReturn['Output'],$txtdupidno);
// 			echo "txtdupidno" ;
// 			print_r($txtdupidno);

			$arCtl['POST']['Submit'] = "+CONFIRM+";
			$arCtl['POST']['txtdupidno'] = $txtdupidno[1];
				
			//https://www.operc.com/ITSSAR/tops-ad-crscreate-op.asp
			$arCtl['url'] = "https://www.operc.com/ITSSAR/tops-ad-crscreate-op.asp";
			$arCtl['ExpectedTitle'] = "TOPS Scheme - Create Operator Course 2";
			$arCtl['SL_PAGENAME'] = "Driver already exists";
			$arReturn = doCurlPull($arCtl);
			unset($arCtl['POST']);
			print_r($arReturn);
				
		} else {
			return $arResult;
		}
		
	}

	
	// get TOPS Ref
	//<span class="fm_text_bold">TOPS:101209</span>
	preg_match("/TOPS:(.*?)<\/span>/ims",$arReturn['Output'],$TOPSRef);
	echo "TOPS" ;
	print_r($TOPSRef);
	
	//<form action="tops-ad-crscreate-crs.asp?op=91334"
	
	preg_match("/tops-ad-crscreate-crs.asp\?op\=(.*?)[\"']/",$arReturn['Output'],$OpID);
	echo "OpID" ;
	print_r($OpID);
	
	$arCtl['url'] = "https://www.operc.com/ITSSAR/tops-ad-crscreate-crs.asp?op=" . $OpID[1];

	$arCtl['POST']['cboopcrstype'] = $POST['cboopcrstype'];
	$arCtl['POST']['cboopcrsratio'] = $POST['cboopcrsratio'];
	$arCtl['POST']['cboopcrsitg'] = $POST['cboopcrsitg'];
	$arCtl['POST']['cboopcrsmotive'] = $POST['cboopcrsmotive'];
	$arCtl['POST']['txtopcrsrated'] = $POST['txtopcrsrated'];
	$arCtl['POST']['cboopcrsins1'] = $POST['cboopcrsins1'];
	$arCtl['POST']['cboopcrsins2'] = $POST['cboopcrsins2'];
	$arCtl['POST']['txtopcrsdatestart'] = $POST['txtopcrsdatestart'];
	$arCtl['POST']['txtopcrsdateend'] = $POST['txtopcrsdateend'] ;
	$arCtl['POST']['txtopcrsdur'] = $POST['txtopcrsdur'];
	$arCtl['POST']['txtopcrsdatetest'] = $POST['txtopcrsdatetest'];
	$arCtl['POST']['cboopcrsexam'] = $POST['cboopcrsexam'];
	$arCtl['POST']['txtopcrslift'] = $POST['txtopcrslift'];
	$arCtl['POST']['cboopcrsidreq'] = $POST['cboopcrsidreq'];
	$arCtl['POST']['Submit'] = "+Submit+";
	$arCtl['POST']['txtopdupid'] = "";
	
	
	$arCtl['ExpectedTitle'] = "TOPS Scheme - Create Operator Course 3";
	$arCtl['SL_PAGENAME'] = "Create course on tops";
	$arReturn = doCurlPull($arCtl);
	unset($arCtl['POST']);
	print_r($arReturn);

	if ($arReturn['Status'] != "Matched"){
		$arResult['Status'] = "NotComplete";
		$arResult['TOPSREF'] = $TOPSRef[1];
	} else {
		$arResult['Status'] = "Complete";
		$arResult['TOPSREF'] = $TOPSRef[1];
	}
	
	
	$arResult['DEBUGS'] = ob_get_contents();
	ob_end_clean();

 	return $arResult;
	
}

?>