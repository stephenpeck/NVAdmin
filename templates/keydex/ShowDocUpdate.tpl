{include file=$SESSION.ENVIRONMENT.EN_HEADER}


   <div id="page-wrapper">
					<BR>
	<div class="row">
	
		<div class="col-sm-6">
	
				<table class="table table-bordered table-hover" >
					<tr>
					    <td  colspan="2" align="center" > 
	
					    
					    <img src={$KD_DOCUMENT.PNG} width=600px>
					    
					    
					      </td>  
					</tr>
		
				</table>
	
			</form>
		</div>
	
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-3"></div>
				<div class="col-sm-6">
					<table class="table table-bordered table-hover" >
						<tr>
							<td> Select Document Type </td>
							<td > <select name="arCtl[DocTypeId]" id="SelectDocTypeId"> {html_options  options=$arDocTypes selected=$arCtl.DocTypeId} </select> </TD>
						</tr>
						<tr>
							<td colspan="2"> {$arCtl.Message} </TD>
						</tr>
					</table>
				</div>
				<div class="col-sm-3"></div>
			</div>
				<br>
		
				{section name=row loop=$arDocTypeKeys}
				
					<div  id="{$arDocTypeKeys[row].Id}" class="DocDetailsDiv" style="display:none;">
						<form action="{$SESSION.PostAction}" method="POST" >
						<input type=hidden name=action value="DocumentKeyUpdate">
						<input type=hidden name=KD_DOCUMENT[KDU_KEY] value="{$KD_DOCUMENT.KDU_KEY}">
						<input type=hidden name=KD_DOCUMENT[KDU_DDKEY] value="{$arDocTypeKeys[row].DD_KEY}">
						<input type=hidden name=KD_DOCUMENT[KDU_DOCTYPE] value="{$arDocTypeKeys[row].Name}">
						<input type=hidden name=KD_DOCUMENT[KDU_DPKEY] value="{$arCtl.DP_KEY}">
		
						<table class="table table-bordered table-hover">
							<tr ><td colspan="2"> Load a <b>{$arDocTypeKeys[row].Name} </b>({$arDocTypeKeys[row].DD_DESCRIPTION}) Document </td></tr>
		
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

		
							<tr >
							    <td align="center"><button type="submit" name="arCtl[Screen]" value="ShowHome"> Save Document Keys </button> </td>  
							    <td align="center" ><button type="submit" name="arCtl[Screen]" value="ShowDocumentLoad" > Save Document Keys & Load Another Doc </button> </td>  
							</tr>
		
						</table>
		
						</form>	
					</div>			
				{/section}
		
			</div>
		</div>


{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
