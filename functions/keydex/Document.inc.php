<?php

/*********************************************************************
 
 Show list of contracts
 
 ********************************************************************/
function ShowDocument($arCtl, $arSAFE_REQUEST) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	
	//print_r($arSAFE_REQUEST);
	
	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	// get document details

	$query = "SELECT *
				FROM KD_DOCUMENT, KD_WORKFLOWS
				WHERE WF_DDKEY = KDU_DDKEY
				AND KDU_KEY = '" . $KD_DOCUMENT ['KDU_KEY']  . "'";
	
	echo $query;
	$result = mysqli_query ($mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$KD_DOCUMENT = mysqli_fetch_assoc ( $result );
	
	
	// now create TIFF version for display
	$ScratchDir = getAppRoot () . "docs/" . $_SESSION['KX_COMPANY']['KC_COMPANY'] . "/scratch";
	$ScratchDir2 = "/docs/" . $_SESSION['KX_COMPANY']['KC_COMPANY'] . "/scratch";
	$evalstring = "/usr/bin/convert -density 300 " . $KD_DOCUMENT ['KDU_SOURCEFILE'] . "[0] -resize 25% " . $ScratchDir ."/" .$KD_DOCUMENT ['KDU_KEY'] . ".png> /dev/null";
//	echo $evalstring;
	$err = array ();
	$res = array ();
	exec ( $evalstring, $res );
	// 		eval($evalstring);
	$KD_DOCUMENT['PNG'] = $ScratchDir2 ."/" .$KD_DOCUMENT ['KDU_KEY'] . ".png";
	
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */
	
	$arStatus [''] = "All";
	$arStatus ['Uploaded'] = "Uploaded Docs";
	$arStatus ['Processed'] = "Processed";
	$arStatus ['Filed'] = "Filed";
	
	
	// also get sttaus list
	$sql = "select * from KD_WORKFLOWS_STEPS WHERE WFS_WFKEY = '" . $KD_DOCUMENT['WF_KEY'] . "' order by WFS_SEQ";
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arWFStatus[$qd['WFS_STATUS']] = $qd['WFS_STATUS'];
	}
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	
	// keys
	$sql = "select * from KD_DOCDEF_KEYS where DDK_DDKEY ='" . $KD_DOCUMENT['KDU_DDKEY'] . "' ORDER BY DDK_KEYSTORE_KEYORDER";
	$result2 = mysqli_query ( $mysqli, $sql);
	if (! $result2)	error_message ( sql_error () );
	$count=0;
	$DocKeysCount=0;
	while ($qd2 = mysqli_fetch_assoc($result2)){

		if ($qd2 ['DDK_KEYSTORE_NAME'] == "Status"){
			// index so put in different
			$arIndexKey['Name'] = $qd2 ['DDK_KEYSTORE_NAME'];
			$arIndexKey['Name'] = $qd2 ['DDK_DEFAULT'];
			$arIndexKey['Name'] = $qd2 ['DDK_READONLY'];
		} else {
			$arDocTypeKeys[$DocKeysCount]['Keys'][$count]['Name'] = $qd2 ['DDK_KEYSTORE_NAME'];
			$arDocTypeKeys[$DocKeysCount]['Keys'][$count]['Value'] = $qd2 ['DDK_DEFAULT'];
			$arDocTypeKeys[$DocKeysCount]['Keys'][$count]['ReadOnly'] = $qd2 ['DDK_READONLY'];
		}
		$count++;
	}
					
	
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	
	
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'KD_DOCUMENT', $KD_DOCUMENT );
	$smarty->assign ( 'arKeyFields', $arKeyFields );
	$smarty->assign ( 'arIndexKey', $arIndexKey );
	$smarty->assign ( 'arDocTypes', $arDocTypes );
	$smarty->assign ( 'arDocPaths', $arDocPaths );
	$smarty->assign ( 'arDocTypeKeys', $arDocTypeKeys );
	$smarty->assign ( 'StatusList', $arStatus );
	$smarty->assign ( 'WFStatusList', $arWFStatus );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	
	$smarty->display ( 'keydex/ShowDocument.tpl' );
}

