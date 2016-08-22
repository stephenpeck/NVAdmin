<?php


function ShowBankUpload($arCtl,$arSAFE_REQUEST) {

	// set up smarty
	$smarty = getSmarty();

	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	// display using template name provided
	$smarty->display("custom/ShowBankUpload.tpl");

}

function ListBankTransactions($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getDBName());
	$sqlserver = sqlserver_connect(getSQLDBName());
	
	$arInsurers['67'] = "Haven";
	$arInsurers['2'] = "Ageas";
	$arInsurers['68'] = "Dual";
	$arInsurers['28'] = "LV";
	
	// get last bank recon details
	
	
	$sql = "select  * from BANKTRANSACTIONS where BK_KEY is not null ";

	if ($arCtl['BK_LOADDATE'] != ""){
		$sql .= " AND BK_LOADDATE ='" . $arCtl['BK_LOADDATE'] . "'";
	}

	if ($arCtl['BK_TRANDATE_FROM'] != ""){
		$sql .= " AND BK_TRANDATE >='" . $arCtl['BK_TRANDATE_FROM'] . "'";
	}
	
	if ($arCtl['BK_TRANDATE_TO'] != ""){
		$sql .= " AND BK_TRANDATE <='" . $arCtl['BK_TRANDATE_TO'] . "'";
	}

	if ($arCtl['BK_RECONCILED'] != ""){
		$sql .= " AND BK_RECONCILED <='" . $arCtl['BK_RECONCILED'] . "'";
	}
	
	
	$sql .= " order by BK_TRANDATE ASC";
	
	//echo $sql; 
	if ($arCtl['Run'] != ""){
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)	{ echo  sql_error (); }
		$count=0;
		$Balance = 0;
		while ($qd = mysqli_fetch_assoc($result)){
			$arBANKTRANSACTIONS[$count] = $qd;
			$Balance = $Balance + $qd['BK_AMOUNT'];
			$arBANKTRANSACTIONS[$count]['BALANCE'] = $Balance;
				
			// get recon info
			if ($qd['BK_RECONCILED'] == "Y"){
				$sql = "select * from BANKRECON where BR_BKKEY = " . $qd['BK_KEY'];
				$result2 = mysqli_query ($mysqli, $sql );
				if (! $result2)	{ echo  sql_error (); }
				$count2 = 0;
				while ($qd2 = mysqli_fetch_assoc($result2)){
					$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2] = $qd2;
					
					// now decide
					if ($qd2['BR_TYPE'] == "brhist"){
						
						$sql = "select * from icp_brhist where Key@ = '" . $qd2['BR_RECONKEY'] . "'";
						//echo $sql;
						$result3 = sqlsrv_query ($sqlserver,$sql );
						if (! $result3)	{ echo  sql_error (); }
						$qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC);
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Polref'] = $qd3['Polref'];
						
						if (is_object($qd3['Dt_settled'])){
							$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Date'] = $qd3['Dt_settled']->format('Y-m-d');
						} else {
							$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Date'] = "NotSet";
						}
						
						$qd3['Settle_amt'] = $qd3['Settle_amt'] * -1;
						
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Amount'] = number_format((float)$qd3['Settle_amt'], 2, '.', '');
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['PayMethod'] = $qd3['Paymethod'];
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Type'] = $qd3['#Type'];
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Insurer'] = $arInsurers[$qd3['Icno_INSC_VTId']];
						
						
					} elseif ($qd2['BR_TYPE'] == "brcashhist") {
						$sql = "select * from icp_brcashhist where Key@ = '" . $qd2['BR_RECONKEY'] . "'";
						//echo $sql;
						$result3 = sqlsrv_query ($sqlserver,$sql );
						if (! $result3)	{ echo  sql_error (); }
						$qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC);
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Polref'] = $qd3['Polref'];
													
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Amount'] = number_format((float)$qd3['R0_amt'], 2, '.', '');
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['PayMethod'] = $qd3['R0_pm'];
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Type'] = $qd3['R0_ttype'];
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Insurer'] = $arInsurers[$qd3['Insco_INSC_VTId']];
						
						if (is_object($qd3['Dat'])){
							$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Date'] = $qd3['Dat']->format('Y-m-d');
						} else {
							$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Date'] = "NotSet";
						}
						
					}  else  {
						
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Polref'] = "Non OGI";
						$arBANKTRANSACTIONS[$count]['BANKRECON'][$count2]['Type'] = $qd2['BR_TYPE'];
						
					}
					
					$count2++;
					
				}
			}
				
			$count++;
		}
	}

	// now go and get reconcilable transactions
	$arOGIBankTrans = getOGIBankTrans($arCtl,$arSAFE_REQUEST);
	
// 	print_r($arBANKTRANSACTIONS);
	
	
	$arManualList[''] = "";
	$arManualList['Interest'] = "Bank Interest";
	$arManualList['BankCharge'] = "Bank Charges";

	$arYesNoList[''] = "";
	$arYesNoList['Y'] = "Reconciled";
	$arYesNoList['N'] = "Not Reconciled";
	
	
	// set up smarty
	$smarty = getSmarty();
	
	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arManualList',    $arManualList);
	$smarty->assign('arYesNoList',    $arYesNoList);
	$smarty->assign('arBANKTRANSACTIONS',    $arBANKTRANSACTIONS);
	$smarty->assign('arPayments',    $arOGIBankTrans['Payments']);
	$smarty->assign('arSettlements',    $arOGIBankTrans['Settlements']);
	$smarty->assign('SESSION',   $_SESSION);
	
	// display using template name provided
	$smarty->display("custom/ListBankTransactions.tpl");
	
}

