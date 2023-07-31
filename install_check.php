<?php
header('Content-type:text/html; charset=utf-8');
$lockFile = 'Public/install.lock';
if (file_exists($lockFile)) {
    exit('The program is already installed, please do not repeat the operation.');
}
if (preg_match('/Public/', $_SERVER['REQUEST_URI'])) {
    exit('Please configure the document_root directory of apache or nginx to the public directory of the project');
}
// PHP version
$phpVersion = PHP_VERSION;
$phpVersionCould = version_compare($phpVersion, '7.1.3', '>=');
// Directory permissions
$bootstrapDirWriteable = is_writable('Upload');
$storageDirWriteable = is_writeable('Runtime');
$envWriteable = is_writeable('pure_config.php');
// Extension
$opensslExtension = extension_loaded('openssl');
$pdoMysqlExtension = extension_loaded('pdo_mysql');
$mbstringExtension = extension_loaded('mbstring');
$curlExtension = extension_loaded('curl');
$tokenizerExtension = extension_loaded('tokenizer');
$xmlExtension = extension_loaded('xml');
$fileinfoExtension = extension_loaded('fileinfo');
$ctypeExtension = extension_loaded('ctype');
$jsonExtension = extension_loaded('json');
$bcmathExtension = extension_loaded('bcmath');
$zipExtension = extension_loaded('zip');
$gdExtension = extension_loaded('gd');
$extensionRows = [
    'openssl' => $opensslExtension,
    'pdo_mysql' => $pdoMysqlExtension,
    'mbstring' => $mbstringExtension,
	'curl' => $curlExtension,
    'tokenizer' => $tokenizerExtension,
    'xml' => $xmlExtension,
    'fileinfo' => $fileinfoExtension,
    'ctype' => $ctypeExtension,
    'json' => $jsonExtension,
    'bcmath' => $bcmathExtension,
    'zip' => $zipExtension,
    'GD' => $gdExtension,
];
$isCross = $phpVersionCould && $storageDirWriteable && $bootstrapDirWriteable && $envWriteable;
if ($isCross) {
    foreach ($extensionRows as $extensionRow) {
        $isCross = $isCross && $extensionRow;
    }
}
?>
<!doctype html>
<html >
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link crossorigin="anonymous" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <title>Codono Environment Check</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12 mb-0 text-center">
                <h2 style="line-height: 60px;">Codono Environment Check</h2>
            </div>
            <div class="col-sm-12">
                <ul class="list-group">
                    <li class="list-group-item list-group-item-info"><b>PHP version detection(Required version:>=7.1.3)</b></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo $phpVersion; ?>
                        <span class="badge badge-<?php echo $phpVersionCould ? 'success' : 'danger' ?> badge-pill">
                            <?php echo $phpVersionCould ? 'Pass' : 'Fail' ?>
                        </span>
                    </li>
                    <li class="list-group-item list-group-item-info"><b>Directory permission writable</b></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo ('Upload'); ?>
                        <span class="badge badge-<?php echo $bootstrapDirWriteable ? 'success' : 'danger' ?> badge-pill">
                            <?php echo $bootstrapDirWriteable ? 'Writable' : 'Not Writable' ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo ('Runtime'); ?>
                        <span class="badge badge-<?php echo $storageDirWriteable ? 'success' : 'danger' ?> badge-pill">
                            <?php echo $storageDirWriteable ? 'Writable' : 'Not Writable' ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo realpath('pure_config.php'); ?>
                        <span class="badge badge-<?php echo $envWriteable ? 'success' : 'danger' ?> badge-pill">
                            <?php echo $envWriteable ? 'Writable' : 'Not Writable' ?>
                        </span>
                    </li>
                    <li class="list-group-item list-group-item-info"><b>PHP extension detection</b></li>
                    <?php foreach($extensionRows as $name => $row) { ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo $name; ?>
                        <span class="badge badge-<?php echo $row ? 'success' : 'danger' ?> badge-pill">
                            <?php echo $row ? 'Detected' : 'Not Found' ?>
                        </span>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="col-sm-12 text-center" style="line-height: 120px;">
                <?php if ($isCross) { ?>
                <span class="alert alert-info">You can continue installation </span>
                <?php } else { ?>
                    <button disabled class="btn btn-info" type="button">Can't continue</button>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>