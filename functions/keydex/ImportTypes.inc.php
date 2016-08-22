<?php
function importmanual($arCtl,$KD_DOCPATH,$KD_UPLOADS){

	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	// manual import file location, doc type and keys already held on doc record
	// so update documenty with upload log key

	$sql = "update KD_DOCUMENT set KDU_KUPKEY = '" . $KD_UPLOADS['KUP_KEY'] . "' where KDU_KUPKEY = 0 and KDU_DPKEY = " . $KD_DOCPATH['DP_KEY'];
	echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	error_message ( sql_error () );
	
	// get count, upload upload log and return count
	$FileCount = mysqli_affected_rows($mysqli);
	
	$KD_UPLOADS['KUP_TEXT'] = "Found " . $FileCount . " Doc Records";
	$KD_UPLOADS['KUP_DOCS_COUNT'] = $FileCount;
	UpdUploadLog($arCtl,$KD_UPLOADS);	
	
	return $FileCount;
	
}
function importftpput($arCtl,$KD_DOCPATH,$KD_UPLOADS){

	// DB Connection
	$mysqli = db_connect ( getDBName () );

	// find doc path
	$DocPath = getAppRoot() . "upload/" . $arCtl['ENVIRONMENTS']['EN_URL'] . $KD_DOCPATH['DP_DOCDIR'];
	
	echo "Looking in " . $DocPath . "\n";
	
	// set destination paths
	
	$loaddate = date ( 'dHis' );
	$loadperiod = date ( 'Ym' );
	$UploadDir = getAppRoot () . "docs/" .$arCtl['ENVIRONMENTS']['EN_URL'] . "/" . $KD_DOCPATH['DP_KEY'];
	$UploadDir2 = "/docs/" . $arCtl['ENVIRONMENTS']['EN_URL'] . "/" . $KD_DOCPATH['DP_KEY'];
	$LoadDir = $UploadDir . "/" . $loadperiod;
	$LoadDir2 = $UploadDir2 . "/" . $loadperiod;
	
	// copy file in to dir
	if (! file_exists ( $LoadDir )) {
		if (! mkdir ( $LoadDir )) {
			print "cannot create directory: " . $LoadDir . "\n";
		} else {
			 print "created directory: " . $LoadDir . "\n";
		}
	}
	
	if ($handle = opendir($DocPath)) {
		echo "Directory handle: $handle\n";
		echo "Entries:\n";
		$FileCount=0;
	
		/* This is the correct way to loop over the directory. */
		while (false !== ($entry = readdir($handle))) {

			$SubDirDocPath = $DocPath . $entry;
				
			echo $entry . " "  . is_dir($entry) . "\n";
			echo $entry . " "  . is_dir($SubDirDocPath) . "\n";
				
			if(is_dir($SubDirDocPath) and $entry != "." and $entry != ".."){
				
				// file is directory whi cwe are assuming to be a doctype
				
				echo $SubDirDocPath;
				
				if ($handle2 = opendir($SubDirDocPath)) {
					while (false !== ($filetoload = readdir($handle2))) {
						if ($filetoload != "." && $filetoload != "..") {
							
							$filetoload2 = str_replace(" ","",$filetoload);
							
							$FullFileName = $SubDirDocPath . "/" . $filetoload;
							echo $FullFileName . "\n";;
							
							// go into directory and find files and create full filenames
							$KD_DOCUMENT ['KDU_FILENAME'] = $LoadDir2 . "/" . $entry . "-" . $filetoload2;
							$KD_DOCUMENT ['KDU_SOURCEFILE'] = $LoadDir . "/" . $entry . "-" . $filetoload2;
							$KD_DOCUMENT ['KDU_IMAGENAME'] = $entry . "-" . $filetoload2;

							
							print_r($KD_DOCUMENT);
							
							// loop roudn each one moving and loading up (using doc type)
							// move form here to loaddir
							rename($FullFileName, $KD_DOCUMENT['KDU_SOURCEFILE'] );
											
							// update DB record
							$sql = "insert into KD_DOCUMENT (KDU_DATE, KDU_DPKEY,KDU_FILENAME,KDU_SOURCEFILE,KDU_IMAGENAME,KDU_STATUS,KDU_SOURCE,KDU_KUPKEY)
								values (now(),'" . $KD_DOCPATH ['DP_KEY'] . "','" . mysql_escape_string ( $KD_DOCUMENT ['KDU_FILENAME'] ) . "','" . mysql_escape_string ( $KD_DOCUMENT ['KDU_SOURCEFILE'] ) . "','" . mysql_escape_string ( $KD_DOCUMENT ['KDU_IMAGENAME'] ) . "','New','A','" . $KD_UPLOADS['KUP_KEY'] . "')";
							
							echo $sql . "\n";;
							
							$result = mysqli_query ( $mysqli,$sql );
							if (! $result)
								error_message ( sql_error () );
							
							$KD_DOCUMENT['KDU_KEY'] = mysqli_insert_id ($mysqli);
							
							// return back status
							
							$arCtl['WFL_TYPE'] = "DocLoad";
							$arCtl['WFL_SUMMARY'] = "FTP PUT Doc Load No " . $KD_DOCUMENT['KDU_KEY'] ;
							UpdWorkflowLog($arCtl,$KD_DOCUMENT);
							
							$FileCount++;
								
						}		
					}					
					
				}
				
			}
		}
	
		closedir($handle);
	}

	$KD_UPLOADS['KUP_TEXT'] = "Found " . $FileCount . " Doc Records";
	$KD_UPLOADS['KUP_DOCS_COUNT'] = $FileCount;
	UpdUploadLog($arCtl,$KD_UPLOADS);

	return $FileCount;

}	
	

?>