function getOGIBankTrans($arCtl,$arSAFE_REQUEST){


	$sqlserver = sqlserver_connect(getSQLDBName());
	
	$arInsurers['67'] = "Haven";
	$arInsurers['2'] = "Ageas";
	$arInsurers['68'] = "Dual";
	$arInsurers['28'] = "LV";
	
	
	
	// now go and get the ledger transactions
	$sql = "select * from icp_brcashhist where branch@ = 0 and R0_ttype is not null";

	// needs 15/03/2016
	
	if ($arCtl['BK_TRANDATE_TO'] != ""){
		$sql .= " and Dat <= '" . DatefromDB($arCtl['BK_TRANDATE_TO'])  . "'";
		
	}
	
	$sql .= " order by Dat asc";
//	echo $sql;
	
	//			echo $sql;
	$result2 = sqlsrv_query ($sqlserver,$sql );
	if (! $result2)	{ echo  sql_error (); }
	$count = 0;
	while ($qd2 = sqlsrv_fetch_array( $result2, SQLSRV_FETCH_ASSOC)){
		$arbrcashhist[$count] = $qd2;
			
			
		if (is_object($qd2['Dat'])){
			$arbrcashhist[$count]['Date'] = $qd2['Dat']->format('Y-m-d');
		} else {
			$arbrcashhist[$count]['Date'] = "NotSet";
		}
		if (is_object($qd2['Ldg_effect_date'])){
			$arbrcashhist[$count]['Ledger_Effective_Date'] = $qd2['Ldg_effect_date']->format('Y-m-d');
		} else {
			$arbrcashhist[$count]['Ledger_Effective_Date'] = "NotSet";
		}
	
		$arbrcashhist[$count]['Key'] = $qd2['Key@'] . "-brcashhist";;
		$arbrcashhist[$count]['RECONKEY'] = $qd2['Key@'];
		$arbrcashhist[$count]['Amount'] = number_format((float)$qd2['R0_amt'], 2, '.', '');
		$arbrcashhist[$count]['PayMethod'] = $qd2['R0_pm'];
		$arbrcashhist[$count]['Type'] = $qd2['R0_ttype'];
		$arbrcashhist[$count]['Insurer'] = $arInsurers[$qd2['Insco_INSC_VTId']];
		
		
		$count++;
	}
	
	
	$sql = "select * from icp_brhist where #Type = 'Settlement' and branch@ = 0";

	if ($arCtl['BK_TRANDATE_TO'] != ""){
		$sql .= " and Dt_settled <= '" . DatefromDB($arCtl['BK_TRANDATE_TO'])  . "'";
		
	}
	
	$sql .= " order by Dt_settled asc";
//	echo $sql;
	$result2 = sqlsrv_query ($sqlserver,$sql );
	if (! $result2)	{ echo  sql_error (); }
	$count=0;
	while ($qd2 = sqlsrv_fetch_array( $result2, SQLSRV_FETCH_ASSOC)){
		$arbrhist[$count] = $qd2;
		$arbrhist[$count]['Type'] = $qd2['#Type'];
	
	
		if (is_object($qd2['Pay_dt'])){
			$arbrhists[$count]['Pay_Date'] = $qd2['Pay_dt']->format('Y-m-d');
		} else {
			$arbrhist[$count]['Pay_Date'] = "NotSet";
		}
		if (is_object($qd2['Dt_settled'])){
			$arbrhist[$count]['Date'] = $qd2['Dt_settled']->format('Y-m-d');
		} else {
			$arbrhist[$count]['Date'] = "NotSet";
		}
	
		$arbrhist[$count]['Pay_amt'] = $arbrhist[$count]['Pay_amt'] * -1;
		$arbrhist[$count]['Settle_amt'] = $arbrhist[$count]['Settle_amt'] * -1;
		$arbrhist[$count]['Key'] = $qd2['Key@'] . "-brhist";
		$arbrhist[$count]['RECONKEY'] = $qd2['Key@'];
		
		$arbrhist[$count]['Amount'] = number_format((float)$arbrhist[$count]['Settle_amt'], 2, '.', '');
		$arbrhist[$count]['PayMethod'] = $qd2['Paymethod'];
		$arbrhist[$count]['Type'] = $arbrhist[$count]['Type'];
		$arbrhist[$count]['Insurer'] = $arInsurers[$qd2['Icno_INSC_VTId']];
		
		$count++;
	}
	
	
	//print_r($arbrhist);
	
	$arReturn['Payments'] = array_merge ($arbrcashhist, $arbrhist);
	$arReturn['Settlements'] = $arbrcashhist;
	
	// sort by date
	usort($arReturn['Payments'], 'compare_date');
	
	
	$mysqli = db_connect(getDBName());
	
	// get last bank recon details
	$sql = "select  * from BANKRECON ";
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	{ echo  sql_error (); }
	$count=0;
	$Balance = 0;
	while ($qd = mysqli_fetch_assoc($result)){
		$arBANKRECON[$qd['BR_RECONKEY']] = "Y";
	}

	foreach ($arReturn['Payments'] as $No => $arPayments){
		if ($arBANKRECON[$arPayments['RECONKEY']] == "Y"){
			// already reconciled remove
			$arReturn['Payments'][$No]['Reconciled'] = "Y";
		}
	}

	// now get unreciled paymenst
	$sql = "select * from BANKPAYMENTS where BP_STATUS = 'New' order by BP_DATE";
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	{ echo  sql_error (); }
	while ($qd = mysqli_fetch_assoc($result)){
		$arPayment['Date'] = $qd['BP_DATE'];
		$arPayment['Key'] = $qd['BP_KEY'] . "-bankpayments";;
		$arPayment['RECONKEY'] = $qd['BP_KEY'];
		$arPayment['Amount'] = number_format($qd['BP_VALUE'], 2, '.', '');
		$arPayment['PayMethod'] = "N\A";
		$arPayment['Type'] = "Office A/C Transfer";
		$arPayment['Insurer'] = "N\A";
		
		$arReturn['Payments'][] = $arPayment;
		
	}
			
	
	return $arReturn;
	
}
function compare_date($a, $b) {

	if ($a['Date'] == $b['Date']) {
		return 0;
	}
	return ($a['Date'] < $b['Date']) ? -1 : 1;
}

