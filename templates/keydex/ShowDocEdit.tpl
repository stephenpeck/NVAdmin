{include file=$SESSION.ENVIRONMENT.EN_HEADER}


   <div id="page-wrapper">
		<div class="row  panel panel-default">

				            				
					{section name=row loop=$arWFSteps}

						<A href="index.php?MenuKey=74&arCtl[Run]=Y&arCtl[WF_KEY]={$arWFSteps[row].WFS_WFKEY}&arCtl[WFS_STATUS]={$arWFSteps[row].STATUS}" >
			                <div class="col-md-2">
			                    <div class="{$arWFSteps[row].PanelStyle}">
			                        <div class="panel-heading">
			                            <div class="row">
			                                <div class="col-xs-2">
			                                    <i class="fa fa-tasks fa-2x"></i>
			                                </div>
			                                <div class="col-xs-10 text-center">
			                                    <div >{$arWFSteps[row].WFS_NEXTACTION} ({$arWFSteps[row].TOTDOC})</div>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
			                </div>
						</A>
						
		            {/section}
				            				
	
		</div>
		<div class="row">
		
			<div class="col-sm-5">
				<div class="row panel panel-default">
					<div class="col-sm-6 panel-text-heading">
						Type:   
					</div>
					<div class="col-sm-6 panel-text-heading">
						{$KD_DOCUMENT.KDU_DOCTYPE} 
					</div>
				</div>
				<div class="row panel panel-default">
					<div class="col-sm-6 panel-text-heading">
						Action:   
					</div>
					<div class="col-sm-6 panel-text-heading">
						{$KD_DOCUMENT.NEXTACTION} 
					</div>
				</div>

					<br>
				<div class="row panel panel-default">
					<div class="col-sm-5">
			
						{section name=row loop=$arDocTypeKeys}
					
							<form action="{$SESSION.PostAction}" method="POST" >
							<input type=hidden name=action value="DocumentKeyUpdate">
							<input type=hidden name=arCtl[Screen] value="ShowDocEdit">
							{if $KD_DOCUMENT.NEXTACTION == "Completed"}
								<input type=hidden name=arCtl[Complete] value="Y">
							{else}
								<input type=hidden name=arCtl[Complete] value="N">
							{/if}
							<input type=hidden name=arCtl[WF_KEY] value="{$arCtl.WF_KEY}">
							<input type=hidden name=arCtl[WFS_STATUS] value="{$arCtl.WFS_STATUS}">
							<input type=hidden name=KD_DOCUMENT[KDU_DDKEY] value="{$KD_DOCUMENT.KDU_DDKEY}">
							<input type=hidden name=KD_DOCUMENT[KDU_KEY] value="{$KD_DOCUMENT.KDU_KEY}">
							<input type=hidden name=KD_DOCUMENT[KDU_DDKEY] value="{$arDocTypeKeys[row].DD_KEY}">
							<input type=hidden name=KD_DOCUMENT[KDU_DOCTYPE] value="{$arDocTypeKeys[row].Name}">
							{if $KD_DOCUMENT.KDU_DPKEY != ""}
								<input type=hidden name=KD_DOCUMENT[KDU_DPKEY] value="{$KD_DOCUMENT.KDU_DPKEY}">
							{else}
								<input type=hidden name=KD_DOCUMENT[KDU_DPKEY] value="{$arCtl.DP_KEY}">
							{/if}
		
							<div class="row">
								<div class="col-sm-12 panel-text-heading"> Document Keys for {$KD_DOCUMENT.KDU_KEY}</div>
							</div>
							<div class="row">
								<div class="col-sm-12 panel-text-heading"> &nbsp; </div>
							</div>
			
								{assign var=arKeys value=$arDocTypeKeys[row].Keys }
								{section name=keyrow loop=$arKeys}

									<div class="row">
										<div class="col-sm-6"> {$arKeys[keyrow].Name} </div>
										{if $arKeys[keyrow].Options }
											<div class="col-sm-6"> {html_options class="form-control" name="arCtl[I]['{$arKeys[keyrow].Name}']" options=$arKeys[keyrow].Options } </div>
										{else}
											<div class="col-sm-6"> 
												{if $arKeys[keyrow].ReadOnly == "Y"}
													<input class="form-control" type=text name="arCtl[I][{$arKeys[keyrow].Name}]" value="{$arKeys[keyrow].Value}" disabled size=40 > 
													<input class="form-control" type=hidden name="arCtl[I][{$arKeys[keyrow].Name}]" value="{$arKeys[keyrow].Value}"  > 
												
												{else}
												
													<input class="form-control" type=text name="arCtl[I][{$arKeys[keyrow].Name}]" value="{$arKeys[keyrow].Value}" size=40 > 
												
												{/if}
											 
											</div>
										{/if}
										
									</div>

								{/section}
	
									{if $arCtl.USF_STATUSUPDATE == "Y" }
									<div class="row">
										<div class="col-sm-6"> Status Override </div>
										<div class="col-sm-6"> 
											<td> {html_options class="form-control"  name="arCtl[I][Status]" options=$arStatusList selected=$KD_DOCUMENT.StatusValue } </div>
										</div>
									
									{/if}
	
								<div class="row">
									<div class="col-sm-12 panel-text-heading"> &nbsp; </div>
								</div>
			
								<div class="row">
									<div class="col-sm-6"> <button class="btn btn-primary" type="submit" name="arCtl[Next]" value="ShowNext"> Save and Get Next Doc </button> </div>
									<div class="col-sm-6"> <button class="btn btn-primary" type="submit" name="arCtl[Next]" value="ShowHome"> Save </button> </div>
								</div>
			
			
							</form>	
						{/section}
			
					</div>
					<div class="col-sm-7">
						{if $arCtl.USF_STATUSUPDATE == "Y" and $KD_DOCUMENT.KDU_INDEX4 != "New" and $KD_DOCUMENT.KDU_INDEX4 != "PO Coded" and $KD_DOCUMENT.KDU_INDEX4 != "Coded" }
				
								<div class="row panel panel-default">
									<div class="col-sm-12 panel-text-heading">
										Document Item Analysis
									</div>   
								</div>
								<div class="row panel panel-default">
									<div class="row">
										<div class="col-sm-12 panel-text-heading"> &nbsp; </div>
									</div>
									<div class="row">
				
										<div class="col-sm-2">  Seq </div>
										<div class="col-sm-4">  Description </div>
										<div class="col-sm-2">  Value </div>
										<div class="col-sm-2">  Code </div>
										<div class="col-sm-2">   </div>
									</div>
								
										{assign var=arItems value=$KD_DOCUMENT.ITEMS}
										{section name=row loop=$arItems}
											<form action="{$SESSION.PostAction}" method="POST" >
												<input type=hidden name=action value="DelDocumentItems">
												<input type=hidden name=arCtl[Screen] value="ShowDocEdit">
												<input type=hidden name=arCtl[WF_KEY] value="{$arCtl.WF_KEY}">
												<input type=hidden name=arCtl[WFS_STATUS] value="{$arCtl.WFS_STATUS}">
												<input type=hidden name=KD_DOCUMENT[KDU_DDKEY] value="{$KD_DOCUMENT.KDU_DDKEY}">
												<input type=hidden name=KD_DOCUMENT[KDU_KEY] value="{$KD_DOCUMENT.KDU_KEY}">
												<input type=hidden name=KD_DOCUMENT_ITEMS[KDI_KEY] value="{$arItems[row].KDI_KEY}">
												<input type=hidden name=KD_DOCUMENT[KDU_DDKEY] value="{$arDocTypeKeys[row].DD_KEY}">
												<input type=hidden name=KD_DOCUMENT[KDU_DOCTYPE] value="{$arDocTypeKeys[row].Name}">
												<input type=hidden name=KD_DOCUMENT[KDU_DPKEY] value="{$arCtl.DP_KEY}">
												<div class="row">
													<div class="col-sm-2">  {$arItems[row].KDI_SEQ} </div>
													<div class="col-sm-4">  {$arItems[row].KDI_DESCRIPTION} </div>
													<div class="col-sm-2">  {$arItems[row].KDI_ITEMVALUE} </div>
													<div class="col-sm-2">  {$arItems[row].KDI_ITEMCODE} </div>
													<div class="col-sm-2">  <button class="btn btn-primary" type="submit"> Del </button> </div>
												</div>
											</form>
										{/section}
						
									<form action="{$SESSION.PostAction}" method="POST" >
									<input type=hidden name=action value="UpdDocumentItems">
									<input type=hidden name=KD_DOCUMENT_ITEMS[KDI_KEY] value="">
									<input type=hidden name=arCtl[Screen] value="ShowDocEdit">
									<input type=hidden name=arCtl[WF_KEY] value="{$arCtl.WF_KEY}">
									<input type=hidden name=arCtl[WFS_STATUS] value="{$arCtl.WFS_STATUS}">
									<input type=hidden name=KD_DOCUMENT[KDU_DDKEY] value="{$KD_DOCUMENT.KDU_DDKEY}">
									<input type=hidden name=KD_DOCUMENT[KDU_KEY] value="{$KD_DOCUMENT.KDU_KEY}">
									<input type=hidden name=KD_DOCUMENT[KDU_DDKEY] value="{$arDocTypeKeys[row].DD_KEY}">
									<input type=hidden name=KD_DOCUMENT[KDU_DOCTYPE] value="{$arDocTypeKeys[row].Name}">
									<input type=hidden name=KD_DOCUMENT[KDU_DPKEY] value="{$arCtl.DP_KEY}">
									<div class="row">
										<div class="col-sm-2">  <input class="form-control" type=text name="KD_DOCUMENT_ITEMS[KDI_SEQ]" > </div>
										<div class="col-sm-4">  <input class="form-control" type=text name="KD_DOCUMENT_ITEMS[KDI_DESCRIPTION]" > </div>
										<div class="col-sm-2">  <input class="form-control" type=text name="KD_DOCUMENT_ITEMS[KDI_ITEMVALUE]" > </div>
										<div class="col-sm-2">  <input class="form-control" type=text name="KD_DOCUMENT_ITEMS[KDI_ITEMCODE]" > </div>
										<div class="col-sm-2"> <button class="btn btn-primary" type="submit"> Add </button> </div>
									</div>
									</form>
				
								</div>
				
							{/if }
					</div>
				</div>

				<div class="row panel panel-default">
					<div class="col-sm-4"> 
					
						<div class="col-sm-12 panel-text-heading">
							Related Documents:   
						</div>
						<div class="col-sm-12 ">
								{assign var=arDocs value=$arWFData.DOCLIST }
								{section name=docrow loop=$arDocs}
									<div class="row">
										<div class="col-sm-12">  <A target="_blank" HREF="index.php?action=ShowDocument&KD_DOCUMENT[KDU_KEY]={$arDocs[docrow].KDU_KEY}">{$arDocs[docrow].KDU_DOCTYPE}</A> </div>
									</div>
								{/section }
						
						</div>					
					</div>
					<div class="col-sm-8"> 
					
						<div class="col-sm-12 panel-text-heading">
							Related Parameters:   
						</div>
						<div class="col-sm-12">
								{assign var=arParas value=$arWFData.PARALIST }
								{section name=docrow loop=$arParas}
									<div class="row">
										<div class="col-sm-6">  {$arParas[docrow].WPI_ATTRIBUTE1} </div>
										<div class="col-sm-6">  {$arParas[docrow].WPI_ATTRIBUTE2} </div>
									</div>
								{/section }
						</div>
					
					</div>
				</div>
			</div>
	
	
			<div class="col-sm-7">
		
					<div class="docview" >
						    
						    <img src={$KD_DOCUMENT.PNG} >
						    
					</div>				
					
			</div>

		</div>

{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
