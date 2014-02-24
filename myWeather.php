<?php

function WeatherDecode($numbCode) {
	// load the possible values for weatherCode into an array for each weather condition type
	// these come from the World Weather Online Docs, i didn't make em up
	$clear = array(116,113,"name"=>"clear");
	$cloudy = array(119,122,"name"=>"cloudy");
	$fog = array(143,248,260,"name"=>"foggy");
	$thunder = array(200,386,389,392,"name"=>"thundering");
	$sleet = array(182,185,281,284,311,314,317,320,350,362,365,374,377,"name"=>"sleeting");
	$rain = array(176,263,266,293,296,299,302,305,308,353,356,359,"name"=>"raining");
	$snow = array(179,227,230,323,326,329,332,335,338,368,371,395,"name"=>"snowing");
	
	// put the weather condition types into an array to search for a match
	$weatherConditions = array($clear,$cloudy,$fog,$thunder,$sleet,$rain,$snow);
	for ($i=0; $i<count($weatherConditions); $i++)  {
		if (in_array($numbCode,$weatherConditions[$i])) return $weatherConditions[$i]["name"];
	}
	// in case World Weather Online cannot find the place or the weather of that place...
	return("unknown");  
}



// $location will come from user input via POST 
// spaces break the query so let's encode them
if ($_POST["location"])  { 
	$location = $_POST["location"];  


	// the data comes from World Weather Online as an XML string
	// tell PHP that this is XML so we can extract the goodies
	$rawData = file_get_contents('http://api.worldweatheronline.com/free/v1/weather.ashx?q='.rawurlencode($location).'&format=xml&date=today&fx=no&includelocation=yes&show_comments=no&key=kb8uw49dy8e6j3br2685fys7');
	$xmlData = simplexml_load_string($rawData);

	// get the weather code, and current temperature
	$weatherCode = $xmlData->current_condition->weatherCode;
	$temperature = $xmlData->current_condition->temp_F;
	$weather = WeatherDecode($weatherCode);

	// also get the city, state and/or country that is closest to $location
	// piece the places back together as $youAreHere
	$nearestArea = $xmlData->nearest_area->areaName;
	$country = $xmlData->nearest_area->country;
	if (empty($nearestArea))  unset($location);
	if ($country=="USA" || $country=="United States Of America") $youAreHere = $nearestArea.", ".$xmlData->nearest_area->region;
	else $youAreHere = $nearestArea.", ".$country;
}
?>
<!DOCTYPE html>
<html>
	<head>	
		<meta charset="UTF-8">
		<title>What's my Weather?</title>
		<script language="javascript" type="text/javascript" src="javascript.js"></script>
		<link rel="stylesheet" type="text/css" href="style.css">

	<?php		
	// if someone has entered their location, add a background image to the container DIV 
	// that graphically displays the current weather condition
	if (!empty($location))  { 
		echo "\t<style type='text/css'> \n";
		echo "\t\t#container { \n";
		echo "\t\t\tbackground-image: url(images/".$weather.".gif);\n";
		echo "\t\t}\n";
		echo "\t\t</style>\n";
		echo "\t\t<script> \n\t\t\t";
		// this is an event listener to avoid using onLoad in the <body> 
		echo "document.addEventListener('DOMContentLoaded', function(){fadeIn();}, false);";
		echo "\n\t\t</script>";
	}
	?>		

	</head>
	<body>
	<div id="container">
	<?php
	// if someone has entered their location, load up both animated gifs of the hippo (one for display, one foe the cache).  
	// otherwise just show the static gif of the house
	if (!empty($location))  {
		echo "\t<img src='images/animation02.gif' class='house' id='comingOut'>";
		echo "\t<img src='images/animation03.gif' class='house' id='comingIn'>";
	}
	else echo "\t<img src='images/house3.gif' class='house' id='comingOut'>";
	?>


	<div id="fader" class="bubble">
<?php
if (!empty($location))  { 
	echo "\t\t<p>The weather in $youAreHere is ".$weather.".</p>\n";
	echo "\t\t<p>It's $temperature&deg;F out here!</p>\n";
}
?>
	</div>



	
	</div>
	<div id="location-form">
		<form method="post"> 
	 		<legend>How's the weather out your window?</legend>
	<?php
	// if someone has entered their location, get ready to load the image of the hippo going back into her home
	// this image is to play when there is Focus on the text input to try another location
	if (!empty($location))  {	 		
		echo "\t\t<input type='text' name='location' id='location' placeholder='city or zipcode' onFocus='hideLustine()'>";
	}
	else echo "\t\t<input type='text' name='location' id='location' placeholder='city or zipcode'>";
	?>	
	 		<input type="submit" value="Find Out">
		</form>
	</div>



	<div id="footer">
		Powered by <a href="http://www.worldweatheronline.com/" title="Free local weather content provider" target="_blank">World Weather Online</a>
	</div>
	</body>
</html>	

