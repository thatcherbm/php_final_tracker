<?php # Script add_effect.php
// This page is used for the adding/removing/editing of the active effects
// in an encounter


// Include the header:
require ('includes/config.inc.php');
require ('includes/login_functions.inc.php');
$page_title = 'Edit Active Effects';
include ('header.php');
require (MYSQL); // Connect to the db.

// Get information for the encounter ID
// Check for a valid encounter ID, through GET or POST:
if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) ) { // From profile.php
	$eid = $mysqli->real_escape_string(trim($_GET['x']));
} else { // No valid encounter ID, kill the script.
	redirect_user('encounters.php');
}

// If no session value is present, redirect the user:
// Also validate the HTTP_USER_AGENT!
if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']) )) {
	redirect_user('login.php');	
}
$uid = $_SESSION['user_id'];

// Now that a user is logged in and we have the encounter id
// we need to retrieve the encounter name and show it
$q = "SELECT * FROM encounters WHERE encounter_id='$eid'";		
$r = $mysqli->query ($q); // Run the query.
$encounter = $r->fetch_array(MYSQLI_ASSOC);
$ename = $encounter['name'];
$oid = $encounter['user_id'];

// Finally as a last measure of protection make sure that the user is the encounter owner
if ($uid != $oid) {
	redirect_user('encounters.php');
}

echo "<h2>{$encounter['name']} ". ' <a style="font-size: .5em" href="view_encounter.php?e=1">return</a></h2>';

echo '<h3>Active Effects</h3>';

//retrieve all active effects
$q = "SELECT active_effect_id, effect_id, participant_id FROM active_effects WHERE encounter_id=" . $eid;		
$r = $mysqli->query ($q); // Run the query.

// prepared statements for effect queries

// get the name of the creature who created the effect
$qp_creature = "SELECT c.name, c.user_id FROM creatures AS c JOIN participants AS p USING (creature_id) WHERE p.participant_id = ? ORDER BY name ASC";
$stmt_p = $mysqli->prepare($qp_creature);
$stmt_p->bind_param('i', $pid);

// get the name and description of the effects  
$qp_effect = "SELECT name, description FROM effects WHERE effect_id = ?";
$stmt_e = $mysqli->prepare($qp_effect);
$stmt_e->bind_param('i', $efid);

// get the names of the targets of the effect
$qp_targets = "SELECT p.name, t.target_id FROM participants AS p JOIN targets as t USING (participant_id) WHERE t.active_effect_id=?";
$stmt_t = $mysqli->prepare($qp_targets);
$stmt_t->bind_param('i', $aid);

// get name and participant id of participants who are not targeted by the effect
$qp_not_targets = "SELECT name, participant_id FROM participants WHERE encounter_id=" . $eid . " AND participant_id NOT IN (SELECT p.participant_id FROM participants AS p JOIN targets AS t USING (participant_id) WHERE t.active_effect_id=?)";
$stmt_nt = $mysqli->prepare($qp_not_targets);
$stmt_nt->bind_param('i', $aid);

echo '<table align="center" width="100%">
	<tr><th width="100em">Creator</th><th width="150em">Name</th><th width="150em">Description</th><th min-width="40em">Targets</th><th width="20em"></th></tr>';
