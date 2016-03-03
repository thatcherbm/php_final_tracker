<?php # Script encounters.php
// This script retrieves all the records from the encounters table.
require ('includes/config.inc.php');

$page_title = 'View Encounters';
include ('header.php');

// Page header:
echo '<h1>Encounters</h1>';

require (MYSQL); // Connect to the db.
		
// Make the query:
$q = "SELECT encounter_id, name, description FROM encounters ORDER BY encounter_id ASC";
$r = $mysqli->query($q); // Run the query.

// Count the number of returned rows:
$num = $r->num_rows;

if ($num > 0) { // If it ran OK, display the records.

	// Print how many encounters there are:
	echo "<p>There are currently $num Encounters.</p>\n";

	// Table header.
	echo '<table align="center" cellspacing="3" cellpadding="3" width="75%">
	<tr><th align="left"><b>ID</b></th><th align="left"><b>Name</b></th><th align="left"><b>Description</b></th></tr>
';
	
	// Fetch and print all the records:
	while ($row = $r->fetch_object()) {
		echo '<tr><td align="left">' . $row->encounter_id . '</td><td align="left" width="200em"><a href="view_encounter.php?e=' . $row->encounter_id . '">' . $row->name . '</a></td><td align="left">' . $row->description . '</td></tr>' . "\n";
	}

	echo '</table>'; // Close the table.
	
	$r->free(); // Free up the resources.
	unset($r);	

} else { // If no records were returned.

	echo '<p class="error">There are currently no Encounters.</p>';

}

// Close the database connection.
$mysqli->close();
unset($mysqli);

include ('footer.php');
?>