<?php # Script add_effect.inc.php
// This script can be accessed via post or get and adds/removes effects from
// encounters and targets from effects
// When adding an effect, the submit button triggers the post method
// When adding/removing a target, or removing 

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
// only game masters and higher should be doing this.
if ( $ul < 2 ){
	$errors[] = 'Insufficient privileges';
} else {
	// Check for form submission:
	if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
		
		// validate user id and current level
		if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) && (isset($_GET['y'])) && (is_numeric($_GET['y'])) ) {
			$p_uid = $mysqli->real_escape_string(trim($_GET['x']));
			$p_ul = $mysqli->real_escape_string(trim($_GET['y']));
			
			// double check to make sure the logged in user has privileges
			// you have to be at least 2 levels higher than the user's present level
			// to promote them
			if (($p_ul + 2) <= $ul) {
				$new_ul = $p_ul + 1;
				// Query 
				$q = "UPDATE users SET user_level={$new_ul} WHERE user_id={$p_uid}";
				// run the query
				if ($mysqli->query ($q)) {
					$url = '../users.php';
					redirect_user($url);
				} else {
					echo $q;
				} // End query if
				
			} else {
				$errors[] = 'user level insufficient';
			}
			
		} else {
			$errors[] = 'data invalid';
			//redirect_user('../users.php');
		}
		
	} else {
		$errors[] = 'No submission';
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
	
	
	
	
	

