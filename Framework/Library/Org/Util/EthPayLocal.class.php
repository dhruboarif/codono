<?php

namespace Org\Util;
class EthPayLocal extends EthCommon
{

    function __construct($host, $port = "80", $version, $caiwu,$password='')
    {
        $this->host = $host;
        $this->port = $port;
        $this->version = $version;
        $this->caiwu = $caiwu;
		$this->password=$password;
						   
    }

    /**
     * Get the balance of Ethereum account Ethereum
     * @author morris@codono
     * @since 2018-8-21
     * @return float|int Return eth number in decimal
     */
    function eth_getBalance($account)
    {
        //echo 11;exit();
        //$account = $_REQUEST['account'];//Get the account public key
        $params = [
            $account,
            "latest"
        ];
        $data = $this->request(__FUNCTION__, $params);
        if (empty($data['error']) && !empty($data['result'])) {
            return $this->fromWei($data['result']);//Return the number of ETH and do the rounding yourself
        } else {
            return $data['error']['message'];
        }
    }

    function eth_ercGetBalance($account, $contract)
    {
        $data_base = "0x70a08231";//70a08231
        $to_data = substr($account, 2);
        $data_base .= "000000000000000000000000" . $to_data;
        $dst = array('to' => $contract, 'data' => $data_base);
        $params = array(
            $dst,
            "latest"
        );

        $data = $this->request("eth_call", $params);
        if (empty($data['error']) && !empty($data['result'])) {
            return $data['result'];//Return the number of ETH and do the rounding yourself
        } else {
            return $data['error']['message'];
        }
    }

    function eth_ercDoTransaction($zhuan)
    {
        $from = $zhuan['fromaddress'];
        $to = $zhuan['toaddress'];
        $heyue = $zhuan['token'];//Contract Address
        $password = cryptString($zhuan['password'],'d');//$this->password;//Unlock Password
        //$value = "0x9184e72a";
        $value = $this->toWei($zhuan['amount']);
        if ($zhuan['type'] == "esm" || $zhuan['type'] == "wicc") {
            $value = $this->toWei3($zhuan['amount']);
        }
        if ($zhuan['type'] == "esm1") {
            $value = '0x'.base_convert($zhuan['amount'], 10, 16);
        }
        //$gas = $this->eth_estimateGas($from, $heyue, $value);//16 gas 0x5209 for binary consumption
        //$gas = base_convert("50000", 10, 16);
        //$gas = "0x" . $gas;
        //echo $gas;die();
        //$gasPrice = $this->eth_gasPrice();//price 0x430e23400
        $status = $this->personal_unlockAccount($from, $password);//Unblock
        if (!$status) {
            return 'Unlock failed';
        }
        $data_base = "0xa9059cbb";//70a08231
        $to_data = substr($to, 2);
        $data_base .= "000000000000000000000000" . $to_data;
        $value_data = substr($value, 2);
        $v = str_pad($value_data, 64, 0, STR_PAD_LEFT);
        //echo $v;
        //die();
        $data_base .= $v;
        $params = array(
            "from" => $from,
            "to" => $heyue,
            "data" => $data_base,
        );

        $data = $this->request("eth_sendTransaction", [$params]);
        return json_encode($data);
    }

    function eth_accounts()
    {
        $params = [];

        $data = $this->request(__FUNCTION__, $params);
        if (empty($data['error']) && !empty($data['result'])) {
            return $data['result'];//Return the number of eth and do the rounding yourself
        } else {
            return $data['error']['message'];
        }
    }

