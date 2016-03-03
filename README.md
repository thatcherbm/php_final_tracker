# php_final_tracker
Final project for PHP and MySQL course, with continued development for personal use.

I'll admit the code is a bit hodgepodge as the point was to show all my wicked coding skills so sometimes I deliberately do the same thing two different ways to show that off.

Noteably missing of course is the mysqli_connect script, can't be giving out that sort of info as the site is live at thatcherbm.com/tracker.

If you are an employer checking this out and want to get the user experience at the live site there are some demo accounts you can try.  Please don't screw around too much, try creating then deleting stuff instead of deleting or messing with what is there.

The primary intended user is the GameMaster level user who has the ability to create and edit encounters (though this could be used by a player to keep track of stuff as easy as the gm).  Username: GameMaster pass: EncGm will let you see some pre-generated stuff and play around a bit.  If you are running a game and want to let players view some things they can do so with a player account (also allows a bit of delegation forcing them to create characters with powers you wish to track) Username: Player pass: EncPlayer.  If you create a new account you start out as a spectator and can't do much until a GameMaster level or higher account promotes you to player. Username: Spectator pass: EncSpec will let you see that functionality without having to register an account.  There is a Moderator level of operation that can promote players to GameMaster level, and further an admin level which promotes to Moderator and will later be able to view some other information.  Username: Moderator pass: EncMod. Username: Admin pass: EncAdmin.

03-03-2016
My short experience at Groopdealz introduced me to bootstrap and a different way of approaching my page creation.  Jack was using objects to collect and format data from the database then inserting small bits of information into html code rather than echoing it all out.  I've made my first attempt at a similar practice with the view_encounter page.  This practice makes my front end design a lot easier to understand, though i may need to do additional development to make the code in the object constructor easier to read.
