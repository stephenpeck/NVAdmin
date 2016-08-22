{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListTOPS">
	      <tr >
	        <td colspan="10" nowrap>
	        		<div class="form-group col-sm-1"> Ref:  </div>
	        		<div class="form-group col-sm-1"> <input type=text class="form-control" name=arCtl[OD_REF] value="{$arCtl.OD_REF}"> </div>
	        		<div class="form-group col-sm-1"> Date From:  </div>
	        		<div class="form-group col-sm-1"> <input type=text class="form-control" name=arCtl[AC_DATE] value="{$arCtl.AC_DATE}"> </div>
	        		<div class="form-group col-sm-1"> Tops Loaded:  </div>
	        		<div class="form-group col-sm-1"> {html_options class="form-control" name=arCtl[AT_TOPSLOADED] options=$arYesNoList selected=$arCtl.AT_TOPSLOADED} </div>
	        		<div class="form-group col-sm-1"> Course:  </div>
	        		<div class="form-group col-sm-3"> {html_options class="form-control" name=arCtl[CO_KEY] options=$arCourseList selected=$arCtl.CO_KEY} </div>
	        		<div class="form-group col-sm-2"> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show Attendees</button> </div>
	        </td>
	      </tr>
	 </form>
    
      <tr>
        <th class="col-md-1">Date</th>
        <th class="col-md-1">Ref</th>
        <th class="col-md-2">Course</th>
        <th class="col-md-1">Instructor</th>
        <th class="col-md-2">Attendee</th>
        <th class="col-md-1">Certificate</th>
        <th class="col-md-1">TOPS Ref</th>
        <th class="col-md-1">TOPS Loaded</th>
        <th class="col-md-2"></th>
      </tr>
    </thead>
    <tbody>
    
	    {section name=row loop=$arACTIVITIES}
    
	      <tr>
	        <td>{$arACTIVITIES[row].AC_DATE}</td>
	        <td>{$arACTIVITIES[row].OD_REF}</td>
	        <td>{$arACTIVITIES[row].CO_NAME}</td>
	        <td>{$arACTIVITIES[row].TRAINER_NAME}</td>
	        <td>{$arACTIVITIES[row].IN_NAME} </td>
	        <td>{$arACTIVITIES[row].AT_CERTNO} </td>
	        <td>{$arACTIVITIES[row].IN_TOPSREF} </td>
	        <td>{$arACTIVITIES[row].AT_TOPSLOADED} </td>
	        <td><a href="#{$arACTIVITIES[row].AT_KEY}" data-toggle="collapse"> Details </A></td>
	      </tr>	
          <tr class="collapse" id={$arACTIVITIES[row].AT_KEY}>
	        <td></td>
	        <td colspan="8">
	            <form action={$SESSION.PostAction} method=post class="form-inline" role="form">
					<input type=hidden name=action value="UpdTOPS">
					<input type=hidden name=arCtl[OD_REF] value="{$arCtl.OD_REF}">
					<input type=hidden name=arCtl[AC_DATE] value="{$arCtl.AC_DATE}">
					<input type=hidden name=arCtl[AT_TOPSLOADED] value="{$arCtl.AT_TOPSLOADED}">
					<input type=hidden name=arCtl[CO_KEY] value="{$arCtl.CO_KEY}">
					<input type=hidden name=TOPS[TO_KEY] value="{$arACTIVITIES[row].TOPS.TO_KEY}">
					<input type=hidden name=TOPS[TO_ACKEY] value="{$arACTIVITIES[row].AC_KEY}">
					<input type=hidden name=INDIVIDUALS[IN_KEY] value="{$arACTIVITIES[row].IN_KEY}">
					<input type=hidden name=ATTENDEES[AT_KEY] value="{$arACTIVITIES[row].AT_KEY}">
					<input type=hidden name=TOPS[TO_ATKEY] value="{$arACTIVITIES[row].AT_KEY}">
		        
					  <table class="table table-hover" >
					      <tr>
					      	<thead>
					      		<td colspan=8> TOPS Data to Be entered </td>
					      	</thead>
					      </tr>
					      <tr>
				      		<th colspan=8> Operator Details </td>
					      </tr>
					      <tr>
					        <td> Title</td>
					        <td> {html_options class="form-control" name=TOPS[TO_TITLE] options=$arTitleList selected=$arACTIVITIES[row].TOPS.TO_TITLE}</td>
					        <td> Forename</td>
					        <td> <input type=text class="form-control" name=TOPS[TO_FORENAME] value={$arACTIVITIES[row].TOPS.TO_FORENAME}></td>
					        <td> Surname</td>
					        <td> <input type=text class="form-control" name=TOPS[TO_SURNAME] value={$arACTIVITIES[row].TOPS.TO_SURNAME}></td>
					        <td> DOB</td>
					        <td> <input type=text class="form-control" name=TOPS[TO_DOB] value={$arACTIVITIES[row].TOPS.TO_DOB}></td>
					       </tr>
					      <tr>
				      		<th colspan=8> Course Details </td>
					      </tr>
					      <tr>
					        <td >Course Type</td>
					        <td> {html_options class="form-control" name=TOPS[TO_COURSETYPE] options=$arITSSARCourseTypeList selected=$arACTIVITIES[row].TOPS.TO_COURSETYPE}</td>
					        <td >Ratio</td>
					        <td> {html_options class="form-control" name=TOPS[TO_RATIO] options=$arRatioList selected=$arACTIVITIES[row].TOPS.TO_RATIO}</td>
					        <td > Ind Truck Group</td>
					        <td colspan=3> {html_options class="form-control" name=TOPS[TO_INDTRUCKGROUP] options=$arITSSARCourseList selected=$arACTIVITIES[row].TOPS.TO_INDTRUCKGROUP}</td>
					      </tr>
					      <tr>
					        <td >Motive</td>
					        <td> {html_options class="form-control" name=TOPS[TO_MOTIVE] options=$arMotiveList selected=$arACTIVITIES[row].TOPS.TO_MOTIVE}</td>
					        <td >Rated Capacity Type</td>
					        <td> <input type=text class="form-control" name=TOPS[TO_RATEDCAPACITY] value={$arACTIVITIES[row].TOPS.TO_RATEDCAPACITY}></td>
					        <td >Instructor 1</td>
					        <td> {html_options class="form-control" name=TOPS[TO_INSTRUCTOR_TRKEY] options=$arInstructorList selected=$arACTIVITIES[row].TOPS.TO_INSTRUCTOR_TRKEY}</td>
					        <td >Instructor 2</td>
					        <td> {html_options class="form-control" name=TOPS[TO_INSTRUCTOR_TRKEY_2] options=$arInstructorList selected=$arACTIVITIES[row].TOPS.TO_INSTRUCTOR_TRKEY_2}</td>
					      </tr>
					      <tr>
					        <td >Start Date</td>
					        <td> <input type=text class="form-control" name=TOPS[TO_STARTDATE] value={$arACTIVITIES[row].TOPS.TO_STARTDATE}></td>
					        <td >Duration</td>
					        <td> <input type=text class="form-control" name=TOPS[TO_DURATION] value={$arACTIVITIES[row].TOPS.TO_DURATION}></td>
					        <td >End Date</td>
					        <td> <input type=text class="form-control" name=TOPS[TO_ENDDATE] value={$arACTIVITIES[row].TOPS.TO_ENDDATE}></td>
					        <td colspan=2></td>
					      </tr>
					      <tr>
				      		<th colspan=8> Course Details </td>
					      </tr>
					      <tr>
					        <td >Test Date</td>
					        <td> <input type=text class="form-control" name=TOPS[TO_TESTDATE] value={$arACTIVITIES[row].TOPS.TO_TESTDATE}></td>
					        <td >Examiner</td>
					        <td> {html_options class="form-control" name=TOPS[TO_EXAMINER_TRKEY] options=$arInstructorList selected=$arACTIVITIES[row].TOPS.TO_EXAMINER_TRKEY}</td>
					        <td >Light Height</td>
					        <td> <input type=text class="form-control" name=TOPS[TO_LIFTHEIGHT] value={$arACTIVITIES[row].TOPS.TO_LIFTHEIGHT}></td>
					        <td >ID Requirement</td>
					        <td> {html_options class="form-control" name=TOPS[TO_IDREQUIREMENT] options=$arIDList selected=$arACTIVITIES[row].TOPS.TO_IDREQUIREMENT}</td>
					      </tr>
					      <tr>
								{if $arACTIVITIES[row].TOPS.TO_KEY != ""}
						      		<td colspan=8 align=right> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-primary ">Save TOPS Data</button> </td>
						      	{else}
						      		<td colspan=8 align=right> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-primary ">Confirm TOPS Data OK</button> </td>
						      	{/if}
					      </tr>
				</form>
				
					{if $arACTIVITIES[row].TOPS.TO_KEY != ""}
					
					      	<thead>
					      <tr>
					      		<td colspan=8> TOPS Upload History </td>
					      </tr>
					      	</thead>

					      {assign var=SCRAPERLOG value=$arACTIVITIES[row].SCRAPERLOG}
					      
					      {if $SCRAPERLOG|@count gt 0}

						      <tr>
						        <th colspan=2> Date</td>
						        <th  colspan=2> Page</td>
						        <th > Result</td>
						        <td colspan=3 > </td>
						      <tr>
					      
					      
						      	{section name=row2 loop=$SCRAPERLOG}
							      <tr>
							        <td colspan=2> {$SCRAPERLOG[row2].SL_DATETIME}</td>
							        <td colspan=2> {$SCRAPERLOG[row2].SL_PAGENAME}</td>
							        <td > {$SCRAPERLOG[row2].SL_STATUS}</td>
							        <td colspan=3 > 
							        		
							        		{if $SCRAPERLOG[row2].SL_STATUS != "Matched"}
								        		<A target="_blank" href="index.php?action=ShowScraperLog&SCRAPERLOG[SL_KEY]={$SCRAPERLOG[row2].SL_KEY}"> Debug Messages </A>
							        		{/if}
					        		</td>
							      </tr>					      
								{/section}

							{else}
						      <tr>
						        <td colspan=8 > TOPS Upload Not Run</td>
						      </tr>					      
							
							{/if}

					      <tr>

			            <form action={$SESSION.PostAction} method=post class="form-inline" role="form">
						<input type=hidden name=action value="runTOPSUpload">
						<input type=hidden name=arCtl[OD_REF] value="{$arCtl.OD_REF}">
						<input type=hidden name=arCtl[AC_DATE] value="{$arCtl.AC_DATE}">
						<input type=hidden name=arCtl[AT_TOPSLOADED] value="{$arCtl.AT_TOPSLOADED}">
						<input type=hidden name=arCtl[CO_KEY] value="{$arCtl.CO_KEY}">
						<input type=hidden name=TOPS[TO_KEY] value="{$arACTIVITIES[row].TOPS.TO_KEY}">
						<input type=hidden name=TOPS[TO_ACKEY] value="{$arACTIVITIES[row].AC_KEY}">
						<input type=hidden name=TOPS[TO_ATKEY] value="{$arACTIVITIES[row].AT_KEY}">
								
								<td colspan=8  align=right>
								{if $arACTIVITIES[row].AT_TOPSLOADED != "Y"}
						      		 <button type="submit" name=arCtl[Run] value="Run" class="btn btn-primary ">Run TOPS Update</button> 
						      	{else}
						      		TOPS already updated
						      	{/if}
						      	
						      	</td>
					      </tr>
						</form>					


					      <tr>
					      	<thead>
					      		<td colspan=8> TOPS Data Upload Result </td>
					      	</thead>
					      </tr>					      
			            <form action={$SESSION.PostAction} method=post class="form-inline" role="form">
						<input type=hidden name=action value="updTOPSOverride">
						<input type=hidden name=arCtl[OD_REF] value="{$arCtl.OD_REF}">
						<input type=hidden name=arCtl[AC_DATE] value="{$arCtl.AC_DATE}">
						<input type=hidden name=arCtl[AT_TOPSLOADED] value="{$arCtl.AT_TOPSLOADED}">
						<input type=hidden name=arCtl[CO_KEY] value="{$arCtl.CO_KEY}">
						<input type=hidden name=INDIVIDUALS[IN_KEY] value="{$arACTIVITIES[row].IN_KEY}">
						<input type=hidden name=ATTENDEES[AT_KEY] value="{$arACTIVITIES[row].AT_KEY}">
					      <tr>
					        <td > Uploaded</td>
					        <td >{html_options class="form-control" name=ATTENDEES[AT_TOPSLOADED] options=$arYesNoList selected=$arACTIVITIES[row].AT_TOPSLOADED}</td>
					        <td >TOPS Ref</td>
					        <td > <input type=text name=INDIVIDUALS[IN_TOPSREF] class="form-control" value="{$arACTIVITIES[row].IN_TOPSREF}"></td>
					        <td colspan=2> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-primary ">Override</button></td>
					      </tr>
						</form>					
					      
					     {/if} 
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