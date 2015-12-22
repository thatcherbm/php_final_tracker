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
// only game masters should be doing this.
if ( $ul != 2 ){
	$errors[] = 'Insufficient privileges';
}

// Check if the form has been submitted via post or get:
if ($_SERVER['REQUEST_METHOD'] == 'POST') { // add/remove effect 
	
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
		// Check value of submit
		if ($sub == 'Remove Effect') { // remove active effect
			// Validate the active effect id:
			if (empty($_POST['aid'])) {
				$errors[] = 'No active effect id';
			} else {
				$aid = $mysqli->real_escape_string(trim($_POST['aid']));
			}
			
			if (empty($errors)) {
				//write the query
				$q = "DELETE FROM active_effects WHERE active_effect_id=" . $aid;
				// run the query
				if ($mysqli->query ($q)) {
					$url = '../add_effect.php?x=' . $eid;
					redirect_user($url);
				} else {
					// echo $q;
				} // End query if
			
			} 			
			
		} else if ($sub == 'Add Effect') { // add an active effect
			
			// Validate the effect id:
			if (empty($_POST['efid'])) {
				$errors[] = 'No effect';
			} else {
				$efid = $mysqli->real_escape_string(trim($_POST['efid']));
			}
			// Validate the Participant id:
			if (empty($_POST['pid'])) {
				$errors[] = 'No participant';
			} else {
				$pid = $mysqli->real_escape_string(trim($_POST['pid']));
			}
			
			if (empty($errors)) {
				//write the query
				$q = 'INSERT INTO active_effects (effect_id, encounter_id';
				
				// add participant id if needed
				$q .= ($pid != 'NULL') ? ', participant_id)': ')';
				
				$q .= ' VALUES (' . $efid . ', ' . $eid;
				
				// add participant id if needed
				$q .= ($pid != 'NULL') ? ', ' . $pid . ')': ')';
				// run the query
				if ($mysqli->query ($q)) {
					$url = '../add_effect.php?x=' . $eid;
					redirect_user($url);
				} else {
					// echo $q;
				} // End query if			
			} 
			
		} else if ($sub == 'Add Target') {
			// Validate the active effect id:
			if (empty($_POST['aid'])) {
				$errors[] = 'No effect';
			} else {
				$aid = $mysqli->real_escape_string(trim($_POST['aid']));
			}
			// Validate the Participant id:
			if (empty($_POST['pid'])) {
				$errors[] = 'No participant';
			} else {
				$pid = $mysqli->real_escape_string(trim($_POST['pid']));
			}
			
			if (empty($errors)) {
				//write the query
				$q = 'INSERT INTO targets (active_effect_id, participant_id) VALUES (' . $aid . ', ' . $pid . ')';
				// run the query
				if ($mysqli->query ($q)) {
					$url = '../add_effect.php?x=' . $eid;
					redirect_user($url);
				} else {
					// echo $q;
				} // End query if
			
			}
		}
	}
	
	
	
	
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') { // remove target 
	
	// validate target id 
	if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) && (isset($_GET['y'])) && (is_numeric($_GET['y'])) ) {
		$eid = $mysqli->real_escape_string(trim($_GET['x']));
		$tid = $mysqli->real_escape_string(trim($_GET['y']));
		
		// Query 
		$q = "DELETE FROM targets WHERE target_id=" . $tid;
		
		// run the query
		if ($mysqli->query ($q)) {
			$url = '../add_effect.php?x=' . $eid;
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
/* if (!empty($errors)) {
	foreach ($errors as $e) {
		echo $e;
	}			
} */
	
	
	
	
	