function ListDocument($arCtl, $arSAFE_REQUEST) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	if ($arCtl ['Records'] == "") {
		$arCtl ['Records'] = 50;
	}
	if ($arCtl ['Sort'] == "") {
		$arCtl ['Sort'] = "KDU_KEY DESC";
	}
	
	$query = "SELECT * 
				FROM KD_DOCUMENT
				LEFT OUTER JOIN KD_DOCPATH ON KDU_DPKEY = DP_KEY
				LEFT OUTER JOIN KD_WORKFLOWS ON WF_DDKEY = KDU_DDKEY
			WHERE KDU_TYPE = 'K'";
	
	
	if ($arCtl ['KDU_INDEX_SEARCH'] != "") {
		$query = $query . " AND (
					KDU_INDEX1 like '" . $arCtl ['KDU_INDEX_SEARCH'] . "%' OR
					KDU_INDEX2 like '" . $arCtl ['KDU_INDEX_SEARCH'] . "%' OR
					KDU_INDEX3 like '" . $arCtl ['KDU_INDEX_SEARCH'] . "%' OR
					KDU_INDEX4 like '" . $arCtl ['KDU_INDEX_SEARCH'] . "%' OR
					KDU_INDEX5 like '" . $arCtl ['KDU_INDEX_SEARCH'] . "%' )";
	}

	if ($arCtl ['KDU_TEXT_SEARCH'] != "") {
		$query = $query . " AND (KDU_TEXT like '%" . mysql_escape_string ( $arCtl ['KDU_TEXT_SEARCH'] ) . "%' OR (
					KDU_INDEX1 like '" . $arCtl ['KDU_TEXT_SEARCH'] . "%' OR
					KDU_INDEX2 like '" . $arCtl ['KDU_TEXT_SEARCH'] . "%' OR
					KDU_INDEX3 like '" . $arCtl ['KDU_TEXT_SEARCH'] . "%' OR
					KDU_INDEX4 like '" . $arCtl ['KDU_TEXT_SEARCH'] . "%' OR
					KDU_INDEX5 like '" . $arCtl ['KDU_TEXT_SEARCH'] . "%' ))";
	}
	
	
	if ($arCtl ['KDU_DPKEY'] != "") {
		$query = $query . " AND KDU_DPKEY = '" . $arCtl ['KDU_DPKEY'] . "'";
	}
	
	if ($arCtl ['KDU_STATUS'] != "") {
		$query = $query . " AND KDU_STATUS = '" . $arCtl ['KDU_STATUS'] . "'";
	}
	
	if ($arCtl ['KDU_PROCESSING_STATUS'] != "") {
		$query = $query . " AND KDU_PROCESSING_STATUS = '" . $arCtl ['KDU_PROCESSING_STATUS'] . "'";
	}
	
	if ($arCtl ['KDU_DDKEY'] != "") {
		$query = $query . " AND KDU_DDKEY = '" . mysql_escape_string ( $arCtl ['KDU_DDKEY'] ) . "'";
	}
	
// 	if ($arCtl ['KDU_TEXT_SEARCH'] != "") {
// 		$query = $query . " AND KDU_TEXT like '%" . mysql_escape_string ( $arCtl ['KDU_TEXT_SEARCH'] ) . "%'";
// 	}
// 	if ($arCtl ['KDU_TEXT_SEARCH_2'] != "") {
// 		$query = $query . " AND KDU_TEXT like '%" . mysql_escape_string ( $arCtl ['KDU_TEXT_SEARCH_2'] ) . "%'";
// 	}
	
	$query = $query . " ORDER BY " . $arCtl ['Sort'];
	
	if ($arCtl ['Records'] != "") {
		$query = $query . " LIMIT " . $arCtl ['Records'] . "";
	}
	