    /**
     * Transfer Ethereum coinpay
     * @author morris@codono
     * @since 2018-8-15
     */
		function txstatus($txhash){
	$result=file_get_contents("https://api.etherscan.io/api?module=transaction&action=gettxreceiptstatus&txhash=".$txhash."&apikey=".ETHERSCAN_KEY);
	if($result){
	$json=json_decode($result);
	$resp=$json->result->status;
		if($json->status==1 &&$resp==1)	{
		return 1;
		}else{
		return 0;}
		}else{
		return 0;}
	} 						
    function eth_sendTransaction($array)
    {
        $from = $array["fromaddress"];
        $to = $array["toaddress"];
        $value = $this->toWei($array["amount"]);
        //If not hexadecimal to hexadecimal
        //$gas = $this->eth_estimateGas($from, $to, $value);//16 gas 0x5209 for binary consumption
        //$gas = "0xea60";//Hexadecimal consumption of gas 0x5209

        //$gasPrice = $this->eth_gasPrice();//Price 0x430e23400
        
        $password =cryptString($array['password'],'d');// $this->password;//Unlock Password 
        $status = $this->personal_unlockAccount($from, $password);//Unlock
        if (!$status) {
            //@SaveLog('EthPayLocal', "Personal_unlockAccount_Unlock failure", _FUNCTION__);
            //return "Personal_unlockAccount_Unlock failure";
            $data['error']['message'] = "Personal_unlockAccount_Unlock failure";
            return json_encode($data);
            return false;
        }
        $params = array(
            "from" => $from,
            "to" => $to,
            //"gas" => $gas,//$gas,//2100
            //"gasPrice " => $gasPrice,//18000000000
            "value" => $value,//2441406250
            //"data" => "",
        );

        $data = $this->request(__FUNCTION__, [$params]);
        return json_encode($data);
        //@SaveLog('EthPayLocal', "eth_sendTransaction_" . json_encode($data), _FUNCTION__);
        //var_dump($data);
        if (empty($data['error']) && !empty($data['result'])) {
            //echo  "eth_success_" . $data['result'];
            // @SaveLog('EthPayLocal', "eth_success_" . $data['result'], _FUNCTION__);
            //  return $data['result'];//After transfer, generate HASH.
            return $data['result'];
        } else {
            return json_encode($data);
            //@SaveLog('EthPayLocal', "eth_error_" . $data['error']['message'], _FUNCTION__);
            return false;
            //return $data['error']['message'];
        }
        //0x536135ef85aa8015b086e77ab8c47b8d40a3d00a975a5b0cc93b2a6345f538cd
    }

    /**
     * Timed task querying whether there is an entry in the ether store. coinpay
     */
	 	public function eth_syncing(){
		$params=array();
		return $this->request("eth_syncing", $params);
	} 
    public function EthInCrontab($oldnum = 0)
    {
        $ucinsert = $params = array();
        $n = $this->request("eth_blockNumber", $params);
        $number = $n['result'];
        $nums = base_convert($number, 16, 10);
        $isk = 1;
        $ucinsert = array();
        $ps = 5;
		if ($oldnum > 0 && $nums >= 0) {
            $ps = $nums - $oldnum;
            if ($ps > 6) {
                $ps = 6;
                $nums = $oldnum + 5;
            } else {
                $ps = 5;
            }
        }
		else{
			die($number);
		}				  
        for ($i = 1; $i < $ps; $i++) {
            $np = '0x' . base_convert($nums - $i, 10, 16);
            $params = array(
                $np,
                true
            );
			
            $data = $this->request("eth_getBlockByNumber", $params);
			if (isset($data["result"]) && isset($data["result"]["transactions"])) {
                foreach ($data["result"]["transactions"] as $k => $t) {
                    $bs = base_convert($t["value"], 16, 10);
                    $b = bcdiv($bs,pow(10,18),8);
                    $ucinsert[$isk]["number"] = $b;
                    $ucinsert[$isk]["hash"] = $t["hash"];
                    $ucinsert[$isk]["from"] = $t["from"];
                    $ucinsert[$isk]["to"] = $t["to"];
                    $ucinsert[$isk]["input"] = $t["input"];
                    $isk++;
                }

            } else {
                continue;
            }
        }
        $ucinsert[0]["block"] = $nums;
        $ucinsert[0]["num"] = $ps;
        return $ucinsert;
    }

