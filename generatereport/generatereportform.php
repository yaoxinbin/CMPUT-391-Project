<!--
This form is used by an administrator to generate a report based upon a patient
having a certain diagnosis. This from asks administrator to enter a diagnosis. After,
an adminstrator can press "Find Reports and this should produce a table of patient based
upon the search results of the diagnosis

Uses: searchrecords.php, generalstylesheets.css, jquery1.1.min.js

//-->
<?php session_start();
	//Checks login has been done and is an administrator
	if(isset($_SESSION['user_name'])){
		require('../login/getuserdata.php');
		//Obtaining user data
		$res = getUserData($_SESSION['user_name']);
		if($res[0][2] == 'a'){
?>
<html>
	<head>
	<link rel="stylesheet" type="text/css" href="../stylesheets/generalstylesheet.css">
	<script type="text/javascript" src="../jquery1.1.min.js"></script>
		<title>Generate Reports</title>
	</head>
	<body> 
		<div id="page-wrap">
			<div id="header">
				<div id="titlebar">
					<h1>Radiology Information System</h1>
				</div>
			</div>
			<div id="content-wrap" class="styleform">
				<h2><a class="button" href="../login/loginform.php">Back</a></h2>
				<h2>Generate Reports</h2>

				<form id='form1' name="form1" action='php/searchrecords.php' method="post" class='ajaxform'>
					<label><u>Find Report</u></label><br/>						
					<label for="diagnosis">Diagnosis:</label><input id="diagnosis" name="diagnosis" type="text"></br>
					<label for="sdate">Start Year (YYYY-MM-DD):</label><input id="sdate" name="sdate" type="text"></br>
					<label for="fdate">Finish Year (YYYY-MM-DD):</label><input id="fdate" name="fdate" type="text"></br>					
					<input type="submit" name="submit" value="Find reports">
				</form>

				<div id="alertbox">
				</div>
				<br/>
				
				<table id="report" border="1">
					<thead id="reportbody">
						<tr><th>Name</th><th>Address</th><th>Phone</th><th>Test Date</th></tr>
					</thead>
					<tbody>
					</tbody>
    				</table>

			</div>
			<div id="footer">
			</div>
		</div>


			
	</body>
	<script>
jQuery(document).ready(function(){


	jQuery('.ajaxform').submit( function() {
		$.ajax({
			url     : $(this).attr('action'),
			type    : $(this).attr('method'),
			data    : $(this).serialize(),
			success : function( data ) {
				//Parses JSON data 
				var data = $.parseJSON(data);

				//Resets input highlights
				$('input').css('border','1px solid #999');

				//Clears input box
				$('#diagnosis').val('');

				if(data['status'] == true) {
					//Setting parameters for alertbox
					$("#alertbox").css('color','green');

					//Clear table and readd rows
					$("#report > tbody").html("");

					var tbody = $('#report tbody');
					var cols = [0, 1, 2, 3];

					$.each(data.results, function(i, patient) {
  						var tr = $('<tr>');
  						$.each(cols, function(i, col) {
    							$('<td>').html(patient[col]).appendTo(tr);  
  						});
  						tbody.append(tr);
					});
 
					
	
				}
				else {
					$('#diagnosis').css('border','1px solid red');

					//Setting parameters for alertbox
					$("#alertbox").css('color','red');
					
				}	
				//Displays message in alert box
				$("#alertbox").html(data['message']);		
			},
			error   : function(jqXHR, textStatus, errorThrown){
				alert('Something wrong');
    				alert(jqXHR.status);
    				alert(errorThrown);
			}
		});
		return false;
	});


});
	
	</script>

</html>
<?php }else{ echo header('Location:../login/loginform.php');}}
	//Redirect to login if fails
	else {
		header('Location:../login/loginform.php');
	}
?>
