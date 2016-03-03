<?php # Script edit.php
// This page is used for the adding/removing/editing of the creatures in the 
// initiative order.
// The page is intended to be called by links and will show the existing data for 
// editing


// Include the header:
require ('includes/config.inc.php');
require ('includes/login_functions.inc.php');
$page_title = 'Edit Initiative';
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

echo '<h3>Participants</h3>';

//retrieve all creatures in the encounter and list them in initiative order
$q = "SELECT * FROM participants WHERE encounter_id='$eid' ORDER BY initiative";		
$r = $mysqli->query ($q); // Run the query.


while ($row = $r->fetch_object()) {
	echo '<form method="post" action="includes/edit_init_order.inc.php"><br>' . "\n";
	echo '<div><input type="hidden" name="pid" value="' . $row->participant_id . '">' . "\n";
	echo '<input type="hidden" name="eid" value="' . $eid . '">' . "\n";
	echo '<input type="hidden" name="name" value="' . $row->name . '">' . "\n";
	echo '<input type="hidden" name="cid" value="' . $row->creature_id . '">' . "\n";
	echo '<label>' . $row->name . '</label><input style="width: 3em" type="number" name="init" value="' . $row->initiative . '">';
	echo '<input type="submit" name="submit" value="Edit">';
	echo '<input type="submit" name="submit" value="Remove">';
	echo '<input type="submit" name="submit" value="Kill"></div>';
	echo '</form>' . "\n";
}

echo "\n\n<h3>Corpses</h3>\n";

//retrieve all corpses in the encounter and list them by name
$q = "SELECT * FROM corpses WHERE encounter_id='$eid' ORDER BY name";		
$r = $mysqli->query ($q); // Run the query.

while ($row = $r->fetch_object()) {
	echo '<form method="post" action="includes/edit_init_order.inc.php"><br>' . "\n";
	echo '<div><input type="hidden" name="coid" value="' . $row->corpse_id . '">' . "\n";
	echo '<input type="hidden" name="eid" value="' . $eid . '">' . "\n";
	echo '<label>' . $row->name . '</label>';
	echo '<input type="submit" name="submit" value="RemoveCorpse">';
	echo '</form>' . "\n";
}


echo "\n\n<h3>Add Participant</h3><br>\n";
// Add drop down menus to select characters to add

// Option to select a creature which is owned by the user
echo '<form method="post" action="includes/add.inc.php"><br>' . "\n";
echo '<label>Owned Creatures</label><select name="cid" style="width: 10em">' . "\n";
$q = 'SELECT * FROM creatures WHERE user_id=' . $uid;
$r = $mysqli->query ($q); // Run the query.

while ($row = $r->fetch_object()) {
	echo '<option value="' . $row->creature_id . '">' . $row->name . '</option><br>' . "\n";
}
echo '</select><br>' . "\n";
echo '<label style="margin-left: 4em">Name</label><input size="20" maxlength="20" type="text" name="name" value=""><br>';
echo '<label style="margin-left: 4em">Initiative</label><input style="width: 3em" type="number" name="init" value="0">';
echo '<input type="hidden" name="eid" value="' . $eid . '">' . "\n";
echo '<input type="submit" name="submit" value="Add">';
echo '</form>' . "\n\n";

// Option to select a creature owned by someone else
echo '<form method="post" action="includes/add.inc.php"><br>' . "\n";
echo '<label>Player Creatures</label><select name="cid"  style="width: 10em">' . "\n";
$q = 'SELECT * FROM creatures WHERE user_id<>' . $uid;
$r = $mysqli->query ($q); // Run the query.

while ($row = $r->fetch_object()) {
	echo '<option value="' . $row->creature_id . '">' . $row->name . '</option><br>' . "\n";;
}
echo '</select><br>' . "\n";
echo '<label style="margin-left: 4em">Name</label><input size="20" maxlength="20" type="text" name="name" value=""><br>';
echo '<label style="margin-left: 4em">Initiative</label><input style="width: 3em" type="number" name="init" value="0">';
echo '<input type="hidden" name="eid" value="' . $eid . '">' . "\n";
echo '<input type="submit" name="submit" value="Add">';
echo '</form>' . "\n";

?>


<?php include ('footer.php'); ?>




