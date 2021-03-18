<?php
//vt.php
//TODO: move key to a file outside docroot, as http servers are untrustworthy.
$API_KEY="x-apikey: ";
$PERMITTED_DOMAIN="";

validate_referer();

$ip_addr = get_ip();
//	$ip_addr = "8.8.8.8"; //Uncomment for testing

if (check_ip($ip_addr)) {
        $result = vt_query($ip_addr, "resolutions");
        $json = json_decode($result);
        if (!array_key_exists('data', $json)) {
                header("HTTP/1.1 500 Server Error");
                echo ("Access VirusTotal API Failed.");
        }
        $output = json_encode($json->data);
        echo ($output);

} else {
        //if validation fails
        header("HTTP/1.1 500 Server Error");
        echo ("Somehow, your Public IP is not a valid IP address");
}

//ensure referal; came from same domain
function validate_referer () {
        $referer = $_SERVER['HTTP_REFERER'];
        if (!preg_match("/^https?:\/\/([\w\d]+\.)?" . $GLOBALS["PERMITTED_DOMAIN"] . "/", $referer)) {
                header("HTTP/1.1 401 Unauthorised");
                echo ("Sorry, we do not allow referrals");
                exit;

        }
}

//Obtain Client's public IP
function get_ip () {
        //whether ip is from share internet
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   {
                $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  {
                $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //whether ip is from remote address
        else {
                $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        return $ip_address;
}


//validate public IP
function check_ip($ip) {
        $ip_regex = "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/";
        return preg_match($ip_regex, $ip);
}

//obtain VirusTotal API data 
function vt_query($ip, $mode) {
        //we need to use php-curl so we can add custom header 
        $uri = "https://www.virustotal.com/api/v3/ip_addresses/" . $ip . "/" . $mode;
        $ch = curl_init($uri);
        curl_setopt_array($ch, array(
                CURLOPT_HTTPHEADER  => array($GLOBALS["API_KEY"]),
                CURLOPT_RETURNTRANSFER  =>true,
                CURLOPT_VERBOSE     => 1
        ));

        // result is in JSON
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
}


?>

