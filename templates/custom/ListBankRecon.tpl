{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListBankRecon">
	      <tr >
    		<th> Date From:  </th>
    		<th> <input type=text class="form-control" name=arCtl[BK_TRANDATE_FROM] value="{$arCtl.BK_TRANDATE_FROM}"> </th>
    		<th> Date To:  </th>
    		<th> <input type=text class="form-control" name=arCtl[BK_TRANDATE_TO] value="{$arCtl.BK_TRANDATE_TO}"> </th>
    		<th colpsan=4> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show</button></th>
	      </tr>
	 </form>
	<thead>
   </table>    
  <table class="table table-striped">
    <thead>


	      <tr>
	        <th rowspan=2 colspan=3> </th>
	        <th rowspan=2 > OGI Date</th>
	        <th rowspan=2 > Type</th>
	        <th rowspan=2 > OGI Amnt</th>
	        <th rowspan=2 > Policy</td>
	        <th rowspan=2 > NB</td>
	        <th rowspan=2 > MTA</td>
	        <th rowspan=2 > Comm </td>
	        <th rowspan=2 > Fee</td>
	        <th colspan=2 > Initial Payment </td>
	        <th colspan=2 > Current Payment </td>
	        <th rowspan=2> Insurer</td>
	        <th rowspan=2> Settled </td>
	        <th rowspan=2 nowrap> Recon Action</td>
	        <th rowspan=2  nowrap> Recon Amount</td>
	      </tr>

	      <tr>
	        <th > Amt </td>
	        <th > Paid </td>
	        <th > Amt </td>
	        <th > Paid </td>
	      </tr>

    </thead>
    <tbody>
    
       	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
			<input type=hidden name=action value="UpdBankPayment">
			<input type=hidden name=BANKPAYMENTS[BP_FROMDATE] value="{$arCtl.BK_TRANDATE_FROM}">
			<input type=hidden name=BANKPAYMENTS[BP_TODATE] value="{$arCtl.BK_TRANDATE_TO}">
    
    
		{assign var=count value=0}
	    {section name=row loop=$arBANKRECON}

		       {assign var=RECON value=$arBANKRECON[row].RECON}
			    {section name=row2 loop=$RECON}
					<input type=hidden name=BANKRECON[{$count}][BR_KEY] value="{$RECON[row2].BR_KEY}">
					<input type=hidden name=BANKRECON[{$count}][BR_PAYMENTACTION] value="{$RECON[row2].Action}">
					<input type=hidden name=BANKRECON[{$count}][ACTIONTYPE] value="{$RECON[row2].ActionType}">
					<input type=hidden name=BANKRECON[{$count}][BR_PAYMENTVALUE] value="{$RECON[row2].ActionAmount}">
			      <tr>
			        <th>{$RECON[row2].BK_KEY} </th>
			        <th>{$RECON[row2].BK_TRANDATE}</th> 
			        <th align=right>{$RECON[row2].BK_AMOUNT}</th>
			        <td>{$RECON[row2].Date}</td>
			        <td nowrap>{$RECON[row2].Type}</td>
			        <td align=right>{$RECON[row2].Amount}</td>
			        <td>{$RECON[row2].Polref}</td>
			        <td align=right>{$RECON[row2].PolicyInfo.MATCHED.NB}</td>
			        <td align=right>{$RECON[row2].PolicyInfo.MATCHED.MTA}</td>
			        <td align=right>{$RECON[row2].PolicyInfo.MATCHED.Commission}</td>
			        <td align=right>{$RECON[row2].PolicyInfo.MATCHED.Fee}</td>
			        <td align=right>{$RECON[row2].PolicyInfo.MATCHED.PaymentAtDate}</td>
			        <td>{$RECON[row2].PolicyInfo.MATCHED.PaidAtDate}</td>
			        <td align=right>{$RECON[row2].PolicyInfo.MATCHED.Payment}</td>
			        <td>{$RECON[row2].PolicyInfo.MATCHED.Paid}</td>
			        <td>{$RECON[row2].Insurer}</td>
			        <td>{$RECON[row2].PolicyInfo.MATCHED.Settled}</td>

				      {if $RECON[row2].BP_STATUS == "New"}
				        <td bgcolor = "orange">{$RECON[row2].Action} ({$RECON[row2].BR_KEY})</td>
				      {elseif $RECON[row2].BP_STATUS == "Reconciled"}
				        <td bgcolor = "lightgreen">{$RECON[row2].Action} ({$RECON[row2].BR_KEY})</td>
				      {else}
				        <td>{$RECON[row2].Action}</td>
				      {/if}
				        <td>{$RECON[row2].ActionAmount}</td>
			      </tr>	
			      {assign var=count value=$count+1}
				{/section}      
			
		{/section}      

	      <tr>
	        <th  colspan=2> Batch</td>
		        <th colspan=2> Commission </td>
		        <th> {$arTotal.Commission}</td>
		        <th  colspan=2> 
		        	
		        
		        	{if $arShowPayment.Commission == "Y"}
		        		<button type="submit" name=BANKPAYMENTS[BP_TYPE] value="Commission" class="btn btn-default ">Create Payment</button>
		        	{else}
		        		N/A
		        	{/if}
		        </td>
		        <th colspan=2> Fee</td>
		        <th>{$arTotal.Fee}</td>
		        <th  colspan=2> 
		        	{if $arShowPayment.Fee == "Y"}
	        			<button type="submit" name=BANKPAYMENTS[BP_TYPE] value="Fee" class="btn btn-default ">Create Payment</button>
		        	{else}
		        		N/A
		        	{/if}
		        </td>
		        <th colspan=2> Interest</td>
		        <th>{$arTotal.Interest}</td>
		        <th colspan=2> 
		        	{if $arShowPayment.Interest == "Y"}
	   					<button type="submit" name=BANKPAYMENTS[BP_TYPE]  value="Interest" class="btn btn-default ">Create Payment</button>
		        	{else}
		        		N/A
		        	{/if}
		        </td>
		     </form>
	        <th  colspan=2></td>
	      </tr>	

    </tbody>
  
  </table>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file="header/admin_footer.tpl"}