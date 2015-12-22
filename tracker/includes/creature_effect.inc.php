<?php # Script creature_effect.inc.php
// This script can be accessed via post or get and links/unlinks effects 
// from creatures
// When adding an effect, the submit button triggers the post method
// When removing an effect the link accesses the get method

require ('config.inc.php');
session_name('ENCOUNTER_TRACKER');
session_start(); // Start the session.
require ('login_functions.inc.php');
require (MYSQL2);
	
// If no session value is present, redirect the user:
// Also validate the HTTP_USER_AGENT!
if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']) )) {
	redirect_user('../login.php');	
} else {
	$ul = $_SESSION['user_level'];
}

// ensure user has privileges 
// only players and game masters should be doing this.
if ( !($ul == 1 || $ul == 2 )){
	$errors[] = 'Insufficient privileges';
}

// Check if the form has been submitted via post or get:
if ($_SERVER['REQUEST_METHOD'] == 'POST') { // add effect 
	
	// Validate the effect id:
	if (empty($_POST['efid'])) {
		$errors[] = 'No effect';
	} else {
		$efid = $mysqli->real_escape_string(trim($_POST['efid']));
	}
	
	// Validate the creature id:
	if (empty($_POST['cid'])) {
		$errors[] = 'No creature';
	} else {
		$cid = $mysqli->real_escape_string(trim($_POST['cid']));
	}
			
	if (empty($errors)) {
		//write the query
		$q = "INSERT INTO creature_effects (effect_id, creature_id) VALUES ({$efid}, {$cid})";
		
		// run the query
		if ($mysqli->query ($q)) {
			$url = '../profile.php';
			redirect_user($url);
		} else {
			// echo $q;
		} // End query if			
	} // end errors if
	
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') { // remove effect 
	
	// validate target id 
	if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) ) {
		$ceid = $mysqli->real_escape_string(trim($_GET['x']));
		
		// Query 
		$q = "DELETE FROM creature_effects WHERE creature_effect_id={$ceid}";
		
		// run the query
		if ($mysqli->query ($q)) {
			$url = '../profile.php';
			redirect_user($url);
		} else {
			// echo $q;
		} // End query if
		
	} else {
		$errors[] = 'No target id';
		redirect_user('../encounters.php');
	}
	
}

// Close the connection:
$mysqli->close();
unset($mysqli);

// if any errors occurred and the script didn't reach a redirect
// for debug
if (!empty($errors)) {
	foreach ($errors as $e) {
		echo $e;
	}			
}
	
	
	
	
	

