<?php

/*********************************************************************
 
 Display appropriate menu
 
 ********************************************************************/
function ShowDocumentLoad($arCtl, $arSAFE_REQUEST ) {

	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];
	
	// see if doc available if so get URL
	
	if ($KD_DOCUMENT['KDU_KEY'] != ""){

		$sql = "select * from KD_DOCUMENT where KDU_KEY = '" . $KD_DOCUMENT['KDU_KEY'] . "'";
		$result = mysqli_query ( $mysqli, $sql);
		if (! $result)	error_message ( sql_error () );
		$KD_DOCUMENT = mysqli_fetch_assoc($result);
		
		$KD_DOCUMENT['URL'] = getWebRoot() . $KD_DOCUMENT ['KDU_FILENAME'];
		
		// now create TIFF version for display
		$ScratchDir = getAppRoot () . "docs/" . $_SESSION['ENVIRONMENT']['EN_URL'] . "/scratch";
		$ScratchDir2 = "/docs/" . $_SESSION['ENVIRONMENT']['EN_URL'] . "/scratch";
		$evalstring = "/usr/bin/convert -density 500 " . $KD_DOCUMENT ['KDU_SOURCEFILE'] . " -resize 25% " . $ScratchDir ."/" .$KD_DOCUMENT ['KDU_KEY'] . ".png> /dev/null";
//		echo $evalstring;
		$err = array ();
		$res = array ();
		exec ( $evalstring, $res );
		// 		eval($evalstring);
		$KD_DOCUMENT['PNG'] = $ScratchDir2 ."/" .$KD_DOCUMENT ['KDU_KEY'] . ".png";
		
		// now create dropdwns for indexing
		
		$arDocTypes [' '] = "Select a Document Type";
		$DocKeysCount = 0;
		
		$sql = "select * from KD_DOCDEF
				LEFT OUTER JOIN KD_WORKFLOWS ON WF_DDKEY = DD_KEY";
		$result = mysqli_query ( $mysqli, $sql);
		if (! $result)	error_message ( sql_error () );
		while ($qd = mysqli_fetch_assoc($result)){
	
			foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
				if ($arDocTypeDetails ['Name'] == $qd['DD_KEYSTORE_DOCTYPE']){
					// dropdown options
					$arDocTypes [$arDocTypeDetails ['Id']] = $arDocTypeDetails ['Name'];
					
					// see if workflow and if so get index
					if ($qd['WF_KEY'] != ""){
						// workflow
						$sql = "select * from KD_DOCDEF_KEYS where DDK_DDKEY =" . $qd['DD_KEY'] . " and DDK_KEYSTORE_KEYORDER != '".  $qd['WF_STATUS_INDEX_NO'] . "' ORDER BY DDK_KEYSTORE_KEYORDER";
						
						$StatusFieldName = "KDU_INDEX" . $qd['WF_STATUS_INDEX_NO'];
						$KD_DOCUMENT['StatusValue'] = $KD_DOCUMENT[$StatusFieldName];

						
					} else {
						$sql = "select * from KD_DOCDEF_KEYS where DDK_DDKEY =" . $qd['DD_KEY'] . " ORDER BY DDK_KEYSTORE_KEYORDER";
					}
					
					// keys
					$result2 = mysqli_query ( $mysqli, $sql);
					if (! $result2)	error_message ( sql_error () );
					$count=0;
					while ($qd2 = mysqli_fetch_assoc($result2)){

						
						$arDocTypeKeys[$DocKeysCount]['Keys'][$count]['Name'] = $qd2 ['DDK_KEYSTORE_NAME'];
						$arDocTypeKeys[$DocKeysCount]['Keys'][$count]['Value'] = $qd2 ['DDK_DEFAULT'];
						$arDocTypeKeys[$DocKeysCount]['Keys'][$count]['ReadOnly'] = $qd2 ['DDK_READONLY'];
						
						$count++;
					}
					
					$arDocTypeKeys[$DocKeysCount]['Id'] = $arDocTypeDetails ['Id'];
					$arDocTypeKeys[$DocKeysCount]['Name'] = $arDocTypeDetails ['Name'];
					$arDocTypeKeys[$DocKeysCount]['DD_KEY'] = $qd['DD_KEY'];
					$arDocTypeKeys[$DocKeysCount]['DD_DESCRIPTION'] = $qd['DD_DESCRIPTION'];
					
					$DocKeysCount++;
				}
			}
		}
	
		// get manual docpaths
		$sql = "select * from KD_DOCPATH where DP_IMPORTTYPE = 'Manual'";
		$result = mysqli_query ( $mysqli, $sql);
		if (! $result)	error_message ( sql_error () );
		while ($qd = mysqli_fetch_assoc($result)){
			$arDocPaths[$qd['DP_KEY']] = $qd['DP_DESCRIPTION'];

			$arCtl['DP_KEY'] = $qd['DP_KEY'];
		}
		
	}
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'arKeyFields', $arKeyFields );
	$smarty->assign ( 'arDocTypes', $arDocTypes );
	$smarty->assign ( 'arDocPaths', $arDocPaths );
	$smarty->assign ( 'arDocTypeKeys', $arDocTypeKeys );
	$smarty->assign ( 'KD_DOCUMENT', $KD_DOCUMENT );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	
	$smarty->display ( 'keydex/ShowDocLoad.tpl' );
}