function ListBankRecon($arCtl,$arSAFE_REQUEST) {

	$mysqli = db_connect(getDBName());
	$sqlserver = sqlserver_connect(getSQLDBName());

	$arInsurers['67'] = "Haven";
	$arInsurers['2'] = "Ageas";
	$arInsurers['68'] = "Dual";
	$arInsurers['28'] = "LV";
	

	
	$arShowPayment['Interest'] = "N";
	$arShowPayment['Fee'] = "N";
	$arShowPayment['Commission'] = "N";
	
	// get last bank recon details

	
	$arTotal['Interest'] = 0;
	$arTotal['Fee'] = 0;
	$arTotal['Commission'] = 0;
	
	$sql = "select  * from BANKTRANSACTIONS where BK_KEY is not null ";

	if ($arCtl['BK_TRANDATE_FROM'] != ""){
		$sql .= " AND BK_TRANDATE >='" . $arCtl['BK_TRANDATE_FROM'] . "'";
	}

	if ($arCtl['BK_TRANDATE_TO'] != ""){
		$sql .= " AND BK_TRANDATE <='" . $arCtl['BK_TRANDATE_TO'] . "'";
	}

	$sql .= " order by BK_TRANDATE ASC";

//	echo $sql;
	if ($arCtl['Run'] != ""){
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)	{ echo  sql_error (); }
		$count=0;
		while ($qd = mysqli_fetch_assoc($result)){
			$arBANKRECON[$count] = $qd;
			
			$arCtl['TransDate'] = $qd['BK_TRANDATE'] ;
			
			$sql = "select  * from BANKRECON 
					LEFT OUTER JOIN BANKPAYMENTS ON BP_KEY = BR_BPKEY
					where BR_BKKEY = " . $qd['BK_KEY'];
			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	{ echo  sql_error (); }
			$count2=0;
			while ($qd2 = mysqli_fetch_assoc($result2)){
				$arBANKRECON[$count]['RECON'][$count2] = $qd2;
				
				if ($count2 == 0){
					$arBANKRECON[$count]['RECON'][$count2]['BK_KEY'] = $qd['BK_KEY'];
					$arBANKRECON[$count]['RECON'][$count2]['BK_AMOUNT'] = $qd['BK_AMOUNT'];
					$arBANKRECON[$count]['RECON'][$count2]['BK_TRANDATE'] = $qd['BK_TRANDATE'];
				} else {
					$arBANKRECON[$count]['RECON'][$count2]['BK_KEY'] = "";
					$arBANKRECON[$count]['RECON'][$count2]['BK_AMOUNT'] = "";
					$arBANKRECON[$count]['RECON'][$count2]['BK_TRANDATE'] = "";
				}
				// now decide
				if ($qd2['BR_TYPE'] == "brhist"){
				
					$sql = "select * from icp_brhist where Key@ = '" . $qd2['BR_RECONKEY'] . "'";
					//echo $sql;
					$result3 = sqlsrv_query ($sqlserver,$sql );
					if (! $result3)	{ echo  sql_error (); }
					$qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC);
					$arBANKRECON[$count]['RECON'][$count2]['Polref'] = $qd3['Polref'];
				
					if (is_object($qd3['Dt_settled'])){
						$arBANKRECON[$count]['RECON'][$count2]['Date'] = $qd3['Dt_settled']->format('Y-m-d');
					} else {
						$arBANKRECON[$count]['RECON'][$count2]['Date'] = "NotSet";
					}
				
					$qd3['Settle_amt'] = $qd3['Settle_amt'] * -1;
				
					$arBANKRECON[$count]['RECON'][$count2]['Amount'] = number_format((float)$qd3['Settle_amt'], 2, '.', '');
					$arBANKRECON[$count]['RECON'][$count2]['PayMethod'] = $qd3['Paymethod'];
					$arBANKRECON[$count]['RECON'][$count2]['Type'] = $qd3['#Type'];
					$arBANKRECON[$count]['RECON'][$count2]['table'] = "brhist";
					$arBANKRECON[$count]['RECON'][$count2]['Insurer'] = $arInsurers[$qd3['Icno_INSC_VTId']];
					$arBANKRECON[$count]['RECON'][$count2]['LedgerSuffix'] = $qd3['LedgerSuffix@'];
						
					
				} elseif ($qd2['BR_TYPE'] == "brcashhist") {
				
					$sql = "select * from icp_brcashhist where Key@ = '" . $qd2['BR_RECONKEY'] . "'";
					//echo $sql;
					$result3 = sqlsrv_query ($sqlserver,$sql );
					if (! $result3)	{ echo  sql_error (); }
					$qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC);
					$arBANKRECON[$count]['RECON'][$count2]['Polref'] = $qd3['Polref'];
						
					$arBANKRECON[$count]['RECON'][$count2]['Amount'] = number_format((float)$qd3['R0_amt'], 2, '.', '');
					$arBANKRECON[$count]['RECON'][$count2]['PayMethod'] = $qd3['R0_pm'];
					$arBANKRECON[$count]['RECON'][$count2]['table'] = "brcashhist";
					$arBANKRECON[$count]['RECON'][$count2]['Type'] = $qd3['R0_ttype'];
					$arBANKRECON[$count]['RECON'][$count2]['Insurer'] = $arInsurers[$qd3['Insco_INSC_VTId']];
					$arBANKRECON[$count]['RECON'][$count2]['LedgerSuffix'] = $qd3['Ldg_suffix'];
						
					if (is_object($qd3['Dat'])){
						$arBANKRECON[$count]['RECON'][$count2]['Date'] = $qd3['Dat']->format('Y-m-d');
					} else {
						$arBANKRECON[$count]['RECON'][$count2]['Date'] = "NotSet";
					}
				
				
				}  else  {
				
//					print_r($qd2);
					
					$arBANKRECON[$count]['RECON'][$count2]['Polref'] = "Non OGI";
					$arBANKRECON[$count]['RECON'][$count2]['Type'] = $qd2['BR_TYPE'];
					$arBANKRECON[$count]['RECON'][$count2]['Action'] = "Transfer Interest";
					$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "Interest";
					$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] =  $arBANKRECON[$count]['BK_AMOUNT'];
					$arTotal['Interest'] = $arTotal['Interest'] + $arBANKRECON[$count]['BK_AMOUNT'];
					$arShowPayment['Interest'] = "Y";
				
				}

				
//				$arBANKRECON[$count]['RECON'][$count2]['PolicyInfo'] = getPolicyInfo($arCtl,$arBANKRECON[$count]['RECON'][$count2]);
				$arCtl['Polref'] = $qd3['Polref'];
				
				$arBANKRECON[$count]['RECON'][$count2]['PolicyInfo'] = getPolicyInfo2($arCtl,$arBANKRECON[$count]['RECON'][$count2]);
				
