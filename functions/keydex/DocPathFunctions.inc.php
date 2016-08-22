<?php
function KeystoreFile($arCtl,$KD_DOCPATH,$KD_UPLOADS){

	// DB Connection
	$mysqli = db_connect ( getDBName () );

	$sql = "select * from KD_DOCUMENT where KDU_KUPKEY = ". $KD_UPLOADS['KUP_KEY'];
	echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	error_message ( sql_error () );
	
	$arCtl ['Company'] = getCompany();
	$arCtl ['Password'] = getKSPassword();
	
	$DocCount=0;
	while ($KD_DOCUMENT = mysqli_fetch_assoc($result)){
	
		echo "doing ", $KD_DOCUMENT ['KDU_DOCNAME'] . " " . $KD_DOCUMENT ['KDU_DOCTYPE'] . " " . $KD_DOCUMENT ['KDU_INDEX1'] . " Size " . $KD_DOCUMENT ['KDU_FILESIZE'] . "\n";
		$time = date ( 'U' );
		
		$KD_DOCUMENT ['KDU_INDEX1'] = preg_replace ( '/[^a-zA-Z0-9_ ]/s', '', $KD_DOCUMENT ['KDU_INDEX1'] );
		
		$filecount ++;
		$arCtl ['arKeys'] = array ();
		// create key arrays
		if ($KD_DOCUMENT ['KDU_INDEX1_NAME'] != "") {
			$arCtl ['arKeys'] [] = array (
					'Name' => $KD_DOCUMENT ['KDU_INDEX1_NAME'],
					'Value' => $KD_DOCUMENT ['KDU_INDEX1']
			);
		}
		if ($KD_DOCUMENT ['KDU_INDEX2_NAME'] != "") {
			$arCtl ['arKeys'] [] = array (
					'Name' => $KD_DOCUMENT ['KDU_INDEX2_NAME'],
					'Value' => $KD_DOCUMENT ['KDU_INDEX2']
			);
		}
		if ($KD_DOCUMENT ['KDU_INDEX3_NAME'] != "") {
			$arCtl ['arKeys'] [] = array (
					'Name' => $KD_DOCUMENT ['KDU_INDEX3_NAME'],
					'Value' => $KD_DOCUMENT ['KDU_INDEX3']
			);
		}
		if ($KD_DOCUMENT ['KDU_INDEX4_NAME'] != "") {
			$arCtl ['arKeys'] [] = array (
					'Name' => $KD_DOCUMENT ['KDU_INDEX4_NAME'],
					'Value' => $KD_DOCUMENT ['KDU_INDEX4']
			);
		}
		if ($KD_DOCUMENT ['KDU_INDEX5_NAME'] != "") {
			$arCtl ['arKeys'] [] = array (
					'Name' => $KD_DOCUMENT ['KDU_INDEX5_NAME'],
					'Value' => $KD_DOCUMENT ['KDU_INDEX5']
			);
		}
		if ($KD_DOCUMENT ['KDU_INDEX6_NAME'] != "") {
			$arCtl ['arKeys'] [] = array (
					'Name' => $KD_DOCUMENT ['KDU_INDEX6_NAME'],
					'Value' => $KD_DOCUMENT ['KDU_INDEX6']
			);
		}
		if ($KD_DOCUMENT ['KDU_INDEX7_NAME'] != "") {
			$arCtl ['arKeys'] [] = array (
					'Name' => $KD_DOCUMENT ['KDU_INDEX7_NAME'],
					'Value' => $KD_DOCUMENT ['KDU_INDEX7']
			);
		}
		if ($KD_DOCUMENT ['KDU_INDEX8_NAME'] != "") {
			$arCtl ['arKeys'] [] = array (
					'Name' => $KD_DOCUMENT ['KDU_INDEX8_NAME'],
					'Value' => $KD_DOCUMENT ['KDU_INDEX8']
			);
		}
		
		$arCtl ['DocType'] = $KD_DOCUMENT ['KDU_DOCTYPE'];
		
		$arResult = DocLoad ( $arCtl, $KD_DOCUMENT );
		
		if ($arResult->Status == "Document Loaded OK") {
			// now update doc to say uploaded
			$sql = "update KD_DOCUMENT set KDU_DOCURL = '" . mysql_escape_string ( $arResult->URL ) . "',KDU_STATUS='Filed' where KDU_KEY =" . $KD_DOCUMENT ['KDU_KEY'];
			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	error_message ( sql_error () );
		} else {
			$sql = "update KD_DOCUMENT set KDU_ERROR_STATUS='Filing Failed'  where KDU_KEY =" . $KD_DOCUMENT ['KDU_KEY'];
			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2) error_message ( sql_error () );
			$Status = "Not OK";
			$DocNo = $DocCount;
		}
		
		$DocCount++;
	}

	if ($Status == "Not OK"){
		$Result['Status'] = "Not Ok";
		$Result['StatusMessage'] = " Problem with Keystore filing at " . $DocNo . " document";
	} else {
		$Result['Status'] = "Ok";
		$Result['StatusMessage'] = " Keystore Load OK " . $DocCount . " Doucments";
	}
	
	return $Result;
	
}

