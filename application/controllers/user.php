<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{	
	public function index()	/* set index page view */
	{
		$this->load->view('user_call_view');
	}
	
	private function getserverurl()	/* get server url so that twilio can access xml files and post status call back */
	{
		return "http://www.ocf.berkeley.edu/~xielu/hackathon/index.php/";
	}
	
	public function initialxml() /* return initial xml file for twilio to begin the call */
	{
		echo "<?xml version='1.0' encoding='UTF-8'?>";
		echo "<Response>";
		echo "<Gather action='".$this->getserverurl()."user/forwardxml?callback=".urlencode($_GET['callback'])."&amp;mail=".urlencode($_GET['mail'])."' numDigits='1'>";
		echo "<Say voice='alice' loop='0'>Please press any key to speak to the customer.</Say>";
		echo "</Gather>";
		echo "</Response>";
	}
	
	public function forwardxml() /* return forward xml file for twilio to connect to customer if rep available */
	{
		echo "<?xml version='1.0' encoding='UTF-8'?>";
		echo "<Response>";
		echo "<Say voice='alice'>Dialing the customer. Please hold on.</Say>";
		echo "<Dial record='record-from-answer' action='".$this->getserverurl()."user/callback?mail=".urlencode($_GET['mail'])."'>".$_GET['callback']."</Dial>";
		echo "<Say voice='alice'>Thanks for your service. Goodbye.</Say>";
		echo "</Response>";
	}
	
	public function call() /* make the call request to twilio; receive post request to process */
	{
		require 'twilio-php-master/Services/Twilio.php';
		
		$sid = "AC64810bed2c2bff032b7e7e09712a6e11";
		$token = "af5ec0ffd4db258e7dbdd33413b26646";
		$from = "+17039911371";
		
		/* // trial account
		$sid = "AC8583719934f105751c38ffcc1ca4aa13";
		$token = "8627a06e8f3462235ed1ef20f0df937d";
		$from = "+16175843998";
		*/
		
		$client = new Services_Twilio($sid, $token);
		
		$targetnumber = isset($_POST['targetnumber'])?$_POST['targetnumber']:"+14084776294";
		$sourcenumber = isset($_POST['sourcenumber'])?$_POST['sourcenumber']:"+16175843998";
		$sourceemail = isset($_POST['sourceemail'])?$_POST['sourceemail']:"eecsxielu@gmail.com";
		
		$call = $client->account->calls->create($from, $targetnumber, $this->getserverurl()."user/initialxml?callback=".urlencode($sourcenumber)."&mail=".urlencode($sourceemail), array(
		'Record' => 'true'
		));
		
		$this->load->view('user_call_success_view');
	}
	
	public function callback()	/* return end xml file to hang up the call; send email to user; called by twilio when call ends */
	{
		echo "<?xml version='1.0' encoding='UTF-8'?>";
		echo "<Response>";
		echo "<Say voice='alice'>The customer has left the call. Thanks for your service. Goodbye!</Say>";
		echo "</Response>";
		
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
		$list = array($_GET['mail']);
		$this->email->to($list);
		$this->email->reply_to('callmemaybeserver@gmail.com', 'CallMeMaybe');
		
		$status = $_POST['DialCallStatus'];
		if ($status == "completed") {		
			$this->email->subject('Your Call Recording is Available for Downloading');
			$this->email->message('Dear Customer, <p>Thanks for choosing CallMeMaybe. The recording of your recent phone call with '.$_POST['To'].' is available at:<p>'.$_POST['RecordingUrl'].'<p>Have a good day!<p><p>Sincerely,<br>Callmaybe');
		}
		else {
			$this->email->subject('Your Recent Call Schedule has Failed');
			$this->email->message('Dear Customer, <p>Thanks for choosing CallMeMaybe. We are sorry to inform you that your recent phone call schedule with '.$_POST['To'].' has failed because we get no response from you. Please try again.<p>Thanks for your understanding. Have a good day!<p><p>Sincerely,<br>Callmaybe');
		}
		$this->email->send();
	}
}

?>
