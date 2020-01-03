<?
/*
YOUTUBE ADS VIDEO HOST BLOCK RULE GENERATOR v1.0
Created by CryptoYakari
02.01.2020
https://twitter.com/CryptoYakari

This PHP script generates Unbound blocking rules for Youtube Advertisement Hosts
Static list https://raw.githubusercontent.com/anudeepND/youtubeadsblacklist/master/domainlist.txt
Static list Mirror https://raw.githubusercontent.com/mkaand/youtube-ads-blocker-ddwrt/master/domainlist.txt
Dynamic list https://api.hackertarget.com/hostsearch/?q=googlevideo.com
*/

$content = file_get_contents("https://raw.githubusercontent.com/anudeepND/youtubeadsblacklist/master/domainlist.txt");
$my_static_array = explode("\n",$content);
echo '#Static List Source: https://raw.githubusercontent.com/anudeepND/youtubeadsblacklist/master/domainlist.txt

';
for ($x = 0; $x < count($my_static_array)-1; $x++) {
If (strpos($my_static_array[$x],"---") == false){echo 'local-data: "' . $my_static_array[$x] . ' A 127.0.0.1"'."\n";}
} //for end

echo '


#Dynamic List Source: https://api.hackertarget.com/hostsearch/?q=googlevideo.com


';
$content = file_get_contents("https://api.hackertarget.com/hostsearch/?q=googlevideo.com");
$content = str_replace("\n",",",$content);
$my_array = explode(",",$content);
//var_dump($my_array);
for ($x = 2; $x <= count($my_array)-1; $x++) {

echo 'local-data: "' . $my_array[$x] . ' A 127.0.0.1"'."\n";
$x = $x + 1;
} //for end

?>
