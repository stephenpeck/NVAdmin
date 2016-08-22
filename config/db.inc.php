<?php

 // Set Default Variables
    $MYSQL_ERRNO = '';
    $MYSQL_ERROR = '';

     // Connect to MySQl DB & Select default DB
    function db_connect($overrideDb = NULL){
        global $Connection;

        $dbhost         = getDBHost();
        $dbusername     = getDBUsername();
        $dbuserpassword = getDBUserPassword();

        if (isset($overrideDb)){
            $default_dbname = $overrideDb;
        }else{
            $default_dbname = getDBName();
        }

        if(isset($Connection) && is_resource($Connection)){
            return $Connection;     
        }

        if(!$Connection = mysqli_connect($dbhost, $dbusername, $dbuserpassword)){
                $MYSQL_ERRNO = 0;
                $MYSQL_ERROR = "Connection Failed to the host $dbhost";

                return 0;
        }else if(empty($dbname) AND !mysqli_select_db($Connection,$default_dbname)){
            $MYSQL_ERRNO = mysqli_errno($Connection);
            $MYSQL_ERROR = mysqli_error($Connection);
                return 0;
        }
          else return $Connection;
    }

     // DB Error Message Handling
    function sql_error($db){
               global $MYSQL_ERRNO, $MYSQL_ERROR;

               if(empty($MYSQL_ERROR)){
                    $MYSQL_ERRNO = mysqli_errno($db);
                    $MYSQL_ERROR = mysqli_error($db);
             }

               return "$MYSQL_ERRNO: $MYSQL_ERROR";
    }

    
    function sqlserver_connect($overrideDb = NULL){

    	global $SQLServerConnection;
    	
    	$dbhost         = getSQLDBHost();
    	$dbusername     = getSQLDBUsername();
    	$dbuserpassword = getSQLDBUserPassword();

    	// now select DB
    	if (isset($overrideDb)){
    		$default_dbname = $overrideDb;
    	}else{
    		$default_dbname = getSQLDBName();
    	}
    	 
    	
    	$connectionInfo = array( 
    				"Database"=>$default_dbname, 
    				"UID"=>$dbusername, 
    				"PWD"=>$dbuserpassword);
    	
    	
    	$SQLServerConnection = sqlsrv_connect( $dbhost, $connectionInfo);
    	
    	if( $SQLServerConnection ) {
    		//echo "Connection established.<br />";
    	}else{
    		echo "Connection could not be established.<br />";
    		die( print_r( sqlsrv_errors(), true));
    	}
    	
    	
    	return $SQLServerConnection;
    	
    	
    }
    
?>