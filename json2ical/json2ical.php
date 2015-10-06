<?php

/* This function allow do strpos function to an array, send the same parameters like in strpos it only change the needle 
* $haystack = string
* $needle   = array
*/
function strposa($haystack, $needle, $offset=0) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $query) {
        if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
    }
    return false;
}


/* At times, you will see that getting a page with curl will not return the same page that you see when getting the page with your browser. 
Then you know it is time to set the User Agent field to fool the server into thinking you're one of those browsers.
*/
$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

//  Initiate curl
$ch = curl_init();
// Disable SSL verification
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Setting the URL
// Set the URL where is allocated the JSON file
$url = "http://www.thevaleonline.com/whats-on/?format=json";
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);

$test = json_decode($result, true);

$output = "BEGIN:VCALENDAR
METHOD:PUBLISH
VERSION:2.0
PRODID:-Business Name//Product Name//EN\n";

foreach ($test["upcoming"] as $key => $value):
	// If the url have the extension output the string completed
	if(strposa($value["assetUrl"],array("jpg","png","jpeg"))){
		$attachment = $value["assetUrl"];
	}
	// If not remove slash (/) and add the jpg extension
	else{ 
		$attachment = substr ($value["assetUrl"] , 0 , (strlen($value["assetUrl"])-1)).".jpg";
	}
	if($value["body"]!=""){
		// Format description, add the link to read more and only take the first 100 characters.
		$description = substr(strip_tags(str_replace(array("\n", "\r","\t","\u2022","•","&nbsp;"), '',preg_replace('/([\,;])/','\\\$1',$value["body"]))), 0,100)."...<a href='http://www.thevaleonline.com/whats-on/".$value["urlId"]."?webview=1'>Read more</a>";
	}else{
		$description = "";
	}
	$output .=
"BEGIN:VEVENT
SUMMARY:".addcslashes($value["title"], ",\\;")."
UID:".$value["id"]."
DTSTART:" .date('Ymd\THis\Z', ($value["startDate"]/1000)) . "
DTEND:" . date('Ymd\THis\Z', ($value["endDate"]/1000))."
DESCRIPTION: ".$description."
LOCATION:".addcslashes($value["location"]["addressTitle"], ",\\;")." ".addcslashes($value["location"]["addressLine1"], ",\\;")." ".addcslashes($value["location"]["addressLine2"], ",\\;")." ".addcslashes($value["location"]["addressCountry"], ",\\;")."
GEO:".$value["location"]["mapLat"].";".$value["location"]["mapLng"]."
ATTACH:".$attachment."
END:VEVENT\n";
endforeach;
 
// close calendar
$output .= "END:VCALENDAR";

// Output to .ics file
$myfile = fopen("events.ics", "w") or die("Unable to open file!");
fwrite($myfile, $output);
fclose($myfile);

// Serve the file to dowload by the user.
$file_url = $_SERVER["SERVER_NAME"].'/json2ical/events.ics';
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
readfile("http://".$file_url);

?>