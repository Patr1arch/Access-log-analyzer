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

// TODO: Test some command inputs and handle scripts without params
$options = getopt("u:t:");
var_dump($options);
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