    public function EthInCrontab_back()
    {
        $params = array();
        $n = $this->request("eth_blockNumber", $params);
        $number = $n['result'];
        for ($i = 1; $i < 11; $i++) {
            $np = '0x' . base_convert($number - $i, 10, 16);
            $params = array(
                $np,
                true
            );
            // @SaveLog('EthPay', "TotalNumber" . $number - $i, _FUNCTION__);
            $data = $this->request("eth_getBlockByNumber", $params);
			
            if (isset($data["result"]) && isset($data["result"]["transactions"])) {
                // $config = Config::get('database');
                //$this->connection = Db::connect($config['AccessData']);
                foreach ($data["result"]["transactions"] as $k => $t) {
                    //$uc = $this->connection->table('user_coin')->where('tx', $t["hash"])->find();
                    echo $t["to"] . "<br>";
                    if (isset($uc["id"])) {
                        continue;
                    } else {
                        // $tc = $this->connection->table('user_token')->where('token_account', $t["to"])->find();
                        if (isset($tc["user_id"])) {
                            //@SaveLog('EthPayLocal', "Number" . $number - $i, _FUNCTION__);
                            // @SaveLog('EthPayLocal', "EthInCrontab" . json_encode($data["result"]["transactions"][$k]), _FUNCTION__);
                            $b = base_convert($t["value"], 16, 10);
                            $b = $this->fromWei2($b);
                            //$mod = new \app\model\Common();
                            //$mod::startTrans();
                            //  try {
                            //$this->connection->table("user_token")->where('id', $tc["id"])->setInc("token_balance", $b);
                            //Insert
                            $ucinsert["user_id"] = $tc["user_id"];
                            $ucinsert["number"] = $b;
                            $ucinsert["tx"] = $t["hash"];
                            $ucinsert["token_out"] = $t["from"];
                            $ucinsert["status"] = 2;
                            $ucinsert["operate_id"] = 1;
                            $ucinsert["income"] = 1;
                            $ucinsert["token_type"] = 2;
                            $ucinsert["time"] = time();
                            $ucinsert["ip"] = $_SERVER["SERVER_ADDR"];
                            //$this->connection->table("user_coin")->insert($ucinsert);
                            //   } catch (\Exception $e) {
                            // Rollback transaction
                            //$mod::rollback();
                            //    }
                            //echo $this->connection->table("user_token")->getLastSql();
                        } else {
                            continue;
                        }
                    }

                }

            } else {
                continue;
            }
        }

        return true;
    }

    /**
     * Timed task querying whether there is an entry in the ether store coinpay
     */
    public function EthInCrontabNumber($number)
    {
        $np = '0x' . base_convert($number, 10, 16);
        $params = array(
            $np,
            true
        );
        $data = $this->request("eth_getBlockByNumber", $params);
		
        if (isset($data["result"]) && isset($data["result"]["transactions"])) {
            //$config = Config::get('database');
            //$this->connection = Db::connect($config['AccessData']);
            foreach ($data["result"]["transactions"] as $k => $t) {
                //$uc = $this->connection->table('user_coin')->where('tx', $t["hash"])->find();
				$uc=array();
                if (isset($uc["id"])) {
                    continue;
                } else {
                    //$tc = $this->connection->table('user_token')->where('token_account', $t["to"])->find();
					$tc=array();
                    if (isset($tc["user_id"])) {
                        @SaveLog('EthPay', "Number" . $number, _FUNCTION__);
                        @SaveLog('EthPay', "EthInCrontabNumber" . json_encode($data["result"]["transactions"][$k]), _FUNCTION__);
                        $b = base_convert($t["value"], 16, 10);
                        $b = $this->fromWei2($b);
                        $mod = new \app\model\Common();
                        $mod::startTrans();
                        try {
                            $this->connection->table("user_token")->where('id', $tc["id"])->setInc("token_balance", $b);
                            //Insert
                            $ucinsert["user_id"] = $tc["user_id"];
                            $ucinsert["number"] = $b;
                            $ucinsert["tx"] = $t["hash"];
                            $ucinsert["token_out"] = $t["from"];
                            $ucinsert["status"] = 2;
                            $ucinsert["operate_id"] = 1;
                            $ucinsert["income"] = 1;
                            $ucinsert["token_type"] = 2;
                            $ucinsert["time"] = time();
                            $ucinsert["ip"] = $_SERVER["SERVER_ADDR"];
                            $this->connection->table("user_coin")->insert($ucinsert);
                            // Submission of transactions
                            $mod::commit();
                        } catch (\Exception $e) {
                            // Rollback transaction
                            $mod::rollback();
                        }
                        //echo $this->connection->table("user_token")->getLastSql();
                    } else {
                        continue;
                    }
                }

            }


        }

        return true;
    }


