# MySQL migration layer

[![Build Status](https://travis-ci.org/UFOMelkor/mysql-migration-layer.png?branch=master)](https://travis-ci.org/UFOMelkor/mysql-migration-layer)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/UFOMelkor/mysql-migration-layer/badges/quality-score.png?s=afd2b8429ba93522e6febb660100698bf59390d1)](https://scrutinizer-ci.com/g/UFOMelkor/mysql-migration-layer/)
[![Coverage Status](https://coveralls.io/repos/UFOMelkor/mysql-migration-layer/badge.png)](https://coveralls.io/r/UFOMelkor/mysql-migration-layer)
[![Total Downloads](https://poser.pugx.org/dkplus/mysql-migration-layer/downloads.png)](https://packagist.org/packages/dkplus/mysql-migration-layer)
[![Latest Stable Version](https://poser.pugx.org/dkplus/mysql-migration-layer/v/stable.png)](https://packagist.org/packages/dkplus/mysql-migration-layer)
[![Latest Unstable Version](https://poser.pugx.org/dkplus/mysql-migration-layer/v/unstable.png)](https://packagist.org/packages/dkplus/mysql-migration-layer)

Layer for migration from mysql to mysqli.

## Installation

### composer
For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

`php composer.phar require dkplus/mysql-migration-layer`

When asked for a version to install, type `0.1`.

### Post-install
After installing replace all `mysql_*()` calls with `\MySQL\Proxy::*()`.

You can do this step automatically. Therefore you must also install [nikic/php-parser](https://github.com/nikic/PHP-Parser):

`php composer.phar require nikic/php-parser`

Then you can run the converter by calling `php vendor/bin/convert-mysql.php [-w] <file>`.

Run the script with the path of the PHP file you wish to convert as argument. This will print the converted source code to STDOUT.

You can add the `-w` switch if you want to override the original file with the converted code.

In case of any error, an error message is written to STDERR and the script exits with a return code of 1.

Use find to convert a whole directory recursively:
`find <directory> -name "*.php" -exec php "vendor/bin/convert-mysql.php" -w "{}" \;`

