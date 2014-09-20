/*
	execute this code in your browser when you are in:
	http://www.wowhead.com/zone=XXX#npcs
	to extract the entries of the NPCs in that zone
	
	if the zone have more of 100 NPCs spawned you need to click
	"Next" to load the others NPCs, now you will be in
	http://www.wowhead.com/zone=XXX#npcs:100+1
	re-execute the code to extract the other entries.
*/
javascript: function ex(){
	var a = document.getElementsByClassName("listview-cleartext");
	var text = "";
	for(var i = 0; i < a.length; i++)
	{
		text += (a[i].href).replace("http://www.wowhead.com/npc=", "") +", ";
	}
	alert(text);
} ex();