// 				print_r($arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']);
				
				if ($qd2['BR_BPKEY'] == 0){
					if ($arBANKRECON[$count]['RECON'][$count2]['Type'] == "Charge"){
						$arBANKRECON[$count]['RECON'][$count2]['Action'] = "Transfer fee";
						$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "Fee";
						$arShowPayment['Fee'] = "Y";
						$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] = $arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['Fee'];
						$arTotal['Fee'] = $arTotal['Fee'] + $arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['Fee'];
						
					} elseif ($arBANKRECON[$count]['RECON'][$count2]['Type'] == "New Business"){
						$arShowPayment['Fee'] = "Y";
						if ($arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['PaidAtDate'] == "Y"){
							if ($arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['Fee'] > 0){
								$arBANKRECON[$count]['RECON'][$count2]['Action'] = "All payments recieved transfer fee";
								$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "Fee";
								$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] = $arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['Fee'];
								$arTotal['Fee'] = $arTotal['Fee'] + $arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['Fee'];
									
							} else {
								$arBANKRECON[$count]['RECON'][$count2]['Action'] = "All payments recieved No Fee";
								$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] = "No Action";
								$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "Fee";
							}
						} else {
							$arBANKRECON[$count]['RECON'][$count2]['Action'] = "Full payment not recieved";
							$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] =  "No Action";;
							$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "Fee";
						}
					} elseif ($arBANKRECON[$count]['RECON'][$count2]['Type'] == "Endorsement"){
						$arShowPayment['Fee'] = "Y";
						if ($arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['PaidAtDate'] == "Y"){
							if ($arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['Fee'] > 0){
								$arBANKRECON[$count]['RECON'][$count2]['Action'] = "All payments recieved transfer fee";
								$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "Fee";
								$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] = $arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['Fee'];
								$arTotal['Fee'] = $arTotal['Fee'] + $arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['Fee'];
									
							} else {
								$arBANKRECON[$count]['RECON'][$count2]['Action'] = "All payments recieved No Fee";
								$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] = "No Action";
								$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "Fee";
							}
						} else {
							$arBANKRECON[$count]['RECON'][$count2]['Action'] = "Full payment not recieved";
							$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] = 0;
							$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "Fee";
						}
					} elseif ($arBANKRECON[$count]['RECON'][$count2]['Type'] == "Settlement"){
						
						$arShowPayment['Commission'] = "Y";
						if ($arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['Commission'] > 0){
							$arBANKRECON[$count]['RECON'][$count2]['Action'] = "Paid Insurer transfer commission";
							$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "Commission";
							// calc commission take anprem and take off settlement
							$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] = $arBANKRECON[$count]['RECON'][$count2]['PolicyInfo']['MATCHED']['Commission'];
							$arTotal['Commission'] = $arTotal['Commission'] + $arBANKRECON[$count]['RECON'][$count2]['ActionAmount'];
							
						} else {
							$arBANKRECON[$count]['RECON'][$count2]['Action'] = "Paid Insurer No Commission";
							$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] = "No Action";
							$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "Commission";
						}
	
					}
				} else {
					if ($qd2['BP_STATUS'] == "New"){
						$arBANKRECON[$count]['RECON'][$count2]['Action'] = "In Payment Batch (" . $qd2['BP_KEY'] . ")";
						$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] = "No Action";
						$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "In Batch";
					} elseif ($qd2['BP_STATUS'] == "Reconciled"){
						$arBANKRECON[$count]['RECON'][$count2]['Action'] = "Payment Batch (" . $qd2['BP_KEY'] . ") Reconciled";
						$arBANKRECON[$count]['RECON'][$count2]['ActionAmount'] = "No Action";
						$arBANKRECON[$count]['RECON'][$count2]['ActionType'] = "In Batch";
					}
						
				}
				
				$count2++;
				
			}
			
			$count++;
		}
	}

	//print_r($arShowPayment);

	// set up smarty
	$smarty = getSmarty();

	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arShowPayment',    $arShowPayment);
	$smarty->assign('arTotal',    $arTotal);
	$smarty->assign('arBANKRECON',    $arBANKRECON);
	$smarty->assign('SESSION',   $_SESSION);

	// display using template name provided
	$smarty->display("custom/ListBankRecon.tpl");

}

function ListBankPayments($arCtl,$arSAFE_REQUEST) {

	$mysqli = db_connect(getDBName());
	$sqlserver = sqlserver_connect(getSQLDBName());

	$arInsurers['67'] = "Haven";
	$arInsurers['2'] = "Ageas";
	$arInsurers['68'] = "Dual";
	$arInsurers['28'] = "LV";

	// get last bank recon details
	if ($arCtl['BP_KEY'] != ""){			
		$sql = "select  * from BANKRECON, BANKTRANSACTIONS, BANKPAYMENTS
				WHERE BP_KEY = BR_BPKEY
				AND BR_BKKEY = BK_KEY
				AND BP_KEY = " . $arCtl['BP_KEY'];
		$result2 = mysqli_query ($mysqli, $sql );
		if (! $result2)	{ echo  sql_error (); }
		$count = 0;
		while ($qd2 = mysqli_fetch_assoc($result2)){
			$arBANKRECON[$count] = $qd2;
	
			// now decide
			if ($qd2['BR_TYPE'] == "brhist"){
	
				$sql = "select * from icp_brhist where Key@ = '" . $qd2['BR_RECONKEY'] . "'";
				//echo $sql;
				$result3 = sqlsrv_query ($sqlserver,$sql );
				if (! $result3)	{ echo  sql_error (); }
				$qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC);
				$arBANKRECON[$count]['Polref'] = $qd3['Polref'];
	
				if (is_object($qd3['Dt_settled'])){
					$arBANKRECON[$count]['Date'] = $qd3['Dt_settled']->format('Y-m-d');
				} else {
					$arBANKRECON[$count]['Date'] = "NotSet";
				}
	
				$qd3['Settle_amt'] = $qd3['Settle_amt'] * -1;
	
				$arBANKRECON[$count]['Amount'] = number_format((float)$qd3['Settle_amt'], 2, '.', '');
				$arBANKRECON[$count]['PayMethod'] = $qd3['Paymethod'];
				$arBANKRECON[$count]['Type'] = $qd3['#Type'];
				$arBANKRECON[$count]['table'] = "brhist";
				$arBANKRECON[$count]['Insurer'] = $arInsurers[$qd3['Icno_INSC_VTId']];
				$arBANKRECON[$count]['LedgerSuffix'] = $qd3['LedgerSuffix@'];
	
					
			} elseif ($qd2['BR_TYPE'] == "brcashhist") {
	
				$sql = "select * from icp_brcashhist where Key@ = '" . $qd2['BR_RECONKEY'] . "'";
				//echo $sql;
				$result3 = sqlsrv_query ($sqlserver,$sql );
				if (! $result3)	{ echo  sql_error (); }
				$qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC);
				$arBANKRECON[$count]['Polref'] = $qd3['Polref'];
	
				$arBANKRECON[$count]['Amount'] = number_format((float)$qd3['R0_amt'], 2, '.', '');
				$arBANKRECON[$count]['PayMethod'] = $qd3['R0_pm'];
				$arBANKRECON[$count]['table'] = "brcashhist";
				$arBANKRECON[$count]['Type'] = $qd3['R0_ttype'];
				$arBANKRECON[$count]['Insurer'] = $arInsurers[$qd3['Insco_INSC_VTId']];
				$arBANKRECON[$count]['LedgerSuffix'] = $qd3['Ldg_suffix'];
	
				if (is_object($qd3['Dat'])){
					$arBANKRECON[$count]['Date'] = $qd3['Dat']->format('Y-m-d');
				} else {
					$arBANKRECON[$count]['Date'] = "NotSet";
				}
	
			}  else  {
	
				$arBANKRECON[$count]['Polref'] = "Non OGI";
				$arBANKRECON[$count]['Type'] = $qd2['BR_TYPE'];
	
			}
	
					//				$arBANKRECON[$count]['RECON'][$count2]['PolicyInfo'] = getPolicyInfo($arCtl,$arBANKRECON[$count]['RECON'][$count2]);
			$arCtl['Polref'] = $qd3['Polref'];
			$arBANKRECON[$count]['PolicyInfo'] = getPolicyInfo2($arCtl,$arBANKRECON[$count]);

			$arTotal['Payment'] = $arTotal['Payment']  + $qd2['BR_PAYMENTVALUE'];
				
			$count++;
		}
	}
	//print_r($arBANKRECON);

	$sql = "select * from BANKPAYMENTS order by BP_KEY";
	$result2 = mysqli_query ($mysqli, $sql );
	if (! $result2)	{ echo  sql_error (); }
	$arPayments[] = "Select Payment";
	while ($qd2 = mysqli_fetch_assoc($result2)){
		$arPayments[$qd2['BP_KEY']] = $qd2['BP_KEY'] . " " . $qd2['BP_DATE'] . " " . $qd2['BP_TYPE'] . " " . $qd2['BP_VALUE'] . " " . $qd2['BP_STATUS'];
	}
	
	// set up smarty
	$smarty = getSmarty();

	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arTotal',    $arTotal);
	$smarty->assign('arPayments',    $arPayments);
	$smarty->assign('arBANKRECON',    $arBANKRECON);
	$smarty->assign('SESSION',   $_SESSION);

	// display using template name provided
	$smarty->display("custom/ListBankPayments.tpl");

}


