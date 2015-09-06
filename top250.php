<?php
error_reporting(0);

include 'simple_html_dom.php';
include 'mysql.class.php';

define('ENV', 'debug');
$config = [
	'charset' => 'utf8',
	'db' => 'test',
	'table' => 'films',
	'debug' => [
		'host' => 'localhost',
		'user' => 'user',
		'password' => 'password',
		'port' => '3306'
	]
];

$mysql = mysql::getInstance();

$mysql->connect($config);

for ($parm = 0; $parm <= 225; $parm += 25) {

	$url = 'http://movie.douban.com/top250?start=' . $parm . '&filter=&type=';

	$html = file_get_contents($url);

	$dom = new simple_html_dom($html);

	$listData = $dom->find('#content .item');

	foreach($listData as $key => $val) {
		$film = [
			'title' => rtrim($val->find(".title", 0)->plaintext),
			'subtitle' => str_replace(['&nbsp', ';/;'], '', $val->find(".title",1)->plaintext),
			'link' => $val->find("img", 0)->parent->attr['href'],
			'img' => $val->find("img", 0)->src,
			'rate' => $val->find('.star em', 0)->plaintext,
			'quote' => $val->find(".quote .inq", 0)->plaintext,
		];
		$mysql->data($film)->add();
		echo $film['title'] . ' -- ' . $film['quote'] . PHP_EOL;
	}
}

//
