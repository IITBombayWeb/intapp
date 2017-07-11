<?php

//use Drupal\basiccart\Utility;

function pdt_token($transaction_id) {
  		
		$pp_hostname = "www.sandbox.paypal.com"; // Change to www.sandbox.paypal.com to test against sandbox
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-synch';
		$tx_token = $transaction_id;
		$auth_token = "GfqCw6b45-T-AZYVEMwCwy0IB4gNCO8QXVvHd0irgnbk4hhB80Xm437MddW";
		$req .= "&tx=$tx_token&at=$auth_token"; 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://$pp_hostname/cgi-bin/webscr");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		//set cacert.pem verisign certificate path in curl using 'CURLOPT_CAINFO' field here,
		//if your server does not bundled with default verisign certificates.
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: $pp_hostname"));
		$res = curl_exec($ch);
		curl_close($ch);
			if(!$res){
    			$status = "Invalid";
			}else{
                         $response = $res;
                         $status = $response;
			}

		return $status;
        
}