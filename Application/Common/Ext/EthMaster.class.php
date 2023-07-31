<?php

namespace Common\Ext;
class EthMaster
{

		private $host;
        private $port;
        private $version = '2.0';
		private $debug = false;
        private $coinbase; //main account
		private $coinbasePwd;
		private $transferGas = '0.0005';
		private $rpcId = 0;
		private $ContractAddress;
		private $etherscan_api_key='TZ9WWPTNRJVIKS2W8VVV9ANM1UKYZ6R6ME'; //Enter your own key from here https://etherscan.io/myapikey
    function __construct($host, $port = "80", $coinbase,$password='',$contract=false)
    {
        $this->host = $host;
        $this->port = $port;
        
        $this->coinbase = $coinbase; //main account
		$this->coinbasePwd=$password;
		$this->ContractAddress=$contract;
						   
    }

     private function checkRpcResult($data){
        $result = null;
        if (empty($data['error']) && !empty($data['result'])) {
            $result = $data['result'];
        } else {
            if ($this->debug) {
                
                $result = $data;
            }else{
                $result = $data;
            }
        }
        return $result;
    }
    
	/* this function was later replaced with native functional So not in use */
    public function es_TokenBalance($address, $contract,$decimal=8){
        $url="https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=".$contract."&address=".$address."&tag=latest&apikey=".$this->etherscan_api_key;
        $ret=file_get_contents($url); //JSON results Something Like {"status":"1","message":"OK","result":"100000000"}
        $result=json_decode($ret); //JSON DECODE
      // $balance=($result->result)/pow(10,$decimal); //Now converting result to actual number by dividing 10^decimals example 10^8
        $balance=($result->result); //
        if ($ret && $result->status==1) {
          	return $balance;
        } else {
            return false;
        }
    }
    
