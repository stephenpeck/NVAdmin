<?php
function DocWorkflowDash($arCtl) {

	// this shows the dashboard

	// so find all workflows and co status
	$arWorkFlows = getWorkFlows($arCtl);

	// find latest set of notifcations
	$arDocMessages = getDocMessages($arCtl);


	// set up smarty get DB connections
	$smarty = getSmarty();

	$smarty->assign('arWorkFlows',    $arWorkFlows);
	$smarty->assign('arDocMessages',    $arDocMessages);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',    $_SESSION);
	$smarty->assign('USERS',    $USERS);

	// Now show
	$smarty->display('keydex/' . $_SESSION['USERS_HOME_TEMPLATE']);

}

// so find all workflows and co status
function getWorkFlows($arCtl){

	$mysqli = db_connect ( getDBName () );

	if (count($_SESSION ['USERS_WORKFLOWS']) > 0) {
		$count=0;
		foreach ($_SESSION ['USERS_WORKFLOWS'] as $key => $USERS_WORKFLOW){

			$arWorkFlows[$count] = $USERS_WORKFLOW;

			if ($USERS_WORKFLOW['USF_WFSKEY'] != ""){
				$sql = "select * from KD_WORKFLOWS_STEPS where WFS_WFKEY = " . $USERS_WORKFLOW['WF_KEY'] . " AND WFS_KEY = " . $USERS_WORKFLOW['USF_WFSKEY'] . " order by WFS_SEQ ASC";
			} else {
				$sql = "select * from KD_WORKFLOWS_STEPS where WFS_WFKEY = " . $USERS_WORKFLOW['WF_KEY'] . " order by WFS_SEQ ASC";

			}
			//		echo $sql;
			$result = mysqli_query ($mysqli, $sql );
			if (! $result)	error_message ( sql_error () );
			$stepcount=0 ;
			while ($qd = mysqli_fetch_assoc($result)) {
				$arWorkFlows[$count]['STEPS'][$stepcount] = $qd;
				$arWorkFlows[$count]['STEPS'][$stepcount]['STATUS'] = urlencode($qd['WFS_STATUS']);

				$sql = "select count(*) TOTDOC from KD_WORKFLOWS, KD_DOCUMENT, KD_DOCDEF
						where KDU_DDKEY = DD_KEY
						and WF_DDKEY = DD_KEY
						and WF_KEY = " .  $USERS_WORKFLOW['WF_KEY'] . "
						AND KDU_INDEX" . $USERS_WORKFLOW['WF_STATUS_INDEX_NO'] . " = '" . $qd['WFS_STATUS'] . "'";
				//echo $sql;
				$result2 = mysqli_query ($mysqli, $sql );
				if (! $result2)	error_message ( sql_error () );
				$KD_DOCUMENTS = mysqli_fetch_assoc($result2);
				$arWorkFlows[$count]['STEPS'][$stepcount]['PanelSizeStyle'] = "col-lg-2 col-md-4";
				$arWorkFlows[$count]['STEPS'][$stepcount]['TOTDOC'] = $KD_DOCUMENTS['TOTDOC'];

				if ($KD_DOCUMENTS['TOTDOC'] > 1){
					$arWorkFlows[$count]['STEPS'][$stepcount]['TOTDOCDESC'] = $KD_DOCUMENTS['TOTDOC'] . " Documents";
				} elseif ($KD_DOCUMENTS['TOTDOC'] == 1) {
					$arWorkFlows[$count]['STEPS'][$stepcount]['TOTDOCDESC'] = "1 Document";
				} else {
					$arWorkFlows[$count]['STEPS'][$stepcount]['TOTDOCDESC'] = "No Documents";
				}

				if ($USERS_WORKFLOW['USF_PROCESSOWNER'] == "Y"){
					$arWorkFlows[$count]['STEPS'][$stepcount]['ACTIONDOCCOUNT'] = $arWorkFlows[$count]['STEPS'][$stepcount]['DOCCOUNT'];
				} else {
					$arWorkFlows[$count]['STEPS'][$stepcount]['ACTIONDOCCOUNT'] = 0;
				}


				if ($KD_DOCUMENTS['ACTIONDOCCOUNT'] > 1){
					$arWorkFlows[$count]['STEPS'][$stepcount]['ACTIONDOCDESC'] = $KD_DOCUMENTS['TOTDOC'] . " Documents for you to action";
				} elseif ($KD_DOCUMENTS['ACTIONDOCCOUNT'] == 1) {
					$arWorkFlows[$count]['STEPS'][$stepcount]['ACTIONDOCDESC'] = "1 Document for you to action";
				} else {
					$arWorkFlows[$count]['STEPS'][$stepcount]['ACTIONDOCDESC'] = "No Documents for you to action";
				}
				$arWorkFlows[$count]['STEPS'][$stepcount]['DOCCOUNT'] = $KD_DOCUMENTS['TOTDOC'];


				if ($arWorkFlows[$count]['STEPS'][$stepcount]['ACTIONDOCCOUNT'] > 0  and $qd['WFS_NEXTACTION'] != "Complete"){
					$arWorkFlows[$count]['STEPS'][$stepcount]['PanelStyle'] = "panel panel-red";
				} elseif ($arWorkFlows[$count]['STEPS'][$stepcount]['DOCCOUNT'] > 0 and $qd['WFS_NEXTACTION'] != "Complete") {
					$arWorkFlows[$count]['STEPS'][$stepcount]['PanelStyle'] = "panel panel-yellow";
				} else {
					$arWorkFlows[$count]['STEPS'][$stepcount]['PanelStyle'] = "panel panel-green";
				}


				$stepcount++;
			}
			$count++;
		}

	}

	return $arWorkFlows;

}

// find latest set of notifcations
function getDocMessages($arCtl){

	// get all docs related to workflows
	$mysqli = db_connect ( getDBName () );

	if (count($_SESSION ['USERS_WORKFLOWS']) > 0) {
		$count=0;
		foreach ($_SESSION ['USERS_WORKFLOWS'] as $key => $USERS_WORKFLOW){

			$sql = "select WFL_TYPE, date_format(WFL_DATE,' %H:%i %a %D %b') WFL_DATE, WFL_SUMMARY from KD_WORKFLOWS, KD_DOCUMENT, KD_DOCDEF, KD_WORKFLOWS_LOG
					where KDU_DDKEY = DD_KEY
					and WFL_KDUKEY = KDU_KEY
					and WF_DDKEY = DD_KEY
					and WF_KEY = '" .  $USERS_WORKFLOW['WF_KEY'] . "' order by WFL_KEY DESC LIMIT 10";
			//			echo $sql;
			$result = mysqli_query ($mysqli, $sql );
			if (! $result)	error_message ( sql_error () );
			while ($KD_WORKFLOWS_LOG = mysqli_fetch_assoc($result)) {
				$arDocMessages[$count] = $KD_WORKFLOWS_LOG;

				if ($KD_WORKFLOWS_LOG['WFL_TYPE'] == "ProcessRule"){
					$arDocMessages[$count]['WFL_TYPE'] = "fa fa-tasks fa-fw";
				} elseif ($KD_WORKFLOWS_LOG['WFL_TYPE'] == "DocUpload") {
					$arDocMessages[$count]['WFL_TYPE'] = "fa fa-upload fa-fw";
				} else {
					$arDocMessages[$count]['WFL_TYPE'] = "fa fa-tasks fa-fw";
				}
				//fa fa-envelope fa-fw
				//fa fa-tasks fa-fw
				//fa fa-upload fa-fw
				//fa fa-tasks fa-fw

				$count++;
			}
		}

	}


	return $arDocMessages;

}
