<?php

namespace Home\Controller;

class IPNController extends HomeController
{
    private function errorAndDie($error_msg)
    {
        echo $error_msg;
        $name = getcwd() . '/Public/Log/errors.txt';
        $json_string = $error_msg;
        file_put_contents($name, $json_string, FILE_APPEND);
        die();

    }

    private function FindWavesAssetID($assetid)
    {
        //	$assetid="4Zekj8nGpwzuEw1B7JFxLnDWmfcaFHyD8ZZKVWLwBbUC";
        $assetid = trim($assetid);
        $assetid = $assetid ? $assetid : "";
        $coin = M('Coin')->field('name')->where(array('type' => 'waves', 'status' => 1, 'dj_yh' => $assetid))->find();
        return ($coin['name'] ? $coin['name'] : null);
    }

    private function ReverseAsset($assetid)
    {
        $assetid = trim($assetid);
        $rsp = M('Coin')->where(array('status' => 1, 'type' => 'waves', 'dj_yh' => $assetid))->find();
        if ($rsp && $rsp['zr_jz'] == 1) {
            $coininfo['error'] = 0;
            $coininfo['decimal'] = $rsp['cs_qk'];
            $coininfo['name'] = $rsp['name'];
        } else {
            $coininfo['error'] = 1;
        }
        return $coininfo;
    }

    public function testme()
    {
        $assetid = 'HsrxkombedRSXDNKkAYd95JxGgPi9bFh1crwPPUCYBAQ';
        var_dump($this->ReverseAsset($assetid));
    }