function getPolicyInfo2($arCtl,$arBANKRECON){

	$sqlserver = sqlserver_connect(getSQLDBName());

	$ledgersql = "select * from icp_brcledger where Polref = '" . $arCtl['Polref'] . "' order by Dt_raised";
	$result3 = sqlsrv_query ($sqlserver,$ledgersql );
	if (! $result3)	{ echo  sql_error (); }
	while($qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC)){
		
		$Date = $qd3['Dt_raised']->format('Y-m-d');
		
		$arPolicyInfo['TRANS'][$Date][] = $qd3;
		
	}

	
	foreach ($arPolicyInfo['TRANS'] as $Date => $arRecords){

		$SuffixMatched = false;
		
		$arPolicyInfo['SUMMARY'][$Date]['Settled'] = "Y";
		$arPolicyInfo['SUMMARY'][$Date]['GWP'] = 0;
		$arPolicyInfo['SUMMARY'][$Date]['Payment'] = 0;
		$arPolicyInfo['SUMMARY'][$Date]['Paid'] = "N";
		$arPolicyInfo['SUMMARY'][$Date]['Fee'] = 0;
		$arPolicyInfo['SUMMARY'][$Date]['Commission'] = 0;
		$arPolicyInfo['SUMMARY'][$Date]['MTA'] = 0;
		$arPolicyInfo['SUMMARY'][$Date]['NB'] = 0;
		$arPolicyInfo['SUMMARY'][$Date]['InsurerPremium'] = 0;
		
		
		foreach ($arRecords as $RecNo => $arbrcledger){
		
// 			print_r($arbrcledger);
			$arPolicyInfo['SUMMARY'][$Date]['GWP'] = $arPolicyInfo['SUMMARY'][$Date]['GWP'] + $arbrcledger['Orig_debt'];
			$arPolicyInfo['SUMMARY'][$Date]['Payment'] = $arPolicyInfo['SUMMARY'][$Date]['Payment'] +  $arbrcledger['Poac'];
			if ($arbrcledger['Trantype'] == "Charge"){
				$arPolicyInfo['SUMMARY'][$Date]['Fee'] = $arPolicyInfo['SUMMARY'][$Date]['Fee'] + $arbrcledger['Orig_debt'];
			}
			if ($arbrcledger['Trantype'] == "New Business"){
				$arPolicyInfo['SUMMARY'][$Date]['NB'] = $arPolicyInfo['SUMMARY'][$Date]['NB'] + $arbrcledger['Orig_debt'];
				$arPolicyInfo['SUMMARY'][$Date]['InsurerPremium'] = $arPolicyInfo['SUMMARY'][$Date]['NB'];
			}
			if ($arbrcledger['Trantype'] == "Endorsement"){
				$arPolicyInfo['SUMMARY'][$Date]['MTA'] = $arPolicyInfo['SUMMARY'][$Date]['MTA'] + $arbrcledger['Orig_debt'];
				$arPolicyInfo['SUMMARY'][$Date]['InsurerPremium'] = $arPolicyInfo['SUMMARY'][$Date]['MTA'];
			}
			$arPolicyInfo['SUMMARY'][$Date]['Commission'] = $arPolicyInfo['SUMMARY'][$Date]['Commission'] + $arbrcledger['Comm_amt'];
			
			if ($arPolicyInfo['SUMMARY'][$Date]['GWP'] = $arPolicyInfo['SUMMARY'][$Date]['Payment']){
				$arPolicyInfo['SUMMARY'][$Date]['Paid'] = "Y";
			} 
	
			if (!is_object($arbrcledger['Dt_settled'])){
				$arPolicyInfo['SUMMARY'][$Date]['Settled'] = "N";
			} 
			
			if ($arbrcledger['Suffix@'] == $arBANKRECON['LedgerSuffix']){
				$SuffixMatched = true;
			}
			
		}
		

		//$arCtl['TransDate']
		if ($arCtl['TransDate'] != ""){

			$arPolicyInfo['SUMMARY'][$Date]['SettledAtDate'] = "Y";
			$arPolicyInfo['SUMMARY'][$Date]['PaymentAtDate'] = 0;
			$arPolicyInfo['SUMMARY'][$Date]['PaidAtDate'] = "N";
		
			
			// find out how much recieved
			$ledgersql = "select * from icp_brcashhist where Polref = '" . $arCtl['Polref']  . "'
			and Dat <= '" . DatefromDB($arCtl['TransDate']) . "'
			and R0_ttype = '" . $arBANKRECON['Type'] . "'
			and Ldg_suffix = '" . $arBANKRECON['LedgerSuffix'] . "'";
			
			
			$manualpclsql = "select * from icp_brhist where Polref = '" . $arCtl['Polref']  . "'
			and Pay_dt <= '" . DatefromDB($arCtl['TransDate']) . "'
			and #Type = 'Payment'
			and LedgerSuffix@ = '" . $arBANKRECON['LedgerSuffix'] . "'";
			
			
			// now get policy details
			//echo $ledgersql;
			$result3 = sqlsrv_query ($sqlserver,$ledgersql );
			if (! $result3)	{ echo  sql_error (); }
			$arPolicyInfo['SUMMARY'][$Date]['PaymentAtDate'] = 0;
			while($qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC)) {
				//print_r($qd3);
				$arPolicyInfo['SUMMARY'][$Date]['PaymentAtDate'] = 	$arPolicyInfo['SUMMARY'][$Date]['PaymentAtDate'] + $qd3['R0_amt'];
			}
		
			if (round($arPolicyInfo['SUMMARY'][$Date]['PaymentAtDate'],2) == round($arPolicyInfo['SUMMARY'][$Date]['GWP'],2)){
				$arPolicyInfo['SUMMARY'][$Date]['PaidAtDate'] = "Y";
			} else {
				$arPolicyInfo['SUMMARY'][$Date]['PaidAtDate'] = "N";
			}
		
		
			if ($arPolicyInfo['SUMMARY'][$Date]['PaymentAtDate'] == "0"){
					//echo $manualpclsql;
				$result3 = sqlsrv_query ($sqlserver,$manualpclsql );
				if (! $result3)	{ echo  sql_error (); }
				$arPolicyInfo['PaymentAtDate'] = 0;
				while($qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC)) {
					//print_r($qd3);
					$arPolicyInfo['SUMMARY'][$Date]['PaymentAtDate'] = $arPolicyInfo['SUMMARY'][$Date]['PaymentAtDate'] + $qd3['Pay_amt'];
				}
				if (round($arPolicyInfo['PaymentAtDate'],2) == round($arPolicyInfo['SUMMARY'][$Date]['GWP'],2)){
					$arPolicyInfo['SUMMARY'][$Date]['PaidAtDate'] = "Y";
					$arPolicyInfo['SUMMARY'][$Date]['PaymentAtDate'] = "Paid";
				} else {
					$arPolicyInfo['SUMMARY'][$Date]['PaidAtDate'] = "N";
				}
			
			}
		}

		if ($SuffixMatched){
			$arPolicyInfo['MATCHED'] = $arPolicyInfo['SUMMARY'][$Date];
		}
		
		
	}

	unset($arPolicyInfo['TRANS']);
	
	return $arPolicyInfo;
}


