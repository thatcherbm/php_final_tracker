<?php # Script loggedin.php 
// The user is redirected here from login.php.
// Set the page title and include the HTML header:
$page_title = 'Logged In!';
include ('includes/header.html');

// If no session value is present, redirect the user:
// Also validate the HTTP_USER_AGENT!
if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']) )) {

	// Need the functions:
	require ('includes/login_functions.inc.php');
	redirect_user('login.php');	

}

// Print a customized message:
echo "<h1>Logged In!</h1>
<p>You are now logged in as, {$_SESSION['username']}!</p>";

include ('includes/footer.html');
?>