    public function WavesDeposit()
    {
        $userid = userid();
        if (!$userid) {
            die('Please login');
        } else {
            $coinb = 'wavesb';
            $address = M('UserCoin')->where(array('userid' => $userid))->field($coinb)->find();
            echo "Your UserID is " . userid() . " and address is " . $address[$coinb] . "<br/>";
        }
        $coin = 'waves';
        $dj_username = C('coin')[$coin]['dj_yh'];  //Assetid
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
        $dj_decimal = (int)C('coin')[$coin]['cs_qk'];
        //   $main_address = C('coin')[$coin]['codono_coinaddress'];

        $waves = WavesClient($dj_username, $dj_password, $dj_address, $dj_port, $dj_decimal, 5, array(), 1);
        $information = json_decode($waves->status(), true);

        if ($information == NULL || ($information['blockchainHeight'] && $information['blockchainHeight'] <= 0)) {
            die('Either Waves is not available for sync Or we are not able to connect it');
        }
        $recipient = $address[$coinb];

        $txs[$coin] = json_decode($waves->AddressTxInfo($recipient, $limit = 10), true);

        $coinList = M('Coin')->where(array('status' => 1, 'type' => 'waves'))->select();

        foreach ($coinList as $k => $v) {

            $coin = $v['name'];
            echo "<br/>++++++++++Checking " . $coin . " and its assets++++++++++<br/>";

            if (!$coin) {
                $this->error('No Such Coins');
            }


            foreach ($txs['waves'][0] as $tx) {

                if ($tx['assetId']) {
                    $coininfo = $this->ReverseAsset($tx['assetId']);
                    $dj_decimal = $coininfo['decimal'];
                    $name = $coininfo['name'];

                    if ($coininfo['error'] == 1) {
                        echo $tx['assetId'] . " No Such Asset<br/>";
                        continue;
                    }
                } else {
                    $dj_decimal = 8;
                    $name = 'waves';
                    //checking if amount is lower than 0.002 then skip
                    if ($tx['amount'] < 200000) {
                        continue;
                    }

                }

                echo "<br/>Checking TXID" . $tx['id'] . "<br/>";

                if ($name != $coin) {
                    $checkname = $tx['assetId'] ? $tx['assetId'] : 'waves';
                    echo "<br/><b>This Tx" . $tx['id'] . " belongs to " . $checkname . ' [' . $name . ']' . ' Skipping and Checking Next</b><br/>';
                    continue;
                }
                //Only transfer deposits
                if ($tx['type'] == 4 && $tx['recipient'] == $recipient) {
                    //All recipients
                    $record = json_decode($waves->ValidateAddress($tx['recipient']), true);
                    if ($record['valid'] == true && $record['address'] == $tx['recipient']) {

                        $already_processed = M('Myzr')->where(array('txid' => $tx['id']))->find();
                        if ($already_processed) {
                            echo $tx['recipient'] . '=>' . $tx['id'] . ' hash has already been added , Look for another TX' . "<br/>";
                            continue;
                        }

                        //We have found that This tx belongs to someone

                        //Now lets find if There is such an asset , and which is the coins , WAVES, MAIS or any token_get_all

                        $coin = $this->FindWavesAssetID($tx['assetId']);

                        if ($coin == NULL) {
                            // There was some transaction Where token was not listed by user, inform admin about this tx and check another tx
                            $this->InformAdmin('waves:' . $tx['id']);
                            continue;
                        }

                        $precision = pow(10, $dj_decimal);
                        $to_num = round((($tx['amount'] * $precision) / $precision) / $precision, 8);

                        if (M('Myzr')->where(array('txid' => $tx['id'], 'status' => '1', 'type' => 'waves'))->find()) {
                            echo 'txid had found continue' . "<br/>";
                            continue;
                        }

                        echo 'all check ok ' . "<br/>";

                        echo '<br/>start receive do:' . "<br/>";
                        $sfee = 0;
                        $true_amount = $to_num;

                        if (C('coin')[$coin]['zr_zs']) {
                            $song = round(($to_num / 100) * C('coin')[$coin]['zr_zs'], 8);

                            if ($song) {
                                $sfee = $song;
                                $true_amount = $true_amount + $song;
                            }
                        }

                        // ($trans['confirmations'] < C('coin')[$coin]['zr_dz'])
                        {
                            echo 'All transactions are confirmed' . "<br/>";
                        }

                        $mo = M();
                        $mo->execute('set autocommit=0');
                        $mo->execute('lock tables  codono_user_coin write , codono_myzr  write ');
                        $rs = array();
                        $user['userid'] = userid();
                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $user['userid']))->setInc($coin, $true_amount);

                        if ($res = $mo->table('codono_myzr')->where(array('txid' => $tx['id']))->find()) {
                            echo '<br/>Deposit already Credited !! Check Next<br/>';
                            continue;
//                        $rs[] = $mo->table('codono_myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => 1));
                        } else {
                            echo 'New Deposit Add it for  ' . $tx['recipient'] . "<br/>";
                            $add_tx = array('userid' => $user['userid'], 'username' => $tx['recipient'], 'coinname' => $coin, 'fee' => $sfee, 'txid' => $tx['id'], 'num' => $true_amount, 'mum' => $true_amount, 'addtime' => time(), 'status' => 1);
                            $rs[] = $mo->table('codono_myzr')->add($add_tx);
                        }

                        if (check_arr($rs)) {
                            $mo->execute('commit');
                            echo $true_amount . ' receive ok ' . $coin . ' ' . $true_amount;
                            $mo->execute('unlock tables');
                            echo 'commit ok' . "<br/>";
                        } else {
                            echo $true_amount . 'receive fail ' . $coin . ' ' . $true_amount;
                            $mo->execute('rollback');
                            $mo->execute('unlock tables');
                            print_r($rs);
                            echo 'rollback ok' . "<br/>";
                        }
                    }
                }


            }
        }
    }


    private function IfExistsTX($address, $txn_id, $status)
    {
        //Check if $txn_id exists for $address with $status 100 or 2
        $findme = M('CoinpayIpn')->where(array('txn_id' => $txn_id, 'address' => $address))->find();
        if ($findme) {
            return true;
        } else {
            return 0;
        }
    }

    private function saveentry($aio_data_array)
    {
        $coinpayipn = M('CoinpayIpn');
        $query = $this->querymaker('codono_coinpay_ipn', $aio_data_array);
        return $coinpayipn->execute($query);

    }

    public function confirm()
    {
        $cp_merchant_id = COINPAY_MERCHANT_ID; //defined in pure_config
        $cp_ipn_secret = COINPAY_SECRET_PIN; //defined in pure_config
        //$hmac = hash_hmac("sha512", $_POST, trim($cp_ipn_secret));
        //These would normally be loaded from your database, the most common way is to pass the Order ID through the 'custom' POST field.


        if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
            $this->errorAndDie('IPN Mode is not HMAC');
        }

        if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
            $this->errorAndDie('No HMAC signature sent.');
        }

        $request = file_get_contents('php://input');
        if ($request == FALSE || empty($request)) {
            $this->errorAndDie('Error reading POST data');
        }

        if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($cp_merchant_id)) {
            $this->errorAndDie('No or incorrect Merchant ID passed');
        }

        $hmac = hash_hmac("sha512", $request, trim($cp_ipn_secret));

        if (!hash_equals($hmac, $_SERVER['HTTP_HMAC'])) {
            $this->errorAndDie($hmac . 'HMAC signature does not match' . $_SERVER['HTTP_HMAC']);

        }
        // HMAC Signature verified at this point, load some variables.
        $cid = $_POST['id'] ? $_POST['id'] : NULL;
        $address = $_POST['address'];
        $txn_id = $_POST['txn_id'];
        $item_name = $_POST['item_name'];
        $item_number = $_POST['item_number'];
        $amount1 = floatval($_POST['amount1']);
        $amount2 = floatval($_POST['amount2']);
        $currency1 = $_POST['currency1'];
        $currency2 = $_POST['currency2'];
        $status = intval($_POST['status']);
        $status_text = $_POST['status_text'];
        $aio_data_array = $_POST;
        $aio_data_array['cid'] = $cid;
        unset($aio_data_array['id']); //saving id as coinpay id cid
        if ($this->IfExistsTX($address, $txn_id, $status) == 1) {
            $this->errorAndDie('Order Already exists!');
            $name = getcwd() . '/Public/Log/exists_coinpay_post.txt';
            $json_string = 'Order Already exists!';
            file_put_contents($name, $json_string, FILE_APPEND);
        }
        //depending on the API of your system, you may want to check and see if the transaction ID $txn_id has already been handled before at this point


        if ($status >= 100 || $status == 2) {
            // payment is complete or queued for nightly payout, success
            $save_entry = $this->saveentry($aio_data_array);

        } else if ($status < 0) {
            //payment error, this is usually final but payments will sometimes be reopened if there was no exchange rate conversion or with seller consent
            $this->errorAndDie('Failed !Currently Payment Status is ' . $status);
        } else {
            //payment is pending, you can optionally add a note to the order page
            $this->errorAndDie('Failed !Currently Payment Status is ' . $status);
        }
        exit('IPN ENDS');
    }
	private function querymaker($tablename, $insData)
    {
	$count = 0;
	$fields = '';

	foreach($insData as $col => $val) {
      if ($count++ != 0) $fields .= ', ';
      //$col = mysqli_real_escape_string($col);
      //$val = mysqli_real_escape_string($val);
      $fields .= "`$col` = '$val'";
	}

	$query = 'INSERT INTO ' . $tablename . ' SET '.$fields;
    return $query;
    }
    private function querymaker_old($tablename, $insData)
    {
        $prep = array();
        foreach ($insData as $k => $v) {
            $prep[$v] = $v;
        }
        $query = 'INSERT INTO ' . $tablename . ' ( ' . implode(", ", array_keys($insData)) . ') VALUES ("' . implode('", "', array_keys($prep)) . '")';

        return $query;
    }
}

?>	