{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListBankTransactions">
	      <tr >
    		<th> Load Date:  </th>
    		<th> <input type=text class="form-control" name=arCtl[BK_LOADDATE] value="{$arCtl.BK_LOADDATE}"> </th>
    		<th> Date From:  </th>
    		<th> <input type=text class="form-control" name=arCtl[BK_TRANDATE_FROM] value="{$arCtl.BK_TRANDATE_FROM}"> </th>
    		<th> Date To:  </th>
    		<th> <input type=text class="form-control" name=arCtl[BK_TRANDATE_TO] value="{$arCtl.BK_TRANDATE_TO}"> </th>
    		<th> Reconciled:  </th>
    		<th> {html_options class="form-control" name=arCtl[BK_RECONCILED] options=$arYesNoList selected=$arCtl.BK_RECONCILED} </th>
    		<th colpsan=4> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show</button></th>
	      </tr>
	 </form>
	<thead>
   </table>    
  <table class="table table-striped">
    <thead>


	      <tr>
	        <th >Key</th>
	        <th >Date</th>
	        <th >Sub Category</th>
	        <th >Memo</th>
	        <th >Amount</th>
	        <th >Reconciled</th>
	        <th ></td>
	      </tr>
    </thead>
    <tbody>
    
	    {section name=row loop=$arBANKTRANSACTIONS}

		      <tr>
		        <td class="bk_key" >{$arBANKTRANSACTIONS[row].BK_KEY}</td>
		        <td>{$arBANKTRANSACTIONS[row].BK_TRANDATE}</td>
		        <td>{$arBANKTRANSACTIONS[row].BK_SUBCATEGORY}</td>
		        <td>{$arBANKTRANSACTIONS[row].BK_MEMO}</td>
		        <td class="bk_amount" >{$arBANKTRANSACTIONS[row].BK_AMOUNT}</td>
		        <td>{$arBANKTRANSACTIONS[row].BK_RECONCILED}</td>
		        {if $arBANKTRANSACTIONS[row].BK_RECONCILED == "Y"}
			        <td> <a href="#{$arBANKTRANSACTIONS[row].BK_KEY}" data-toggle="collapse">Show Rec Details</A></td>
			    {else}
			        <td> 
			        		<button type="button" class="btn btn-info recon" >Reconcile Payments</button>
			        </td>
			    {/if}
		      </tr>	
		      <tr class="collapse" id={$arBANKTRANSACTIONS[row].BK_KEY}>
		      	<td></td>
		      	<td colspan=9>
			      		<table  class="table table-striped">
						      <tr>
						        <th>Recon Date</td>
						        <th>OGI Date</td>
						        <th>PolRef</td>
						        <th>Type</td>
						        <th>Amount</td>
						        <th>Insurer</td>
						      </tr>	
					    		      		
				      		{assign var=BANKRECON value=$arBANKTRANSACTIONS[row].BANKRECON}
						    {section name=row2 loop=$BANKRECON}
						    
							      <tr>
							        <td>{$BANKRECON[row2].BR_TRANDATE}</td>
							        <td>{$BANKRECON[row2].Date}</td>
							        <td>{$BANKRECON[row2].Polref}</td>
							        <td>{$BANKRECON[row2].Type}</td>
							        <td>{$BANKRECON[row2].Amount}</td>
							        <td>{$BANKRECON[row2].Insurer}</td>
							      </tr>	
						    
						    {/section}
			      		
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

<div id="Payment" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Unreconciled Payments</h4>
      </div>
      <div class="modal-body">
			  <table class="table table-striped">
		      	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
				<input type=hidden name=action value="UpdBankRecon">
				<input type=hidden name=BANKTRANSACTIONS[BK_KEY] value="" id=bk_key>
				<input type=hidden name=BANKTRANSACTIONS[BK_AMOUNT] value="" id=bk_amount_form>
				<input type=hidden class="form-control" name=arCtl[BK_LOADDATE] value="{$arCtl.BK_LOADDATE}"> 
    			<input type=hidden class="form-control" name=arCtl[BK_TRANDATE_FROM] value="{$arCtl.BK_TRANDATE_FROM}">
    			<input type=hidden class="form-control" name=arCtl[BK_TRANDATE_TO] value="{$arCtl.BK_TRANDATE_TO}"> 
    			<input type=hidden class="form-control" name=arCtl[Run] value="Y"> 
				  <tr>
			        <td><input type=text id=bk_amount name=bk_amount></td>
			        <td><input type=text id=bk_reconamount name=bk_reconamount></td>
			        <td><button id="ReconBtn" type="submit" class="btn btn-info collapse" >Reconcile</button></td>
			      </tr>
				  <tr>
			        <td colspan=3><button id="ShowReconRows" type="button" class="btn btn-info" >Show Reconciled</button></td>
			      </tr>
			  </table>
			  <table class="table table-striped recontable">
			    <thead>
				      <tr>
				        <th>Date</td>
				        <th>Type</td>
				        <th>Ref</td>
				        <th>Pay Method</td>
				        <th>Insurer</td>
				        <th>Amount</td>
				        <th>Reconcile</td>
				      </tr>
			    </thead>
			    <tbody>
			    	{assign var=count value=0}
					{section name=row2 loop=$arPayments}
				  {if $arPayments[row2].Reconciled == "Y"}
				  		<tr class="reconrows collapse">
				  {else}
					  <tr>
				  {/if}
			        <td>{$arPayments[row2].Date}</td>
			        <td>{$arPayments[row2].Type}</td>
			        <td>{$arPayments[row2].Polref}</td>
			        <td>{$arPayments[row2].PayMethod}</td>
			        <td>{$arPayments[row2].Insurer}</td>
			        <td class="bk_reconamount" align=right>{$arPayments[row2].Amount}</td>
			        {if $arPayments[row2].Reconciled == "Y"}
				        <td>Reconciled</td>
			        {else}
				        <td><input class="reconselect" name=BANKRECON[{$count}][OGI] type=checkbox value="{$arPayments[row2].Key}" ></td>
			        {/if}
			      </tr>	
			    	{assign var=count value=$count+1}
			      {/section}
			      
				  <tr>
			        <td colspan="2"> Non OGI </td>
			        <td colspan=4>{html_options name=BANKRECON[BR_TYPE] options=$arManualList}</td>
			        <td><input class="manualreconselect" name=BANKRECON[{$count}][OGI] type=checkbox value="{$arPayments[row2].Key}" ></td>
			      </tr>	
			      
			      
			</table>		    
		</form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


{include file="header/admin_footer.tpl"}