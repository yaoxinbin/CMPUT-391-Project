<?php
//Gets users data based on username
function getUserData($username){
	require('/compsci/webdocs/kjross/web_docs/database/dbconnect.php');
	require('/compsci/webdocs/kjross/web_docs/database/executecommand.php');

	//Establish connection to database
	$conn = dbConnect();

	//Executes sql command
	$num = executeCommand($conn,'SELECT user_name, password, class, date_registered FROM users WHERE user_name =\''.$username.'\'');

	return $num;
}
?>