//	 echo $query;
	
	if (isset ( $arCtl ['Run'] )) {
		
	//	 echo $query;
		$result = mysqli_query ($mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$arCtl ['count'] = 0;
		while ( $qd = mysqli_fetch_assoc ( $result ) ) {
			$count = $arCtl ['count'];
			$arKD_DOCUMENT [$count] = $qd;
			$arKD_DOCUMENT [$count] ['SIZE'] = number_format ( $qd ['KDU_FILESIZE'] / 1024, 1 );
			
			// see if workflwo
			if ($arKD_DOCUMENT [$count]['WF_KEY'] != ""){
				$sql = "select count(*) TOT from KD_WORKFLOWS_LOG where WFL_KDUKEY = " . $arKD_DOCUMENT [$count]['KDU_KEY'];
				$result2 = mysqli_query ($mysqli, $sql );
				if (! $result)	error_message ( sql_error () );
				$qd2 = mysqli_fetch_assoc ( $result2 );
				$arKD_DOCUMENT [$count] ['WFLOG_COUNT'] = $qd2['TOT'];

				// get status
				$StatusFieldName = "KDU_INDEX" . $arKD_DOCUMENT [$count]['WF_STATUS_INDEX_NO'];
				
// 				echo $StatusFieldName.
				$arKD_DOCUMENT [$count]['WF_STATUS'] = $arKD_DOCUMENT [$count][$StatusFieldName];
				
			
			
			} else {
				$arKD_DOCUMENT [$count] ['WFLOG_COUNT'] = "-1";
			}
			
			
			$arCtl ['count'] ++;
		}
	}
	
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */
	// source
	// get manual docpaths
	$sql = "select * from KD_DOCPATH ";
	$arSource[''] = '..';		
	$result = mysqli_query ( $mysqli, $sql);
	if (! $result)	error_message ( sql_error () );
	while ($qd = mysqli_fetch_assoc($result)){
		$arSource[$qd['DP_KEY']] = $qd['DP_DESCRIPTION'];		
	}
	
	$sql = "select * from KD_DOCDEF";
	$result = mysqli_query ( $mysqli, $sql);
	if (! $result)	error_message ( sql_error () );
	$arDocTypes [''] = '..';
	while ($qd = mysqli_fetch_assoc($result)){
	
		foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
			if ($arDocTypeDetails ['Name'] == $qd['DD_KEYSTORE_DOCTYPE']){
				$arDocTypes [$qd['DD_KEY']] = $arDocTypeDetails ['Name'];
			}
		}
	
	}
	
	$arSort ['KDU_KEY DESC'] = "By Key Desc";
	$arSort ['KDU_KEY ASC'] = "By Key Asc";
	$arSort ['KDU_DATE ASC'] = "By Date Asc";
	$arSort ['KDU_DATE DESC'] = "By Date Desc";
	
	// source
	$DocProcessing [''] = "..";
	$DocProcessing ['C'] = "Complete";
	$DocProcessing ['N'] = "Not Started";
	$DocProcessing ['X'] = "Errored";
	
	$Records ['50'] = "Top 50";
	$Records ['100'] = "Top 100";
	$Records ['200'] = "Top 200";
	$Records ['300'] = "Top 300";
	$Records ['400'] = "Top 400";
	$Records ['500'] = "Top 500";
	$Records [''] = "All";
	
	// Create Menu Drop Down
	
	$arStatus [''] = "All";
	$arStatus ['Uploaded'] = "Uploaded Docs";
	$arStatus ['Processing'] = "Processing";
	$arStatus ['Filed'] = "Filed";
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'arKD_DOCUMENT', $arKD_DOCUMENT );
	$smarty->assign ( 'SortList', $arSort );
	$smarty->assign ( 'StatusList', $arStatus );
	$smarty->assign ( 'IndexStatus', $IndexStatus );
	$smarty->assign ( 'SourceList', $arSource );
	$smarty->assign ( 'RecordsList', $Records );
	$smarty->assign ( 'arDocTypes', $arDocTypes );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	
	$smarty->display ( 'keydex/ListDocument.tpl' );
}

function ListDocumentWorkflow($arCtl, $arSAFE_REQUEST) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */

	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];
	// DB Connection
	$mysqli = db_connect ( getDBName () );

