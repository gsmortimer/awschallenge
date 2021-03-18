<?php
//php-based whois plugin
include 'phpWhois.org/src/whois.main.php';

$PERMITTED_DOMAIN="";

validate_referer ();

//get public IP
$ip_addr = get_ip();

//validate public IP
if (check_ip($ip_addr)) {
        //execute whois lookup
        $whois = new Whois();
        $result = $whois->Lookup($ip_addr,false);
        echo (json_encode($result));

} else {
        //if IP validation fails.
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

?>
