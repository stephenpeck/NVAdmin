{include file=$SESSION.ENVIRONMENT.EN_HEADER} <div class="row">  <div class="col-sm-4" ></div>  <div class="col-sm-4" >			<BR>			<form role="form" action='{$SESSION.PostAction}' method='post' >				<input type="hidden" name="action" value="Login" class="form-control">						<table class="table table-bordered">				<tr><td>					  <div class="form-group">					    <label class="control-label col-sm-6" for="Logon">Login:</label>					    <div class="col-sm-6">					      <input text=text name="USERS[US_LOGON]" class="form-control" id="focusedInput" placeholder="Enter Login">					    </div>					  </div>				</td></tr>				<tr><td>				  <div class="form-group">				    <label class="control-label col-sm-6" for="pwd">Password:</label>				    <div class="col-sm-6">				      <input type="password" name="USERS[US_PASSWORD]" class="form-control" id="focusedInput" placeholder="Enter password">				    </div>				  </div>				</td></tr>				<tr><td>				  <div class="form-group">				    <div class="col-sm-12">				      <button type="submit" class="btn btn-primary btn-block">Logon</button>				    </div>				  </div>				</td></tr>				</table>			</form>    </div>  <div class="col-sm-4" ></div></div>	

{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
