<?php

function saveToDB($arSQL){

	ob_start();
	
	// table keys - stick this in a table
	$arTableKeys['PARAMETERS'] = "PA_KEY";
	$arTableKeys['ENVIRONMENT_PARAMETERS'] = "EP_KEY";
	$arTableKeys['COMPANY_FUNCTIONS'] = "CF_KEY";
	$arTableKeys['ENVIRONMENTS'] = "EN_KEY";
	$arTableKeys['FUNCTIONS'] = "FU_KEY";
	$arTableKeys['COMPANY'] = "CO_KEY";
	$arTableKeys['CUSTOMERS'] = "CU_KEY";
	$arTableKeys['USERS'] = "US_KEY";
	$arTableKeys['USERS_WORKFLOWS'] = "USF_KEY";
	$arTableKeys['USERS_DOCUMENTS'] = "USD_KEY";
	$arTableKeys['LOCATIONS'] = "LO_KEY";
	$arTableKeys['USER_ROLES'] = "UO_KEY";
	$arTableKeys['JOBTYPES'] = "JT_KEY";
	$arTableKeys['JOBS'] = "JO_KEY";
	$arTableKeys['JOBSTEPS'] = "JOS_KEY";
	$arTableKeys['JOBSTEPFIELDS'] = "JOSF_KEY";
	$arTableKeys['JOBTYPESTEPS'] = "JTS_KEY";
	$arTableKeys['JOBTYPESTEPFIELDS'] = "JTSF_KEY";

	
	$arTableDB['PARAMETERS'] = getControlDBName();
	$arTableDB['ENVIRONMENT_PARAMETERS'] = getControlDBName();
	$arTableDB['COMPANY_FUNCTIONS'] = getConfigDBName();
	$arTableDB['ENVIRONMENTS'] = getControlDBName();
	$arTableDB['FUNCTIONS'] = getControlDBName();
	$arTableDB['COMPANY'] = getControlDBName();
	$arTableDB['CUSTOMERS'] = getDBName();
	$arTableDB['USERS'] = getConfigDBName();
	$arTableDB['USERS_WORKFLOWS'] = getConfigDBName();
	$arTableDB['USERS_DOCUMENTS'] = getConfigDBName();
	$arTableDB['USER_ROLES'] = getConfigDBName();
	$arTableDB['LOCATIONS'] = getDBName();
	$arTableDB['JOBTYPES'] = getDBName();
	$arTableDB['JOBS'] = getDBName();
	$arTableDB['JOBSTEPS'] = getDBName();
	$arTableDB['JOBSTEPFIELDS'] = getDBName();
	$arTableDB['JOBTYPESTEPS'] = getDBName();
	$arTableDB['JOBTYPESTEPFIELDS'] = getDBName();
	
	// connect to DB	
	
	// loop round SQL array to create statements
	foreach ($arSQL as $Table => $arRecords){

		$mysqli = db_connect($arTableDB[$Table]);
		
		echo "<BR> SQL DB to Update " . $arTableDB[$Table] . "<BR>";
		print_r($arSQL);
		
		foreach ($arRecords as $RecordNo => $arFields){

			$insert = true;
			$KeyValue = "";
				
			foreach ($arFields as $field => $value){
				// first check to see if Primary key exists
				if ($arTableKeys[$Table] == $field and trim($value) != ""){
					// if so this is a update not insert
					$KeyValue = $value;
					$insert=false;
				}
			}
			
			if ($insert){

				$sql = "INSERT INTO " . $Table;
				$fields = "(";
				$values = " values (";
				$count = 0;
				foreach ($arFields as $field => $value){
					if (substr($field,0,3) != "CTL" and $arTableKeys[$Table] != $field){
						if ($count == 0) {
							$fields .= "" . $field . "";
							$values .= "'" . mysqli_real_escape_string ($mysqli,$value) . "'";
						} else {
							$fields .= "," . $field . "";
							$values .= ",'" . mysqli_real_escape_string ($mysqli,$value) . "'";
						}
						$count++;
					}
				}
				$fields .= ")";
				$values .= ")";
				$sql = $sql . $fields . " ". $values;
				
				// now run
				echo $sql. "<BR>\n";
				$result = mysqli_query ($mysqli, $sql );
				if (! $result)	{ echo  sql_error (); }
				// stick primary key back into SQl array
				echo $Table . "-" . $RecordNo . "-" . $arTableKeys[$Table] . "----" . mysqli_insert_id($mysqli);
				$arSQL[$Table][$RecordNo][$arTableKeys[$Table]] = mysqli_insert_id($mysqli);
			
			} else{
				// create update
				$sql = "UPDATE " . $Table . " SET ";
				$count = 0;
				foreach ($arFields as $field => $value){
					if (substr($field,0,3) != "CTL"){
						if ($count == 0) {
							$sql .= $field . " = '" . mysqli_real_escape_string ($mysqli,$value) . "'";
						} else {
							$sql .= "," . $field . " = '" . mysqli_real_escape_string ($mysqli,$value) . "'";
						}
						$count++;
					}
				}
				$sql .= " where " . $arTableKeys[$Table] . " = " . $KeyValue;

				// now run			
				echo $sql . "<BR>\n";
				$result = mysqli_query ($mysqli, $sql );
				if (! $result)	{ echo  sql_error (); }
				// stick primary key back into SQl array
				echo $Table . "-" . $RecordNo . "-" . $arTableKeys[$Table] . "----" . mysqli_insert_id($mysqli);
				
			}
		}
	}
	
	echo "<BR> SQL DB after <BR>";
	print_r($arSQL);
 //	$mail = ob_get_contents();
 	ob_end_clean();
	
	//	exit();
	//	mail("stephen@nextventure.co.uk","DB Save " . $arSQL['REQUEST'][0]['RQ_QUOTEID'],$mail);
	
// 	$MESSAGES['ME_TEXT'] = $mail;
// 	$MESSAGES['ME_QUOTEID'] = $arSQL['REQUEST'][0]['RQ_QUOTEID'];
// 	$MESSAGES['ME_SUMMARY'] = "DB Save";
// 	createMessage($MESSAGES);
	
 //	mail("stephen@nextventure.co.uk","saveDB " . $arSQL['REQUEST'][0]['RQ_QUOTEID'],$mail);
	return $arSQL;
}