function ShowDoc($arCtl, $arSAFE_REQUEST ) {

	// DB Connection
	$mysqli = db_connect ( getDBName () );

	
	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];
	
	// see if doc available if so get URL

	if ($KD_DOCUMENT['KDU_KEY'] != ""){

		$sql = "select * from KD_DOCUMENT where KDU_KEY = '" . $KD_DOCUMENT['KDU_KEY'] . "'";
		$result = mysqli_query ( $mysqli, $sql);
		if (! $result)	error_message ( sql_error () );
		$KD_DOCUMENT = mysqli_fetch_assoc($result);

		$KD_DOCUMENT['URL'] = getWebRoot() . $KD_DOCUMENT ['KDU_FILENAME'];
		
		// get doucment items
		$sql = "select * from KD_DOCUMENT_ITEMS where KDI_KDUKEY = '" . $KD_DOCUMENT['KDU_KEY'] . "'";
		$result = mysqli_query ( $mysqli, $sql);
		if (! $result)	error_message ( sql_error () );
		while ($qd = mysqli_fetch_assoc($result)) {
			$KD_DOCUMENT['ITEMS'][] = $qd;
		}
		
		// now create TIFF version for display
		$ScratchDir = getAppRoot () . "docs/" . $_SESSION['ENVIRONMENT']['EN_URL']  . "/scratch";
		$ScratchDir2 = "/docs/" . $_SESSION['ENVIRONMENT']['EN_URL']  . "/scratch";
		//$evalstring = "/usr/bin/convert -density 300 " . $KD_DOCUMENT ['KDU_SOURCEFILE'] . "[0] -resize 25% " . $ScratchDir ."/" .$KD_DOCUMENT ['KDU_KEY'] . ".png> /dev/null";
		$evalstring = "/usr/bin/convert -density 500 " . $KD_DOCUMENT ['KDU_SOURCEFILE'] . " -resize 25% " . $ScratchDir ."/" .$KD_DOCUMENT ['KDU_KEY'] . ".png> /dev/null";
		//echo $evalstring;
		$err = array ();
		$res = array ();
		exec ( $evalstring, $res );
		// 		eval($evalstring);
		$KD_DOCUMENT['PNG'] = $ScratchDir2 ."/" .$KD_DOCUMENT ['KDU_KEY'] . ".png";

		// now create dropdwns for indexing

		$arDocTypes [' '] = "Select a Document Type";
		$DocKeysCount = 0;

		if ($KD_DOCUMENT['KDU_DDKEY'] != 0){
			// doctype already defined so just allow key edit	
			$sql = "select * from KD_DOCDEF
					LEFT OUTER JOIN KD_WORKFLOWS ON WF_DDKEY = DD_KEY
					WHERE DD_KEY = " . $KD_DOCUMENT['KDU_DDKEY'];
			$result = mysqli_query ( $mysqli, $sql);
			if (! $result)	error_message ( sql_error () );
			$KD_DOCDEF = mysqli_fetch_assoc($result);

			
			$arDocTypeKeys[0]['Id'] = $arDocTypeDetails ['Id'];
			$arDocTypeKeys[0]['Name'] = $KD_DOCUMENT ['KDU_DOCTYPE'];
			$arDocTypeKeys[0]['DD_KEY'] = $KD_DOCDEF['DD_KEY'];
			$arDocTypeKeys[0]['DD_DESCRIPTION'] = $KD_DOCDEF['DD_DESCRIPTION'];
				
			
				// see if workflow and if so get index
			if ($KD_DOCDEF['WF_KEY'] != ""){
				// workflow
				
						
				// also see what rights the user has
				foreach ($_SESSION['USERS_WORKFLOWS'] as $key => $arWorkflowDetails){
					if ($arWorkflowDetails['WF_KEY'] == $KD_DOCDEF['WF_KEY']){
						$arCtl['USF_STATUSUPDATE'] = $arWorkflowDetails['USF_STATUSUPDATE'];
						$arCtl['USF_PROCESSOWNER'] = $arWorkflowDetails['USF_PROCESSOWNER'];
					}
				}
// 				print_r($arStatus);
				
			
				$StatusFieldName = "KDU_INDEX" . $KD_DOCDEF['WF_STATUS_INDEX_NO'];
				$arDocTypeKeys[0]['StatusValue'] = $KD_DOCUMENT[$StatusFieldName];

				// get list of status fo rworkflow
				
				$sql = "select * from KD_WORKFLOWS_STEPS where WFS_WFKEY = " . $KD_DOCDEF['WF_KEY'];
				$result2 = mysqli_query ( $mysqli, $sql);
				if (! $result2)	error_message ( sql_error () );
				while ($qd2 = mysqli_fetch_assoc($result2)){
					$arStatus[$qd2['WFS_STATUS']] = $qd2['WFS_STATUS'];
					
// 					echo $StatusFieldName . " " . $KD_DOCUMENT[$StatusFieldName] . " " . $qd2['WFS_STATUS'] . "HH";
					
					if ($qd2['WFS_STATUS'] == $KD_DOCUMENT[$StatusFieldName] ) {
						$KD_DOCUMENT['NEXTACTION'] = $qd2['WFS_NEXTACTION'];
					}
				}
				
				$sql = "select * from KD_DOCDEF_KEYS where DDK_DDKEY =" . $KD_DOCDEF['DD_KEY'] . " and DDK_KEYSTORE_KEYORDER != '".  $KD_DOCDEF['WF_STATUS_INDEX_NO'] . "' ORDER BY DDK_KEYSTORE_KEYORDER";
				
			} else {
				$sql = "select * from KD_DOCDEF_KEYS where DDK_DDKEY =" . $KD_DOCDEF['DD_KEY'] . " ORDER BY DDK_KEYSTORE_KEYORDER";
			}
				
			// keys
			$result2 = mysqli_query ( $mysqli, $sql);
			if (! $result2)	error_message ( sql_error () );
			$count=0;
			while ($qd2 = mysqli_fetch_assoc($result2)){
			
				// define key name
				$KeyName = "KDU_INDEX" . $qd2['DDK_KEYSTORE_KEYORDER'];
			
				$arDocTypeKeys[0]['Keys'][$count]['Name'] = $qd2 ['DDK_KEYSTORE_NAME'];
				$arDocTypeKeys[0]['Keys'][$count]['ReadOnly'] = $qd2 ['DDK_READONLY'];
				
				// value
				
				if ($KD_DOCUMENT[$KeyName] != "") {
					$arDocTypeKeys[0]['Keys'][$count]['Value'] = $KD_DOCUMENT[$KeyName];
				} else {
					$arDocTypeKeys[0]['Keys'][$count]['Value'] = $qd2 ['DDK_DEFAULT'];
				}
				
				$count++;
			}
								
		
		} else {
			$sql = "select * from KD_DOCDEF
					LEFT OUTER JOIN KD_WORKFLOWS ON WF_DDKEY = DD_KEY";
			$result = mysqli_query ( $mysqli, $sql);
			if (! $result)	error_message ( sql_error () );
			while ($qd = mysqli_fetch_assoc($result)){
				foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
					if ($arDocTypeDetails ['Name'] == $qd['DD_KEYSTORE_DOCTYPE']){
						// dropdown options
						$arDocTypes [$arDocTypeDetails ['Id']] = $arDocTypeDetails ['Name'];
							
						// see if workflow and if so get index
						if ($qd['WF_KEY'] != ""){
							// workflow
							
							$sql = "select * from KD_DOCDEF_KEYS where DDK_DDKEY =" . $qd['DD_KEY'] . " and DDK_KEYSTORE_KEYORDER != '".  $qd['WF_STATUS_INDEX_NO'] . "' ORDER BY DDK_KEYSTORE_KEYORDER";
							
							$StatusFieldName = "KDU_INDEX" . $qd['WF_STATUS_INDEX_NO'];
							$KD_DOCUMENT['StatusValue'] = $KD_DOCUMENT[$StatusFieldName];
								
							
						} else {
							$sql = "select * from KD_DOCDEF_KEYS where DDK_DDKEY =" . $qd['DD_KEY'] . " ORDER BY DDK_KEYSTORE_KEYORDER";
						}
							
						// keys
						$result2 = mysqli_query ( $mysqli, $sql);
						if (! $result2)	error_message ( sql_error () );
						$count=0;
						while ($qd2 = mysqli_fetch_assoc($result2)){
	
	
							$arDocTypeKeys[$DocKeysCount]['Keys'][$count]['Name'] = $qd2 ['DDK_KEYSTORE_NAME'];
							$arDocTypeKeys[$DocKeysCount]['Keys'][$count]['Value'] = $qd2 ['DDK_DEFAULT'];
							$arDocTypeKeys[$DocKeysCount]['Keys'][$count]['ReadOnly'] = $qd2 ['DDK_READONLY'];
	
							$count++;
						}
							
						$arDocTypeKeys[$DocKeysCount]['Id'] = $arDocTypeDetails ['Id'];
						$arDocTypeKeys[$DocKeysCount]['Name'] = $arDocTypeDetails ['Name'];
						$arDocTypeKeys[$DocKeysCount]['DD_KEY'] = $qd['DD_KEY'];
						$arDocTypeKeys[$DocKeysCount]['DD_DESCRIPTION'] = $qd['DD_DESCRIPTION'];
							
						$DocKeysCount++;
					}
				}
			}
		}
			
		// get manual docpaths
		$sql = "select * from KD_DOCPATH where DP_IMPORTTYPE = 'Manual'";
		$result = mysqli_query ( $mysqli, $sql);
		if (! $result)	error_message ( sql_error () );
		while ($qd = mysqli_fetch_assoc($result)){
			$arDocPaths[$qd['DP_KEY']] = $qd['DP_DESCRIPTION'];

			$arCtl['DP_KEY'] = $qd['DP_KEY'];
		}

	}
	
	
	
	// so find all workflows and co status
	$arWorkFlows = getWorkFlows($arCtl);
