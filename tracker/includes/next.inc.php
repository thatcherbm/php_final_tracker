<?php # Script next.inc.php
// This script will advance the initiative to the next creature. If the new initiative returned
// by the init_up function is zero, or if the round sent to the script is zero (sent by the start
// button) the script will also increment the round

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
// Get the next highest initiative
$new_init = init_up($mysqli, $e, $i);

// The assumption is that a returned value of 0 means we need to increment the round and set
// the init to the first creature in the order
$q = "UPDATE encounters SET ";		
if ($new_init == 0 || $r == 0) { // increment round and get init of first creature 
	$new_round = $r + 1;
	$q .= "current_round = '$new_round', ";
	$new_init = init_up($mysqli, $e, 0);
} 
$q .= "current_init=$new_init WHERE encounter_id=$e";			
$r = $mysqli->query ($q); // Run the query.

if ($mysqli->affected_rows == 1) {
	redirect_user('../view_encounter.php?e=' . $e );
} else {
	//echo $q;
	//echo $r;
}