function UpdBankRecon($arCtl,$arSAFE_REQUEST) {
	
 	//print_r($arSAFE_REQUEST);

	$BANKTRANSACTIONS = $arSAFE_REQUEST['BANKTRANSACTIONS'];
	$BANKRECON = $arSAFE_REQUEST['BANKRECON'];

	$mysqli = db_connect(getDBName());
	
	// first delete all recons for the bk_key
	$sql = "DELETE from BANKRECON where BR_BKKEY = " . $BANKTRANSACTIONS['BK_KEY'];
	//echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	{ echo  sql_error (); }

	
	// now decide if OGOI or none
	if ($BANKRECON['BR_TYPE'] != ""){
		$sql = "insert into BANKRECON (BR_BKKEY, BR_TRANDATE, BR_TYPE) values (" . $BANKTRANSACTIONS['BK_KEY'] . ",now(),'" . $BANKRECON['BR_TYPE']. "')";
		echo $sql;
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)	{ echo  sql_error (); }
			
	} else {
		// add in new ones
		foreach ($BANKRECON as $No => $arDetails){
			$arFields = explode("-",$arDetails['OGI']);
			$sql = "insert into BANKRECON (BR_BKKEY, BR_TRANDATE, BR_RECONKEY, BR_TYPE) values (" . $BANKTRANSACTIONS['BK_KEY'] . ",now(),'" . $arFields[0] . "','" . $arFields['1'] . "')"; 
			//echo $sql;
			$result = mysqli_query ($mysqli, $sql );
			if (! $result)	{ echo  sql_error (); }
		}
	}
	
	// update bk_key as reconciled
	$sql = "update BANKTRANSACTIONS set BK_RECONCILED = 'Y' where BK_KEY = " . $BANKTRANSACTIONS['BK_KEY'];
	//echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	{ echo  sql_error (); }
	
	
// 	exit();
	header("location:" . getAdminCommand());
	
}


function UpdLoadBankTransactions($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getDBName());
	
	$csv = array();
	
	// check there are no errors
	if($_FILES['banktrans']['error'] == 0){
		$name = $_FILES['banktrans']['name'];
		$ext = strtolower(end(explode('.', $_FILES['banktrans']['name'])));
		$type = $_FILES['banktrans']['type'];
		$tmpName = $_FILES['banktrans']['tmp_name'];
	
		// check the file is a csv
		if($ext === 'csv'){
			if(($handle = fopen($tmpName, 'r')) !== FALSE) {
				// necessary if a large csv file
				set_time_limit(0);
	
				$row = 0;
	
				while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
					// number of fields in the csv
					$col_count = count($data);

					if ($data[0] == "Number"){
						// head dont import
					} elseif ($data[1] != "") {
						// do load
						$BANKTRANSACTIONS['BK_LOADDATE'] = date('Y-m-d H:i');
						$BANKTRANSACTIONS['BK_TRANDATE'] = mysqli_escape_string($mysqli,DatetoDB($data[1]));
						$BANKTRANSACTIONS['BK_USKEY'] = $_SESSION['US_KEY'];
						$BANKTRANSACTIONS['BK_ACCOUNT'] = mysqli_escape_string($mysqli,$data[2]);
						$BANKTRANSACTIONS['BK_AMOUNT'] = mysqli_escape_string($mysqli,$data[3]);
						$BANKTRANSACTIONS['BK_SUBCATEGORY'] = mysqli_escape_string($mysqli,$data[4]);
						$BANKTRANSACTIONS['BK_MEMO'] = mysqli_escape_string($mysqli,$data[5]);
						
						// now insert
						$sql = "INSERT INTO BANKTRANSACTIONS ";
						$fields = "(";
						$values = " values (";
						$count = 0;
						foreach ($BANKTRANSACTIONS as $field => $value){
							if (substr($field,0,3) != "CTL" ){
								if ($count == 0) {
									$fields .= "" . $field . "";
									$values .= "'" . $value . "'";
								} else {
									$fields .= "," . $field . "";
									$values .= ",'" . $value . "'";
								}
								$count++;
							}
						}
						$fields .= ")";
						$values .= ")";
						$sql = $sql . $fields . " ". $values;
						
						// now run
						//echo $sql. "<BR>\n";
						$result = mysqli_query ($mysqli, $sql );
						if (! $result)	{ echo  sql_error (); }
						
						
					}
					
					// inc the row
					$row++;
				}
				fclose($handle);
			}
		}
	}
	
	$_SESSION['arCtl']['BK_LOADDATE'] = $BANKTRANSACTIONS['BK_LOADDATE'] ;
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
}

