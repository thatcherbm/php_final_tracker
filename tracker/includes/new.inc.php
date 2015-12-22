<?php # Script new.inc.php
// This script creates a new encounter entity and redirects back to the profile page

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
		
	// Validate the name:
	if (empty($_POST['name'])) {
		$errors[] = 'No name';
	} else {
		$n = $mysqli->real_escape_string(trim($_POST['name']));
	}

	// Validate the description:
	if (empty($_POST['desc'])) {
		$errors[] = 'No description';
	} else {
		$d = $mysqli->real_escape_string(trim($_POST['desc']));
	}
	
	// Validate the type:
	if (empty($_POST['type'])) {
		$errors[] = 'No type';
	} else {
		$t = $mysqli->real_escape_string(trim($_POST['type']));
	}
	
	// ensure user has privileges to create entity type
	// only players and game masters can create creatures and effects, and only
	// game masters can create encounters.
	if ( (($t == 2 || $t == 3) && ($ul == 0 || $ul == 3 || $ul == 4)) || ($t == 1 && $ul != 2) ){
		echo 'Insufficient privileges: ' . $t . ' : ' . $ul;
	} else {
	
		if (empty($errors)) { // If everything's OK.

			// begin constructing the query:
			$q = "INSERT INTO ";	
			
			// specify table
			switch ($t) {  // heading
				case 1: // encounter
					$q .= "encounters ";
					break;
				case 2: // creature
					$q .= "creatures ";
					break;
				case 3: // effect
					$q .= "effects ";
					break;
			}
			
			// specify columns and values
			$q .= "(name, description, user_id) VALUES ('" . $n . "', '" . $d . "', " . $uid . ")";
			
			if ($mysqli->query ($q)) {
				redirect_user('../profile.php');
			} else {echo $q;}
			
			
		} // End of empty($errors) IF.
	}	
	// Close the connection:
	$mysqli->close();
	unset($mysqli);

} // End of the main submit conditional.

?>