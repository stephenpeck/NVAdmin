{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListICPolicies">
	      <tr >
    		<th> Date From:  </th>
    		<th> <input type=text class="form-control" name=arCtl[date_from] value="{$arCtl.date_from}"> </th>
    		<th> Date To:  </th>
    		<th> <input type=text class="form-control" name=arCtl[date_to] value="{$arCtl.date_to}"> </th>
    		<th> Term Code:  </th>
    		<th> {html_options class="form-control" name=arCtl[term_code] options=$arTermCodeList selected="{$arCtl.term_code} </th>
    		<th colpsan=4> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show</button></th>
	      </tr>
	 </form>
	<thead>
   </table>    
  <table class="table table-striped">
    <thead>


	      <tr>
	        <th >Policy Ref</th>
	        <th >Exec</th>
	        <th >Pol No</th>
	        <th >R Date</th>
	        <th >I Date</th>
	        <th >Term Date</th>
	        <th >Premium</th>
	        <th >Fee</th>
	        <th >Payment Received</th>
	        <th >Settement Paid</th>
	        <th >Source</th>
	        <th >Term Code</th>
	        <th ></td>
	      </tr>
    </thead>
    <tbody>
    
	    {section name=row loop=$arPolicy}

		      <tr>
		        <td>{$arPolicy[row].Refno}</td>
		        <td>{$arPolicy[row].Exec}</td>
		        <td>{$arPolicy[row].Polno}</td>
		        <td>{$arPolicy[row].RDate}</td>
		        <td>{$arPolicy[row].IDate}</td>
		        <td>{$arPolicy[row].TermDate}</td>
		        <td>{$arPolicy[row].GWP}</td>
		        <td>{$arPolicy[row].FeeTotal}</td>
		        <td>{$arPolicy[row].PaymentReceived}</td>
		        <td>{$arPolicy[row].SettlementMade}</td>
		        <td>{$arPolicy[row].Access} </td>
		        <td>{$arPolicy[row].Term_code} </td>
		        <td> <a href="#{$arPolicy[row].Refno}" data-toggle="collapse">Show Transactions </A></td>
		      </tr>	
		      <tr class="collapse" id={$arPolicy[row].Refno}>
		      	<td></td>
		      	<td colspan=9>
		      		<table  class="table table-striped">
		      		<tr>
				      	<th>brledger</th>
				      	<td >
				      		<table  class="table table-striped">
							      <tr>
							        <th>Suffix</td>
							        <th>Trantype</td>
							        <th>Paymentmeth</td>
							        <th>Ledger_Date</td>
							        <th>Date_Raised</td>
							        <th>Date_Settled</td>
							        <th>Orig_debt</td>
							        <th>Poac</td>
							      </tr>	
						    		      		
				      		{assign var=brcledger value=$arPolicy[row].brcledger}
						    {section name=row2 loop=$brcledger}
						    
							      <tr>
							        <td>{$brcledger[row2].Suffix}</td>
							        <td>{$brcledger[row2].Trantype}</td>
							        <td>{$brcledger[row2].Paymethod}</td>
							        <td>{$brcledger[row2].Ledger_Date}</td>
							        <td>{$brcledger[row2].Date_Raised}</td>
							        <td>{$brcledger[row2].Date_Settled}</td>
							        <td>{$brcledger[row2].Orig_debt}</td>
							        <td>{$brcledger[row2].Poac}</td>
							      </tr>	
						    
						    {/section}
				      		
				      		
				      		</table>
				      	</td>
				      </tr
				      <tr>
				      	<th>brcashhist</th>
				      	<td >
				      		<table  class="table table-striped">
							      <tr>
							        <th>Rtype</td>
							        <th>Xttype</td>
							        <th>Dat</td>
							        <th>Ledger effect date</td>
							        <th>Suffix</td>
							        <th>X_val</td>
							        <th>X_Val2</td>
							        <th>X_comm</td>
							        <th>Paystat</td>
							        <th>R0_ttype</td>
							        <th>R0_amt</td>
							        <th>R0_chg</td>
							      </tr>	
						    		      		
					      		{assign var=brcashhist value=$arPolicy[row].brcashhist}
							    {section name=row2 loop=$brcashhist}
							    
								      <tr>
								        <td>{$brcashhist[row2].Rtype}</td>
								        <td>{$brcashhist[row2].X_ttype}</td>
								        <td>{$brcashhist[row2].Date}</td>
								        <td>{$brcashhist[row2].Ledger_Effective_Date}</td>
								        <td>{$brcashhist[row2].Ldg_suffix}</td>
								        <td>{$brcashhist[row2].X_val1}</td>
								        <td>{$brcashhist[row2].X_val2}</td>
								        <td>{$brcashhist[row2].X_comm}</td>
								        <td>{$brcashhist[row2].Paystat}</td>
								        <td>{$brcashhist[row2].R0_ttype}</td>
								        <td>{$brcashhist[row2].R0_amt}</td>
								        <td>{$brcashhist[row2].R0_chg}</td>
								      </tr>	
							    
							    {/section}
				      		
				      		</table
				      	</td>
				      </tr>
				      <tr>
				      	<th>brhist</th>
				      	<td >
				      		<table  class="table table-striped">
							      <tr>
							        <th>Type</td>
							        <th>Tran Suffix</td>
							        <th>Pay Method</td>
							        <th>Pay Date</td>
							        <th>Pay Amount</td>
							        <th>Settled Date</td>
							        <th>Settle Amount</td>
							      </tr>	
						    		      		
					      		{assign var=brhist value=$arPolicy[row].brhist}
							    {section name=row2 loop=$brhist}
							    
								      <tr>
								        <td>{$brhist[row2].Type}</td>
								        <td>{$brhist[row2].Tran_suff}</td>
								        <td>{$brhist[row2].Paymethod}</td>
								        <td>{$brhist[row2].Pay_Date}</td>
								        <td>{$brhist[row2].Pay_amt}</td>
								        <td>{$brhist[row2].Settle_Date}</td>
								        <td>{$brhist[row2].Settle_amt}</td>
								      </tr>	
							    
							    {/section}
				      		
				      		</table
				      	</td>
				      </tr>
				     </table>
				    </td>
				   </tr>
			
			
		{/section}      

	      <tr>	
	        <th>Totals</td>
	        <td></td>
	        <td></td>
	        <td></td>
	        <td></td>
	        <td></td>
	        <th>{$arTotals.CPCHours}</td>
	        <th>{$arTotals.TPHours}</td>
	        <th>{$arTotals.PER_COMPLETE_UPLOADED} </td>
	      </tr>	
		

    </tbody>
  
  </table>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file="header/admin_footer.tpl"}