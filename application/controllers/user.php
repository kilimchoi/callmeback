<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{
	public function call()
	{
		require 'twilio-php-master/Services/Twilio.php';
		//$sid = "AC64810bed2c2bff032b7e7e09712a6e11";
		$sid = "AC8583719934f105751c38ffcc1ca4aa13";
		//$token = "af5ec0ffd4db258e7dbdd33413b26646";
		$token = "8627a06e8f3462235ed1ef20f0df937d";
		$client = new Services_Twilio($sid, $token);
		$call = $client->account->calls->create("+16175843998", "+16175843998", "http://www.ocf.berkeley.edu/~xielu/voice.php?callback=".urlencode("+12137008466"), array(
			'Record' => 'true'
		));
		$recordings = $client->account->recordings->get($call->sid);
		echo $recordings;
	}

	public function sms()
	{
		require 'twilio-php-master/Services/Twilio.php';
		$sid = "AC64810bed2c2bff032b7e7e09712a6e11";
		$token = "af5ec0ffd4db258e7dbdd33413b26646";
		$client = new Services_Twilio($sid, $token);
		$message = $client->account->messages->sendMessage("+16175843998", "+14084776294", "Hello monkey!");
		echo $message->sid;
	}
}

?>
