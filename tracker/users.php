<?php # Script encounters.php
// This script shows all users except for admin level accounts, the accounts
// are organized by user level.  Only gms or higher will have access (via 
// profile page link) but this script verifies the user level.  gms will see
// a link to promote spectators to players, mods will also see a link to promote
// players to gms (will have to first promote to player status cause I'm lazy) and
// admins will see a link to promote gms to mods
require ('includes/config.inc.php');

$page_title = 'View users';
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

// Finally as a last measure of protection make sure that the user level is appropriate
if ($ul < 2) {
	redirect_user('login.php');
}

require (MYSQL); // Connect to the db.
		
// Prepare a query to retrieve users of a specified level
$qp = "SELECT username, user_id FROM users WHERE user_level=? ORDER BY username";
$stmt = $mysqli->prepare($qp);
$stmt->bind_param('i', $view_ul);

$titles = array('Spectators', 'Players', 'Game Masters', 'Moderators');
for ($i = 0; $i < 4; $i++) {
	echo '<h1>' . $titles[$i] . '</h1>';
	$view_ul = $i;
	$stmt->execute();
	$stmt->store_result();
	if ($stmt->num_rows == 0) { // no users returned
		echo '<i>None</i>';
	} else { // create table showing users
		$stmt->bind_result($username, $view_uid);
		echo '<table align="center" cellspacing="3" cellpadding="3" width="75%">
		<tr><th width="70em" align="left"></th><th width="100em" align="left"></th></tr>';
		
		while ($stmt->fetch()) { // list each returned user with a promote link if applicable
			echo '<tr><td>'	. (($ul > ($i + 1)) ? '<a href="includes/promote.inc.php?x=' . $view_uid . '&y=' . $i . '">promote</a>' : '') . '</td><td>' . $username . '</td></tr>' . "\n";
		}
		
		echo '</table>'; // Close the table.
	}

}

// Close the database connection.
$mysqli->close();
unset($mysqli);

include ('footer.php');
?>