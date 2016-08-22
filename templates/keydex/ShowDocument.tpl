{include file=$SESSION.ENVIRONMENT.EN_HEADER}


   <div id="page-wrapper">
					<BR>
	<div class="row">
	
		<div class="col-sm-6">
	
				<table class="table table-bordered table-hover" >
					<tr>
					    <td  colspan="2" align="center" > 
	
							{if $KD_DOCUMENT.KDU_KEY == ""}
							
								No document selected
								
							{else}
					    
					    <img src={$KD_DOCUMENT.PNG} width=600px>
					    
							{/if}
					    
					      </td>  
					</tr>
		
				</table>
	
		</div>
	
		<div class="col-sm-6">
				<br>
		
				{section name=row loop=$arDocTypeKeys}
				
					<div  id="{$arDocTypeKeys[row].Id}" class="DocDetailsDiv">
						<form action="{$SESSION.PostAction}" method="POST" >
						<input type=hidden name=action value="DocumentLoadUpdate">
						<input type=hidden name=KD_DOCUMENT[KDU_KEY] value="{$KD_DOCUMENT.KDU_KEY}">
						<input type=hidden name=KD_DOCUMENT[KDU_DDKEY] value="{$arDocTypeKeys[row].DD_KEY}">
						<input type=hidden name=KD_DOCUMENT[KDU_DOCTYPE] value="{$arDocTypeKeys[row].Name}">
		
						<table class="table table-bordered table-hover">
		
							{assign var=arKeys value=$arDocTypeKeys[row].Keys }
							{section name=keyrow loop=$arKeys}
								<tr>
									<td align="left"> {$arKeys[keyrow].Name} </td>
									{if $arKeys[keyrow].Options }
										<td > {html_options name="arCtl[I][$arKeys[keyrow].Name]" options=$arKeys[keyrow].Options } </TD>
									{else}
										<td>
											{if $arKeys[keyrow].ReadOnly == "Y"}
												<input type=text name="arCtl[I][{$arKeys[keyrow].Name}]" value="{$arKeys[keyrow].Value}" disabled size=40 > 
												<input type=hidden name="arCtl[I][{$arKeys[keyrow].Name}]" value="{$arKeys[keyrow].Value}"  > 
											
											{else}
											
												<input type=text name="arCtl[I][{$arKeys[keyrow].Name}]" value="{$arKeys[keyrow].Value}" size=40 > 
											
											{/if}
										 
										</td>
									{/if}
		
								</tr>
							{/section}
								<tr>
									<td align="left"> Status </td>
									<td>
										 		{html_options name="arCtl[I][$arKeys[keyrow].Name]" selected=$arIndexKey.Value options=$WFStatusList}

										</td>
								</tr>
		
							<tr >
							    <td align="center" colspan="2" ><button type="submit" > Save  </button> </td>  
							</tr>
		
						</table>
		
						</form>	
					</div>			
				{/section}
		
			</div>
		</div>


{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
