<?php
require_once __DIR__ . '/vendor/nikic/php-parser/lib/bootstrap.php';
require_once __DIR__ . '/autoload_register.php';

$filePath = null;
$saveFile = false;

if ($argc > 3) {
    file_put_contents('php://stderr', 'Usage: php convert.php [-w] <file>' . "\n");
    exit(1);
}

for($i = 1; $i < $argc; ++$i) {
    if($argv[$i] && $argv[$i][0] == '-') {
        $saveFile = ($argv[$i] == '-w');
    } else {
        $filePath = $argv[$i];
    }
}

if(!$filePath) {
    file_put_contents('php://stderr', 'Usage: php convert.php [-w] <file>' . "\n");
    exit(1);
} elseif(!file_exists($filePath)) {
    file_put_contents('php://stderr', 'File "' . $filePath . '" not found.' . "\n");
    exit(1);
}

$file = __DIR__ . '/index.php';
$code = file_get_contents($file);

$parser    = new PHPParser_Parser(new PHPParser_Lexer());
$printer   = new PHPParser_PrettyPrinter_Default();
$traverser = new PHPParser_NodeTraverser();
$traverser->addVisitor(new \MySQL\Converter\RenameVisitor());

$nodes  = $parser->parse($code);
$nodes  = $traverser->traverse($nodes);
$result = "<?php\n" . $printer->prettyPrint($nodes);

if ($saveFile) {
    file_put_contents($filePath, $result);
    return;
}
print $result;
