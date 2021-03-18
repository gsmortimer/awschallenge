<?php
$PERMITTED_DOMAIN="";

validate_referer ();

echo (get_ip());

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

//ensure referal; came from same domain
function validate_referer () {
        $referer = $_SERVER['HTTP_REFERER'];
        if (!preg_match("/^https?:\/\/([\w\d]+\.)?" . $GLOBALS["PERMITTED_DOMAIN"] . "/", $referer)) {
                header("HTTP/1.1 401 Unauthorised");
                echo ("Sorry, we do not allow referrals");
                exit;

        }
}

?>
