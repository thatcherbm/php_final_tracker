<?php # index.php
$page_title = 'Tabletop RPG Tools :: Dice Simulator';
include ('includes/header.html');
?>
<h2>Rolling Dice</h2>
<p>In a movie, everything which happens follows a carefully written script, if it is
	written, then it happens.  In life, the results of what we want to do aren't so
	guaranteed.	We try to do things and sometimes we succeed and sometimes we don't.
</p>
<p>RPG's simulate this randomness with with a dice roll. The roll then is compared with 
	a standard of success or failure; if you meet or beat the standard, you succeed. 
	Some tasks are more difficult and need a higher roll to accomplish, but you also 
	might be particularly good at something so your chance of success is higher.  This
	is simulated with a Modifier to the roll.
</p>

<form method="post" action="">
	<label for="numDie">How many dice do you wish to roll?</label>
	<input type="text" name="numDie" id="numDie" size="2"><br><br>
	<label for="dieType">Die Type?</label>
	<input type="text" name="dieType" id="dieType" size="2" required="required"><br><br>
	<label for="modifier">Modifier?</label>
	<input type="text" name="modifier" id="text" size="2"><br><br>
	<input type="button" name="button" style="margin-left: 120px;" value="Roll Dice" onClick="rollDice(this.form)">  
</form>
<br><p id="result">Result of rolling: <span id="dieNumb" style="color: #0000FF;">?</span>d<span id="die" style="color: #0000FF;">?</span> + <span id="bonus" style="color: #0000FF;">?</span> = <span id="roll" style="color: #0000FF;"></span><br><br>
Rolls:<span id="rolls" style="margin-left: 90px; color: #0000FF;"></span></p>

<script>
	function rollDice(form) {
		var n = parseInt(form.numDie.value);
		var d = parseInt(form.dieType.value);
		var m = parseInt(form.modifier.value);
		var r, a;
		var out = "";
		
		a = Math.floor(Math.random()*d)+1;
		out += a;
		if  (n <= 1) {
		n = 1;
		}
		else {
			document.getElementById('dieNumb').innerHTML = n ;
			n--;
			for (n; n > 0; n--) {
				r =  Math.floor(Math.random()*d)+1;
				a += r;
				out += "+" + r;
			}		
		}
		a += m;
		document.getElementById('die').innerHTML = d ;
		document.getElementById('bonus').innerHTML = m ;
		document.getElementById('roll').innerHTML = a ;
		document.getElementById('rolls').innerHTML = out ;
	}
</script>


	
<?php 
include ('includes/footer.html');
?>