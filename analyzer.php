<?php
include_once(__DIR__.'/vendor/autoload.php');
use Kassner\LogParser\LogParser;
date_default_timezone_set('Asia/Vladivostok');
$patterns = [
        '/\/01\//', '/\/02\//', '/\/03\//', '/\/04\//',
        '/\/05\//', '/\/06\//', '/\/07\//', '/\/08\//',
        '/\/09\//', '/\/10\//', '/\/11\//', '/\/12\//'
];

$replacements = [
        '/Jan/', '/Feb/', '/Mar/', '/Apr/',
        '/May/', '/Jun/', '/Jul/', '/Aug/',
        '/Sep/', '/Oct/', '/Nov/', '/Dec/',
];

// strtotime not work with DD/MM/YYYY, 'cause it's ambiguous with American datetime MM/DD/YYYY
// TODO: Add tests for it
function formatDate(&$pt, &$rep, &$str){
        return preg_replace($pt, $rep, $str);
}

// TODO: Test some command inputs
$options = getopt("u:t:");
if (!isset($options['u']) || !isset($options['t']))
        throw new Exception("Required command parameters are not set");
if (strval(floatval($options['u'])) != $options['u'])
        throw new Exception("Required command parameter for -u is not a number");
if (strval(floatval($options['t'])) != $options['t'])
        throw new Exception("Required command parameter for -t is not a number");
$parser = new LogParser();
$parser->addPattern('%c', '(?P<caller>@[\w?-]+|-)');
$parser->addPattern('%g', '(?P<priority>[a-zA-Z]+\:\d+)');
$parser->setFormat('%h %l %u %t "%r" %>s %b %T "%{Referer}i" "%c" %g');
$startTime = null;
$endTime = null;
$currentAccess = 0;
$count = 0;
$notFailCount = 0;
while(!feof(STDIN)){ // TODO: Empty input?
	$line = fgets(STDIN);
	if ($line == '' || ctype_space($line)) 
		continue;
        $fLine = formatDate($patterns, $replacements, $line);
        $entry = $parser->parse($fLine);
        $entry->isFail = bccomp(floatval($entry->requestTime), floatval($options['t']), 6) > 0
                || !preg_match('/[^5]\d{2}|-/', $entry->status);
	if (!isset($startTime)) {
		if ($entry->isFail) {
			$notFailCount = 0;
			$startTime = $entry->stamp;
			$endTime = $startTime;
			$currentAccess = 0;
			$count = 1;
		}
		continue;
	}
	else if (!$entry->isFail)
		$notFailCount++;
	$currentAccessK = (floatval($notFailCount) / floatval(++$count)) * 100;
	if (bccomp(floatval($currentAccessK), floatval($options['u']), 6) < 0) {
		$currentAccess = $currentAccessK;
		$endTime = $entry->stamp;
	}
	else {
		echo date("H:i:s", $startTime)."\t".date("H:i:s", $endTime)."\t".number_format($currentAccess, 2)."\n";
		$startTime = null;
	}
}
if (isset($startTime) && bccomp(floatval($currentAccess), floatval($options['u']), 6) <= 0)
	echo date("H:i:s", $startTime)."\t".date("H:i:s", $endTime)."\t".number_format($currentAccess, 2)."\n";
?>

