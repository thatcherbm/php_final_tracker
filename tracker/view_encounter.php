<?php # Script view_encounter.php
// This page prints any errors associated with logging in
// and it creates the entire login page, including the form.
require ('includes/config.inc.php');
require ('includes/login_functions.inc.php');
require ('includes/encounter_functions.inc.php');

// header:
$page_title = 'View Encounter';
include ('includes/header.html');


// Get information for the encounter to be displayed
// Check for a valid encounter ID, through GET or POST:
if ( (isset($_GET['e'])) && (is_numeric($_GET['e'])) ) { // From encounters.php
	$e = $_GET['e'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['e'])) ) { // Form submission.
	$e = $_POST['e'];
} else { // No valid encounter ID, kill the script.
	redirect_user('encounters.php');
}

//get encounter information
require (MYSQL);
$enc_id = $mysqli->real_escape_string(trim($e));
$q = "SELECT * FROM encounters WHERE encounter_id='$enc_id'";		
$r = $mysqli->query ($q); // Run the query.
$encounter = $r->fetch_array(MYSQLI_ASSOC);


$init = $encounter['current_init'];
$round = $encounter['current_round'];
// user id of encounter ownner
$oid = $encounter['user_id'];

// If no session value is present, redirect the user:
// Also validate the HTTP_USER_AGENT!
if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']) )) {
	redirect_user('encounters.php');	
}

$uid = $_SESSION['user_id'];

echo "<h2>{$encounter['name']}";
// add edit link for the encounter owner
if ($oid == $uid) {
	echo ' <a style="font-size: .5em" href="edit.php?x=1&y=' . $enc_id . '"">edit</a>';
}
echo "</h2>"; 
echo "<p>Round: " . $round . "</p>\n";

// add links to progress through list
if ($oid == $uid) {
	echo '<p style="padding-left: 1em">';
	if ($round == 0) { //display start button
		echo '<a href="includes/next.inc.php?e=' . $enc_id . '&i=' . $init . '&r=' . $round .'">start</a>';
		$active_participant = 0;
	} else if ($encounter['current_init'] > 0) {
		echo '<a href="includes/prev.inc.php?e=' . $enc_id . '&i=' . $init . '&r=' . $round .'">prev</a>
		<a href="includes/next.inc.php?e=' . $enc_id . '&i=' . $init . '&r=' . $round .'">next</a>';
	}
	echo '</p>';
}
//retrieve all creatures in the encounter and list them in initiative order
$q = "SELECT * FROM participants WHERE encounter_id='$enc_id' ORDER BY initiative";		
$r = $mysqli->query ($q); // Run the query.

echo '<div id="init"><ul>';
	$active_participant = 0;
	while ($row = $r->fetch_object()) {
		echo '<li ';
		// highlight the creature who's turn it is.
		if ($encounter['current_init'] == $row->initiative) {
			echo 'style="background-color: #99EEFF;"';
			$active_participant = $row->participant_id;
		}
		echo '>' . $row->initiative . ' :: ' . $row->name . '</li>' . "\n";
	}


//retrieve all corpses in the encounter and list them
$q = "SELECT name FROM corpses WHERE encounter_id='$enc_id' ORDER BY name";		
$r = $mysqli->query ($q); // Run the query.

	while ($row = $r->fetch_object()) {
		echo '<li style="color: #A9A9A9;">'. $row->name . '</li>' . "\n";
	}
	
// Add edit link for the encounter owner
if ($oid == $uid) {
	echo ' <a style="font-size: 1em" href="edit_init_order.php?x=' . $enc_id . '"">add/edit</a>';
}
echo '</ul></div>'; //init


//retrieve all active effects which target the active creature
$q = "SELECT a.effect_id, a.participant_id FROM targets as t JOIN active_effects as a USING (active_effect_id) WHERE t.participant_id='$active_participant'";		
$r = $mysqli->query ($q); // Run the query.

// prepared statements for effect queries
// get the name of the creature who created the effect
$qp_creature = "SELECT c.name, c.user_id FROM creatures AS c JOIN participants AS p USING (creature_id) WHERE p.participant_id = ? ORDER BY name ASC";
$stmt_p = $mysqli->prepare($qp_creature);
$stmt_p->bind_param('i', $pid);

// get the name and description of the effects which are targeting the active creature 
$qp_effect = "SELECT name, description FROM effects WHERE effect_id = ?";
$stmt_e = $mysqli->prepare($qp_effect);
$stmt_e->bind_param('i', $eid);

echo '<div id="effects"><h3>Effects Targeting:</h3><ul class="effect">';
while ($row = $r->fetch_object()) {
	// get the creator's name
	$pid = $row->participant_id;
	$stmt_p->execute();
	$stmt_p->store_result();
	$stmt_p->bind_result($c_name, $c_uid);
	$stmt_p->fetch();
	// get the effect's name,description and owner id
	$eid = $row->effect_id;
	$stmt_e->execute();
	$stmt_e->store_result();
	$stmt_e->bind_result($e_name, $e_description);
	$stmt_e->fetch();
	
	// output data
	// The logic is intended to hide effects from the players which are owned by the gm
	// logic statement evaluates as true for all effects of players and only gm effects if the gm is the encounter owner
	// if ((the creature who created the effect is not owned by the encounter owner) or (the encounter owner is the user who is logged in))
	
	if ($oid != $c_uid || $oid == $uid) {
		echo '<li class="effect">' . $c_name . ' -> ' . $e_name . ' :: ' . $e_description . '</li>' . "\n";
	}
}

echo '</ul><h3>Own Effects:</h3><ul class="effect">';
//retrieve all active effects which are owned by the active creature
$q = "SELECT e.name, e.description FROM effects AS e JOIN active_effects as a USING (effect_id) WHERE a.participant_id='$active_participant'";		
$r = $mysqli->query ($q); // Run the query.

while ($row = $r->fetch_object()) {
	// output data
	echo '<li class="effect">' . $row->name . ' :: ' . $row->description . '</li>' . "\n";
}
echo '</ul>';

// Add edit link for the encounter owner
if ($oid == $uid) {
	echo ' <a style="font-size: .9em" href="add_effect.php?x=' . $enc_id . '"">add/remove</a>';
}
echo '</div>'; //effects

// Close the connection:
$mysqli->close();
unset($mysqli);








?>

<?php include ('includes/footer.html'); ?>