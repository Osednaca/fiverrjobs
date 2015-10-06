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
$url = "http://out.basetool.se/export/rss?channel=1000&a=13&c=5&al=4&s=0&l=se&url=http://www.vastsverige.com/sv/uddevalla/products";
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);


$xml=new SimpleXMLElement($result) or die("Error: Cannot create object");


$output = "BEGIN:VCALENDAR
METHOD:PUBLISH
VERSION:2.0
PRODID:-Business Name//Product Name//EN\n";

foreach ($xml->channel[0]->item as $key => $value):

	preg_match_all('/<img[^>]+>/i',$value->description, $result); 
	preg_match_all('/(src)=("[^"]*")/i',$result[0][0], $img);

	if($value->description!=""){
		// Format description, add the link to read more and only take the first 100 characters.
		$description = substr(strip_tags(str_replace(array("\n", "\r","\t","\u2022","•","&nbsp;"), '',preg_replace('/([\,;])/','\\\$1',$value->description))), 0,200)."...<a href='http://www.vastsverige.com/mobile/Product.aspx?pid=52853&l=sv&prodid=".substr($value->guid,9)."'>Read more</a>";
	}else{
		$description = "";
	}
	$output .=
"BEGIN:VEVENT
SUMMARY:".addcslashes($value->title, ",\\;")."
UID:".substr($value->guid,9)."
DTSTART:" .date('Ymd\THis\Z', strtotime($value->pubDate)) . "
DTEND:" .date('Ymd\THis\Z', strtotime($value->pubDate)) . "
DESCRIPTION: ".$description."
ATTACH:".str_replace('"', '', $img[2][0])."
END:VEVENT\n";
endforeach;

// close calendar
$output .= "END:VCALENDAR";

// Output to .ics file
$myfile = fopen("events.ics", "w") or die("Unable to open file!");
fwrite($myfile, $output);
fclose($myfile);
// Serve the file to dowload by the user.
$file_url = $_SERVER["SERVER_NAME"].'/rss2ical/events.ics';
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 

readfile("http://".$file_url);

?>