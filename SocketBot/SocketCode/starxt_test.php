<?php
use Clue\React\Redis\Factory;
use Clue\React\Redis\Client;
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;


$worker = new Worker('websocket://0.0.0.0:7272');
$worker->transport = 'tcp';

$worker->count = 4;
$worker->name = 'RedisWorker';
$worker->onWorkerStart = function($worker) {
    global $factory;

    $loop    = Worker::getEventLoop();
    $factory = new Factory($loop);
	
	
$worker->onConnect = function($connection) {
	 //   echo "new connection from ip " . $connection->getRemoteIp() . "\n";
	echo "Worker onConnect\n\r\t";
    global $factory;
    $factory->createClient('localhost:6379')->then(function (Client $client) use ($connection) {
        
$markets=MARKETS_WS_SOCKET;
foreach ($markets as $market => $value) {
	
  		echo $market.PHP_EOL ;
		$client->get('wG6QJ8_getActiveDepth_'.$market)->then(function ($depth) use ($connection){
      //      echo $depth.PHP_EOL ;
	  

						$data = $depth['btc_usd'][1];
						$data['type'] = "depth";
						$data['market'] = "btcusdt";  
						$connection->send($depth);
            
        });
}
    });
	
	  $connection->onClose = function($connection) {
        //If disconnected, reconnect after 1 second
        $connection->reConnect(1);
    };


    // Perform an asynchronous connection
    $connection->connect();
};
 $con->onMessage = function($con, $data)use($worker)
    {

        $data = gzdecode($data);
        $data = json_decode($data, true);
        if(isset($data['ping'])) {
            $con->send(json_encode([
                "pong" => $data['ping']
            ]));
		}
	};
};
// Run all workers
Worker::runAll();
