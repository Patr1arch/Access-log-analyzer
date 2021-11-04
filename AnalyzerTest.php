<?php
use PHPUnit\Framework\TestCase;

class AnalyzerTest extends TestCase {
        public function testExcWithoutParams() {
                $this->assertNotTrue(exec('php analyzer.php'));
                $this->assertNotTrue(exec('php analyzer.php -u'));
                $this->assertNotTrue(exec('php analyzer.php -t'));
                $this->assertNotTrue(exec('php analyzer.php -t -u'));

        }

        public function testExcIncorrectParams() {
                $this->assertNotTrue(exec('php analyzer.php -u'));
                $this->assertNotTrue(exec('php analyzer.php -u abacaba -t abacaba'));
                $this->assertNotTrue(exec('php analyzer.php -u aba10caba -t 1abacaba0'));
                $this->assertNotTrue(exec('php analyzer.php -u 4.a1ba2ca3ba1 -t .a45bac12aba111.45'));
        }

        public function testFormatDate() {
                $file = fopen('access_logs/format_access.log', 'r');
                $months = [
                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar',
                        4 => 'Apr', 5 => 'May', 6 => 'Jun',
                        7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
                        10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
                ];

                try {
                        require 'analyzer.php';
                }
                catch (Exception $e) {
                        $count = 0;
                        while(!feof($file)) {
                                $line = trim(fgets($file));
                                if ($line == '' || ctype_space($line))
                                        continue;
                                $outNum = str_pad(++$count,2,"0",STR_PAD_LEFT);
                                $this->assertSame('192.168.32.181 - - ['.$outNum.'/'.$months[$count].'/20'.$outNum.
					':'.$outNum.':'.$outNum.':'.$outNum.' +1000] "PUT /rest/v1.4/documents HTTP/1.1"'.
					' 200 2 44.510983 "-" "@list-item-updater" prio:0', formatDate($patterns, $replacements, $line));
                        }
                }
        }

}
?>

