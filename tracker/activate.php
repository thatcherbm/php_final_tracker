<?php # Script activate.php
// This page activates the user's account.
require ('includes/config.inc.php'); 
$page_title = 'Activate Your Account';
include ('includes/header.html');

// If $x and $y don't exist or aren't of the proper format, redirect the user:
if (isset($_GET['x'], $_GET['y']) 
	&& filter_var($_GET['x'], FILTER_SANITIZE_NUMBER_INT)
	&& (strlen($_GET['y']) == 32 )
	) {

	// Update the database...
	require (MYSQL);
	$q = "UPDATE users SET active=NULL WHERE (user_id='" . $mysqli->real_escape_string($_GET['x']) . "' AND active='" . $mysqli->real_escape_string($_GET['y']) . "') LIMIT 1";
	$r = $mysqli->query($q);
	
	// Print a customized message:
	if ($mysqli->affected_rows == 1) {
		echo "<h3>Your account is now active. You may now log in.</h3>";
	} else {
		echo '<p class="error">Your account could not be activated. Please re-check the link or contact the system administrator.</p>'; 
	}

	// Close the connection:
	$mysqli->close();
	unset($mysqli);

} else { // Redirect.

	$url = BASE_URL . 'index.php'; // Define the URL.
	ob_end_clean(); // Delete the buffer.
	header("Location: $url");
	exit(); // Quit the script.

} // End of main IF-ELSE.

include ('includes/footer.html');
?>