//	print_r($arWorkFlows);
	$arWFData = getWorkflowData($KD_DOCUMENT);
	
	//print_r($arWFData);
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();

	// results info
	$smarty->assign ( 'arKeyFields', $arKeyFields );
	$smarty->assign ( 'arWFData', $arWFData );
	$smarty->assign ( 'arWFSteps', $arWorkFlows['0']['STEPS'] );
	$smarty->assign ( 'arDocTypes', $arDocTypes );
	$smarty->assign ( 'arDocPaths', $arDocPaths );
	$smarty->assign ( 'arDocTypeKeys', $arDocTypeKeys );
	$smarty->assign ( 'arStatusList', $arStatus );
	$smarty->assign ( 'KD_DOCUMENT', $KD_DOCUMENT );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );

	if ($arCtl['Screen'] == "ShowDocEdit"){
		$smarty->display ( 'keydex/ShowDocEdit.tpl' );
	} else {
		$smarty->display ( 'keydex/ShowDocUpdate.tpl' );
	}
	
}


function DocumentLoad($arCtl,$arSAFE_REQUEST) {
	
	// print_r($_POST);
// 	print_r($_SESSION);
// 	exit();
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	// check fields are populated
	
	// now create directory to hold doc and index
	$loaddate = date ( 'dHis' );
	$loadperiod = date ( 'Ym' );
	$UploadDir = getAppRoot () . "docs/" .$_SESSION['ENVIRONMENT']['EN_URL'] . "/manual";
	$UploadDir2 = "/docs/" . $_SESSION['ENVIRONMENT']['EN_URL']  . "/manual/";;
	$LoadDir = $UploadDir . "/" . $loadperiod;
	$LoadDir2 = $UploadDir2 . "/" . $loadperiod;
	
	// copy file in to dir
	if (! file_exists ( $LoadDir )) {
		if (! mkdir ( $LoadDir )) {
			print "cannot create directory: " . $LoadDir . "\n";
		} else {
			// print "created directory: " . $LoadDir . "\n";
		}
	}

	// rmeove special chars
	$target_path = $LoadDir . "/" . $loaddate . "-" . basename ( str_replace ( " ", "", $_FILES ['docimage'] ['name'] ) );
	$target_path2 = $LoadDir2 . "/" . $loaddate . "-" . basename ( str_replace ( " ", "", $_FILES ['docimage'] ['name'] ) );
	// echo $target_path;
	
	if (move_uploaded_file ( $_FILES ['docimage'] ['tmp_name'], $target_path )) {
		// echo "The file ". basename( $_FILES['uploadedfile']['name']). " has been uploaded";
		$KD_DOCUMENT ['KDU_FILENAME'] = $target_path2;
		$KD_DOCUMENT ['KDU_SOURCEFILE'] = $target_path;
		$KD_DOCUMENT ['KDU_IMAGENAME'] = $_FILES ['docimage'] ['name'];

		// update DB record
		$sql = "insert into KD_DOCUMENT (KDU_DATE,KDU_USKEY, KDU_DPKEY,KDU_FILENAME,KDU_SOURCEFILE,KDU_IMAGENAME,KDU_STATUS,KDU_FILESIZE,KDU_SOURCE)
				values (now(),'" . $_SESSION ['US_KEY'] . "','" . $arCtl ['KDU_DPKEY'] . "','" . mysql_escape_string ( $KD_DOCUMENT ['KDU_FILENAME'] ) . "','" . mysql_escape_string ( $KD_DOCUMENT ['KDU_SOURCEFILE'] ) . "','" . mysql_escape_string ( $KD_DOCUMENT ['KDU_IMAGENAME'] ) . "','New','" . $_FILES ['docimage'] ['size'] . "','M')";
		
		//echo $sql;
		
		$result = mysqli_query ( $mysqli,$sql );
		if (! $result)
			error_message ( sql_error () );

		$KD_DOCUMENT['KDU_KEY'] = mysqli_insert_id ($mysqli);
		$_SESSION['KD_DOCUMENT'] = $KD_DOCUMENT;
		
		// put in session for redirect
		
		// return back status
		$arCtl ['Message'] = $arCtl ['KDU_DOCTYPE'] . " document was loaded using doc key " . $KD_DOCUMENT ['KDU_KEY'];
		
		$arCtl['WFL_TYPE'] = "DocLoad";
		$arCtl['WFL_SUMMARY'] = $arCtl ['Message'] ;
		UpdWorkflowLog($arCtl,$KD_DOCUMENT);
		
	} else {
		$arCtl ['Message'] = "There was an error uploading the file, please try again! Copying " . $_FILES ['docimage'] ['tmp_name'] . " to " . $target_path;
		$arCtl['WFL_TYPE'] = "DocLoad";
		$arCtl['WFL_SUMMARY'] = $arCtl ['Message'] ;
		UpdWorkflowLog($arCtl,$KD_DOCUMENT);
	}

// 	exit();
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
}

