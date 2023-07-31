<?php

namespace Think;
vendor("vendor.autoload");

use Omnipay\Omnipay;
use Omnipay\AuthorizeNet;

class Payment
{
    public function __construct()
    {

        parent::__construct();
    }
	public function authorize(){
		$input=$_POST;
		$request = $gateway->purchase(
    [
        'notifyUrl' => SITE_URL.'Content/save',
        'amount' => $amount,
        'opaqueDataDescriptor' => $input['dataDescriptor'],
        'opaqueDataValue' => $input['dataValue'],
    ])->send();
	if ($response->isSuccessful()) {

    // Payment was successful
    return json_encode($response);

} elseif ($response->isRedirect()) {

    // Redirect to offsite payment gateway
   return $response->redirect();

} else {
    // Payment failed
    return $response->getMessage();
}
	
		
	}
    

}

?>