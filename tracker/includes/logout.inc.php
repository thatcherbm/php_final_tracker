<?php # Script logout.inc.php #2
// This script lets the user logout.
require ('config.inc.php');
require ('login_functions.inc.php');

session_name('ENCOUNTER_TRACKER');
session_start(); // Start the session.

// If no session variable exists, redirect the user:
if (!isset($_SESSION['user_id'])) {

	// Need the functions:
	require ('login_functions.inc.php');
	redirect_user();	
	
} else { // Cancel the session:

	$_SESSION = array(); // Clear the variables.
	session_destroy(); // Destroy the session itself.
	setcookie ('PHPSESSID', '', time()-3600, '/', '', 0, 0); // Destroy the cookie.
	redirect_user('../loggedout.php');
}

?>