<?php
namespace Org\Util;
class EthCommon
{

    protected $host, $port, $version;
    protected $id = 0;
    public $base = 1000000000000000000;//1e18 wei  Base unit:
    

    /** Wei is the base unit (lowest denomination) of ether. 10^18 Wei = 1 Ether
     * constructor
     * Common constructor.
     * @param $host
     * @param string $port
     * @param string $version
     */
    function __construct($host, $port = "80",$version)
    {
        $this->host = $host; //geth Address
        $this->port = $port;
        $this->version = $version;
    }
	function bcmul_custom($_ro, $_lo, $_scale=0) {
	if(function_exists('bcmul')){
        return bcmul($_ro,$_lo, $_scale);
    }else{
    return round($_ro*$_lo, $_scale);
    }
	}
	function bcdiv_custom( $first, $second, $scale = 0 )
	{
	if(function_exists('bcdiv')){
		return bcdiv($first,$second, $_scale);
	}
    else{
    return round(  $first / $second, $scale );
	}
	}



    /**
     * Send Request
     * @author morris@codono
     * @since 2018-8-21
     * @param $method
     * @param array $params
     * @return mixed
     */
    function request($method, $params = array())
    {
        $data = array();
        $data['jsonrpc'] = $this->version;
        $data['id'] = $this->id + 1;
        $data['method'] = $method;
        $data['params'] = $params;
        //@SaveLog('EthPayLocal', "params_".$method.json_encode($data), __FUNCTION__);
        //echo json_encode($data);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->host);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $ret = curl_exec($ch);
        //echo "$ret".$ret;
        //Back to results
        if ($ret) {
            curl_close($ch);
            return json_decode($ret, true);
        } else {
            $error = curl_errno($ch);
            //echo $error;
            curl_close($ch);
            return false;
            //throw new Exception("Curl error, error code:$error");
        }
    }

    /**
     * @author morris@codono
     * @since 2018-8-21
     * @param $weiNumber 16 Binary Wei Units
     * @return float|int Decimal eth unit [normal unit]
     */
	function baseconvert($weiNumber) {
		return base_convert($weiNumber, 16, 10);
	}
    function fromWei($weiNumber)
    {
        $tenNumber = base_convert($weiNumber, 16, 10);
        $ethNumber = bcdiv($tenNumber,$this->base,8);
        return $ethNumber;
    }

    function fromWei2($weiNumber)
    {
        $ethNumber = $this->bcdiv_custom($weiNumber,$this->base,8);//High precision floating point number division
        return $ethNumber;
    }
    function fromWei3($weiNumber)
    {
        $tenNumber = base_convert($weiNumber, 16, 10);
        //echo $tenNumber."<br/>";
        $ethNumber = $this->bcdiv_custom($tenNumber,100000000,8);
        return $ethNumber;
    }

    /**
     * @author morris@codono
     * @since 2018-8-21
     * @param $ethNumber 10 hexadecimal eth units
     * @return string    16 Binary Wei Units
     */
    function toWei($ethNumber)
    {
        //echo "ethNumber".$ethNumber;
        //echo "base".$this->base;
        $tenNumer=$this->bcmul_custom($ethNumber,$this->base);//High precision floating-point multiplication
        //echo $tenNumer;die();

        $weiNumber = base_convert($tenNumer, 10, 16);
        return  '0x'.$weiNumber;
    }
    function toWei3($ethNumber)
    {
        //echo "ethNumber".$ethNumber;
        //echo "base".$this->base;
        $tenNumer=$this->bcmul_custom($ethNumber,100000000);//High precision floating-point multiplication
        //echo $tenNumer;die();

        $weiNumber = base_convert($tenNumer, 10, 16);
        return  '0x'.$weiNumber;
    }

    /**
     * @param $ethNumber
     * @return string  start 10  end 10
     */
    function toWei2($ethNumber)
    {
        $weiNumber = $this->bcmul_custom($ethNumber,$this->base);
        return $weiNumber;
    }

    /**
     * Determine whether it is hexadecimal
     * @author morris@codono
     * @since 2018-8-21
     * @param $a
     * @return int
     */
    function assertIsHex($a)
    {
        if (ctype_xdigit($a)) {
            return true;
        } else {
            return false;
        }
    }


}