<?php
(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

if (!class_exists('PHPParser_Parser')) {
    file_put_contents('php://stderr', 'Could not find PHPParser_Parser. Did you install nikic/php-parser?' . "\n");
    exit(1);
}

if (!class_exists('MySQL\\Converter\\RenameVisitor')) {
    file_put_contents('php://stderr', 'Could not find the migration layer library ' . "\n");
    exit(1);
}

$filePath = null;
$saveFile = false;

if ($argc > 3) {
    file_put_contents('php://stderr', 'Usage: php convert-mysql.php [-w] <file>' . "\n");
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
    file_put_contents('php://stderr', 'Usage: php convert-mysql.php [-w] <file>' . "\n");
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