while ($row = $r->fetch_object()) {
	// get the creator's name
	$pid = $row->participant_id;
	$stmt_p->execute();
	$stmt_p->store_result();
	$stmt_p->bind_result($c_name, $c_uid);
	$stmt_p->fetch();
	// get the effect's name,description and owner id
	$efid = $row->effect_id;
	$stmt_e->execute();
	$stmt_e->store_result();
	$stmt_e->bind_result($e_name, $e_description);
	$stmt_e->fetch();
	
	// output data we have
	echo '<tr><td>' . $c_name . '</td><td>' . $e_name . '</td><td>' . $e_description . '</td><td>';
	// get the names of all the targets of the effect and output with a link to remove
	// the target from the effect
	$aid = $row->active_effect_id;
	$stmt_t->execute();
	$stmt_t->store_result();
	$stmt_t->bind_result($t_name, $t_id);
	while ($stmt_t->fetch()) {
		echo $t_name . ' <a href="includes/add_effect.inc.php?x=' . $eid . '&y=' . $t_id . '">remove</a><br>';
	}
	// create a drop down selection to add targets to the effect
	echo '<form method="post" action="includes/add_effect.inc.php"><input type="hidden" name="eid" value="' . $eid . '"><input type="hidden" name="aid" value="' . $aid . '">';
	echo '<select name="pid" style="width: 6em">' . "\n";
	
	// get the participant's name and id
	// $aid was already set for previous query
	$stmt_nt->execute();
	$stmt_nt->store_result();
	$stmt_nt->bind_result($nt_name, $nt_id);
	while ($stmt_nt->fetch()) {
		echo '<option value="' . $nt_id . '">' . $nt_name . '</option><br>' . "\n";
	}
	echo '</select>' . "\n";
	
	echo '<input type="submit" name="submit" value="Add Target"></form>';
	
	//add button to remove the effect from the encounter
	echo '</td><td><form method="post" action="includes/add_effect.inc.php"><input type="hidden" name="aid" value="' . $aid . '"><input type="hidden" name="eid" value="' . $eid . '"><input type="submit" name="submit" value="Remove Effect"></form></td></tr>' . "\n";
	
}
echo '</table>'; // Close the table.

echo "\n\n<h3>Add Effect</h3><br>\n";
// Add drop down menus to select characters to add

// get all the participants in the encounter
$q = "SELECT name, creature_id, participant_id FROM participants WHERE encounter_id=" . $eid;		
$r = $mysqli->query ($q); // Run the query.

// prepared statement to get the effects linked to the participant
$qp = "SELECT e.name, e.effect_id FROM creature_effects AS c JOIN effects AS e USING (effect_id) WHERE c.creature_id = ? ORDER BY name ASC";
$stmt_pe = $mysqli->prepare($qp);
$stmt_pe->bind_param('i', $cid);

// Option to select effects linked to creatures in the encounter

while ($row = $r->fetch_object()) { // 
	echo '<form method="post" action="includes/add_effect.inc.php"><br>' . "\n";
	echo '<input type="hidden" name="eid" value="' . $eid . '">' . "\n";
	echo '<input type="hidden" name="pid" value="' . $row->participant_id . '">' . "\n";
	echo '<label>' . $row->name .'</label>';
	echo '<select name="efid" style="width: 10em">' . "\n";
	
	// get the effect's name and id
	$cid = $row->creature_id;
	$stmt_pe->execute();
	$stmt_pe->store_result();
	$stmt_pe->bind_result($pe_name, $pe_id);
	while ($stmt_pe->fetch()) {
		echo '<option value="' . $pe_id . '">' . $pe_name . '</option><br>' . "\n";
	}
	echo '</select>' . "\n";
	echo '<input type="submit" name="submit" value="Add Effect">';
	echo '</form>' . "\n\n";
}
// Option to select any effect the gm owns (won't be linked to a creature in the encounter)
echo '<form method="post" action="includes/add_effect.inc.php"><br>' . "\n";
echo '<input type="hidden" name="eid" value="' . $eid . '">' . "\n";
echo '<input type="hidden" name="pid" value="NULL">' . "\n";
echo '<label>Unlinked</label>';
echo '<select name="efid" style="width: 10em">' . "\n";

// get the effect's name and id
$q = "SELECT name, effect_id FROM effects WHERE user_id=" . $uid;		
$r = $mysqli->query ($q); // Run the query.
while ($row = $r->fetch_object()) {
	echo '<option value="' . $row->effect_id . '">' . $row->name . '</option><br>' . "\n";
}
echo '</select>' . "\n";
echo '<input type="submit" name="submit" value="Add Effect">';
echo '</form>' . "\n\n";




?>


<?php include ('footer.php'); ?>




