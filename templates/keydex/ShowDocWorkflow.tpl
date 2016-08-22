{include file=$SESSION.ENVIRONMENT.EN_HEADER}

<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">

		<table class="table table-striped table-hover">

				<thead>
			
				<form action="{$SESSION.PostAction}" method="POST">
				<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
				<input type=hidden name=action value=UpdDocWorkflow>
					<tr  >
						<th colspan=4> Workflow Details </td>
					</tr>
					</thead>
					<tr  >
						<td> Name </td>
						<td> <input class="form-control" type=text size=20 name=KD_WORKFLOWS[WF_NAME] value="{$KD_WORKFLOWS.WF_NAME}"> </td>
						<td> Description </td>
						<td> <input class="form-control" type=text size=50 name=KD_WORKFLOWS[WF_DESCRIPTION] value="{$KD_WORKFLOWS.WF_DESCRIPTION}"> </td>
					</tr>
					<tr  >
						<td> Doc Type </td>
						<td> {html_options class="form-control"  name="KD_WORKFLOWS[WF_DDKEY]" options=$DocTypeList selected=$KD_WORKFLOWS.WF_DDKEY} </td>
						<td> Doc Type Status Field </td>
						<td> {html_options class="form-control" name="KD_WORKFLOWS[WF_STATUS_INDEX_NO]" options=$StatusIndexList selected=$KD_WORKFLOWS.WF_STATUS_INDEX_NO} </td>
					</tr>
					<tr  >
			  			<td colspan=4 align=right ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Update"> Update</button></td>
					</tr>
				</form>
			
				</table>
				<br>
				<table class="table table-bordered table-hover">
				<thead>
					<tr >	<th colspan=8 aligh='left'> Linked Workflow Docs</th>	</tr>
					</thead>
					<tr >
			  			<th align=left> Document Name </td>
			  			<th align=left> KeyStore Name </td>
			  			<th align=left> Link Key From</td>
			  			<th align=left> Link Key To </td>
			  			<th>  </td>
			  			<th> </td>
					</tr>
					
					{section name=row loop=$arKD_WORKFLOWS_LINKED_DOCTYPES}
				
					<form action="{$SESSION.PostAction}" method="POST">
					<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
					<input type=hidden name=KD_WORKFLOWS_LINKED_DOCTYPES[WFLD_WFKEY] value={$KD_WORKFLOWS.WF_KEY}>
					<input type=hidden name=KD_WORKFLOWS_LINKED_DOCTYPES[WFLD_KEY] value={$arKD_WORKFLOWS_LINKED_DOCTYPES[row].WFLD_KEY}>
					<input type=hidden name=action value=UpdWorkflowLinkedDoc>
						<tr class='{cycle values="RowNormal,RowWhite"}' height='30'>
				  			<td> {$arKD_WORKFLOWS_LINKED_DOCTYPES[row].DD_DESCRIPTION}</td>
				  			<td> {$arKD_WORKFLOWS_LINKED_DOCTYPES[row].DD_KEYSTORE_DOCTYPE}</td>
				  			<td > {html_options class="form-control" name="KD_WORKFLOWS_LINKED_DOCTYPES[WFLD_LINK_KEY_FROM]" options=$arKD_WORKFLOWS_LINKED_DOCTYPES[row].FieldList selected=$arKD_WORKFLOWS_LINKED_DOCTYPES[row].WFLD_LINK_KEY_FROM}</td>
				  			<td > {html_options class="form-control" name="KD_WORKFLOWS_LINKED_DOCTYPES[WFLD_LINK_KEY]" options=$DocIndexList selected=$arKD_WORKFLOWS_LINKED_DOCTYPES[row].WFLD_LINK_KEY}</td>
				  			<td ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Upd"> Upd</button></td>
					</form>
					<form action="{$SESSION.PostAction}" method="POST">
					<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
					<input type=hidden name=KD_WORKFLOWS_LINKED_DOCTYPES[WFLD_WFKEY] value={$KD_WORKFLOWS.WF_KEY}>
					<input type=hidden name=KD_WORKFLOWS_LINKED_DOCTYPES[WFLD_KEY] value={$arKD_WORKFLOWS_LINKED_DOCTYPES[row].WFLD_KEY}>
					<input type=hidden name=action value=DelWorkflowLinkedDoc>
				  			<td ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Remove"> Remove</button></td>
						</tr>
					</form>
				
					{/section}
			
					<tr height='30' >	<th colspan=8 align='left'> Add Document to Workflow</th>	</tr>
				
					<tr >
						<form action="{$SESSION.PostAction}" method="POST">
						<input type=hidden name=KD_WORKFLOWS_LINKED_DOCTYPES[WFLD_KEY] value=>
						<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
						<input type=hidden name=KD_WORKFLOWS_LINKED_DOCTYPES[WFLD_WFKEY] value={$KD_WORKFLOWS.WF_KEY}>
						<input type=hidden name=action value=UpdWorkflowLinkedDoc>
				  			<td colspan=4> {html_options class="form-control" name="KD_WORKFLOWS_LINKED_DOCTYPES[WFLD_DDKEY]" options=$DocTypeList }</td>
				  			<td ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Add"> Add</button></td>
						</form>
					</tr>
			
				</table>
				
				<br>
					<table class="table table-bordered table-hover">
					<thead>
					<tr class="Title" >	<th colspan=8 > Workflow Parameter Tables</th>	</tr>
					</thead>
			
					<tr  height='30'>
			  			<th align=left> List Name </td>
			  			<th align=left> Doc Link Key </td>
			  			<th align=left> Value Name </td>
			  			<th> </td>
			  			<th> </td>
					</tr>
			
					{section name=row loop=$arKD_WORKFLOWS_LINKED_PARAS}
				
					<form action="{$SESSION.PostAction}" method="POST">
					<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
					<input type=hidden name=KD_WORKFLOWS_LINKED_PARAS[WFLP_WFKEY] value={$KD_WORKFLOWS.WF_KEY}>
					<input type=hidden name=KD_WORKFLOWS_LINKED_PARAS[WFLP_KEY] value={$arKD_WORKFLOWS_LINKED_PARAS[row].WFLP_KEY}>
					<input type=hidden name=action value=UpdWorkflowLinkedParas>
						<tr  height='30'>
				  			<td> {$arKD_WORKFLOWS_LINKED_PARAS[row].WFP_DESCRIPTION} </td>
				  			<td> {html_options class="form-control" name="KD_WORKFLOWS_LINKED_PARAS[WFLP_CODE_NAME]" options=$DocIndexList selected=$arKD_WORKFLOWS_LINKED_PARAS[row].WFLP_CODE_NAME} </td>
				  			<td> {$arKD_WORKFLOWS_LINKED_PARAS[row].WFLP_VALUE_NAME}</td>
				  			<td ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Upd"> Upd</button></td>
						</form>
					<form action="{$SESSION.PostAction}" method="POST">
					<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
					<input type=hidden name=KD_WORKFLOWS_LINKED_PARAS[WFLP_WFKEY] value={$KD_WORKFLOWS.WF_KEY}>
					<input type=hidden name=KD_WORKFLOWS_LINKED_PARAS[WFLP_KEY] value={$arKD_WORKFLOWS_LINKED_PARAS[row].WFLP_KEY}>
					<input type=hidden name=action value=DelWorkflowLinkedParas>
				  			<td ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Remove"> Remove</button></td>
						</tr>
					</form>
				
					{/section}
			
					<tr  >	<th colspan=8 align='left'> Add Parameter Table to Workflow</th>	</tr>
				
					<tr >
						<form action="{$SESSION.PostAction}" method="POST">
						<input type=hidden name=KD_WORKFLOWS_LINKED_PARAS[WFLP_KEY] value=>
						<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
						<input type=hidden name=KD_WORKFLOWS_LINKED_PARAS[WFLP_WFKEY] value={$KD_WORKFLOWS.WF_KEY}>
						<input type=hidden name=action value=UpdWorkflowLinkedParas>
				  			<td > {html_options class="form-control" name="KD_WORKFLOWS_LINKED_PARAS[WFLP_WFPKEY]" options=$ParaTypeList }</td>
				  			<td> {html_options class="form-control" name="KD_WORKFLOWS_LINKED_PARAS[WFLP_CODE_NAME]" options=$DocIndexList } </td>
				  			<td> <input class="form-control" type=text size=20 name=KD_WORKFLOWS_LINKED_PARAS[WFLP_VALUE_NAME] > </td>
				  			<td ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Add"> Add</button></td>
						</form>
					</tr>
			
				</table>
				
			
				<br>
				<table >
					<tr  >	<th colspan=8 aligh='left'> Workflow Steps & Status</th>	</tr>
						<tr >
							<th align=left> Seq </td>
							<th align=left> Description </td>
				  			<th align=left> End Status </td>
				  			<th align=left> Success Action </td>
				  			<th align=left> Fail Action </td>
							<td colspan=5> </td>
						</tr>
					{section name=row loop=$arKD_WORKFLOWS_STEPS}
					<form action="{$SESSION.PostAction}" method="POST">
					<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
					<input type=hidden name=KD_WORKFLOWS_STEPS[WFS_KEY] value={$arKD_WORKFLOWS_STEPS[row].WFS_KEY}>
					<input type=hidden name=action value=UpdDocWorkflowSteps>
						<tr >
							<td>  <input type=text class="form-control" size=2 name=KD_WORKFLOWS_STEPS[WFS_SEQ] value="{$arKD_WORKFLOWS_STEPS[row].WFS_SEQ}"> </td>
							<td>  <input type=text class="form-control" size=30 name=KD_WORKFLOWS_STEPS[WFS_DESCRIPTION] value="{$arKD_WORKFLOWS_STEPS[row].WFS_DESCRIPTION}"> </td>
							<td>  <input type=text class="form-control" size=10 name=KD_WORKFLOWS_STEPS[WFS_STATUS] value="{$arKD_WORKFLOWS_STEPS[row].WFS_STATUS}"> </td>
							<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS[WFS_SUCCESSFUNCTION]" options=$ActionList selected=$arKD_WORKFLOWS_STEPS[row].WFS_SUCCESSFUNCTION } </td>
							<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS[WFS_FAILFUNCTION]" options=$ActionList selected=$arKD_WORKFLOWS_STEPS[row].WFS_FAILFUNCTION } </td>
							<td colspan=2><button type="submit" class="btn btn-default " name=arCtl[Run] value="Upd"> Upd</button></td>
					</form>
					<form action="{$SESSION.PostAction}"p method="POST">
					<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
					<input type=hidden name=KD_WORKFLOWS_STEPS[WS_KEY] value={$arKD_WORKFLOWS_STEPS[row].WFS_KEY}>
					<input type=hidden name=action value=DelDocWorkflowSteps>
							<td><button type="submit" class="btn btn-default " name=arCtl[Run] value="Delete"> Delete</button></td>
						</tr>		
						</form>
						<tr >
						<td colspan=10>
							<table class="table table-bordered table-hover">

								{assign var=arKD_WORKFLOWS_STEPS_RULES value=$arKD_WORKFLOWS_STEPS[row].RULES}

								{if $arKD_WORKFLOWS_STEPS[row].WFS_TYPE == "F"}
									<tr  >
										<th >  </td>
										<th colspan="5" align="left"> {$arKD_WORKFLOWS_STEPS_RULES.0.TEXT} </td>
									</tr>
								
								{else}
									<tr  >
										<th >  </td>
										<th colspan="5" align="left"> Rules </td>
									</tr>
									<tr  >
											{section name=row2 loop=$arKD_WORKFLOWS_STEPS_RULES}
											<form action="{$SESSION.PostAction}" method="POST">
											<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
											<input type=hidden name=KD_WORKFLOWS_STEPS[WFS_KEY] value={$arKD_WORKFLOWS_STEPS[row].WFS_KEY}>
											<input type=hidden name=KD_WORKFLOWS_STEPS_RULES[WFSR_KEY] value={$arKD_WORKFLOWS_STEPS_RULES[row2].WFSR_KEY}>
											<input type=hidden name=action value=UpdDocWorkflowStepRules>

											<tr >
													<td> </td>
													<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS_RULES[WFSR_SOURCE1]" options=$SourceList selected=$arKD_WORKFLOWS_STEPS_RULES[row2].WFSR_SOURCE1 } </td>
													<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS_RULES[WFSR_OPERATOR]" options=$OperatorList  selected=$arKD_WORKFLOWS_STEPS_RULES[row2].WFSR_OPERATOR} </td>
													<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS_RULES[WFSR_SOURCE2]" options=$SourceList  selected=$arKD_WORKFLOWS_STEPS_RULES[row2].WFSR_SOURCE2} </td>
													<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS_RULES[WFSR_JOIN]" options=$JoinList  selected=$arKD_WORKFLOWS_STEPS_RULES[row2].WFSR_JOIN} </td>
													<td><button type="submit" class="btn btn-default " name=arCtl[Run] value="Upd"> Upd</button></td>
	
											</form>

												{/section}					
							
												<form action="{$SESSION.PostAction}" method="POST">
												<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$KD_WORKFLOWS.WF_KEY}>
												<input type=hidden name=KD_WORKFLOWS_STEPS[WS_KEY] value={$arKD_WORKFLOWS_STEPS[row].WFS_KEY}>
												<input type=hidden name=KD_WORKFLOWS_STEPS_RULES[WFSR_WFSKEY] value={$arKD_WORKFLOWS_STEPS[row].WFS_KEY}>
												<input type=hidden name=action value=UpdDocWorkflowStepRules>
												<tr >
													<td>  </td>
													<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS_RULES[WFSR_SOURCE1]" options=$SourceList} </td>
													<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS_RULES[WFSR_OPERATOR]" options=$OperatorList} </td>
													<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS_RULES[WFSR_SOURCE2]" options=$SourceList} </td>
													<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS_RULES[WFSR_JOIN]" options=$JoinList} </td>
													<td><button type="submit" class="btn btn-default " name=arCtl[Run] value="Add"> Add</button></td>
												</tr>
											</form>
									{/if}
								</table>
						</td></tr>
							
						{/section}
			
					<tr class='Title' >	<th colspan=8 aligh='left'> Add New Workflow Step</th>	</tr>
				
					<tr >
						<form action="{$SESSION.PostAction}" method="POST">
						<input type=hidden name=KD_WORKFLOWS[WF_KEY] value="{$KD_WORKFLOWS.WF_KEY}">
						<input type=hidden name=KD_WORKFLOWS_STEPS[WFS_WFKEY] value={$KD_WORKFLOWS.WF_KEY}>
						<input type=hidden name=action value=UpdDocWorkflowSteps>
							<td>  <input type=text class="form-control" size=2 name=KD_WORKFLOWS_STEPS[WFS_SEQ]> </td>
							<td>  <input type=text class="form-control" size=30 name=KD_WORKFLOWS_STEPS[WFS_DESCRIPTION]> </td>
							<td>  <input type=text class="form-control" size=15 name=KD_WORKFLOWS_STEPS[WFS_STATUS]> </td>
							<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS[WFS_SUCCESSFUNCTION]" options=$ActionList  } </td>
							<td> {html_options class="form-control" name="KD_WORKFLOWS_STEPS[WFS_FAILFUNCTION]" options=$ActionList  } </td>
							<td><button type="submit" class="btn btn-default " name=arCtl[Run] value="Add"> Add</button></td>
							
						</form>
					</tr>
			
				</table>

  </div>
  <div class="col-sm-1"></div>
</div>
{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
