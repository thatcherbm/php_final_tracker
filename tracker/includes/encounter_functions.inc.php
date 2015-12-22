<?php # Script encounter_functions.inc.php
// This page contains functions used by encounter processes.

// this function returns the next highest initiative in an encounter
// used when advancing the initiative order
function init_up ($mysqli, $e, $i) {
	// Retrieve the next highest initiative:
	$q = "SELECT initiative FROM participants WHERE encounter_id='$e' AND initiative > '$i' ORDER BY initiative LIMIT 1";		
	$r = $mysqli->query ($q); // Run the query.
	// if a result is returned, return the result otherwise return 0
	if ($r->num_rows == 1) {
		$row = $r->fetch_array();
		$new_i = $row['initiative'];
		return ($new_i);
	} else {
		return 0;
	}
}
	
// this function returns the next lowest initiative in an encounter
// used when retreating the initiative order
function init_down ($mysqli, $e, $i) {
	//first we check the received i, if it is zero, we want the highest init. 

	// Retrieve the next lowest initiative:
	$q = "SELECT initiative FROM participants WHERE encounter_id='$e' ";
	if ($i == 0) { //check the received i, if it is zero, we want the highest init. 
		$q .= "ORDER BY initiative DESC LIMIT 1";
	} else { // we want the next lowest
		$q .= "AND initiative < '$i' ORDER BY initiative DESC LIMIT 1";	
	}		
	$r = $mysqli->query ($q); // Run the query.
	// if a result is returned, return the result otherwise return 0
	if ($r->num_rows == 1) {
		$row = $r->fetch_array();
		$new_i = $row['initiative'];
		return ($new_i);
	} else {
		return 0;
	}
}



