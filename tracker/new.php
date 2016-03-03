<?php # Script edit.php
// This page is used for the editing of encounter entities: encounters, creatures, and effects.
// The page is intended to be called by links from the profile page (and others) and will only show the form(s) for 
// the entity type specified in the link


// Include the header:
require ('includes/config.inc.php');
require ('includes/login_functions.inc.php');
$page_title = 'Create New';
include ('header.php');
require (MYSQL); // Connect to the db.

// Get information for the entity type and id to be edited
// Check for a valid encounter ID, through GET or POST:
if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) ) { // From profile.php
	$t = $_GET['x'];
} else { // No valid encounter ID, kill the script.
	redirect_user('profile.php');
}

// If no session value is present, redirect the user:
// Also validate the HTTP_USER_AGENT!
if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']) )) {
	redirect_user('login.php');	
}

// Now that a user is logged in and we have the entity type and id
// we need to retrieve basid information and start the form
// Encounters are type 1, creatures are type 2, effects are type 3
$t_value = array(1 => 'encounter', 2 => 'creature', 3 => 'effect');

// All entities will have a name and description which can be edited
// Display heading specific to the entity type

echo '<h1>New ' . ucfirst($t_value[$t]) . '</h1>';

// Create the form
echo '<form method="post" action="includes/new.inc.php">
	<label for="name">Name</label>
	<input type="text" name="name" id="name" size="20" maxlength="20"><br>
	<label for="desc">Description</label>
	<textarea name="desc" id="desc" rows="3" cols="40"></textarea><br>
	<input type="hidden" name="type" value="' . $t . '">
	<input type="submit" name="submit" style="margin-left: 10em; margin-top: 1em;" value="Submit">
	</form>';
?>

<?php include ('footer.php'); ?>