    /**
     * Transfer bank ERC20 token coinpay
     * @author morris@codono
     * @since 2018-8-15
     */
    function eth_sendContactTransaction($array)
    {
        $from = $this->caiwu; //Financial account entry
        $heyue = $array["contact"];//Contract Address
        //$value = "0x9184e72a";
        //$value = "0x" . base_convert("5000000000000000000", 10, 16);
        $value = $this->toWei($array["amount"]);
        //If it is not 16 binary to 16
//        if (!ctype_xdigit($value)) {
//            $value = $this->toWei($value);
//        }
        //$gas = $this->eth_estimateGas($from, $heyue, $value);//16 binary consumption gas 0x5209
        $gas = base_convert("50000", 10, 16);
        $gas = "0x" . $gas;
        //echo $gas;die();
        $gasPrice = $this->eth_gasPrice();//Price 0x430e23400
        $password = cryptString($array['password'],'d');//$array["user_id"];//Unlock Password
        $status = $this->personal_unlockAccount($from, $password);//Unlock
        if (!$status) {
            return 'Unlock Failed';
        }
        //Parameter assembly
        //"dd62ed3e": "allowance(address,address)",
        //    "095ea7b3": "approve(address,uint256)",
        //    "cae9ca51": "approveAndCall(address,uint256,bytes)",
        //    "70a08231": "balanceOf(address)",
        //    "42966c68": "burn(uint256)",
        //    "79cc6790": "burnFrom(address,uint256)",
        //    "313ce567": "decimals()",
        //    "06fdde03": "name()",
        //    "95d89b41": "symbol()",
        //    "18160ddd": "totalSupply()",
        //    "a9059cbb": "transfer(address,uint256)",
        //    "23b872dd": "transferFrom(address,address,uint256)"

        $data_base = "0xa9059cbb";//70a08231
        $to = $array["toaddress"];//Transfer address:
        $to_data = substr($to, 2);
        $data_base .= "000000000000000000000000" . $to_data;
        $value_data = substr($value, 2);
        $v = str_pad($value_data, 64, 0, STR_PAD_LEFT);
        //echo $v;
        //die();
        $data_base .= $v;
        $params = array(
            "from" => $from,
            "to" => $heyue,
            //"gas" => $gas,//$gas,//2100
            //"gasPrice " => $gasPrice,//18000000000
            //"value" => $value,//2441406250
            "data" => $data_base,
        );
        @SaveLog('EthPayLocal', "eth_sendContactTransaction_" . json_decode($params), _FUNCTION__);
        $data = $this->request("params", [$params]);
        if (empty($data['error']) && !empty($data['result'])) {
            @SaveLog('EthPayLocal', 'Success_' . $data['result'], __FUNCTION__);
            return $data['result'];//After transfer, generate HASH
        } else {
            @SaveLog('EthPayLocal', 'ContactTransaction_' . $data['error']['message'], __FUNCTION__);
            return false;
            //return $data['error']['message'];
        }
        //0x536135ef85aa8015b086e77ab8c47b8d40a3d00a975a5b0cc93b2a6345f538cd
    }

    /**
     * Transfer bank ERC20 token Test code
     * @author morris@codono
     * @since 2018-8-15
     */
    function eth_ercsendTransaction($zhuan)
    {
        $from = $this->caiwu;//Transfer address:0.000000000000001014
        $to = $zhuan['toaddress'];
        $heyue = $zhuan['token'];//Contract Address
        $password = cryptString($zhuan['password'],'d');//$this->password;//Unlock Password
        //$value = "0x9184e72a";
        $value = $this->toWei($zhuan['amount']);
        if ($zhuan['type'] == "btm" || $zhuan['type'] == "esm" || $zhuan['type'] == "wicc") {
            $value = $this->toWei3($zhuan['amount']);
        }
        //$gas = $this->eth_estimateGas($from, $heyue, $value);//16 binary consumption gas 0x5209
        $gas = base_convert("50000", 10, 16);
        $gas = "0x" . $gas;
        //echo $gas;die();
        $gasPrice = $this->eth_gasPrice();//Price 0x430e23400
        $status = $this->personal_unlockAccount($from, $password);//Unlock
        if (!$status) {
            return 'Unlock Failed';
        }
        $data_base = "0xa9059cbb";//70a08231
        $to_data = substr($to, 2);
        $data_base .= "000000000000000000000000" . $to_data;
        $value_data = substr($value, 2);
        $v = str_pad($value_data, 64, 0, STR_PAD_LEFT);
        //echo $v;
        //die();
        $data_base .= $v;
        $params = array(
            "from" => $from,
            "to" => $heyue,
            //"gas" => $gas,//$gas,//2100
            //"gasPrice " => $gasPrice,//18000000000
            //"value" => $value,//2441406250
            "data" => $data_base,
        );

        $data = $this->request("eth_sendTransaction", [$params]);
        return json_encode($data);
        if (empty($data['error']) && !empty($data['result'])) {
            return $data;//After transfer, generate HASH
        } else {
            return $data['error']['message'];
        }
        //0x536135ef85aa8015b086e77ab8c47b8d40a3d00a975a5b0cc93b2a6345f538cd
    }

