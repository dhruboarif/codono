<?php
/*A quick solution to SHOW XML based Error */
header("Content-type: text/xml");
$requestid=strtoupper(substr(md5(uniqid(rand(), true)),0,12));
$hostid=base64_encode(md5("Codono".date('DYMHis')));
?>
<?xml version="1.0" encoding="UTF-8"?>
<Error><Code>AccessDenied</Code><Message>Access Denied</Message><RequestId><?php echo $requestid;?></RequestId><HostId><?php echo $hostid;?> </HostId></Error>