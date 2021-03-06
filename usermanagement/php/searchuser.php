<?php
/*
This module searchs for a user used when updating a user. If user
is found sucessfully it returns to data of the user to be used in updateuser.php

Uses: processfield.php, checkfieldlength.php, checkfieldempty.php, dbconnect.php, gettableid.php, executecommand.php
*/
//Inserts general functions
require('processfield.php');
require('checkfieldlength.php');
require('checkfieldempty.php');

//Create errorcode array that hold status of errors and messages
$errorcode = array(true,'');

//Processes fields text
$username = strtolower(processField($_POST['username']));

//Check that fields arent empty
$errorcode = checkFieldEmpty($username,'Please enter an username <br/>',$errorcode);

//Checks that fields are appropriate size
$errorcode = checkFieldLength(24,$username,"Please enter an username with less then 28 characters <br/>",$errorcode);

if($errorcode[0] == 'true') {
	require('../../database/dbconnect.php');
	require('../../database/gettableid.php');
	require('../../database/executecommand.php');
	
	//Establish connection to database
	$conn = dbConnect();
	
	$row = findUsername($conn,$username,$errorcode);

	//Closes connection
	oci_close($conn);
	
	if($row[0][0] == 'true') {
		echo json_encode(array('status'=>$row[0][0],'message'=>'User Found','username'=>$row[1][0][0],'password'=>$row[1][0][1],'class'=>$row[1][0][2],'personid'=>$row[1][0][3],'dateregistered'=>$row[1][0][4]));
	}
	else {
		echo json_encode(array('status'=>$row[0][0],'message'=>$row[0][1]));
	}
}
else {
	//Returns status of .php code and messages
	echo json_encode(array('status'=>$errorcode[0],'message'=>$errorcode[1]));
}

function findUsername($conn,$username,$errorcode) {

	//Executes sql command
	$num = executeCommand($conn,'SELECT u.user_name, u.password, u.class, u.person_id, u.date_registered FROM users u WHERE u.user_name =\''.$username.'\'');
	
	//If oci_parse finds email in use return false and alert user
	if($num[0][0] != $username) {
		$errorcode[1] = 'Username for user doesnt exist';
		$errorcode[0] = false;	
	}
	return array($errorcode,$num);
}

?>
