<?php

/*

毎時30分にcronで自動実行するようにしています。
詳細は
# crontab -e

*/

function ping($host,$port=443,$timeout=5){

	//pingを撃つ
	$fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
	if ( ! $fsock ) {
	return FALSE;
	} else{
	return TRUE;
	}

	}

	$host = '127.128.129.130';
	$up = ping($host);

	include("healthCheck.php");

	$date = date('Y/m/d H:i:s');

	mb_internal_encoding("UTF-8");
	mb_language("Japanese");

	switch ($up) {
		case true:
		$subject = "【正常】死活監視情報配信システム";
		$message = "結果:サーバは正常に稼働しています。".PHP_EOL."対象ホスト:".$host.PHP_EOL."検査日時:".$date;
			exit;
			break;
		
		default:
		$subject = "【停止】死活監視情報配信システム";
		$message = "結果:サーバが停止しています。".PHP_EOL."直ちに点検してください。".PHP_EOL."対象ホスト:".$host.PHP_EOL."検査日時:".$date;
			break;
	}

$message 	.= PHP_EOL.PHP_EOL."-------------------------------------".PHP_EOL;
$message 	.= "ヘルスチェックの結果は以下の通りです".PHP_EOL;
$message 	.= "-------------------------------------".PHP_EOL;
$message	.= "コンディション:".$condu.PHP_EOL;
$message	.= "平均アップロード時間:".$aver."秒".PHP_EOL;
$message	.= "DISK残量:".bconv($df[10] * 1024).PHP_EOL;
$message 	.= "DISK使用状況:".bconv($df[8] * 1024)."(".$df[12].")";


$to      = 'info@example.com';
$headers = 'From: watchdog@example.com' . "\r\n";

mb_send_mail($to, $subject, $message, $headers);

