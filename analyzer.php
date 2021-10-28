<?php
include_once(__DIR__.'/vendor/autoload.php');
use Kassner\LogParser\LogParser;

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
var_dump($options);
if (empty($options['u']) || empty($options['t']))
        throw new Exception("Required command parameters are not set");
if (!preg_match('/\d+?(.\d*)/', $options['u']))
        throw new Exception("Required command parameter for -u is not a number");
if (!preg_match('/\d+?(.\d*)/', $options['t']))
        throw new Exception("Required command parameter for -t is not a number");
$parser = new LogParser();
$parser->addPattern('%c', '(?P<caller>@[\w?-]+|-)');
$parser->addPattern('%g', '(?P<priority>[a-zA-Z]+\:\d+)');
$parser->setFormat('%h %l %u %t "%r" %>s %b %T "%{Referer}i" "%c" %g');
while(!feof(STDIN)){ // TODO: Empty input?
	$line = fgets(STDIN);
	if ($line == '' || ctype_space($line)) 
		continue;
        $fLine = formatDate($patterns, $replacements, $line);
        var_dump($fLine);
        $entry = $parser->parse($fLine);
        $entry->isFail = floatval($entry->requestTime) > floatval($options['t'])
                || !preg_match('/[^5]\d{2}|-/', $entry->status); // Warn: float comparing
        var_dump($entry);
}

?>

