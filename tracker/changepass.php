<?php # Script changepass.php
// This page allows users to change their password for the site.
require ('includes/config.inc.php');
$page_title = 'Change Password';
include ('header.php');

// If no session value is present, redirect the user:
// Also validate the HTTP_USER_AGENT!
if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']) )) {
	// Need the functions:
	require ('includes/login_functions.inc.php');
	redirect_user(login.php);	
}

$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.

	// Need the database connection:
	require (MYSQL);
	
	// Trim all the incoming data:
	$trimmed = array_map('trim', $_POST);

	// Assume invalid values:
	$op = $p = FALSE;

	// Check for the old password:
	if (preg_match ('/^\w{4,20}$/', $trimmed['oldpass'])) {
		$op = $mysqli->real_escape_string ($trimmed['oldpass']);
	} else {
		echo '<p class="error">Please enter your old password!</p>';
	}
	
	// Check for a new password and match against the confirmed password:
	if (preg_match ('/^\w{4,20}$/', $trimmed['password1']) ) {
		if ($trimmed['password1'] == $trimmed['password2']) {
			$p = $mysqli->real_escape_string ($trimmed['password1']);
		} else {
			echo '<p class="error">Your password did not match the confirmed password!</p>';
		}
	} else {
		echo '<p class="error">Please enter a valid password!</p>';
	}
	
	if ($op && $p) { // If everything's OK...

		// Make sure the old password matches the one provided:
		$q = "SELECT pass FROM users WHERE user_id='$uid'";
		$r = $mysqli->query ($q);
		
		if ($r->num_rows == 1) { // user exists
		
			$row = $r->fetch_object();
			$pass = $row->pass;
			
			if (sha1($op)==$pass) { // matches
				// Update the user's password:
				$q = "UPDATE users SET pass=SHA1('$p') WHERE user_id='$uid'";
				$r = $mysqli->query ($q);				
				
				if ($mysqli->affected_rows == 1) { // If it ran OK.

					// Finish the page:
					echo '<h3>Password Successfully Updated</h3>';
					include ('includes/footer.html'); // Include the HTML footer.
					exit(); // Stop the page.
					
				} else { // If it did not run OK.
					echo '<p class="error">Password could not be changed due to a system error. We apologize for any inconvenience.</p>';
				}

			} else {
				echo '<p class="error">Password does not match the one on file.</p>';
			}
			
		} else { // Not a registered user
			echo '<p class="error">You are not a registered user</p>';
		}
		
	} else { // If one of the data tests failed.
		echo '<p class="error">Please try again.</p>';
	}

	// Close the connection:
	$mysqli->close();
	unset($mysqli);

} // End of the main Submit conditional.
?>
	
<h1>Change Password</h1>
<form action="changepass.php" method="post">
	<fieldset>
	
	<p><b>Old Password:</b> <input type="password" name="oldpass" size="20" maxlength="40" value="<?php if (isset($trimmed['oldpass'])) echo $trimmed['oldpass']; ?>" /></p>
	
	<p><small>Use only letters, numbers, and the underscore. Must be between 4 and 20 characters long.</small></p>
	<p><b>New Password:</b> <input type="password" name="password1" size="20" maxlength="20" value="<?php if (isset($trimmed['password1'])) echo $trimmed['password1']; ?>" /> </p>

	<p><b>Confirm Password:</b> <input type="password" name="password2" size="20" maxlength="20" value="<?php if (isset($trimmed['password2'])) echo $trimmed['password2']; ?>" /></p>
	</fieldset>
	
	<div align="center"><input type="submit" name="submit" value="Change" /></div>

</form>

<?php include ('footer.php'); ?>