<?php
include_once(__DIR__.'/vendor/autoload.php');
use Kassner\LogParser\LogParser;
date_default_timezone_set('Asia/Vladivostok');

class TimeInterval {
	public $startTime;
	public $endTime;
	public $currentAccess;
	public $count;
	public $notFailCount;

	public function __construct() {
		$this->startTime = null;
		$this->endTime = null;
		$this->currentAccess = 0;
		$this->count = 0;
		$this->notFailCount = 0;
	}

	public function reset(&$stamp) {
		$this->notFailCount = 0;
		$this->startTime = $stamp;
		$this->endTime = $this->startTime;
		$this->currentAccess = 0;
		$this->count = 1;
	}

	public function __toString() {
		return date("H:i:s", $this->startTime)."\t".date("H:i:s", $this->endTime)
			."\t".number_format($this->currentAccess, 2)."\n";
	}

}

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
function formatDate(&$pt, &$rep, &$str){
        return preg_replace($pt, $rep, $str);
}

$options = getopt("u:t:");
if (!isset($options['u']) || !isset($options['t']))
        throw new InvalidArgumentException("Required command parameters are not set");
if (strval(floatval($options['u'])) != $options['u'] ||
	strval(floatval($options['t'])) != $options['t'])
        throw new InvalidArgumentException("Required command parameter for -u and/or -t is not a number");

$parser = new LogParser();
$parser->addPattern('%c', '(?P<caller>@[\w?-]+|-)');
$parser->addPattern('%g', '(?P<priority>[a-zA-Z]+\:\d+)|-');
$parser->setFormat('%h %l %u %t "%r" %>s %b %T "%{Referer}i" "%c" %g');

$ti = new TimeInterval();
while(!feof(STDIN)){
	$line = fgets(STDIN);
	if ($line == '' || ctype_space($line)) 
		continue;
        $fLine = formatDate($patterns, $replacements, $line);
        $entry = $parser->parse($fLine);
        $entry->isFail = bccomp(floatval($entry->requestTime), floatval($options['t']), 6) > 0
		|| !preg_match('/[^5]\d{2}|-/', $entry->status);

	if (!isset($ti->startTime)) {
		if ($entry->isFail)
			$ti->reset($entry->stamp);
		continue;
	}
	else if (!$entry->isFail)
		$ti->notFailCount++;
	$currentAccessK = (floatval($ti->notFailCount) / floatval(++$ti->count)) * 100;
	if (bccomp(floatval($currentAccessK), floatval($options['u']), 6) < 0) {
		$ti->currentAccess = $currentAccessK;
		$ti->endTime = $entry->stamp;
	}
	else {
		echo $ti;
		$ti->startTime = null;
		if ($entry->isFail)
			$ti->reset($entry->stamp);
	}
}

if (isset($ti->startTime) && bccomp(floatval($currentAccess), floatval($options['u']), 6) < 0)
	echo $ti;
?>

