<?php

namespace Think;
vendor("vendor.autoload");

use Omnimail\Email;
use Omnimail\AmazonSES;
use Omnimail\Mailgun;
use Omnimail\Mailjet;
use Omnimail\Mandrill;
use Omnimail\Postmark;
use Omnimail\SendinBlue;
use Omnimail\Sendgrid;

class SuperEmail
{
    public function __construct()
    {

        parent::__construct();
    }

    public function sendemail($to, $subject, $content)
    {
        if (empty($subject)) {
            $subject = "Mail from " . SHORT_NAME;
        }
        if (empty($content)) {
            $subject = "No content was  from provided in email";
        }

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            die('Invalid email');
        }
        if (APP_DEMO == 0) {
            $array = array('status' => 1, 'message' => 'Email Ran');
            switch (DEFAULT_MAILER) {
                case sendgrid:
                    self::sendgrid($to, $subject, $content);
                    break;
                case sendinblue:
                    self::sendinblue($to, $subject, $content);
                    break;
                case mailjet:
                    self::mailjet($to, $subject, $content);
                    break;
				case mailgun:
                    self::mailgun($to, $subject, $content);
                    break;
				case mandrill:
                    self::mandrill($to, $subject, $content);
                    break;
				case amazonses:
                    self::amazonses($to, $subject, $content);
                    break;
				case postmark:
                    self::postmark($to, $subject, $content);
                    break;
				case mailjet:
                    self::mailjet($to, $subject, $content);
                    break;
				case phpmail:
                    self::phpmail($to, $subject, $content);
                    break;
				case smtpmail:
                    self::smtpmail($to, $subject, $content);
                    break;
                default :
                    self::phpmail($to, $subject, $content);

            }
        } else {
            $array = array('status' => 0, 'message' => 'App is in demo mode');
        }
        return json_encode($array);


    }
	private function phpmail($to, $subject, $content)
    {
        $setFrom = GLOBAL_EMAIL_SENDER;        
		$headers = "From: " . strip_tags($setFrom) . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$mailer=mail($to,$subject,$content,$headers);
        if (ADMIN_DEBUG == 1) {
            clog("phpmail", $mailer);
        }
    }
	private function smtpmail($to, $subject, $content)
    {
        $setFrom = GLOBAL_EMAIL_SENDER;        
		$headers = "From: ".$setFrom;
		//$mailer=mail($to,$subject,$content,$headers);
		$mailer=SMTPMail($to, $setFrom, $subject, $content);
        if (ADMIN_DEBUG == 1) {
            clog("smtpmail", $mailer);
        }
    }
    private function amazonses($to, $subject, $content)
    {
        $mailer = new AmazonSES(AMAZONSES_accessKey, AMAZONSES_secretKey, AMAZONSES_region, AMAZONSES_verifyPeer, AMAZONSES_verifyHost);

        $setFrom = GLOBAL_EMAIL_SENDER;
        $email = (new Email())
            ->addTo($to)
            ->setFrom($setFrom)
            ->setSubject($subject)
            ->setTextBody($content);
        $mailer->send($email);
        if (ADMIN_DEBUG == 1) {
            clog("amazonses", $mailer);
        }
    }

	private function sendgrid($to, $subject, $content)
    {
		
		$params = array(
    'to'        => $to,
    'from'      => GLOBAL_EMAIL_SENDER,
    'fromname'  => SHORT_NAME,
    'subject'   => $subject,
    'text'      =>$content,
    'html'      => $content,
    
  );
		$url = 'https://api.sendgrid.com/';
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . SENDGRID_API_KEY));
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

		// obtain response
		$response = curl_exec($session);
		curl_close($session);

		// print everything out
        if (ADMIN_DEBUG == 1) {
            clog("sendgrid", $response);
        }
    }
    private function v1sendgrid($to, $subject, $content)
    {
        $mailer = new Sendgrid(SENDGRID_API_KEY);
        $setFrom = GLOBAL_EMAIL_SENDER;
        $email = (new Email())
            ->addTo($to)
            ->setFrom($setFrom)
            ->setSubject($subject)
            ->setTextBody($content);
        $mailer->send($email);
        if (ADMIN_DEBUG == 1) {
            clog("sendgrid", $mailer);
        }
    }

    private function mandrill($to, $subject, $content)
    {
        $mailer = new Mandrill(MANDRILL_API_KEY);
        $setFrom = GLOBAL_EMAIL_SENDER;
        $email = (new Email())
            ->addTo($to)
            ->setFrom($setFrom)
            ->setSubject($subject)
            ->setTextBody($content);
        $mailer->send($email);
        if (ADMIN_DEBUG == 1) {
            clog("mandrill", $mailer);
        }
    }

    private function postmark($to, $subject, $content)
    {
        $mailer = new Postmark(POSTMARK_serverApiToken);
        $setFrom = GLOBAL_EMAIL_SENDER;
        $email = (new Email())
            ->addTo($to)
            ->setFrom($setFrom)
            ->setSubject($subject)
            ->setTextBody($content);
        $mailer->send($email);
        if (ADMIN_DEBUG == 1) {
            clog("postmark", $mailer);
        }
    }

    private function sendinblue($to, $subject, $content)
    {
        $mailer = new SendinBlue(SENDINBLUE_API_KEY);
        $setFrom = GLOBAL_EMAIL_SENDER;
        $email = (new Email())
            ->addTo($to)
            ->setFrom($setFrom)
            ->setSubject($subject)
            ->setTextBody($content);
        $mailer->send($email);
        if (ADMIN_DEBUG == 1) {
            clog("sendinblue", $mailer);
        }
    }

    private function mailjet($to, $subject, $content)
    {
        $mailjet_public_key = MAILJET_PUBLIC_KEY;
        $mailjet_private_secret = MAILJET_PRIVATE_SECRET;

        $mailer = new Mailjet($mailjet_public_key, $mailjet_private_secret);

        $setFrom = GLOBAL_EMAIL_SENDER;
        $email = (new Email())
            ->addTo($to)
            ->setFrom($setFrom)
            ->setSubject($subject)
            ->setTextBody($content);
        $mailer->send($email);
        if (ADMIN_DEBUG == 1) {
            clog("mailjet", $mailer);
        }
    }
	private function mailgun($to, $subject, $content)
    {

	$array_data = array(
		'from'=> GLOBAL_EMAIL_SENDER,
		'to'=>$to,
		'subject'=>$subject,
		'html'=>$content,
		'text'=>$content,
		'o:tracking'=>'yes',
		'o:tracking-clicks'=>'yes',
		'o:tracking-opens'=>'yes',
		'h:Reply-To'=>GLOBAL_EMAIL_SENDER
    );
    $session = curl_init(MAILGUN_DOMAIN.'/messages');
    curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  	curl_setopt($session, CURLOPT_USERPWD, 'api:'.MAILGUN_API_KEY);
    curl_setopt($session, CURLOPT_POST, true);
    curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($session);
    curl_close($session);
    $results = json_decode($response, true);
    
        if (ADMIN_DEBUG == 1) {
            clog("mailgun", $results);
        }
    }

}

?>