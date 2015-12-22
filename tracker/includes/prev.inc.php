<?php # Script prev.inc.php
// This script will retreat the initiative to the previous creature retreating the round if needed
// if successful the script will redirect to the encounter view effectively reloading the page
// if not successful it will output errors

require ('config.inc.php');
require ('login_functions.inc.php');
require ('encounter_functions.inc.php');

// Get information for from the submission
// Check for a valid encounter ID, through GET:
if ( (isset($_GET['e'])) && (is_numeric($_GET['e'])) &&
		(isset($_GET['i'])) && (is_numeric($_GET['i'])) &&
		(isset($_GET['r'])) && (is_numeric($_GET['r'])) ) { // From encounters.php
	$e = $_GET['e'];
	$i = $_GET['i'];
	$r = $_GET['r'];
} else { // No valid encounter ID, Init, or round, kill the script.
	header("Location: ../encounters.php");
	exit(); // Quit the script.
}

require (MYSQL2);
// Get the next lowest initiative
$new_init = init_down($mysqli, $e, $i);

// The assumption is that a returned value of 0 means we need to decrement the round and set
// the init to the last creature in the order
$q = "UPDATE encounters SET ";		
if ($new_init == 0) { // increment round and get init of first creature if needed 
	$new_round = $r - 1;
	$q .= "current_round = '$new_round', ";
	if ($new_round == 0) { // if we have decremented to round 0, the init in round 0 should be 0
		$new_init = 0;
	} else {
		$new_init = init_down($mysqli, $e, 0);
	}
} 
$q .= "current_init=$new_init WHERE encounter_id=$e";			
$r = $mysqli->query ($q); // Run the query.

if ($mysqli->affected_rows == 1) {
	redirect_user('../view_encounter.php?e=' . $e );
} else {
	//echo $q;
	//echo $r;
}