<?php # Script profile.php
// This page shows details of the user's account including their username, and 
// all the entities they own, and provides links/buttons to create new entities, link
// creatures to effects, and provides upper level users access to the users page.


// Include the header:
require ('includes/config.inc.php');
$page_title = 'Profile';
include ('header.php');

// If no session value is present, redirect the user:
// Also validate the HTTP_USER_AGENT!
if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']) )) {
	// Need the functions:
	require ('includes/login_functions.inc.php');
	redirect_user(login.php);	
}

$uid = $_SESSION['user_id'];
$ul = $_SESSION['user_level'];
echo "<table>";
echo "<tr>";
echo '<td width="175em"><h2 style="color: #9933FF;">USERNAME:' . "</h2></td><td><h2>{$_SESSION['username']}</h2>";
echo "</tr>";
echo '<tr style="background-color: #FFFFFF">';
echo "<td><h3 " . 'style="color: #9933FF;">USERLEVEL:' . "</h3></td><td><h3>{$_SESSION['title']}</h3>";
echo "</tr>";
echo "</table>";
echo '<a href="changepass.php">Change Password</a><br>';

 if ($ul >= 2) { 
	echo '<br><a href="users.php">Manage Users</a><br>';
}



if ($ul == 1 || $ul == 2) { //display owned creatures and effects for players and gms
	echo '<br><h2 style="color: #000000">OWNED ENTITIES</h2>';
	
	require (MYSQL); // Connect to the db.
	
	// Display creatures owned by user
	$q = "SELECT name, description, creature_id FROM creatures WHERE user_id = {$uid} ORDER BY name ASC";
	$r = $mysqli->query($q); // Run the query.
	
	// prepared statement for effect queries
	$qp = "SELECT e.name, c.creature_effect_id FROM creature_effects AS c JOIN effects AS e USING (effect_id) WHERE c.creature_id = ? ORDER BY name ASC";
	$stmt = $mysqli->prepare($qp);
	$stmt->bind_param('i', $cid);
	
	// prepared statement to get effects the the user owns that are not yet linked to
	// the creature
	// get name and participant id of participants who are not targeted by the effect
	$qp_not_linked = "SELECT name, effect_id FROM effects WHERE user_id={$uid} AND effect_id NOT IN (SELECT e.effect_id FROM creature_effects AS c JOIN effects AS e USING (effect_id) WHERE c.creature_id = ?)";
	$stmt_nl = $mysqli->prepare($qp_not_linked);
	$stmt_nl->bind_param('i', $cid);
	
	echo '<h4>Creatures: <a href="new.php?x=2">new</a></h4>';
	echo '<table align="center" width="100%">
	<tr><th width="200em">Name</th><th width="200em">Description</th><th min-width="300em">Effects</th></tr>';
	while ($row = $r->fetch_object()) {
		echo '<tr><td align="left"><a href="edit.php?x=2&y=' . $row->creature_id . '">' . $row->name . '</a></td><td align="left">' . $row->description . '</td><td>';
		$cid = $row->creature_id;
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows == 0) { // no effects returned
			echo '<i>None</i>';
		} else { // list effects linked to creature
			$stmt->bind_result($name, $ceid);
			while ($stmt->fetch()) { // list each returned effect name
				echo '- ' . $name . ' <a href="includes/creature_effect.inc.php?x=' . $ceid . '">remove</a><br>' . "\n";
			}
		}
		
		// create a drop down selection to add effects to the creature
		echo '<form method="post" action="includes/creature_effect.inc.php"><input type="hidden" name="cid" value="' . $cid . '">';
		echo '<select name="efid" style="width: 6em">' . "\n";
		
		// get the effect's name and id
		// $aid was already set for previous query
		$stmt_nl->execute();
		$stmt_nl->store_result();
		$stmt_nl->bind_result($nl_name, $nl_id);
		while ($stmt_nl->fetch()) {
			echo '<option value="' . $nl_id . '">' . $nl_name . '</option><br>' . "\n";
		}
		echo '</select>' . "\n";
		
		echo '<input type="submit" name="submit" value="Add"></form>';
		
		echo '</td></tr>'; // end creature row
	}
	echo '</table>'; // Close the table.
	$stmt->close();
	
	// Display effects owned by user
	$q = "SELECT name, description, effect_id FROM effects WHERE user_id = {$_SESSION['user_id']} ORDER BY name ASC";
	$r = $mysqli->query($q); // Run the query.
	
	echo '<h4>Effects: <a href="new.php?x=3">new</a></h4>';
	echo '<table align="center" width="100%">
	<tr><th width="200em">Name</th><th>Description</th></tr>';
	while ($row = $r->fetch_object()) {
		echo '<tr><td align="left" width="200em"><a href="edit.php?x=3&y=' . $row->effect_id . '">' . $row->name . '</a></td><td align="left">' . $row->description . '</td></tr>
		';
	}
	echo '</table>'; // Close the table.
	
	if ($ul == 2) { //display owned encounters for GMs
		// Make the query:
		$q = "SELECT encounter_id, name, description FROM encounters WHERE user_id = {$_SESSION['user_id']} ORDER BY encounter_id ASC";
		$r = $mysqli->query($q); // Run the query.
		
		echo '<h4>Encounters: <a href="new.php?x=1">new</a></h4>';
		echo '<table align="center" width="100%">
		<tr><th width="200em">Name</th><th>Description</th></tr>';
		while ($row = $r->fetch_object()) {
			echo '<tr><td align="left"><a href="view_encounter.php?e=' . $row->encounter_id . '">' . $row->name . '</a></td><td align="left">' . $row->description . '</td></tr>
			';
		}
		echo '</table>'; // Close the table.
	}

// Close the connection:
$mysqli->close();
unset($mysqli);
}







?>

<?php include ('footer.php'); ?>