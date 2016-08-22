{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListInstructors">
	      <tr >
	        <td colspan="10" nowrap>
	        		<div class="form-group col-sm-1"> Name:  </div>
	        		<div class="form-group col-sm-2"> <input type=text class="form-control" name=arCtl[TR_NAME]  value="{$arCtl.TR_NAME}"> </div>
	        		<div class="form-group col-sm-1"> Employment Status:  </div>
	        		<div class="form-group col-sm-2"> {html_options class="form-control" name=arCtl[TR_CLASS] options=$arEmpStatusList selected=$arCtl.TR_CLASS} </div>
	        		<div class="form-group col-sm-1"> Availability:  </div>
	        		<div class="form-group col-sm-2"> {html_options class="form-control" name=arCtl[TR_AVAILABLE] options=$arAvailableList2 selected=$arCtl.TR_AVAILABLE} </div>
	        		<div class="form-group col-sm-1"> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show Courses</button> </div>
	        </td>
	      </tr>
	 </form>
	 
	 {if $arCtl.Error != ""}
	 <tr><td colspan=10>
	 	{$arCtl.Error}
	 </td></tr>
	 {/if}
	 
	<table>
  <table class="table table-striped">
    <thead>
    
      <tr>
        <th >Ins ID</th>
        <th >Name</th>
        <th >Status</th>
        <th >Available</th>
        <th ></th>
      </tr>
    </thead>
    <tbody>
    
	    {section name=row loop=$arTRAINERS}
    
            <form action={$SESSION.PostAction} method=post class="form-inline" role="form">
			<input type=hidden name=action value="UpdInstructors">
			<input type=hidden name=arCtl[TR_CLASS] value="{$arCtl.TR_CLASS}">
			<input type=hidden name=arCtl[TR_NAME] value="{$arCtl.TR_NAME}">
			<input type=hidden name=arCtl[Run] value="Y">
			<input type=hidden name=arCtl[TR_AVAILABLE] value="{$arCtl.TR_AVAILABLE}">
			<input type=hidden name=TRAINERS[TR_KEY] value="{$arTRAINERS[row].TR_KEY}">
		      <tr>
		        <td><input type=text size=3 class="form-control" name=TRAINERS[TR_REF] value="{$arTRAINERS[row].TR_REF}"></td>
		        <td>{$arTRAINERS[row].TR_NAME}</td>
		        <td>{html_options class="form-control" name=TRAINERS[TR_CLASS] options=$arEmpStatusList selected=$arTRAINERS[row].TR_CLASS}</td>
		        <td>{html_options class="form-control" name=TRAINERS[TR_AVAILABLE] options=$arAvailableList selected=$arTRAINERS[row].TR_AVAILABLE}</td>
		        <td><button type="submit" class="btn btn-default ">Update</button></td>
		      </tr>	
	          <tr>
		        <td></td>
		        <td colspan="5">
					  <table class="table table-hover" >
					  
					  {assign var=arCourses value=$arTRAINERS[row].COURSETRAINERS}
					  {section name=row2 loop=$arCourses}
	
			        
						      <tr>
						        <td nowrap > {$arCourses[row2].CO_NAME}  {$arCourses[row2].CO_CODE}</td>
						        <td> {$arCourses[row2].CT_ACCREDITATION}</td>
						        <td> Operator Expiry</td>
						        <td> {$arCourses[row2].CT_OPERATOR_EXPIRY}</td>
						        <td> Instructor Expiry</td>
						        <td> {$arCourses[row2].CT_INSTRUCTOR_EXPIRY}</td>
			            
					{/section}
					
				  	</table>
		        
		        </td>
		      </tr>
			</form>
		
		{/section}      

    </tbody>
  </table>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file="header/admin_footer.tpl"}