<?php # Script register.php
// This is the registration page for the site.
require ('includes/config.inc.php');
$page_title = 'Register';
include ('header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.

	// Need the database connection:
	require (MYSQL);
	
	// Trim all the incoming data:
	$trimmed = array_map('trim', $_POST);

	// Assume invalid values:
	$u = $e = $p = FALSE;

	// Check for a username:
	if (preg_match ('/^\w{4,40}$/i', $trimmed['username'])) {
		$u = $mysqli->real_escape_string ($trimmed['username']);
	} else {
		echo '<p class="error">Please enter your desired username!</p>';
	}
	
	// Check for an email address:
	if (filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL)) {
		$e = $mysqli->real_escape_string ($trimmed['email']);
	} else {
		echo '<p class="error">Please enter a valid email address!</p>';
	}

	// Check for a password and match against the confirmed password:
	if (preg_match ('/^\w{4,20}$/', $trimmed['password1']) ) {
		if ($trimmed['password1'] == $trimmed['password2']) {
			$p = $mysqli->real_escape_string ($trimmed['password1']);
		} else {
			echo '<p class="error">Your password did not match the confirmed password!</p>';
		}
	} else {
		echo '<p class="error">Please enter a valid password!</p>';
	}
	
	if ($u && $e && $p) { // If everything's OK...

		// Usernames must be unique
		// Make sure the username is available:
		$q = "SELECT user_id FROM users WHERE username='$u'";
		$r = $mysqli->query ($q);
		
		if ($r->num_rows == 0) { // Available.

			// Create the activation code:
			$a = md5(uniqid(rand(), true));

			// Add the user to the database:
			$q = "INSERT INTO users (email, pass, username, active, registration_date) VALUES ('$e', SHA1('$p'), '$u', '$a', NOW() )";
			$r = $mysqli->query ($q);

			if ($mysqli->affected_rows == 1) { // If it ran OK.
			
				// get the newly generated user_id (it is auto-incremented)
				$q = "SELECT user_id FROM users WHERE username='$u'";
				$r = $mysqli->query ($q);
				
				//store the result of the user_id query
				$row = $r->fetch_object();
				$uid = $row->user_id;

				// Send the email:
				$body = "Thank you for registering at Encounter Tracker. To activate your account, please click on this link:\n\n";
				$body .= BASE_URL . 'activate.php?x=' . $uid . "&y=$a";
				mail($trimmed['email'], 'Registration Confirmation', $body, 'From: thatcherbm@thatcherbm.com');
				
				// Finish the page:
				echo '<h3>Thank you for registering! A confirmation email has been sent to your address. Please click on the link in that email in order to activate your account.</h3>';
				include ('includes/footer.html'); // Include the HTML footer.
				exit(); // Stop the page.
				
			} else { // If it did not run OK.
				echo '<p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>';
			}
			
		} else { // The username is not available.
			echo '<p class="error">That username has already been registered.</p>';
		}
		
	} else { // If one of the data tests failed.
		echo '<p class="error">Please try again.</p>';
	}

	// Close the connection:
	$mysqli->close();
	unset($mysqli);

} // End of the main Submit conditional.
?>
	
<h1>Register</h1>
<form action="register.php" method="post">
	<fieldset>
	
	<p><b>Username:</b> <input type="text" name="username" size="20" maxlength="40" value="<?php if (isset($trimmed['username'])) echo $trimmed['username']; ?>" /><small>Use only letters, numbers, and the underscore. Must be between 4 and 40 characters long.</small></p>

	<p><b>Email Address:</b> <input type="text" name="email" size="30" maxlength="60" value="<?php if (isset($trimmed['email'])) echo $trimmed['email']; ?>" /> </p>
		
	<p><b>Password:</b> <input type="password" name="password1" size="20" maxlength="20" value="<?php if (isset($trimmed['password1'])) echo $trimmed['password1']; ?>" /> <small>Use only letters, numbers, and the underscore. Must be between 4 and 20 characters long.</small></p>

	<p><b>Confirm Password:</b> <input type="password" name="password2" size="20" maxlength="20" value="<?php if (isset($trimmed['password2'])) echo $trimmed['password2']; ?>" /></p>
	</fieldset>
	
	<div align="center"><input type="submit" name="submit" value="Register" /></div>

</form>

<?php include ('footer.php'); ?>