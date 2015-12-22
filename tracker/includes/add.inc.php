<?php # Script add.inc.php
// This script adds a participant to an encounter and redirects to the edit_init_order page

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	require ('config.inc.php');
	session_name('ENCOUNTER_TRACKER');
	session_start(); // Start the session.
	require ('login_functions.inc.php');
	
	// If no session value is present, redirect the user:
	// Also validate the HTTP_USER_AGENT!
	if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']) )) {
		redirect_user('../login.php');	
	} else {
		$uid = $_SESSION['user_id'];
		$ul = $_SESSION['user_level'];
	}

	require (MYSQL2);
	
	// Validate the creature id:
	if (empty($_POST['cid'])) {
		$errors[] = 'No creature';
	} else {
		$cid = $mysqli->real_escape_string(trim($_POST['cid']));
	}
	
	// Validate the encounter id:
	if (empty($_POST['cid'])) {
		$errors[] = 'No encounter';
	} else {
		$eid = $mysqli->real_escape_string(trim($_POST['eid']));
	}
		
	// Validate the name:
	if (empty($_POST['name'])) {
		$errors[] = 'No name';
	} else {
		$n = $mysqli->real_escape_string(trim($_POST['name']));
	}

	// Validate the init:
	if (empty($_POST['init'])) {
		$errors[] = 'No description';
	} else {
		$i = $mysqli->real_escape_string(trim($_POST['init']));
	}	
	
	// ensure user has privileges to create entity type
	// only game masters should be doing this.
	if ( $ul != 2 ){
		$errors[] = 'Insufficient privileges';
	}
	
	if (empty($errors)) { // If everything's OK.
			
		// begin constructing the query:
		$q = "INSERT INTO participants (name, creature_id, encounter_id, initiative) VALUES ('" . $n . "', " . $cid . ", " . $eid . ", " . $i . ")";
		
		if ($mysqli->query ($q)) {
			redirect_user('../edit_init_order.php?x=' . $eid);
		} else {// echo $q;
		}
		
	} else {
		foreach ($errors as $e) {
			echo $e;
		}		
	} // End of empty($errors) IF.
	
	// Close the connection:
	$mysqli->close();
	unset($mysqli);

} // End of the main submit conditional.

?>