// 	if ($arCtl ['Records'] == "") {
// 		$arCtl ['Records'] = 50;
// 	}
	if ($arCtl ['Sort'] == "") {
		$arCtl ['Sort'] = "KDU_KEY DESC";
	}

	$query = "SELECT *
				FROM KD_WORKFLOWS
				WHERE WF_KEY = '" . $arCtl ['WF_KEY'] . "'";
	$result = mysqli_query ($mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$arCtl ['count'] = 0;
	$KD_WORKFLOWS = mysqli_fetch_assoc ( $result );
	
	
	$query = "SELECT *
				FROM KD_WORKFLOWS, KD_DOCUMENT
				LEFT OUTER JOIN KD_DOCPATH ON KDU_DPKEY = DP_KEY
				WHERE KDU_TYPE = 'K'
				AND WF_DDKEY = KDU_DDKEY
				AND WF_KEY = '" . $arCtl ['WF_KEY'] . "'";

	
	if ($_SESSION['USERS_DOCUMENTS']){
		// then find out what docs they can see
		foreach ($_SESSION['USERS_DOCUMENTS'] as $key => $USERS_DOCUMENTS){
			if ($USERS_DOCUMENTS['DD_KEY'] == $arCtl['KDU_DDKEY']){
				if ($USERS_DOCUMENTS['USD_USERDOCSONLY'] == "Y"){
					$query = $query . " AND KDU_USKEY = '" . $_SESSION ['US_KEY'] . "'";
				}
			}
		}
	}

	if ($_SESSION['USERS_WORKFLOWS']){
		// then find out what docs they can see
		foreach ($_SESSION['USERS_WORKFLOWS'] as $key => $USERS_WORKFLOWS){
			if ($USERS_WORKFLOWS['WF_KEY'] == $arCtl['WF_KEY']){
				if ($USERS_WORKFLOWS['USF_STATUSUPDATE'] == "Y"){
					$arCtl['StatusUpdate'] = "Y";
				} else {
					$arCtl['StatusUpdate'] = "N";
				}
				
				//finally get status field
				if ($arCtl['WF_STATUS'] != ""){
					$query = $query . " AND KDU_INDEX" . $USERS_WORKFLOWS['WF_STATUS_INDEX_NO'] . " = '" . $arCtl ['WF_STATUS'] . "'";
				}
				
				$WORKFLOW_INDEX = "KDU_INDEX" . $USERS_WORKFLOWS['WF_STATUS_INDEX_NO'];
				
			}
		}
	}
	
	if ($arCtl ['KDU_DPKEY'] != "") {
		$query = $query . " AND KDU_DPKEY = '" . $arCtl ['KDU_DPKEY'] . "'";
	}

	if ($arCtl ['KDU_PROCESSING_STATUS'] != "") {
		$query = $query . " AND KDU_PROCESSING_STATUS = '" . $arCtl ['KDU_PROCESSING_STATUS'] . "'";
	}

	if ($arCtl ['KDU_TEXT_SEARCH'] != "") {
		$query = $query . " AND (KDU_TEXT like '%" . mysql_escape_string ( $arCtl ['KDU_TEXT_SEARCH'] ) . "%') OR (
					KDU_INDEX1 like '" . $arCtl ['KDU_INDEX_SEARCH'] . "%' OR
					KDU_INDEX2 like '" . $arCtl ['KDU_INDEX_SEARCH'] . "%' OR
					KDU_INDEX3 like '" . $arCtl ['KDU_INDEX_SEARCH'] . "%' OR
					KDU_INDEX4 like '" . $arCtl ['KDU_INDEX_SEARCH'] . "%' OR
					KDU_INDEX5 like '" . $arCtl ['KDU_INDEX_SEARCH'] . "%' )";
	}

	if ($arCtl ['WFS_STATUS'] != "") {
		$query = $query . " AND KDU_INDEX" . $KD_WORKFLOWS['WF_STATUS_INDEX_NO'] . " = '" . $arCtl ['WFS_STATUS'] . "'";
	}
	
	// 	if ($arCtl ['KDU_TEXT_SEARCH_2'] != "") {
	// 		$query = $query . " AND KDU_TEXT like '%" . mysql_escape_string ( $arCtl ['KDU_TEXT_SEARCH_2'] ) . "%'";
	// 	}

	$query = $query . " ORDER BY " . $arCtl ['Sort'];

	if ($arCtl ['Records'] != "") {
		$query = $query . " LIMIT " . $arCtl ['Records'] . "";
	}

 	// echo $query;

	if (isset ( $arCtl ['Run'] )) {

		// echo $query;
		$result = mysqli_query ($mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$count = 0;
		while ( $qd = mysqli_fetch_assoc ( $result ) ) {
			$arKD_DOCUMENT [$count] = $qd;
			$arKD_DOCUMENT [$count] ['SIZE'] = number_format ( $qd ['KDU_FILESIZE'] / 1024, 1 );

			$arKD_DOCUMENT[$count]['WORKFLOW_STATUS'] = $qd[$WORKFLOW_INDEX];
			
			// see if workflwo
			if ($arKD_DOCUMENT [$count]['WF_KEY'] != ""){
				$sql = "select count(*) TOT from KD_WORKFLOWS_LOG where WFL_KDUKEY = " . $arKD_DOCUMENT [$count]['KDU_KEY'];
				$result2 = mysqli_query ($mysqli, $sql );
				if (! $result)	error_message ( sql_error () );
				$qd2 = mysqli_fetch_assoc ( $result2 );
				$arKD_DOCUMENT [$count] ['WFLOG_COUNT'] = $qd2['TOT'];
			} else {
				$arKD_DOCUMENT [$count] ['WFLOG_COUNT'] = "-1";
			}
				
			$count++;
		}
	}

	
	//print_r($arKD_DOCUMENT);
	$arCtl ['count'] = $count;
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */

// 	if ($_SESSION['USERS_DOCUMENTS']){
// 		foreach ($_SESSION['USERS_DOCUMENTS'] as $key => $USERS_DOCUMENTS){
// 			$arDocTypes [$USERS_DOCUMENTS['DD_KEY']] = $USERS_DOCUMENTS['DD_DESCRIPTION'];
// 		}
// 	} else {
	
// 		$sql = "select * from KD_DOCDEF";
// 		$result = mysqli_query ( $mysqli, $sql);
// 		if (! $result)	error_message ( sql_error () );
// 		$arDocTypes [''] = '..';
// 		while ($qd = mysqli_fetch_assoc($result)){
// 			$arDocTypes [$qd['DD_KEY']] = $qd ['DD_DESCRIPTION'];
// 		}
// 	}

// 	if ($_SESSION['USERS_WORKFLOWS']){
// 		foreach ($_SESSION['USERS_WORKFLOWS'] as $key => $USERS_WORKFLOWS){
// 			$arWorkflowList [$USERS_WORKFLOWS['WF_KEY']] = $USERS_WORKFLOWS['WF_DESCRIPTION'];
// 		}
// 	} else {
// 		$sql = "select * from KD_WORKFLOWS";
// 		$result = mysqli_query ( $mysqli, $sql);
// 		if (! $result)	error_message ( sql_error () );
// 		$arWorkflowList [''] = "Select Workflow";
// 		while ($qd = mysqli_fetch_assoc($result)){
// 			$arWorkflowList [$qd['WF_KEY']] = $qd['WF_DESCRIPTION'];
// 		}
		
// 	}

	// also get sttaus list
	$sql = "select * from KD_WORKFLOWS_STEPS WHERE WFS_WFKEY = '" . $arCtl['WF_KEY'] . "' order by WFS_SEQ";
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arWFStatus[$qd['WFS_STATUS']] = $qd['WFS_STATUS'];
	}
	
	$arSort ['KDU_KEY DESC'] = "By Key Desc";
	$arSort ['KDU_KEY ASC'] = "By Key Asc";
	$arSort ['KDU_DATE ASC'] = "By Date Asc";
	$arSort ['KDU_DATE DESC'] = "By Date Desc";

	// source
	$DocProcessing [''] = "..";
	$DocProcessing ['C'] = "Complete";
	$DocProcessing ['N'] = "Not Started";
	$DocProcessing ['X'] = "Errored";

	$Records ['50'] = "Top 50";
	$Records ['100'] = "Top 100";
	$Records ['200'] = "Top 200";
	$Records ['300'] = "Top 300";
	$Records ['400'] = "Top 400";
	$Records ['500'] = "Top 500";
	$Records [''] = "All";

	// Create Menu Drop Down

	$arStatus [''] = "All";
	$arStatus ['Uploaded'] = "Uploaded Docs";
	$arStatus ['Processing'] = "Processing";
	$arStatus ['Filed'] = "Filed";

	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();

	// results info
	$smarty->assign ( 'arKD_DOCUMENT', $arKD_DOCUMENT );
	$smarty->assign ( 'KD_WORKFLOWS', $KD_WORKFLOWS );
	$smarty->assign ( 'SortList', $arSort );
	$smarty->assign ( 'StatusList', $arWFStatus );
	$smarty->assign ( 'IndexStatus', $IndexStatus );
	$smarty->assign ( 'WorkflowList', $arWorkflowList );
	$smarty->assign ( 'RecordsList', $Records );
	$smarty->assign ( 'arDocTypes', $arDocTypes );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );

	if ($KD_WORKFLOWS['WF_LISTTEMPLATE'] != ""){
		$smarty->display ( 'keydex/' . $KD_WORKFLOWS['WF_LISTTEMPLATE']);
	} else {
		$smarty->display ( 'keydex/ListDocumentWorkflow.tpl' );
	}
	
}