    //Ethereum Contract Query operation
    function eth_call()
    {
        $from = "0x64fcc62e4d2e7d09907b10ad5ed76c8503363e8a";//Transfer address:
        $to = "0x4c4d11f7ec61d0cff19a80bb513695bf12177398";//Contract Address
        //Parameter assembly
        //"dd62ed3e": "allowance(address,address)",
        //    "095ea7b3": "approve(address,uint256)",
        //    "cae9ca51": "approveAndCall(address,uint256,bytes)",
        //    "70a08231": "balanceOf(address)",
        //    "42966c68": "burn(uint256)",
        //    "79cc6790": "burnFrom(address,uint256)",
        //    "313ce567": "decimals()",
        //    "06fdde03": "name()",
        //    "95d89b41": "symbol()",
        //    "18160ddd": "totalSupply()",
        //    "a9059cbb": "transfer(address,uint256)",
        //    "23b872dd": "transferFrom(address,address,uint256)"

        $data_base = "0x70a08231";//70a08231
        $from_data = substr($from, 2);
        //echo $from_data;die();
        $data_base .= "000000000000000000000000" . $from_data;
        $params = array(
            "from" => $from,
            "to" => $to,
            "data" => $data_base,
        );

        $data = $this->request("eth_call", [$params, "latest"]);
        //var_dump($data);
        if (empty($data['error']) && !empty($data['result'])) {
            return $data['result'];//After transfer, generate HASH
        } else {
            return $data['error']['message'];
        }
    }

    /**Get transfer details based on transfer hash
     * Transfer details
     * @author morris@codono
     * @since 2018-8-20
     */
    function eth_getTransactionReceipt($transactionHash)
    {
        //Transaction Hash
        $params = array(
            $transactionHash,
        );
        $data = $this->request(__FUNCTION__, $params);
        return $data;
    }

    /** required
     * Creating a new account is a bit time consuming. After generating it for the user, the password is saved in the database.
     * @author morris@codono
     * @since 2018-8-19
     */
    function personal_newAccount($password)
    {
        //$password = "123";//Password
        $params = array(
            $password,
        );
        $data = $this->request(__FUNCTION__, $params);
        //return json_encode($data);
        if (empty($data['error']) && !empty($data['result'])) {
            //@SaveLog('EthPay', "account" . $data['result'], "personal_newAccount");
            return $data['result'];//Newly generated account public key
        } else {
            //@SaveLog('EthPay', "password" . $password, "personal_newAccount");
            return false;
        }
    }

    /**
     * Get how much gas you consume
     * @author morris@codono
     * @since 2018-8-15
     */
    function eth_estimateGas($from, $to, $value)
    {
        $params = array(
            "from" => $from,
            "to" => $to,
            "value" => $value
        );
        //echo "$value".$value;die();
        $data = $this->request(__FUNCTION__, [$params]);
        //var_dump($data);die();
        return $data['result'];
    }

    /**
     * Get current Gasprice
     * @author morris@codono
     * @since 2018-8-15
     */
    function eth_gasPrice()
    {
        $params = array();
        $data = $this->request(__FUNCTION__, $params);
        return $data['result'];
    }


    /**required
     * Unlock account this function may be time-consuming.
     * @author morris@codono
     * @since 2018-8-15
     */
    function personal_unlockAccount($account, $password)
    {
        $params = array(
            $account,
            $password,
            100,
        );
        $data = $this->request(__FUNCTION__, $params);
        if (!empty($data['error'])) {
            return $data['error']['message'];//Unlock Failed
        } else {
            return $data['result'];//Return true for success
        }
    }

    function eth_getTransactionCount()
    {
        $params = array(
            "0x8d7c0440e01f4840aeafe4a9039b41e00f4157af",
            "latest"
        );
        $data = $this->request(__FUNCTION__, $params);
        var_dump($data);
        die();
        if (!empty($data['error'])) {
            return $data['error']['message'];//Unlock Failed
        } else {
            return $data['result'];//Return true for success
        }
    }

    function eth_getBlockByNumber($block=0)
    {
        $number = '0x' . base_convert($block, 10, 16);
        echo $number;
        $params = array(
            $number, // 436
            true
        );
        $data = $this->request(__FUNCTION__, $params);
        if (!empty($data['error'])) {
            return $data['error']['message'];//Unlock Failed
        } else {
            return $data['result'];//Return true for success
        }
    }


}
