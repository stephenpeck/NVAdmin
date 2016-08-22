{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListBankPayments">
	      <tr >
    		<th> Payment:  </th>
    		<th> {html_options name=arCtl[BP_KEY] class="form-control" options=$arPayments selected=$arCtl.BP_KEY} </th>
    		<th > <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show</button></th>
	      </tr>
	 </form>
	<thead>
   </table>    
  <table class="table table-striped">
    <thead>


	      <tr>
	        <th > Bank Trans Id</th>
	        <th > Bank Tran Date</th>
	        <th > OGI Date</th>
	        <th > Type</th>
	        <th > OGI Amnt</th>
	        <th > Policy</td>
	        <th > NB</td>
	        <th > MTA</td>
	        <th > Comm </td>
	        <th > Fee</td>
	        <th > Payment Amt </td>
	        <th > Paid?  </td>
	        <th > Insurer</td>
	        <th > Settled </td>
	        <th > Payment Action </td>
	        <th > Value </td>
	      </tr>


    </thead>
    <tbody>
    
	    {section name=row2 loop=$arBANKRECON}
	      <tr>
	        <th>{$arBANKRECON[row2].BK_KEY} {$RECON[row2].BR_KEY} </th>
	        <th>{$arBANKRECON[row2].BK_TRANDATE}</th> 
	        <td>{$arBANKRECON[row2].Date}</td>
	        <td nowrap>{$arBANKRECON[row2].Type}</td>
	        <td align=right>{$arBANKRECON[row2].Amount}</td>
	        <td>{$arBANKRECON[row2].Polref}</td>
	        <td align=right>{$arBANKRECON[row2].PolicyInfo.MATCHED.NB}</td>
	        <td align=right>{$arBANKRECON[row2].PolicyInfo.MATCHED.MTA}</td>
	        <td align=right>{$arBANKRECON[row2].PolicyInfo.MATCHED.Commission}</td>
	        <td align=right>{$arBANKRECON[row2].PolicyInfo.MATCHED.Fee}</td>
	        <td align=right>{$arBANKRECON[row2].PolicyInfo.MATCHED.Payment}</td>
	        <td>{$arBANKRECON[row2].PolicyInfo.MATCHED.Paid}</td>
	        <td>{$arBANKRECON[row2].Insurer}</td>
	        <td>{$arBANKRECON[row2].PolicyInfo.MATCHED.Settled}</td>
	        <td>{$arBANKRECON[row2].BR_PAYMENTACTION}  </td>
	        <td align="right">{$arBANKRECON[row2].BR_PAYMENTVALUE}  </td>
	      </tr>	
		{/section}      
			
			
			      <tr>
	        <th colspan=8 ></td>
	        <td align=right></td>
	        <td align=right></td>
	        <td colspan=5 > Total Payment Value</td>
	        <th align=right>{$arTotal.Payment}</td>
	      </tr>		
			
    </tbody>
  
  </table>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file="header/admin_footer.tpl"}