/**
 * *******************************************************************
 *
 * Update \ Create New Parts
 *
 * ******************************************************************
 */
function UpdDocumentWorkflowStatus($arCtl, $arSAFE_REQUEST) {
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];
	
	if ($_SESSION['USERS_WORKFLOWS']){
		// then find out what docs they can see
		foreach ($_SESSION['USERS_WORKFLOWS'] as $key => $USERS_WORKFLOWS){
			if ($USERS_WORKFLOWS['WF_KEY'] == $arCtl['WF_KEY']){
				$WORKFLOW_INDEX = "KDU_INDEX" . $USERS_WORKFLOWS['WF_STATUS_INDEX_NO'];
			}
		}
	}
	
	$query = "UPDATE KD_DOCUMENT SET " .
				$WORKFLOW_INDEX . " = '" . $arCtl ['DOCSTATUS'] . "'
				WHERE KDU_KEY = '" . $KD_DOCUMENT ['KDU_KEY'] . "'";
	$result = mysqli_query ($mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	$arCtl['WFL_DESCRIPTION'] = "Manual Update to status " . $arCtl ['DOCSTATUS'] . " by " . $_SESSION['US_NAME'];
	UpdWorkflowLog($arCtl,$KD_DOCUMENT);
	
	ListDocumentWorkflow ( $arCtl, $KD_DOCUMENT );
}

