{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListCourses">
	      <tr >
	        <td colspan="10" nowrap>
	        		<div class="form-group col-sm-1"> Course:  </div>
	        		<div class="form-group col-sm-4"> {html_options class="form-control" name=arCtl[CO_KEY] options=$arCourseList selected=$arCtl.CO_KEY} </div>
	        		<div class="form-group col-sm-1"> Show on Admin:  </div>
	        		<div class="form-group col-sm-2"> {html_options class="form-control" name=arCtl[CO_SHOW] options=$arYesNoList selected=$arCtl.CO_SHOW} </div>
	        		<div class="form-group col-sm-4"> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show Courses</button> </div>
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
        <th >Code</th>
        <th >Name</th>
        <th >Show</th>
        <th ></th>
      </tr>
    </thead>
    <tbody>
    
	    {section name=row loop=$arCOURSES}
    
	      <tr>
	        <td>{$arCOURSES[row].CO_CODE}</td>
	        <td>{$arCOURSES[row].CO_NAME}</td>
	        <td>{$arCOURSES[row].CO_SHOW}</td>
	        <td></td>
	      </tr>	
          <tr>
	        <td></td>
	        <td colspan="4">
				  <table class="table table-hover" >
				  
				  {assign var=arInstructors value=$arCOURSES[row].COURSETRAINERS}
				  {section name=row2 loop=$arInstructors}

		            <form action={$SESSION.PostAction} method=post class="form-inline" role="form">
					<input type=hidden name=action value="UpdCourseTrainers">
					<input type=hidden name=arCtl[CO_SHOW] value="{$arCtl.CO_SHOW}">
					<input type=hidden name=arCtl[Run] value="Y">
					<input type=hidden name=arCtl[CO_KEY] value="{$arCtl.CO_KEY}">
					<input type=hidden name=COURSETRAINERS[CT_KEY] value="{$arInstructors[row2].CT_KEY}">
					<input type=hidden name=COURSES[CO_KEY] value="{$arCOURSES[row].CO_KEY}">
		        
					      <tr>
					        <td nowrap > {$arInstructors[row2].TR_NAME}  {$arInstructors[row2].TR_CODE}</td>
					        <td> {html_options class="form-control" name=COURSETRAINERS[CT_ACCREDITATION] options=$arAccreditationList selected=$arInstructors[row2].CT_ACCREDITATION}</td>
					        <td> Operator Expiry</td>
					        <td> <input type=text class="form-control" name=COURSETRAINERS[CT_OPERATOR_EXPIRY] value="{$arInstructors[row2].CT_OPERATOR_EXPIRY}"></td>
					        <td> Instructor Expiry</td>
					        <td> <input type=text class="form-control" name=COURSETRAINERS[CT_INSTRUCTOR_EXPIRY] value="{$arInstructors[row2].CT_INSTRUCTOR_EXPIRY}"></td>
					        <td> <button type="submit" class="btn btn-default ">Update</button> </td>
					</form>
		            <form action={$SESSION.PostAction} method=post class="form-inline" role="form">
					<input type=hidden name=action value="DelCourseTrainers">
					<input type=hidden name=arCtl[Run] value="Y">
					<input type=hidden name=arCtl[CO_KEY] value="{$arCtl.CO_KEY}">
					<input type=hidden name=arCtl[CO_SHOW] value="{$arCtl.CO_SHOW}">
					<input type=hidden name=COURSETRAINERS[CT_KEY] value="{$arInstructors[row2].CT_KEY}">
					<input type=hidden name=COURSES[CO_KEY] value="{$arCOURSES[row].CO_KEY}">
					        <td> <button type="submit" class="btn btn-default ">Remove</button> </td>
					      </tr>
					</form>

				{/section}
				
				    <form action={$SESSION.PostAction} method=post class="form-inline" role="form">
					<input type=hidden name=action value="UpdCourseTrainers">
					<input type=hidden name=arCtl[CO_SHOW] value="{$arCtl.CO_SHOW}">
					<input type=hidden name=arCtl[CO_KEY] value="{$arCtl.CO_KEY}">
					<input type=hidden name=arCtl[Run] value="Y">
					<input type=hidden name=COURSETRAINERS[CT_KEY] value="">
					<input type=hidden name=COURSES[CO_KEY] value="{$arCOURSES[row].CO_KEY}">
					<input type=hidden name=COURSETRAINERS[CT_COKEY] value="{$arCOURSES[row].CO_KEY}">
		        
					      <tr>
					        <td> {$arInstructors[row2].TR_NAME}  {$arInstructors[row2].TR_CODE}</td>
					        <td> {html_options class="form-control" name=COURSETRAINERS[CT_TRKEY] options=$arCOURSES[row].InstructorList }</td>
					        <td> {html_options class="form-control" name=COURSETRAINERS[CT_ACCREDITATION] options=$arAccreditationList }</td>
					        <td> Operator Expiry</td>
					        <td> <input type=text class="form-control" name=COURSETRAINERS[CT_OPERATOR_EXPIRY] value=""></td>
					        <td> Instructor Expiry</td>
					        <td> <input type=text class="form-control" name=COURSETRAINERS[CT_INSTRUCTOR_EXPIRY] value=""></td>
					        <td colspan=2> <button type="submit" class="btn btn-default ">Add</button> </td>
					      </tr>
					</form>
				
				
			  	</table>
	        
	        </td>
	      </tr>

		{/section}      

    </tbody>
  </table>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file="header/admin_footer.tpl"}