    private function request($method, $params = array()){
        $data = array();
        $data['jsonrpc'] = $this->version;
        $data['id'] = 999999;
        // $data['id'] = $this->rpcId + 1;
        $data['method'] = $method;
        $data['params'] = $params;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->host);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $ret = curl_exec($ch);
        curl_close($ch);
        //Return result
        if ($ret) {
            // dump(json_decode($ret, true));exit();
          	//return $ret;
            return $this->checkRpcResult(json_decode($ret, true));
        } else {
            return false;
        }
    }
    private function eth_gasPrice(){
        return $this->request(__FUNCTION__);
    }
    private function eth_getBalance($address){
        return $this->request(__FUNCTION__, array($address, 'latest'));
    }
  
  
  	private function eth_getTransactionByHash($txHash){
        return $this->request(__FUNCTION__, array($txHash));
    }
    private function eth_sendTransaction($from, $to, $gas, $gasPrice, $value, $data){
        $transVue = array();
        $transVue['from']      = $from;
        $transVue['to']        = $to;
        $transVue['gas']       = $gas;
        $transVue['gasPrice']  = $gasPrice;
        $transVue['value']     = $value;
        $transVue['data']      = $data;
        return $this->request(__FUNCTION__, array( $transVue ));
    }
    private function eth_call($from, $to, $gas, $gasPrice, $value, $data){
        $callVue = array();
        $callVue['from']      = $from;
        $callVue['to']        = $to;
		if($gas!='0x0'){
        $callVue['gas']       = $gas;
		}
		if($gasPrice!='0x0'){
        $callVue['gasPrice']  = $gasPrice;
		}
		if($value!='0x0'){
        $callVue['value']     = $value;
		}
        $callVue['data']      = $data;
        return $this->request(__FUNCTION__, array( $callVue, 'latest' ));
    }
    private function eth_estimateGas($from, $to, $gas, $gasPrice, $value, $data){
        $callVue = array();
        $callVue['from']      = $from;
        $callVue['to']        = $to;
        $callVue['gas']       = $gas;
        $callVue['gasPrice']  = $gasPrice;
        $callVue['value']     = $value;
        $callVue['data']      = $data;
        return $this->request(__FUNCTION__, array( $callVue ));
    }
    private function personal_unlockAccount($account, $password, $duration = 20){
        $params = array(
            $account,
            $password,
            $duration
        );
        return $this->request(__FUNCTION__, $params);
    }
    private function personal_newAccount($password){
        $result = false;
        if (is_string($password) && strlen($password) > 0) {
            return $this->request(__FUNCTION__, array($password));
        }
    }
    private function createPassword(){
        $str = md5(rand(0,999) . rand(0,999) . time() . rand(0,999) . rand(0,999));
        $strArr = str_split($str);
        for($i=0;$i<(count($strArr)-1)/2;$i++){
            $temp = $strArr[$i];
            $strArr[$i] = $str[count($strArr)-1-$i];
            $strArr[count($strArr)-1-$i] = $temp;
        }
        $str = '';
        foreach ($strArr as $v){
            $str = $str.$v;
        }
        return $str;
    }
  
  
  	public function checkTransactioning($txHash){
        $result = false;
        $transaction = $this->eth_getTransactionByHash($txHash);
        if ($transaction && isset($transaction['blockNumber'])) {
            
            $result = $transaction['blockNumber'] ? true : false;
        }
        return $result;
    }
    public function checkAmount($amount){
        return is_numeric($amount);
    }
    public function checkAddress($address){
        $result = false;
        if (ctype_alnum($address)) {
            
            if (strlen($address) == 42 && substr($address, 0, 2) == '0x') {
                
                $result = true;
            }
        }
        return $result;
    }
    //New account 
    public function createWallet($password){
        $result = false;
        $wallet = null;
        $wallet['password'] = $password();
        $wallet['address'] = $this->personal_newAccount($wallet['password']);
        if ($wallet['address'] && is_string($wallet['address'])) {
            
            if (strlen($wallet['address']) == 42) {
                
                $result = $wallet;
              
            }
        }
        return $result;
    }
	/* Coinbase Token Balance*/
    public function balanceofCoinbaseToken($contractAddress=""){
        return $this->balanceOfToken($this->coinbase,$contractAddress);
    }
 
    public function balanceofCoinBase(){
        return $this->balanceOf($this->coinbase);
    }
    public function balanceOfToken($address,$contractAddress = "",$decimal=8){
        $result = 0;
        if ($this->checkAddress($address)) {
            $data = null;
                
            $dataCode = '0x70a08231000000000000000000000000' . substr($address, 2, 40);
            
            $data = $this->eth_call($address,  $contractAddress , '0x0', '0x0', '0x0', $dataCode);
            
            //print_r($data);
            
            if ($data && !is_array($data)) {
                $result = bcdiv(number_format(hexdec($data),0,'.',''), number_format(pow(10,$decimal),0,'.',''), 2);
            }
        }
        return $result;
    }
    public function baloftoken($address,$contractAddress = ""){
        $callVue['from']=$address;
        $callVue['to']=$contractAddress;
        $short=substr($contractAddress, 2);
        $callVue['data']='0x70a08231000000000000000000000000'.$short;
        $this->request(__FUNCTION__, array( $callVue, 'latest' ));
        
    }
    public function balanceOf($address){
        $result = 0;
        if ($this->checkAddress($address)) {
            $data = $this->eth_getBalance($address);
            if ($data && !is_array($data)) {
                $result = bcdiv(number_format(hexdec($data),0,'.',''), number_format(1000000000000000000,0,'.',''), 18);
            }
        }
        return $result;
    }
    public function transferToken($toAddress, $value, $contractAddress = "",$decimal=8){
        
        if ($this->checkAddress($toAddress) && is_numeric($value)) {
			
            $ethBalance = $this->balanceOf($this->coinbase);
          	
            $gasPriceHex = $this->eth_gasPrice();
			
            $tokenEnough = false;
            $data = array();
        //    $tokenBalance = $this->balanceofCoinbaseToken($contractAddress);
           $tokenBalance=$this->es_TokenBalance($this->coinbase, $contractAddress);
            $tokenEnough = bcsub($tokenBalance, $value, 2) >= 0;
		
            if (1==1) {
        
                $data['to'] = !$contractAddress ? $this->contractAddress : $contractAddress;
                $data['value'] = '0x0';
                $data['data'] = '0xa9059cbb000000000000000000000000' . substr($toAddress, 2, 40);
                $new_val=bcmul($value, bcpow("10", strval($decimal), 0), 0);
                $valueHex = base_convert($new_val, 10, 16);
            //    $valueHex = base_convert(bcmul($value, number_format(100, 0, '.', ''), 0), 10, 16);
				
                $zeroStr = '';
                for($i = 1; $i <= (64 - strlen($valueHex)); $i ++){
                    $zeroStr .= '0';
                  
                }
                $data['data'] = $data['data'] . $zeroStr . $valueHex;
                 
                $gasLimitHex = $this->eth_estimateGas($this->coinbase, $data['to'], '0x0', '0x0', $data['value'], $data['data']);
				
                if (bcsub($ethBalance,bcdiv(bcmul(hexdec($gasPriceHex), hexdec($gasLimitHex)), number_format(1000000000000000000, 0, '.', ''), 18),18) >= 0) {
                    
                    $unlockStatus = $this->personal_unlockAccount($this->coinbase, $this->coinbasePwd);
					
                    if ($unlockStatus) {
                        
                        $result = $this->eth_sendTransaction($this->coinbase, $data['to'], $gasLimitHex, $gasPriceHex, $data['value'], $data['data']);
                     	//echo json_encode($gasPriceHex);exit;
                    }
                }
            }
        }
        return $result;
    }
    public function RequiredEthertokenTransfer($fromAddress, $value, $password,$contractAddress,$decimal=8){
        $result = false;
        if ($this->checkAddress($fromAddress) && is_numeric($value)) {
            $ethBalance = $this->balanceOf($fromAddress);
            $gasPriceHex = $this->eth_gasPrice();
            $tokenEnough = false;
            $data = array();
            //$tokenBalance = $this->balanceOfToken($fromAddress);
            //$tokenEnough = bcsub($tokenBalance, $value, 2) >= 0;
            
            if ($value) {
                
                $data['to'] = $contractAddress;
              //  $data['value'] = '0x' . base_convert(bcmul($amount, number_format(100, 0, '.', ''), 0), 10, 16);//'0x0';
                $data['value'] ='0x0';
				//Encode the transaction to send to the proxy contract
				
				$amounthex = sprintf("%064s",$value);
                $data['data'] = '0xa9059cbb000000000000000000000000' . substr($this->coinbase, 2, 40);
                $new_val=bcmul($value, bcpow("10", strval($decimal), 0), 0);
                //$valueHex = base_convert(bcmul($value, number_format(100, 0, '.', ''), 0), 10, 16);
                $valueHex = base_convert($new_val, 10, 16);
                $zeroStr = '';
                for($i = 1; $i <= (64 - strlen($valueHex)); $i ++){
                    $zeroStr .= '0';
                }
                $data['data'] = $data['data'] . $zeroStr . $valueHex;
                
                $gasLimitHex = $this->eth_estimateGas($fromAddress, $data['to'], '0x0', $gasPriceHex, $data['value'], $data['data']);
                $eth_gas_required=bcsub(bcdiv(bcmul(hexdec($gasPriceHex), hexdec($gasLimitHex)), number_format(1000000000000000000, 0, '.', ''), 18),0,18);
                
                if (bcsub($ethBalance,bcdiv(bcmul(hexdec($gasPriceHex), hexdec($gasLimitHex)), number_format(1000000000000000000, 0, '.', ''), 18),18) >= 0) {
                    
                    $resp['status']=1;
                    $resp['eth_bal']=$ethBalance;
                    $resp['required']=$eth_gas_required;
                   return json_encode($resp);
                }
                else{
                    $resp['status']=0;
                    $resp['eth_bal']=$ethBalance;
                    $resp['required']=$eth_gas_required;
                   return json_encode($resp);
                }
            }
        }
        return $result;
    }
    public function transferTokentoCoinbase($fromAddress, $value, $password,$contractAddress,$decimal=8){
        
        $result = false;
        if ($this->checkAddress($fromAddress) && is_numeric($value)) {
            $ethBalance = $this->balanceOf($fromAddress);
            $gasPriceHex = $this->eth_gasPrice();
            $tokenEnough = false;
            $data = array();
            //$tokenBalance = $this->balanceOfToken($fromAddress);
            //$tokenEnough = bcsub($tokenBalance, $value, 2) >= 0;
            
            if ($value) {
                
                $data['to'] = $contractAddress;
              //  $data['value'] = '0x' . base_convert(bcmul($amount, number_format(100, 0, '.', ''), 0), 10, 16);//'0x0';
                $data['value'] ='0x0';
				//Encode the transaction to send to the proxy contract
				
				$amounthex = sprintf("%064s",$value);
                $data['data'] = '0xa9059cbb000000000000000000000000' . substr($this->coinbase, 2, 40);
                $new_val=bcmul($value, bcpow("10", strval($decimal), 0), 0);
                //$valueHex = base_convert(bcmul($value, number_format(100, 0, '.', ''), 0), 10, 16);
                $valueHex = base_convert($new_val, 10, 16);
                $zeroStr = '';
                for($i = 1; $i <= (64 - strlen($valueHex)); $i ++){
                    $zeroStr .= '0';
                }
                $data['data'] = $data['data'] . $zeroStr . $valueHex;
                
                $gasLimitHex = $this->eth_estimateGas($fromAddress, $data['to'], '0x0', $gasPriceHex, $data['value'], $data['data']);
                $eth_gas_required=bcsub(bcdiv(bcmul(hexdec($gasPriceHex), hexdec($gasLimitHex)), number_format(1000000000000000000, 0, '.', ''), 18),0,18);
                if (bcsub($ethBalance,bcdiv(bcmul(hexdec($gasPriceHex), hexdec($gasLimitHex)), number_format(1000000000000000000, 0, '.', ''), 18),18) < 0) {
                                        $gas_xfer=$this->transferFromCoinbase($fromAddress,floatval($eth_gas_required));
                                       
                }
                if (bcsub($ethBalance,bcdiv(bcmul(hexdec($gasPriceHex), hexdec($gasLimitHex)), number_format(1000000000000000000, 0, '.', ''), 18),18) >= 0) {
                    
                    $unlockStatus = $this->personal_unlockAccount($fromAddress, $password);
                    if ($unlockStatus) {
                        
                        $result = $this->eth_sendTransaction($fromAddress, $data['to'], $gasLimitHex, $gasPriceHex, $data['value'], $data['data']);
                    }
                }
                else{
                                       
                                        $resp['error']='Low ETH Balance:'.$ethBalance. 'Minimum required for this tx is '.$eth_gas_required;
                                        $resp['action']=$gas_xfer;
                    return json_encode($resp);
                }
            }
        }
        return $result;
    }
    public function transferGas($toAddress){
        $result = false;
        if ($this->checkAddress($toAddress)) {
            $ethBalance = $this->balanceOf($this->coinbase);
            $gasPriceHex = $this->eth_gasPrice();
            $tokenEnough = false;
            $data = array();
            $ethBalance = bcsub($ethBalance, $this->transferGas, 18);
            $tokenEnough = $ethBalance >= 0;
            if ($tokenEnough) {
                
                $data['to'] = $toAddress;
                $data['value'] = '0x' . base_convert(bcmul($this->transferGas, number_format(1000000000000000000, 0, '.', ''), 0), 10, 16);
                $data['data'] = '0x';
                
                $gasLimitHex = $this->eth_estimateGas($this->coinbase, $data['to'], '0x0', $gasPriceHex, $data['value'], $data['data']);
                if (bcsub($ethBalance,bcdiv(bcmul(hexdec($gasPriceHex), hexdec($gasLimitHex)), number_format(1000000000000000000, 0, '.', ''), 18),18) >= 0) {
                    
                    $unlockStatus = $this->personal_unlockAccount($this->coinbase, $this->coinbasePwd);
                    if ($unlockStatus) {
                        
                        $result = $this->eth_sendTransaction($this->coinbase, $data['to'], $gasLimitHex, $gasPriceHex, $data['value'], $data['data']);
                    }
                }
            }
        }
        return $result;
    }
    public function transferFromCoinbase($toAddress, $amount){
        $result = false;
        if ($this->checkAddress($toAddress) && is_numeric($amount)) {
            $ethBalance = $this->balanceOf($this->coinbase);
            $gasPriceHex = $this->eth_gasPrice();
            $tokenEnough = false;
            $data = array();
            $ethBalance = bcsub($ethBalance, $amount, 18);
            $tokenEnough = $ethBalance >= 0;
            if ($tokenEnough) {
                
                $data['to'] = $toAddress;
                $data['value'] = '0x' . base_convert(bcmul($amount, number_format(1000000000000000000, 0, '.', ''), 0), 10, 16);
                $data['data'] = '0x';
                
                $gasLimitHex = $this->eth_estimateGas($this->coinbase, $data['to'], '0x0', $gasPriceHex, $data['value'], $data['data']);
                if (bcsub($ethBalance,bcdiv(bcmul(hexdec($gasPriceHex), hexdec($gasLimitHex)), number_format(1000000000000000000, 0, '.', ''), 18),18) >= 0) {
                    
                    $unlockStatus = $this->personal_unlockAccount($this->coinbase, $this->coinbasePwd);
                    if ($unlockStatus) {
                        
                        $result = $this->eth_sendTransaction($this->coinbase, $data['to'], $gasLimitHex, $gasPriceHex, $data['value'], $data['data']);
                    }
                }
            }
        }
        return $result;
    }
    	public function emptyEthOfAccount($from, $amount,$pwd){
        $result = false;
        if ($this->checkAddress($from) && is_numeric($amount)) {
            
            $ethBalance = $this->balanceOf($from);
            $gasPriceHex = $this->eth_gasPrice();
            $tokenEnough = false;
            $data = array();
            $ethBalance = bcsub($ethBalance, $amount, 8);
            $tokenEnough = $ethBalance >= 0;
              $data['to'] = $this->coinbase;
                $data['value'] = '0x' . base_convert(bcmul($amount, number_format(1000000000000000000, 0, '.', ''), 0), 10, 16);
                $data['data'] = '0x';
            
                $gasLimitHex = $this->eth_estimateGas($from, $data['to'], '0x0', $gasPriceHex, $data['value'], $data['data']);
				
				$the_fees=bcdiv(bcmul(hexdec($gasPriceHex), hexdec($gasLimitHex)), number_format(1000000000000000000, 0, '.', ''), 18);
			
            if ($tokenEnough) {
             
                if (bcsub($ethBalance,$the_fees,18) >= 0) {
             
             
                    $unlockStatus = $this->personal_unlockAccount($from, $pwd);
                    if ($unlockStatus) {
                        
                        $result = $this->eth_sendTransaction($from, $data['to'], $gasLimitHex, $gasPriceHex, $data['value'], $data['data']);
                    }else{
						
					}
                }
				else{
				$send=bcsub($ethBalance,$the_fees,18);
				  $data['value'] = '0x' . base_convert(bcmul($send, number_format(1000000000000000000, 0, '.', ''), 0), 10, 16);
				  $unlockStatus = $this->personal_unlockAccount($from, $pwd);
                    if ($unlockStatus) {
                        
                        $result = $this->eth_sendTransaction($from, $data['to'], $gasLimitHex, $gasPriceHex, $data['value'], $data['data']);
                    }else{
						
					}
			}
            }
        }

        return $result;
    }


}
