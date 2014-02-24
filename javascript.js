function fadeIn()  {
	// this fades the speech bubble in but waits three seconds before doing so
	// to allow the animated gif to catch up
	setTimeout(function(){document.getElementById("fader").className += " load"}, 2000);
	}
function hideLustine()  {
	// this looks at the source for the house image with the id "comingOut"
	// it checks to see if the source contains the string "animation03" which is the file of the hippo going back into the house
	// if the image is NOT the one where the hippo goes back in, then make it be that image and fade out the speech bubble
	// this prevents the image from replaying after someone has put focus on the text input and the hippo has already gone inside
	var pattern = new RegExp("animation03");
	if (!pattern.test(document.getElementById("comingOut").src))  {
		document.getElementById("comingOut").src = "images/animation03.gif";
		document.getElementById("fader").className += " out"
		}		
	}
