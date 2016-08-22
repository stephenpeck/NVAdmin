<!doctype HTML>
<meta charset=utf-8>

<head>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>


  <link rel="stylesheet" href="/assets/css/admin-override.css">
 
 </head>
 <body>
  
<header role="banner" >


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10" >
		<nav role="navigation" >
		    <div class="navbar-header navbar-left">
		      <div class="navbar-brand" >Admin Menu</div>
		    </div>
			    <ul class="nav navbar-nav navbar-right">

					 {assign var=MENU value=$SESSION.Menu}
					 {section name=row loop=$MENU}

		                <li class="dropdown">
		
		                    <a class="dropdown-toggle" href="{$SESSION.POSTAction}?MenuKey={$MENU[row].MI_KEY}">{$MENU[row].Name}</A> 
		                    <a href="#" data-toggle="dropdown" class="dropdown-toggle" ><b class="caret"></b></a>
		
		                    <ul class="dropdown-menu">

								 {assign var=OPTIONS value=$MENU[row].Options}
								 {section name=row2 loop=$OPTIONS}

			                        <li><a href="{$SESSION.POSTAction}?MenuKey={$OPTIONS[row2].MI_KEY}">{$OPTIONS[row2].OptionName}</a></li>

								 {/section}	
		
		                    </ul>
		
		                </li>

					{/section}


		                <li >
		                    <a class="dropdown-toggle" href={$SESSION.POSTAction}?action=LogOff>Log Off </A>
		                </li>

				</ul>
		</nav>
	</div>
 	<div class="col-sm-1"></div>
</div>

</header>

<br>
<br>
<br>

<div class="fluid-container" style="min-height:800px">



<!--- End of Header --->