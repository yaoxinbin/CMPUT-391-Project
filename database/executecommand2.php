<?php
function executeCommand($conn,$sql) {
	//Prepares sql using connection and returns statement identifier	
	$stid = oci_parse($conn,$sql);

	//Executes statement returned from oci_parse
	$res = oci_execute($stid);

	//If error, retrieve the error using oci_error() function and output an error message
	if (!$res) {
		$err = oci_error($stid);
		echo htmlentities($err['message']);
		return array(null,false);
	}
	else{
		//Fetches results of oci_parse and outputs it as array
		$num = oci_fetch_all($stid,$res, null, null, OCI_FETCHSTATEMENT_BY_ROW + OCI_NUM); 

		// Frees the statement identifier
		oci_free_statement($stid);

		return array($res,true);
	}
}

?>
