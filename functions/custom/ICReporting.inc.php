<?php


function ListICPolicies($arCtl,$arSAFE_REQUEST) {
	
	$sqlserver = sqlserver_connect(getSQLDBName());

		$sql = "select * from icp_brpolicy where branch@ = 0  ";

		if ($arCtl['term_code'] != ""){
			if ($arCtl['term_code'] == "XX"){
				$sql .= " AND term_code is null ";
			} else {
				$sql .= " AND term_code ='" . $arCtl['term_code'] . "'";
			}
		}
		
		$sql .= "order by IDat";
		
	echo $sql; 
		$result = sqlsrv_query ($sqlserver,$sql );
		if (! $result)	{ echo  sql_error (); }
		$count=0;
		while ($qd = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC)){
			$arPolicy[$count] = $qd;
			$arPolicy[$count]['Exec'] = $qd['#Exec'];
			$arPolicy[$count]['GWP'] = $qd['Orig_debt#'];
				
			if (is_object($qd['Rdat'])){
				$arPolicy[$count]['RDate'] = $qd['Rdat']->format('Y-m-d');
			} else {
				$arPolicy[$count]['RDate'] = "NotSet";
			}
			if (is_object($qd['Idat'])){
				$arPolicy[$count]['IDate'] = $qd['Idat']->format('Y-m-d');
			} else {
				$arPolicy[$count]['IDate'] = "NotSet";
			}
			if (is_object($qd['Term_date'])){
				$arPolicy[$count]['TermDate'] = $qd['Term_date']->format('Y-m-d');
			} else {
				$arPolicy[$count]['TermDate'] = "NotSet";
			}
			// 			print_r($qd['Rdat']);
			// now go and get the ledger transactions
			$sql = "select * from icp_brcledger where Polref = '" . $qd['Refno'] . "' and branch@ = 0 order by suffix";
//			echo $sql;
 			$result2 = sqlsrv_query ($sqlserver,$sql );
			if (! $result2)	{ echo  sql_error (); }
			$count2=0;
			$FeeTotal = 0;
			while ($qd2 = sqlsrv_fetch_array( $result2, SQLSRV_FETCH_ASSOC)){
				$arPolicy[$count]['brcledger'][$count2] = $qd2;
				
				
				if (is_object($qd2['Ledger_dt'])){
					$arPolicy[$count]['brcledger'][$count2]['Ledger_Date'] = $qd2['Ledger_dt']->format('Y-m-d');
				} else {
					$arPolicy[$count]['brcledger'][$count2]['Ledger_Date'] = "NotSet";
				}
				if (is_object($qd2['Dt_settled'])){
					$arPolicy[$count]['brcledger'][$count2]['Date_Settled'] = $qd2['Dt_settled']->format('Y-m-d');
				} else {
					$arPolicy[$count]['brcledger'][$count2]['Date_Settled'] = "NotSet";
				}
				if (is_object($qd2['Dt_raised'])){
					$arPolicy[$count]['brcledger'][$count2]['Date_Raised'] = $qd2['Dt_raised']->format('Y-m-d');
				} else {
					$arPolicy[$count]['brcledger'][$count2]['Date_Raised'] = "NotSet";
				}
				
				if ($qd2['Trantype'] == "Charge" and $arPolicy[$count]['brcledger'][$count2]['Ledger_Date'] == $arPolicy[$count]['IDate']){
					$FeeTotal = $FeeTotal + $qd2['Orig_debt'];
				} 
				
				$count2++;
			}
			
			// now go and get the ledger transactions
			$sql = "select * from icp_brcashhist where Polref = '" . $qd['Refno'] . "'  and branch@ = 0";
			//			echo $sql;
			$result2 = sqlsrv_query ($sqlserver,$sql );
			if (! $result2)	{ echo  sql_error (); }
			$count2=0;
			while ($qd2 = sqlsrv_fetch_array( $result2, SQLSRV_FETCH_ASSOC)){
				$arPolicy[$count]['brcashhist'][$count2] = $qd2;
			
			
				if (is_object($qd2['Dat'])){
					$arPolicy[$count]['brcashhist'][$count2]['Date'] = $qd2['Dat']->format('Y-m-d');
				} else {
					$arPolicy[$count]['brcashhist'][$count2]['Date'] = "NotSet";
				}
				if (is_object($qd2['Ldg_effect_date'])){
					$arPolicy[$count]['brcashhist'][$count2]['Ledger_Effective_Date'] = $qd2['Ldg_effect_date']->format('Y-m-d');
				} else {
					$arPolicy[$count]['brcashhist'][$count2]['Ledger_Effective_Date'] = "NotSet";
				}

			
				$count2++;
			}
			
			// now go and get the ledger transactions
			$sql = "select * from icp_brhist where Polref = '" . $qd['Refno'] . "'  and branch@ = 0";
			//			echo $sql;
			$result2 = sqlsrv_query ($sqlserver,$sql );
			if (! $result2)	{ echo  sql_error (); }
			$count2=0;
			$SettleTotal = 0;
			$PaymentTotal = 0;
			while ($qd2 = sqlsrv_fetch_array( $result2, SQLSRV_FETCH_ASSOC)){
				$arPolicy[$count]['brhist'][$count2] = $qd2;
				$arPolicy[$count]['brhist'][$count2]['Type'] = $qd2['#Type'];
				
					
				if (is_object($qd2['Pay_dt'])){
					$arPolicy[$count]['brhist'][$count2]['Pay_Date'] = $qd2['Pay_dt']->format('Y-m-d');
				} else {
					$arPolicy[$count]['brhist'][$count2]['Pay_Date'] = "NotSet";
				}
				if (is_object($qd2['Dt_settled'])){
					$arPolicy[$count]['brhist'][$count2]['Settle_Date'] = $qd2['Dt_settled']->format('Y-m-d');
				} else {
					$arPolicy[$count]['brhist'][$count2]['Settle_Date'] = "NotSet";
				}
			
				if ($qd2['#Type'] == "Payment"){
					$PaymentTotal = $PaymentTotal + $qd2['Pay_amt'];
				} else if ($qd2['#Type'] == "Settlement") {
					$SettleTotal = $SettleTotal + $qd2['Settle_amt'];
				}
						
					
				$count2++;
			}
			
			$arPolicy[$count]['FeeTotal'] = $FeeTotal;
			$arPolicy[$count]['PaymentReceived'] = $PaymentTotal;
			$arPolicy[$count]['SettlementMade'] = $SettleTotal;
				
			$count++;
		}

	
		
	$arTermCode[''] = "All";	
	$arTermCode['XX'] = "No Term Code";	
	$arTermCode['Canc-led'] = "Canc-led";	
	$arTermCode['NonRnwbl'] = "NonRnwbl";
	$arTermCode['NTU'] = "NTU";
	
 	//print_r($arPolicy);
	
	// set up smarty
	$smarty = getSmarty();
	
	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arTermCodeList',    $arTermCode);
	$smarty->assign('arPolicy',    $arPolicy);
	$smarty->assign('SESSION',   $_SESSION);
	
	// display using template name provided
	$smarty->display("custom/ListICPolicy.tpl");
	
}
function ListICPayments($arCtl,$arSAFE_REQUEST) {

	$sqlserver = sqlserver_connect(getSQLDBName());

	// now go and get the ledger transactions
	$sql = "select * from icp_brhist where branch@ = 0 and Dt_settled > '2016-02-01' order by Dt_settled asc, Paymethod";
	$sql = "select * from icp_brhist where branch@ = 0 order by Dt_settled asc";
	if ($arCtl['Dt_settled_from'] != ""){
		$sql .= " and Dt_settled >- '" . $arCtl['Dt_settled_from']  . "'";
	}
	if ($arCtl['Dt_settled_to'] != ""){
		$sql .= " and Dt_settled <= '" . $arCtl['Dt_settled_from']  . "'";
	}
	
	$sql .= "order by Dt_settled asc";
	echo $sql;
	$result2 = sqlsrv_query ($sqlserver,$sql );
	if (! $result2)	{ echo  sql_error (); }
	$count=0;
	$SettleTotal = 0;
	$PaymentTotal = 0;
	$CulmTotal = 0;
	$CulmDay = 0;
// 	while ($qd2 = sqlsrv_fetch_array( $result2, SQLSRV_FETCH_ASSOC)){
// 		$arPayments[$count] = $qd2;
// 		$arPayments[$count]['Type'] = $qd2['#Type'];

			
// 		if (is_object($qd2['Pay_dt'])){
// 			$arPayments[$count]['Pay_Date'] = $qd2['Pay_dt']->format('Y-m-d');
// 		} else {
// 			$arPayments[$count]['Pay_Date'] = "NotSet";
// 		}
// 		if (is_object($qd2['Dt_settled'])){
// 			$arPayments[$count]['Settle_Date'] = $qd2['Dt_settled']->format('Y-m-d');
// 		} else {
// 			$arPayments[$count]['Settle_Date'] = "NotSet";
// 		}
			
// 		if ($qd2['#Type'] == "Payment"){
// 			$PaymentTotal = $PaymentTotal + $qd2['Pay_amt'];
			
// 			$CulmTotal = $CulmTotal + $qd2['Pay_amt'];
// 		} else if ($qd2['#Type'] == "Settlement") {
// 			$arPayments[$count]['Pay_amt'] = $arPayments[$count]['Pay_amt'] * -1;
// 			$arPayments[$count]['Settle_amt'] = $arPayments[$count]['Settle_amt'] * -1;
// 			$SettleTotal = $SettleTotal + $arPayments[$count]['Settle_amt'];
// 			$CulmTotal = $CulmTotal + $arPayments[$count]['Settle_amt'];
// 		}

		
// 		$arPayments[$count]['CulmTotal'] = $CulmTotal;
		
// 		if ($arPayments[$count]['Settle_Date'] == $LastSettledDate){
// 			// add to SettleDateTotal
// 			$CulmDay = $CulmDay + $arPayments[$count]['Settle_amt'];
// 		} else {
				
// 			$CulmDay = $arPayments[$count]['Settle_amt'];
// 		}
		
// 		$LastSettledDate = $arPayments[$count]['Settle_Date'];
		
// 		$arPayments[$count]['CulmDay'] = $CulmDay;
		
// 		$count++;
// 	}

	
	// now go and get the ledger transactions
	$sql = "select * from icp_brcashhist where branch@ = 0 and R0_ttype is not null  order by Dat asc";
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
		
		$count++;
	}
	
	
	$sql = "select * from icp_brhist where #Type = 'Settlement' and branch@ = 0 order by Dt_settled asc, Paymethod";
	echo $sql;
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
			$arbrhist[$count]['Settle_Date'] = $qd2['Dt_settled']->format('Y-m-d');
		} else {
			$arbrhist[$count]['Settle_Date'] = "NotSet";
		}
	
		$arbrhist[$count]['Pay_amt'] = $arbrhist[$count]['Pay_amt'] * -1;
		$arbrhist[$count]['Settle_amt'] = $arbrhist[$count]['Settle_amt'] * -1;

		$count++;
	}
	
	foreach ($arbrhist as $key => $arData){
		$arDatebrhist[$arData['Settle_Date']][] = $arData;		
	}
	foreach ($arbrcashhist as $key => $arData){
		$arDatebrcashhist[$arData['Date']][] = $arData;
	}
	
