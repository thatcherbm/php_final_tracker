<?php # Script edit_init_order.inc.php
// This script edits the initiative value of participants 
// and redirects back to the edit_init_order page

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

	// connect to the database
	require (MYSQL2);
			
	// Validate the encounter id:
	if (empty($_POST['eid'])) {
		$errors[] = 'No encounter';
	} else {
		$eid = $mysqli->real_escape_string(trim($_POST['eid']));
	}
	
	// Validate the submit:
	if (empty($_POST['submit'])) {
		$errors[] = 'No submit';
	} else {
		$sub = $mysqli->real_escape_string(trim($_POST['submit']));
		// check for values used by anything other than RemoveCorpse
		if ($sub != 'RemoveCorpse') { 
			// Validate the participant id:
			if (empty($_POST['pid'])) {
				$errors[] = 'No participant id';
			} else {
				$pid = $mysqli->real_escape_string(trim($_POST['pid']));
			}

			// Validate the initiative:
			if (empty($_POST['init'])) {
				$errors[] = 'No initiative';
			} else {
				$init = $mysqli->real_escape_string(trim($_POST['init']));
			}
			
			// Validate the name:
			if (empty($_POST['name'])) {
				$errors[] = 'No name';
			} else {
				$name = $mysqli->real_escape_string(trim($_POST['name']));
			}
			
			// Validate the creature id:
			if (empty($_POST['cid'])) {
				$errors[] = 'No creature';
			} else {
				$cid = $mysqli->real_escape_string(trim($_POST['cid']));
			}
		} else { // Check for corpse id
			// Validate the corpse id:
			if (empty($_POST['coid'])) {
				$errors[] = 'No corpse';
			} else {
				$coid = $mysqli->real_escape_string(trim($_POST['coid']));
			}
		}
	}
	
	// ensure user has privileges to create entity type
	// only game masters should be doing this.
	if ( $ul != 2 ){
		$errors[] = 'Insufficient privileges';
	}
	
	if (empty($errors)) { // If everything's OK.
		// Generate query based on button clicked
		if ($sub == 'Edit') { // User has selected option of update initiative value
			// begin constructing the query:
			$q = "UPDATE participants SET initiative=" . $init . " WHERE participant_id=" . $pid;						
			
		} else if ($sub == 'Remove'){ // user wishes to remove the participant
			$q = "DELETE FROM participants WHERE participant_id=" . $pid;
			
		} else if ($sub == 'Kill') { // user wishes to remove the participant and add as a corpse
			$q = "INSERT INTO corpses (name, creature_id, encounter_id) VALUES ('" . $name . "', " . $cid . ", " . $eid . ")";
			if ($mysqli->query ($q)) {
			$q = "DELETE FROM participants WHERE participant_id=" . $pid;
			} 
		}	else if ($sub == 'RemoveCorpse') { // user wishes to remove the participant and add as a corpse
			$q = "DELETE FROM corpses WHERE corpse_id=" . $coid;
			
		} else {
			$errors[] = 'No submit value';
		} // End submit selection if
		
		// run the query
		if ($mysqli->query ($q)) {
			$url = '../edit_init_order.php?x=' . $eid;
			redirect_user($url);
		} else {
			// echo $q;
		} // End query if
		
	} else {
		foreach ($errors as $e) {
			echo $e;
		}
	}// End of empty($errors) IF.
		
	// Close the connection:
	$mysqli->close();
	unset($mysqli);

} // End of the main submit conditional.

?>