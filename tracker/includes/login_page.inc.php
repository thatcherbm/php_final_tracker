<?php # Script login_page.inc.php
// This page is included by login.php
// This page prints any errors associated with logging in
// and it creates the entire login page, including the form.


// Print any error messages, if they exist:
if (isset($errors) && !empty($errors)) {
	echo '<h1>Error!</h1>
	<p class="error">The following error(s) occurred:<br />';
	foreach ($errors as $msg) {
		echo " - $msg<br />\n";
	}
	echo '</p><p>Please try again.</p>';
}

// Display the form:
?><h1>Login</h1>
<form action="login.php" method="post">
	<label>Username:</label><input type="text" name="username" size="20" maxlength="40" />
	<br>
	<label>Password:</label><input type="password" name="pass" size="20" maxlength="20" />
	<br>
	<input style="margin-left: 17em" type="submit" name="submit" value="Login" />
</form>
<h2>Not registered?</h2>
<a href="register.php">REGISTER</a>

<?php include ('includes/footer.html'); ?>