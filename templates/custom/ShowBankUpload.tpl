{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form" ENCTYPE="MULTIPART/FORM-DATA">
		<input type=hidden name=action value="UpdLoadBankTransactions">
	      <tr >
    		<th> Load Bank CSV  </th>
    		<th> <INPUT size=50 TYPE = "FILE" NAME="banktrans" > </th>
    		<th colpsan=4> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Upload</button></th>
	      </tr>
	 </form>
	<thead>
   </table>    
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file="header/admin_footer.tpl"}