// 	print_r($arDatebrhist);
// 	print_r($arDatebrcashhist);
	// now create arPayments

	// create a loop for each day
	$arDateRange = dateRange( '2016-02-19', date('Y-m-d'), '+1 day', 'Y-m-d' );

	//print_r($arDateRange);
	
	$count=0;
	$CulmTotal = 0;
	foreach ($arDateRange as $Date){
		$CulmDay = 0;
		$CulmDayInsurer = 0;
		$CulmDayCustomer = 0;
		if (isset($arDatebrhist[$Date])){
			foreach ($arDatebrhist[$Date] as $Key => $arDetails){
				$arPayments[$count]['Date'] = $Date;
				$arPayments[$count]['Polref'] = $arDetails['Polref'];
				$arPayments[$count]['Amount'] = $arDetails['Settle_amt'];
				$arPayments[$count]['PayMethod'] = $arDetails['Paymethod'];
				$arPayments[$count]['Type'] = $arDetails['Type'];
				$arPayments[$count]['Insurer'] = $arDetails['Icno_INSC_VTId'];
				
				$CulmTotal = $CulmTotal + $arDetails['Settle_amt'];
				$CulmDay = $CulmDay + $arDetails['Settle_amt'];
				$CulmDayInsurer = $CulmDayInsurer + $arDetails['Settle_amt'];
				$arPayments[$count]['CulmTotal'] = $CulmTotal;
				
				$count++;
			}
		}
		if (isset($arDatebrcashhist[$Date])){
			foreach ($arDatebrcashhist[$Date] as $Key => $arDetails){
				$arPayments[$count]['Date'] = $Date;
				$arPayments[$count]['Polref'] = $arDetails['Polref'];
				$arPayments[$count]['Amount'] = $arDetails['R0_amt'];
				$arPayments[$count]['PayMethod'] = $arDetails['R0_pm'];
				$arPayments[$count]['Type'] = $arDetails['R0_ttype'];
				$arPayments[$count]['Insurer'] = $arDetails['Insco_INSC_VTId'];

				$CulmTotal = $CulmTotal + $arDetails['R0_amt'];
				$CulmDay = $CulmDay + $arDetails['R0_amt'];
				$CulmDayCustomer = $CulmDayCustomer + $arDetails['R0_amt'];
				$arPayments[$count]['CulmTotal'] = $CulmTotal;
				
				$count++;
			}
		}
		
		// craeet total line for day
		$arPayments[$count]['Title'] = "Total for " . $Date;
		$arPayments[$count]['Date'] = $Date;
		$arPayments[$count]['Polref'] = "";
		$arPayments[$count]['Amount'] = "";
		$arPayments[$count]['PayMethod'] = "";
		$arPayments[$count]['Type'] = "";
		$arPayments[$count]['Insurer'] = "";
		
		$arPayments[$count]['CulmTotalCustomer'] = $CulmDayCustomer;
		$arPayments[$count]['CulmTotalInsurer'] = $CulmDayInsurer;
		$arPayments[$count]['CulmTotal'] = $CulmDay;
		
		$count++;
	}	
// 	$arPayments[$count]['FeeTotal'] = $FeeTotal;
// 	$arPayments[$count]['PaymentReceived'] = $PaymentTotal;
// 	$arPayments[$count]['SettlementMade'] = $SettleTotal;

//print_r($arPayments);


	// set up smarty
	$smarty = getSmarty();

	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arPayments',    $arPayments);
	$smarty->assign('SESSION',   $_SESSION);

	// display using template name provided
	$smarty->display("custom/ListICPayments.tpl");

}


?>