function deleteFromDB($arSQL){

	ob_start();

	// table keys - stick this in a table
	$arTableKeys['PARAMETERS'] = "PA_KEY";
	$arTableKeys['ENVIRONMENT_PARAMETERS'] = "EP_KEY";
	$arTableKeys['COMPANY_FUNCTIONS'] = "CF_KEY";
	$arTableKeys['ENVIRONMENTS'] = "EN_KEY";
	$arTableKeys['FUNCTIONS'] = "FU_KEY";
	$arTableKeys['COMPANY'] = "CO_KEY";
	$arTableKeys['CUSTOMER'] = "CU_KEY";
	$arTableKeys['LOCATIONS'] = "LO_KEY";
	$arTableKeys['JOBTYPES'] = "JT_KEY";
	$arTableKeys['JOBS'] = "JO_KEY";
	$arTableKeys['JOBTYPESTEPS'] = "JTS_KEY";
	$arTableKeys['USERS'] = "US_KEY";
	$arTableKeys['USER_ROLES'] = "UO_KEY";
	$arTableKeys['USERS_WORKFLOWS'] = "USF_KEY";
	$arTableKeys['USERS_DOCUMENTS'] = "USD_KEY";
	$arTableKeys['JOBTYPESTEPFIELDS'] = "JTSF_KEY";
	
	
	$arTableDB['PARAMETERS'] = getControlDBName();
	$arTableDB['ENVIRONMENT_PARAMETERS'] = getControlDBName();
	$arTableDB['COMPANY_FUNCTIONS'] = getConfigDBName();
	$arTableDB['ENVIRONMENTS'] = getControlDBName();
	$arTableDB['FUNCTIONS'] = getControlDBName();
	$arTableDB['COMPANY'] = getControlDBName();
	$arTableDB['CUSTOMERS'] = getDBName();
	$arTableDB['USERS'] = getConfigDBName();
	$arTableDB['USER_ROLES'] = getConfigDBName();
	$arTableDB['USERS_WORKFLOWS'] = getConfigDBName();
	$arTableDB['USERS_DOCUMENTS'] = getConfigDBName();
	$arTableDB['LOCATIONS'] = getDBName();
	$arTableDB['JOBTYPES'] = getDBName();
	$arTableDB['JOBS'] = getDBName();
	$arTableDB['JOBSTEPS'] = getDBName();
	$arTableDB['JOBSTEPFIELDS'] = getDBName();
	$arTableDB['JOBTYPESTEPS'] = getDBName();
	$arTableDB['JOBTYPESTEPFIELDS'] = getDBName();
	
	echo "<BR> SQL DB to Update <BR>";
	print_r($arSQL);

	// loop round SQL array to create statements
	foreach ($arSQL as $Table => $arRecords){
		
		$mysqli = db_connect($arTableDB[$Table]);
		
		foreach ($arRecords as $RecordNo => $arFields){
			$delete = false;
				
			foreach ($arFields as $field => $value){
				// first check to see if Primary key exists
				if ($arTableKeys[$Table] == $field and trim($value) != ""){
					// if so this is a update not insert
					$KeyValue = $value;
					$delete=true;
				}
				
				if ($delete){
					$sql .= "Delete from " . $Table . " where " . $arTableKeys[$Table] . " = " . $KeyValue;
				}				
				// now run
				echo $sql . "<BR>\n";
				$result = mysqli_query ($mysqli, $sql );
				if (! $result)	{ echo  sql_error (); }
				// stick primary key back into SQl array
				echo $Table . "-" . $RecordNo . "-" . $arTableKeys[$Table] . "----" . mysqli_affected_rows($mysqli);
			}
		}
	}

	$mail = ob_get_contents();
	//	exit();
	//	mail("stephen@nextventure.co.uk","DB Save " . $arSQL['REQUEST'][0]['RQ_QUOTEID'],$mail);
	ob_end_clean();

	// 	$MESSAGES['ME_TEXT'] = $mail;
	// 	$MESSAGES['ME_QUOTEID'] = $arSQL['REQUEST'][0]['RQ_QUOTEID'];
	// 	$MESSAGES['ME_SUMMARY'] = "DB Save";
	// 	createMessage($MESSAGES);

	//	mail("stephen@nextventure.co.uk","saveDB " . $arSQL['REQUEST'][0]['RQ_QUOTEID'],$mail);
	return $arSQL;
}

?>