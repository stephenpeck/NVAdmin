{include file=$SESSION.ENVIRONMENT.EN_HEADER}


   <div id="page-wrapper">
					<BR>
	<div class="row">
	
		<div class="col-sm-6">
			<form action="{$SESSION.PostAction}" method="POST"  ENCTYPE="MULTIPART/FORM-DATA">
			<input type=hidden name=action value="DocumentLoad">
			<input type=hidden name=arCtl[Screen] value="ShowDoc">
	
				<table class="table table-bordered table-hover" >
					<tr>
					    <td  colspan="2" align="center" > 
	
							No document loaded please load doc to start
					    
					      </td>  
					</tr>
					<tr >
					    <td align="center" ><INPUT size=50 TYPE = "FILE" NAME="docimage" > </td>
					    <td align="center" ><button type="submit" > Load Document </button> </td>  
				 	</tr>
		
				</table>
	
			</form>
		</div>
	
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-3"></div>
				<div class="col-sm-6">
					
							No document loaded please load doc to start
	
				</div>
				<div class="col-sm-3"></div>
			</div>
				<br>
		
		
			</div>
		</div>


{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