function UpdBankTransactions($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getAdminDBName());

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
				WHERE TO_KEY = '" . $TOPS['TO_KEY'];
		
	} else {
		
		$sql = "INSERT INTO TOPS (
				TO_ACKEY,
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
				TO_INSTRUCTOR_TRKEY_2',
				TO_DURATION',
				TO_STARTDATE',
				TO_ENDDATE',
				TO_TESTDATE',
				TO_EXAMINER_TRKEY',
				TO_LIFTHEIGHT,
				TO_IDREQUIREMENT
		) values (
				'" . $TOPS['TO_ACKEY'] . "',
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

	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
}

	
function UpdBankPayment($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getDBName());

	//print_r($arSAFE_REQUEST);
	$BANKPAYMENTS = $arSAFE_REQUEST['BANKPAYMENTS'];
	$BANKRECON = $arSAFE_REQUEST['BANKRECON'];
	
	
	// create payment rcord
	$sql = "INSERT INTO BANKPAYMENTS (
				BP_DATE,
				BP_TYPE,
				BP_FROMDATE,
				BP_TODATE
		) values (
				now(),
				'" . $BANKPAYMENTS['BP_TYPE'] . "',
				'" . $BANKPAYMENTS['BP_FROMDATE'] . "',
				'" . $BANKPAYMENTS['BP_TODATE'] . "'
				)";
	
	//echo $sql . "<BR>";
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	
	$BANKPAYMENTS['BP_KEY'] = mysqli_insert_id($mysqli);
	
	// update bank recon
	
	$BANKPAYMENTS['BP_VALUE'] = 0;
	foreach ($BANKRECON as $Key => $arBANKRECON){
		
		if ($arBANKRECON['ACTIONTYPE'] == $BANKPAYMENTS['BP_TYPE']){
			if ($arBANKRECON['BR_PAYMENTVALUE'] == "No Action"){
				$arBANKRECON['BR_PAYMENTVALUE'] = 0;
			}
			
			$sql = "update BANKRECON set BR_PAYMENTVALUE = '" . $arBANKRECON['BR_PAYMENTVALUE'] . "', BR_PAYMENTACTION = '" . $arBANKRECON['BR_PAYMENTACTION'] . "', BR_BPKEY =  " . $BANKPAYMENTS['BP_KEY'] . " where BR_KEY = " . $arBANKRECON['BR_KEY'];
//			echo $sql . "<BR>";
			$result2 = mysqli_query ($mysqli, $sql );
			if (!$result2)	{ echo  sql_error (); }
			
			$BANKPAYMENTS['BP_VALUE'] = $BANKPAYMENTS['BP_VALUE'] + $arBANKRECON['BR_PAYMENTVALUE'];
			
		}		
	}

	if ($BANKPAYMENTS['BP_VALUE'] == 0){
		$BANKPAYMENTS['BP_STATUS'] = "Reconciled";
	} else {
		$BANKPAYMENTS['BP_STATUS'] = "New";
	}
	
	$sql = "UPDATE BANKPAYMENTS SET BP_VALUE = '" . $BANKPAYMENTS['BP_VALUE'] . "', BP_STATUS = '" . $BANKPAYMENTS['BP_STATUS'] . "' WHERE BP_KEY = " . $BANKPAYMENTS['BP_KEY'];
// 	echo $sql;
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	
	
//  	exit();
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
}