function DocumentKeyUpdate($arCtl,$arSAFE_REQUEST) {

// 	print_r($_POST);
// 	print_r($_FILES);

	// DB Connection
	
	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];
	
	if ($arCtl['Complete'] != "Y"){
	
		$mysqli = db_connect ( getDBName () );
	
		// get workflow 
		$sql = "select * from KD_WORKFLOWS where WF_DDKEY = " . $KD_DOCUMENT['KDU_DDKEY'];
		$result = mysqli_query ( $mysqli,$sql );
		if (! $result)
			error_message ( sql_error () );
		$KD_WORKFLOWS = mysqli_fetch_assoc($result);
	
		if ($KD_WORKFLOWS['WF_KEY'] != ""){
			// doctype in work flow so set the initial status
			$arCtl ['I']['Status'] = $KD_WORKFLOWS['WF_STARTSTATUS'];
		}
		
		// get keys names
		$sql = "select * from KD_DOCDEF, KD_DOCDEF_KEYS where DDK_DDKEY = DD_KEY and DD_KEY = " . $KD_DOCUMENT['KDU_DDKEY'];
		$result = mysqli_query ( $mysqli,$sql );
		if (! $result)
			error_message ( sql_error () );
	
		// see if new so can populate workflow status
		
		$sql = "UPDATE KD_DOCUMENT SET KDU_STATUS = 'Uploaded', KDU_DPKEY  = '" . $KD_DOCUMENT['KDU_DPKEY'] . "',  KDU_DOCTYPE = '" . $KD_DOCUMENT['KDU_DOCTYPE'] . "', KDU_DDKEY = '" . $KD_DOCUMENT['KDU_DDKEY'] . "'";
		$count = 1;
		
		while ($qd = mysqli_fetch_assoc($result)){
			foreach ( $arCtl ['I'] as $field => $value ) {
				// see if matches key
				if ($field == $qd['DDK_KEYSTORE_NAME']){
					$field = str_replace ( "'", "", $field );
					$sql .= ",KDU_INDEX" . $qd['DDK_KEYSTORE_KEYORDER'] . " = '" . mysqli_real_escape_string ($mysqli,$value) . "', KDU_INDEX" . $qd['DDK_KEYSTORE_KEYORDER']  . "_NAME = '" . mysqli_real_escape_string ($mysqli,$field) . "'";
				}
			}
		}
	
		$sql .= " where KDU_KEY = '" . $KD_DOCUMENT['KDU_KEY'] . "'";
	
		$result = mysqli_query ( $mysqli,$sql );
		if (! $result)
			error_message ( sql_error () );
	
	
		$arCtl['WFL_TYPE'] = "DocKeyUpdate";
		$arCtl['WFL_SUMMARY'] = $_SESSION['Name'] . " updated doc " . $KD_DOCUMENT['KDU_KEY'];
		UpdWorkflowLog($arCtl,$KD_DOCUMENT);
		
		// return back status
	
		checkWorkflow($arCtl,$KD_DOCUMENT);
	
	// print_r($arCtl);
		
		if ($arCtl['Screen'] == "ShowDocumentLoad"){
			// do upload agains
			$_SESSION['NextAction'] = $arCtl['Screen'];
			unset($_SESSION['KD_DOCUMENT']);
		} elseif ($arCtl['Next'] == "ShowNext"){
			
			// doc edit screen asking for next doc to update
			$query = "SELECT *
						FROM KD_WORKFLOWS
						WHERE WF_KEY = '" . $arCtl ['WF_KEY'] . "'";
			echo $query;
			$result = mysqli_query ($mysqli, $query );
			if (! $result)
				error_message ( sql_error () );
			$KD_WORKFLOWS = mysqli_fetch_assoc ( $result );		
	
			$query = "select * from KD_DOCUMENT 
					where KDU_INDEX" . $KD_WORKFLOWS['WF_STATUS_INDEX_NO'] . " = '" . $arCtl ['WFS_STATUS'] . "'
					and KDU_DDKEY = " . $KD_DOCUMENT['KDU_DDKEY'] . '
					order by KDU_KEY LIMIT 1';
			echo $query;
			$result = mysqli_query ($mysqli, $query );
			if (! $result)
				error_message ( sql_error () );
			$KD_DOCUMENT = mysqli_fetch_assoc ( $result );		
			$_SESSION['KD_DOCUMENT']['KDU_KEY'] = $KD_DOCUMENT['KDU_KEY'];			
			$_SESSION['NextAction'] = "ShowDocEdit";
	
		}

	}
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
}

