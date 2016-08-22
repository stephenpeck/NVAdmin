{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListOrderSkills">
	      <tr >
	        <td colspan="10" nowrap>
	        		<div class="form-group col-sm-1"> Course:  </div>
	        		<div class="form-group col-sm-3"> {html_options class="form-control" name=arCtl[CO_KEY] options=$arCourseList selected=$arCtl.CO_KEY} </div>
	        		<div class="form-group col-sm-2"> Booked Date (From):  </div>
	        		<div class="form-group col-sm-2"> <input type=text class="form-control" name=arCtl[AC_DATE] value="{$arCtl.AC_DATE}"> </div>
	        		<div class="form-group col-sm-1"> Qualified:  </div>
	        		<div class="form-group col-sm-2"> {html_options class="form-control" name=arCtl[QUALIFIED] options=$arYesNoList selected=$arCtl.QUALIFIED} </div>
	        		<div class="form-group col-sm-1"> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show Orders</button> </div>
	        </td>
	      </tr>
	 </form>
	<table>
  <table class="table table-striped">
    <thead>
    
      <tr>
        <th >Order</th>
        <th >Customer</th>
        <th >Date</th>
        <th >Course</th>
        <th >Instructor</th>
        <th colspan=2>Status</th>
      </tr>
    </thead>
    <tbody>
    
	    {section name=row loop=$arORDERS}
    
	      <tr>
	        <td>{$arORDERS[row].OD_REF}</td>
	        <td>{$arORDERS[row].CU_NAME}</td>
	        <td>{$arORDERS[row].AC_DATE}</td>
	        <td>{$arORDERS[row].CO_NAME}</td>
	        <td>{$arORDERS[row].TR_NAME}</td>
	        <td>{$arORDERS[row].SKILLDESC}</td>
	        <td>{$arORDERS[row].EXPIRYDATE}</td>
	      </tr>	

		{/section}      

    </tbody>
  </table>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file="header/admin_footer.tpl"}