function getPolicyInfo($arCtl,$arBankRecon){

	$sqlserver = sqlserver_connect(getSQLDBName());

	$ledgersql = "select * from icp_brcledger where Polref = '" . $arBankRecon['Polref']  . "' and Suffix = '" . $arBankRecon['LedgerSuffix'] . "'";
	$result3 = sqlsrv_query ($sqlserver,$ledgersql );
	if (! $result3)	{ echo  sql_error (); }
	$qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC);
	if (is_object($qd3['Ledger_dt'])){
		$arPolicyInfo['LedgerDate'] = $qd3['Ledger_dt']->format('Y-m-d');
	} else {
		$arPolicyInfo['LedgerDate'] = "NotSet";
	}
	if (is_object($qd3['Cashbook_dt'])){
		$arPolicyInfo['CashbookDate'] = $qd3['Cashbook_dt']->format('M');
	} else {
		$arPolicyInfo['CashbookDate'] = "NotSet";
	}

	$ledgersql = "select * from icp_brcledger where Polref = '" . $arBankRecon['Polref']  . "' and Ledger_dt = '" . DatefromDB($arPolicyInfo['LedgerDate']) . "'";

	// now get policy details
	//echo $ledgersql;
	$result3 = sqlsrv_query ($sqlserver,$ledgersql );
	if (! $result3)	{ echo  sql_error (); }
	$arPolicyInfo['Fee'] = 0;
	$arPolicyInfo['NB'] = 0;
	$arPolicyInfo['Endorsement'] = 0;
	$arPolicyInfo['InsurerPremium'] = 0;
	while($qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC)) {
			
		//print_r($qd3);
		if($qd3['Trantype'] == "Charge"){
			$arPolicyInfo['Fee'] = $arPolicyInfo['Fee'] + $qd3['Orig_debt'];
		}
		if($qd3['Trantype'] == "New Business" or $qd3['Trantype'] == "Adjustment"){
			$arPolicyInfo['NB'] = $arPolicyInfo['NB'] + $qd3['Orig_debt'];
			$arPolicyInfo['InsurerPremium']  = $arPolicyInfo['InsurerPremium'] + $qd3['Orig_debt'];
				
			//	echo $qd3['Trantype'] . " " . $arPolicyInfo['NB'] . " " . $qd3['Orig_debt'] . "<BR>";
		}

		if($qd3['Trantype'] == "Endorsement"){
			$arPolicyInfo['Endorsement'] = $arPolicyInfo['Endorsement'] + $qd3['Orig_debt'];
			$arPolicyInfo['InsurerPremium']  = $arPolicyInfo['InsurerPremium']  + $qd3['Orig_debt'];
		}

	}


	if ($arBankRecon['Type'] == "New Business"){
		$Total = $arPolicyInfo['NB'] + $arPolicyInfo['Fee'];
	} else {
		$Total = $arPolicyInfo['Endorsement'] + $arPolicyInfo['Fee'];
	}

	// 	echo $arBankRecon['Type'] . $arPolicyInfo['InsurerPremium'] . "HHH";

	// now get policy details
	$sql = "select * from icp_brpolicy where RefNo = '" . $arBankRecon['Polref']  . "'";
	//	echo $sql;
	$result3 = sqlsrv_query ($sqlserver,$sql );
	if (! $result3)	{ echo  sql_error (); }
	$qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC);
	//	print_r($qd3);
	$arPolicyInfo['AnnualPremium'] = $qd3['Anprem'];
	if($qd3['Finance_provider_B006_VTId'] == "1"){
		$arPolicyInfo['Finance'] = "Y";
	} else {
		$arPolicyInfo['Finance'] = "N";
	}

	// find out how much recieved
	$ledgersql = "select * from icp_brcashhist where Polref = '" . $arBankRecon['Polref']  . "'
			and Dat <= '" . DatefromDB($arBankRecon['Date']) . "'
			and R0_ttype = '" . $arBankRecon['Type'] . "'
			and Ldg_suffix = '" . $arBankRecon['LedgerSuffix'] . "'";


	$ledgersql2 = "select * from icp_brcashhist where Polref = '" . $arBankRecon['Polref']  . "'
			and R0_ttype = '" . $arBankRecon['Type'] . "'
			and Ldg_suffix = '" . $arBankRecon['LedgerSuffix'] . "'";

	$manualpclsql = "select * from icp_brhist where Polref = '" . $arBankRecon['Polref']  . "'
			and Pay_dt <= '" . DatefromDB($arBankRecon['Date']) . "'
			and #Type = 'Payment'
			and LedgerSuffix@ = '" . $arBankRecon['LedgerSuffix'] . "'";
	$manualpclsql2 = "select * from icp_brhist where Polref = '" . $arBankRecon['Polref']  . "'
			and #Type = 'Payment'
			and LedgerSuffix@ = '" . $arBankRecon['LedgerSuffix'] . "'";


	// now get policy details
	//echo $ledgersql;
	if ($arBankRecon['Type'] != "Settlement"){
		$result3 = sqlsrv_query ($sqlserver,$ledgersql );
		if (! $result3)	{ echo  sql_error (); }
		$arPolicyInfo['PaymentRecieved'] = 0;
		while($qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC)) {
			//print_r($qd3);
			$arPolicyInfo['PaymentRecieved'] = 	$arPolicyInfo['PaymentRecieved'] + $qd3['R0_amt'];
		}

		if (round($arPolicyInfo['PaymentRecieved'],2) == round($Total,2)){
			$arPolicyInfo['Paid'] = "Y";
			$arPolicyInfo['PaymentRecieved'] = "Paid";
		} else {
			$arPolicyInfo['Paid'] = "N";
		}

		$result3 = sqlsrv_query ($sqlserver,$ledgersql2 );
		if (! $result3)	{ echo  sql_error (); }
		$arPolicyInfo['TotalPaymentRecieved'] = 0;
		while($qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC)) {
			//print_r($qd3);
			$arPolicyInfo['TotalPaymentRecieved'] = 	$arPolicyInfo['TotalPaymentRecieved'] + $qd3['R0_amt'];
		}
		if (round($arPolicyInfo['TotalPaymentRecieved'],2) == round($Total,2)){
			$arPolicyInfo['NowPaid'] = "Y";
		} else {
			$arPolicyInfo['NowPaid'] = "N";
		}

		if ($arPolicyInfo['Paid'] == "N"){
			//echo $manualpclsql;
			$result3 = sqlsrv_query ($sqlserver,$manualpclsql );
			if (! $result3)	{ echo  sql_error (); }
			$arPolicyInfo['PaymentRecieved2'] = 0;
			while($qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC)) {
				//print_r($qd3);
				$arPolicyInfo['PaymentRecieved2'] = $arPolicyInfo['PaymentRecieved2'] + $qd3['Pay_amt'];
			}
			if (round($arPolicyInfo['PaymentRecieved'],2) == round($Total,2)){
				$arPolicyInfo['Paid'] = "Y";
				$arPolicyInfo['PaymentRecieved'] = "Paid";
			} else {
				$arPolicyInfo['Paid'] = "N";
			}

			$result3 = sqlsrv_query ($sqlserver,$manualpclsql2 );
			if (! $result3)	{ echo  sql_error (); }
			$arPolicyInfo['TotalPaymentRecieved'] = 0;
			while($qd3 = sqlsrv_fetch_array( $result3, SQLSRV_FETCH_ASSOC)) {
				//print_r($qd3);
				$arPolicyInfo['TotalPaymentRecieved'] = 	$arPolicyInfo['TotalPaymentRecieved'] + $qd3['Pay_amt'];
				//echo $arBankRecon['Polref']  . " " . $arPolicyInfo['NB'] , " " , $arPolicyInfo['TotalPaymentRecieved']  . " " . $qd3['Pay_amt'] . "<BR>";
			}
				
			if (round($arPolicyInfo['TotalPaymentRecieved'],2) == round($arPolicyInfo['NB'],2)){
				$arPolicyInfo['NowPaid'] = "Y";
			} else {
				$arPolicyInfo['NowPaid'] = "N";
			}

				
		}



	} else {
		$arPolicyInfo['PaymentRecieved'] = "N/A";
		$arPolicyInfo['Paid'] = "N/A";
	}

	return $arPolicyInfo;
}

?>