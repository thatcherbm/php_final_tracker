<?php # index.php
$page_title = 'Encounter Tracker';
include ('includes/header.html');
?>

<h2>Tracking the Action</h2>
<p>Recently I've played a lengthy campaign of Dungeon's and Dragons 4E, as both player
	and GM over the course of the three year campaign as we've attempted to help my 
	friend realise a life goal of playing a single character in a Tabletop game all the
	way from level 1 to 30. There are a lot of things we enjoyed about 4E, though once 
	we hit Paragon Tier (levels 11-20) we started to lose interest as the game got more 
	complex and the combat sessions started to drag on. I have for a while wanted to 
	create a program to help manage this flow a little better.
</p>
<p>The purpose of the program is to keep track of some of the more pesky details of
	the combat. By Epic Tier (levels 21-30) in 4E I was having to keep track of dozens of 
	effects in play. Things like Monster Auras, environmental effects, and the durations 
	a variety of effects created by both the players and the monsters.  Future additions 
	to the tool will also aid players who have a hard time remembering to use certain 
	powers or abilities.
</p>	
<p>Once you are logged in you can view encounters which have been created. New registrations 
	won't have the ability to create anything, just see public details.  GMs and MODs can 
	promote your account to PLAYER status allowing you to create characters which then can be 
	added to encounters, and MOD's can promote accounts to GM status allowing the creation of 
	encounters.  At present the deletion of creatures/effects/encounters is not implemented.  
	Future implementations will also allow MODs to transfer ownership of entities between users.
</p>

	
<?php 
include ('includes/footer.html');
?>