<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{	
	public function index()
	{
		$this->load->view('user_call_view');
	}
	
	private function getserverurl()
	{
		return "http://www.ocf.berkeley.edu/~xielu/hackathon/index.php/";
	}
	
	public function initialxml()
	{
		echo "<?xml version='1.0' encoding='UTF-8'?>";
		echo "<Response>";
		echo "<Gather action='".$this->getserverurl()."user/forwardxml?callback=".urlencode($_GET['callback'])."' numDigits='1'>";
		echo "<Say voice='alice' loop='0'>Please press any key to speak to the customer.</Say>";
		echo "</Gather>";
		echo "</Response>";
	}
	
	public function forwardxml()
	{
		echo "<?xml version='1.0' encoding='UTF-8'?>";
		echo "<Response>";
		echo "<Say voice='alice'>Please hold on. Dialing the customer.</Say>";
		echo "<Dial timeout='60'>".$_GET['callback']."</Dial>";
		echo "</Response>";
	}
	
	public function call()
	{
		require 'twilio-php-master/Services/Twilio.php';
		//$sid = "AC64810bed2c2bff032b7e7e09712a6e11";
		$sid = "AC8583719934f105751c38ffcc1ca4aa13";
		//$token = "af5ec0ffd4db258e7dbdd33413b26646";
		$token = "8627a06e8f3462235ed1ef20f0df937d";
		$from = "+16175843998";
		
		$client = new Services_Twilio($sid, $token);
		
		$targetnumber = isset($_POST['targetnumber'])?$_POST['targetnumber']:"+16175843998";
		$sourcenumber = isset($_POST['sourcenumber'])?$_POST['sourcenumber']:"+12137008466";
		$sourceemail = isset($_POST['sourceemail'])?$_POST['sourceemail']:"eecsxielu@gmail.com";
		
		$call = $client->account->calls->create($from, $targetnumber, $this->getserverurl()."user/initialxml?callback=".urlencode($sourcenumber), array(
			'Record' => 'true',
			'StatusCallback' => $this->getserverurl().'user/callback?email='.$sourceemail
		));
		$this->load->view('user_call_success_view');
	}
	
	public function callback()
	{
		$this->load->library('email');
		$config['protocol'] = "smtp";
		$config['smtp_host'] = "ssl://smtp.gmail.com";
		$config['smtp_port'] = "465";
		$config['smtp_user'] = "callmemaybeserver@gmail.com"; 
		$config['smtp_pass'] = "greylock2014";
		$config['charset'] = "utf-8";
		$config['mailtype'] = "html";
		$config['newline'] = "\r\n";
		
		$this->email->initialize($config);
		
		$this->email->from('callmemaybeserver@gmail.com', 'CallMeMaybe');
		$list = array($_GET['email']);
		$this->email->to($list);
		$this->email->reply_to('callmemaybeserver@gmail.com', 'CallMeMaybe');
		$this->email->subject('Your Call Recording is Available for Downloading');
		$this->email->message('Dear Customer, <p>Thanks for choosing CallMeMaybe. The recording of your recent phone call with '.$_POST['To'].' is available at:<p>'.$_POST['RecordingUrl'].'<p>Have a good day!<p><p>Sincerely,<br>Callmaybe');
		$this->email->send();
	}
}

?>