function UpdDocumentItems ( $arCtl, $arSAFE_REQUEST ) {

	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];
	$KD_DOCUMENT_ITEMS = $arSAFE_REQUEST['KD_DOCUMENT_ITEMS'];
	
	if ($KD_DOCUMENT_ITEMS['KDI_KEY'] != ""){
		
		
	} else {
		
		$sql = "insert into KD_DOCUMENT_ITEMS (
					KDI_KDUKEY,
					KDI_SEQ,
					KDI_DESCRIPTION,
					KDI_ITEMCODE,
					KDI_ITEMVALUE
				) values (
					'" . $KD_DOCUMENT['KDU_KEY'] . "',
					'" . $KD_DOCUMENT_ITEMS['KDI_SEQ'] . "',
					'" . $KD_DOCUMENT_ITEMS['KDI_DESCRIPTION'] . "',
					'" . $KD_DOCUMENT_ITEMS['KDI_ITEMCODE'] . "',
					'" . $KD_DOCUMENT_ITEMS['KDI_ITEMVALUE'] . "'
				)";
		
		echo $sql;

		$result = mysqli_query ($mysqli, $sql );
		if (! $result)
			error_message ( sql_error () );
	}		

	// now check if OK
	$sql = "select * from KD_DOCUMENT where KDU_KEY = " . $KD_DOCUMENT['KDU_KEY'];
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)
		error_message ( sql_error () );
	$KD_DOCUMENT = mysqli_fetch_assoc($result);	

	$sql = "select * from KD_DOCUMENT_ITEMS where KDI_KDUKEY = " . $KD_DOCUMENT['KDU_KEY'];
	echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)
		error_message ( sql_error () );
	$TotalValue = 0;
	while ($qd = mysqli_fetch_assoc($result)){
		$TotalValue = $TotalValue + $qd['KDI_ITEMVALUE'];
	}
	if ($TotalValue == $KD_DOCUMENT['KDU_INDEX3']){
		$sql = "update KD_DOCUMENT set KDU_INDEX4 = 'PO Approved' where KDU_KEY = " . $KD_DOCUMENT['KDU_KEY'];
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)
			error_message ( sql_error () );
		$arCtl ['Message'] = $KD_DOCUMENT ['KDU_DOCTYPE'] . " " . $KD_DOCUMENT ['KDU_KEY'] . " GL Codes updated and match total value";
		
	} else {
		$arCtl ['Message'] = $KD_DOCUMENT ['KDU_DOCTYPE'] . " " . $KD_DOCUMENT ['KDU_KEY'] . " GL Codes updated and not match total value";
		
	}
	
	$arCtl['WFL_TYPE'] = "DocKeyUpdate";
	$arCtl['WFL_SUMMARY'] = $arCtl ['Message'] ;
	UpdWorkflowLog($arCtl,$KD_DOCUMENT);
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());

}

