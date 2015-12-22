<?php # Script login_functions.inc.php
// This page defines two functions used by the login/logout process.
// The redirect function is used in various places on the site primarily relating
// to security (sending users away from scripts they shouldn't be in) and returning
// to an editing screen from a script

/* This function determines an absolute URL and redirects the user there.
 * The function takes one argument: the page to be redirected to.
 * The argument defaults to index.php.
 */
function redirect_user ($page = 'index.php') {

	// Start defining the URL...
	// URL is http:// plus the host name plus the current directory:
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	
	// Remove any trailing slashes:
	$url = rtrim($url, '/\\');
	
	// Add the page:
	$url .= '/' . $page;
	
	// Redirect the user:
	header("Location: $url");
	exit(); // Quit the script.

} // End of redirect_user() function.


/* This function validates the form data (the email address and password).
 * If both are present, the database is queried.
 * The function requires a database connection.
 * The function returns an array of information, including:
 * - a TRUE/FALSE variable indicating success
 * - an array of either errors or the database result
 */
function check_login($mysqli, $username = '', $pass = '') {

	$errors = array(); // Initialize error array.

	// Validate the username:
	if (empty($username)) {
		$errors[] = 'You forgot to enter your username.';
	} else {
		$u = $mysqli->real_escape_string(trim($username));
	}

	// Validate the password:
	if (empty($pass)) {
		$errors[] = 'You forgot to enter your password.';
	} else {
		$p = $mysqli->real_escape_string(trim($pass));
	}

	if (empty($errors)) { // If everything's OK.

		// Retrieve the user_id, username, and user_level for that username/password combination:
		$q = "SELECT user_id, username, user_level FROM users WHERE username='$u' AND pass=SHA1('$p')";		
		$r = $mysqli->query ($q); // Run the query.
		
		// Check the result:
		if ($r->num_rows == 1) {

			// Fetch the record:
			$row = $r->fetch_array(MYSQLI_ASSOC);
	
			// Return true and the record:
			return array(true, $row);
			
		} else { // Not a match!
			$errors[] = 'The username and password entered do not match those on file.';
		}
		
	} // End of empty($errors) IF.
	
	// Return false and the errors:
	return array(false, $errors);

} // End of check_login() function.