function ImportDir($arCtl,$KD_DOCPATH,$KD_UPLOADS){
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
		
	// loop round all docs
	$sql = "select * from KD_DOCUMENT where KDU_KUPKEY = ". $KD_UPLOADS['KUP_KEY'];
	echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	error_message ( sql_error () );
	
	while ($KD_DOCUMENT = mysqli_fetch_assoc($result)){
		// get file name 
		// take first bit for doc type
		$arFile = explode("-",$KD_DOCUMENT['KDU_IMAGENAME']);
		
		// now get doctype keys
		$sql = "select * from KD_DOCDEF where DD_KEYSTORE_DOCTYPE ='" . $arFile[0] . "'";
		$result2 = mysqli_query ($mysqli, $sql );
		if (! $result2)	error_message ( sql_error () );
		$KD_DOCDEF = mysqli_fetch_assoc($result2);
		
		// check for workflow and if so set initial status
		$sql = "select * from KD_WORKFLOWS where WF_DDKEY ='" . $KD_DOCDEF['DD_KEY'] . "'";
		$result2 = mysqli_query ($mysqli, $sql );
		if (! $result2)	error_message ( sql_error () );
		$KD_WORKFLOWS = mysqli_fetch_assoc($result2);
		if ($KD_WORKFLOWS['WF_KEY'] != ""){
			//update doc type
			$StatusFieldName = "KDU_INDEX" . $KD_WORKFLOWS['WF_STATUS_INDEX_NO'];
			$StatusValue =  $KD_WORKFLOWS['WF_STARTSTATUS'];
			
			$sql = "update KD_DOCUMENT set KDU_DOCTYPE = '" . $arFile[0] . "', KDU_DDKEY = '" . $KD_DOCDEF['DD_KEY']. "', " . $StatusFieldName . " = '" . $StatusValue. "' where KDU_KEY = " . $KD_DOCUMENT['KDU_KEY'];
				
		} else {
			//update doc type
			$sql = "update KD_DOCUMENT set KDU_DOCTYPE = '" . $arFile[0] . "', KDU_DDKEY = '" . $KD_DOCDEF['DD_KEY']. "' where KDU_KEY = " . $KD_DOCUMENT['KDU_KEY'];
				
		}
		
		$arCtl['WFL_TYPE'] = "Doc Path Action";
		$arCtl['WFL_SUMMARY'] = "Doc " . $KD_DOCUMENT['KDU_KEY'] . " set to doc type " .  $arFile[0];
		UpdWorkflowLog($arCtl,$KD_DOCUMENT);
		
		mail("stephen@nextventure.co.uk",$sql,"");
		
		$result2 = mysqli_query ($mysqli, $sql );
		if (! $result2)	error_message ( sql_error () );
		
		
	}
	
}

?>