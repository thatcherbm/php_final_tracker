<?php # Script login.php
// This page processes the login form submission.
// The script stores the HTTP_USER_AGENT value for added security.
require ('includes/config.inc.php');

// Include the header:
$page_title = 'Login';
include ('header.php');

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Need two helper files:
	require ('includes/login_functions.inc.php');
	require (MYSQL);
		
	// Check the login:
	list ($check, $data) = check_login($mysqli, $_POST['username'], $_POST['pass']);
	
	if ($check) { // OK!
		
		// Set the session data:
		$_SESSION['user_id'] = $data['user_id'];
		$_SESSION['username'] = $data['username'];
		$_SESSION['user_level'] = $data['user_level'];
		
		// Set a session variable for the text version of the user level as title
		$titles = array('Spectator', 'Player', 'GameMaster', 'Moderator', 'Administrator');
		$_SESSION['title'] = $titles[$data['user_level']];
		
		// Store the HTTP_USER_AGENT:
		$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);

		// Redirect:
		redirect_user('loggedin.php');
			
	} else { // Unsuccessful!

		// Assign $data to $errors for login_page.inc.php:
		$errors = $data;

	}
		
	// Close the connection:
	$mysqli->close();
	unset($mysqli);

} // End of the main submit conditional.

// Create the page:
include ('includes/login_page.inc.php');
?>