function UpdDocument($arCtl, $arSAFE_REQUEST) {
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];

	$query = "UPDATE KD_DOCUMENT SET
				KDU_DOCTYPE = '" . $KD_DOCUMENT ['KDU_DOCTYPE'] . "',
				KDU_STATUS = '" . $KD_DOCUMENT ['KDU_STATUS'] . "',
				KDU_INDEX1 = '" . $KD_DOCUMENT ['KDU_INDEX1'] . "',
				KDU_INDEX2 = '" . $KD_DOCUMENT ['KDU_INDEX2'] . "',
				KDU_INDEX3 = '" . $KD_DOCUMENT ['KDU_INDEX3'] . "',
				KDU_INDEX4 = '" . $KD_DOCUMENT ['KDU_INDEX4'] . "',
				KDU_INDEX5 = '" . $KD_DOCUMENT ['KDU_INDEX5'] . "',
				KDU_INDEX6 = '" . $KD_DOCUMENT ['KDU_INDEX6'] . "',
				KDU_INDEX7 = '" . $KD_DOCUMENT ['KDU_INDEX7'] . "',
				KDU_INDEX8 = '" . $KD_DOCUMENT ['KDU_INDEX8'] . "'
				WHERE KDU_KEY = '" . $KD_DOCUMENT ['KDU_KEY'] . "'";
	$result = mysqli_query ($mysqli, $query );
	if (! $result)
		error_message ( sql_error () );

	ShowDocument ( $arCtl, $KD_DOCUMENT );
}
/**
 * *******************************************************************
 *
 * Delete Menu Entry
 *
 * ******************************************************************
 */
function DelDocument($arCtl, $arSAFE_REQUEST) {
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCUMENT = $arSAFE_REQUEST['KD_DOCUMENT'];
	
	$query = "DELETE FROM KD_DOCUMENT WHERE KDU_KEY = '" . $KD_DOCUMENT ['KDU_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ($mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	ListDocument ( $arCtl, $KD_DOCUMENT );
}

?>
