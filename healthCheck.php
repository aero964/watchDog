<?php
date_default_timezone_set('Asia/Tokyo');
//header('Content-Type: text/plain');

//サーバヘルスチェック

$mtime = 0; //初期値を与える
$try = 5; //トライする回数

echo "処理を開始します。".PHP_EOL;
echo str_pad(" ",4096)."<br />\n";

ob_flush();
flush();

for ($record=0; $record < $try; $record++) { 

	if($mtime > 15){//サーバにあまり負担をかけたくない
		echo $record."回目:タイムアウト<br>".PHP_EOL;
		$try = $record; //平均を出すにはちょうどいい数になっている
		break;
	}

	$time_start = microtime(true);
	$context = stream_context_create(array(
	      'http' => array('ignore_errors' => true)
	 ));
	$body = file_get_contents('https://example.com/test.bmp', false, $context); //検査対象サーバに設置した高負荷ファイルのURL

	$time = microtime(true) - $time_start;
	$mtime += $time;
	echo $record."回目:{$time} 秒<br>".PHP_EOL;

ob_flush();
flush();
}

echo "計測時間:{$mtime}<br>".PHP_EOL;

$aver = $mtime / $try;

echo "計測時間平均:{$aver}<br>".PHP_EOL;

if ($aver <= 3) {
	$condt = "<font color=blue>快適です</font>".PHP_EOL;
	$condu = "快適";
}else if($aver <= 5){
	$condt = "普通です".PHP_EOL;
	$condu = "普通";
}else if($aver <= 7){
	$condt = "<font color=orange>混み合っています</font>".PHP_EOL;
	$condu = "混み合っています";
}else if($aver > 7){
	$condt = "<font color=red>非常に混み合っています</font>".PHP_EOL;
	$condu = "非常に混み合っています";
}

$resulthtml = "<strong>サーバの状態</strong>:".$condt."<br>".PHP_EOL;


//DFコマンドの実行

exec ('df', $df, $status);

$df = explode(" ", $df[1]);

function bconv($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

$datetime = date("※Y/m/d G:iの状態");

$resulthtml .= "DISK残量:".bconv($df[10] * 1024)."<br>".PHP_EOL;
$resulthtml .= "DISK使用状況:".bconv($df[8] * 1024)."(".$df[12].")<br>".PHP_EOL;
$resulthtml .= "<small>".$datetime."</small>".PHP_EOL;

file_put_contents( '/path/to/result.html', $resulthtml );