function DelDocumentItems ( $arCtl, $arSAFE_REQUEST ) {

	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];
	$KD_DOCUMENT_ITEMS = $arSAFE_REQUEST['KD_DOCUMENT_ITEMS'];;

	$sql = "delete from KD_DOCUMENT_ITEMS where KDI_KEY = " . $KD_DOCUMENT_ITEMS['KDI_KEY'];
	echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)
		error_message ( sql_error () );

	
	// now check if OK
	$sql = "select * from KD_DOCUMENT where KDU_KEY = " . $KD_DOCUMENT['KDU_KEY'];
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)
		error_message ( sql_error () );
	$KD_DOCUMENT = mysqli_fetch_assoc($result);
	
	$sql = "select * from KD_DOCUMENT_ITEMS where KDI_KDUKEY = " . $KD_DOCUMENT['KDU_KEY'];
	echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)
		error_message ( sql_error () );
	$TotalValue = 0;
	while ($qd = mysqli_fetch_assoc($result)){
		$TotalValue = $TotalValue + $qd['KDI_ITEMVALUE'];
	}
	if ($TotalValue == $KD_DOCUMENT['KDU_INDEX3']){
		$sql = "update KD_DOCUMENT set KDU_INDEX4 = 'PO Approved' where KDU_KEY = " . $KD_DOCUMENT['KDU_KEY'];
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)
			error_message ( sql_error () );
		$arCtl ['Message'] = $KD_DOCUMENT ['KDU_DOCTYPE'] . " " . $KD_DOCUMENT ['KDU_KEY'] . " GL Codes updated and match total value";
	
	} else {
		$arCtl ['Message'] = $KD_DOCUMENT ['KDU_DOCTYPE'] . " " . $KD_DOCUMENT ['KDU_KEY'] . " GL Codes updated and not match total value";
	
	}
	
	$arCtl['WFL_TYPE'] = "DocKeyUpdate";
	$arCtl['WFL_SUMMARY'] = $arCtl ['Message'] ;
	UpdWorkflowLog($arCtl,$KD_DOCUMENT);
	
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());

}

?>
