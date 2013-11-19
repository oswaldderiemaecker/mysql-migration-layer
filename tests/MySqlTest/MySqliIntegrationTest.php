<?php
namespace MySQLTest;

use MySql\Proxy;
use PHPUnit_Extensions_Database_TestCase as TestCase;

require_once __DIR__ . '/TestAsset/FetchObjectClass.php';

class MySqliIntegrationTest extends TestCase
{
    /** @var \PDO */
    private static $pdo;
    private static $link;
    private $connection;

    protected function getConnection()
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:dbname=' . $GLOBALS['DB_DBNAME']
                 . ';host=' . $GLOBALS['DB_HOST']
                 . ';charset=' . $GLOBALS['DB_CHARSET'];
            self::$pdo = new \PDO($dsn, $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);

            self::$link = $this->connectProxyWithGlobalData();
            Proxy::set_charset($GLOBALS['DB_CHARSET'], self::$link);
            Proxy::select_db($GLOBALS['DB_DBNAME'], self::$link);
        }

        if ($this->connection === null) {
            $this->connection = $this->createDefaultDBConnection(self::$pdo);
        }

        return $this->connection;
    }

    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet(__DIR__ . '/../dataset.xml');
    }

    public function testShouldSetTheCharacterEncoding()
    {
        $this->assertSame($GLOBALS['DB_CHARSET'], Proxy::client_encoding());
    }

    // @todo depends query, fetch_row
    public function testShouldSelectADatabase()
    {
        $this->assertCurrentDatabase($GLOBALS['DB_DBNAME']);
    }

    // @todo depends query
    public function testShouldProvideAffectedRows()
    {
        $result = Proxy::query('SELECT * FROM `select`', self::$link);
        $this->assertSame(3, Proxy::affected_rows(self::$link));
    }

    // @todo depends query
    public function testShouldProvideMinusOneForAffectedRowsAfterAnErrorOccured()
    {
        Proxy::query('foo', self::$link);
        $this->assertSame(-1, Proxy::affected_rows(self::$link));
    }

    // @todo depends thread_id
    public function testShouldUseNewConnectionsAsDefault()
    {
        $threadId = Proxy::thread_id(self::$link);
        $this->connectProxyWithGlobalData();
        $this->assertNotSame($threadId, Proxy::thread_id());
    }

    // @todo depends thread_id
    public function testShouldCloseAConnection()
    {
        $this->connectProxyWithGlobalData();
        Proxy::close();

        $this->setExpectedException('PHPUnit_Framework_Error', 'Couldn\'t fetch mysqli');
        Proxy::thread_id();
    }

    public function testShouldCreateDatabases()
    {
        $this->assertTrue(Proxy::create_db(__FUNCTION__, self::$link), 'Should return true for created databases');
        $this->assertDatabaseExists(__FUNCTION__);

        $this->dropDb(__FUNCTION__);
    }

    public function testShouldReturnFalseAfterDatabaseCouldNotBeCreated()
    {
        $this->assertFalse(Proxy::create_db($GLOBALS['DB_DBNAME'], self::$link));
    }

    // @todo depends fetch_object
    public function testShouldMoveTheInternalRowPointerWithDataSeek()
    {
        $result = Proxy::query('SELECT id FROM `select` ORDER BY id ASC', self::$link);
        $this->assertTrue(Proxy::data_seek($result, 1));

        $this->assertEquals(2, Proxy::fetch_object($result)->id);
    }

    public function testShouldReturnFalseIfTheInternalRowPointerCouldNotBeMovedByDataSeek()
    {
        $result = Proxy::query('SELECT id FROM `select` ORDER BY id ASC', self::$link);
        $this->assertFalse(Proxy::data_seek($result, 3));
    }

    // @todo depends num_rows
    public function testShouldProvideDatabaseNames()
    {
        $result         = Proxy::list_dbs(self::$link);
        $countDatabases = Proxy::num_rows($result);

        $this->assertGreaterThan(0, $countDatabases);
        for ($i = 0; $i < $countDatabases; ++$i) {
            $this->assertDatabaseExists(Proxy::db_name($result, $i));
        }
    }

    public function testShouldReturnFalseOnWrongQueryWhenUsingDbQuery()
    {
        $this->assertFalse(Proxy::db_query($GLOBALS['DB_DBNAME'], 'foo', self::$link));
    }

    public function testShouldReturnFalseOnNotExistingDatabaseWhenUsingDbQuery()
    {
        $this->assertFalse(Proxy::db_query(__FUNCTION__, 'SELECT NULL', self::$link));
    }

    // @todo depends select_db
    public function testShouldSwitchTheDatabaseWhenUsingDbQuery()
    {
        $this->createDb(__FUNCTION__);
        $this->assertCurrentDatabase($GLOBALS['DB_DBNAME']);
        Proxy::db_query(__FUNCTION__, 'SELECT NULL', self::$link);

        $this->assertCurrentDatabase(__FUNCTION__, self::$link);

        Proxy::select_db($GLOBALS['DB_DBNAME'], self::$link);
        $this->dropDb(__FUNCTION__);
    }

    public function testShouldDropDatabases()
    {
        $this->createDb(__FUNCTION__);
        Proxy::drop_db(__FUNCTION__, self::$link);

        $this->assertDatabaseNotExists(__FUNCTION__);
    }

    // @todo depends query
    public function testShouldReturnZeroWhenUsingErrnoAndNoErrorOccured()
    {
        Proxy::query('SELECT NULL', self::$link);
        $this->assertSame(0, Proxy::errno(self::$link));
    }

    // @todo depends query
    public function testShouldReturnNotZeroWhenUsingErrnoAndAnErrorOccured()
    {
        Proxy::query('foo', self::$link);
        $this->assertGreaterThan(0, Proxy::errno(self::$link));
    }

    // @todo depends query
    public function testShouldReturnAnEmptyStringWhenUsingErrorAndNoErrorOccured()
    {
        Proxy::query('SELECT NULL', self::$link);
        $this->assertSame('', Proxy::error(self::$link));
    }

    // @todo depends query
    public function testShouldReturnTheErrorMessageWhenUsingErrorAndAnErrorOccured()
    {
        Proxy::query('foo', self::$link);
        $this->assertGreaterThan(0, strlen(Proxy::error(self::$link)));
    }

    public function testShouldUseTheCurrentConnectionToEscapeStringsWithoutGivenConnection()
    {
        $this->connectProxyWithGlobalData();
        $this->assertSame("Zak\\'s Laptop", Proxy::escape_string("Zak's Laptop"));
    }

    // @todo depends query
    public function testShouldFetchNumericAndAssociativeVarsWhenUsingFetchArrayByDefault()
    {
        $result = Proxy::query('SELECT id FROM `select`', self::$link);
        $row    = Proxy::fetch_array($result);

        $this->assertArrayHasKey(0, $row);
        $this->assertArrayHasKey('id', $row);
    }

    // @todo depends query
    public function testShouldBeAbleFetchOnlyNumericArrayWhenUsingFetchArray()
    {
        $result = Proxy::query('SELECT id FROM `select`', self::$link);
        $row    = Proxy::fetch_array($result, 2); // MYSQL_NUM

        $this->assertArrayHasKey(0, $row);
        $this->assertArrayNotHasKey('id', $row);
    }

    // @todo depends query
    public function testShouldBeAbleFetchOnlyAssociativeArrayWhenUsingFetchArray()
    {
        $result = Proxy::query('SELECT id FROM `select`', self::$link);
        $row    = Proxy::fetch_array($result, 1); // MYSQL_ASSOC

        $this->assertArrayNotHasKey(0, $row);
        $this->assertArrayHasKey('id', $row);
    }

    // @todo depends query
    public function testShouldReturnFalseIfThereAreNoMoreRowsWhenUsingFetchArray()
    {
        $result = Proxy::query('SELECT id FROM `select` WHERE id=5000', self::$link);
        $this->assertFalse(Proxy::fetch_array($result));
    }

    // @todo depends query
    public function testShouldIncreaseTheInternalRowPointerWhenUsingFetchArray()
    {
        $result = Proxy::query('SELECT id FROM `select`', self::$link);
        $rowA   = Proxy::fetch_array($result);
        $rowB   = Proxy::fetch_array($result);

        $this->assertNotSame($rowA[0], $rowB[0]);
    }

    // @todo depends query
    public function testShouldFetchOnlyAssociativeVarsWhenUsingFetchAssoc()
    {
        $result = Proxy::query('SELECT id FROM `select`', self::$link);
        $row    = Proxy::fetch_assoc($result);

        $this->assertArrayNotHasKey(0, $row);
        $this->assertArrayHasKey('id', $row);
    }

    // @todo depends query
    public function testShouldReturnFalseIfThereAreNoMoreRowsWhenUsingFetchAssoc()
    {
        $result = Proxy::query('SELECT id FROM `select` WHERE id=5000', self::$link);
        $this->assertFalse(Proxy::fetch_assoc($result));
    }

    // @todo depends query
    public function testShouldIncreaseTheInternalRowPointerWhenUsingFetchAssoc()
    {
        $result = Proxy::query('SELECT id FROM `select`', self::$link);
        $rowA   = Proxy::fetch_assoc($result);
        $rowB   = Proxy::fetch_assoc($result);

        $this->assertNotSame($rowA['id'], $rowB['id']);
    }

    // @todo depends query
    public function testShouldFetchOnlyAssociativeVarsWhenUsingFetchRow()
    {
        $result = Proxy::query('SELECT id FROM `select`', self::$link);
        $row    = Proxy::fetch_row($result);

        $this->assertArrayHasKey(0, $row);
        $this->assertArrayNotHasKey('id', $row);
    }

    // @todo depends query
    public function testShouldReturnFalseIfThereAreNoMoreRowsWhenUsingFetchRow()
    {
        $result = Proxy::query('SELECT id FROM `select` WHERE id=5000', self::$link);
        $this->assertFalse(Proxy::fetch_row($result));
    }

    // @todo depends query
    public function testShouldIncreaseTheInternalRowPointerWhenUsingFetchRow()
    {
        $result = Proxy::query('SELECT id FROM `select`', self::$link);
        $rowA   = Proxy::fetch_row($result);
        $rowB   = Proxy::fetch_row($result);

        $this->assertNotSame($rowA[0], $rowB[0]);
    }

    // @todo depends query
    public function testShouldReturnAnInstanceofStdClassByDefaultWhenUsingFetchObject()
    {
        $result = Proxy::query('SELECT id FROM `select`', self::$link);
        $row    = Proxy::fetch_object($result);
        $this->assertInstanceOf('stdClass', $row);
    }

    // @todo depends query
    public function testShouldUseOtherClassesToCreateResultWhenUsingFetchObject()
    {
        $result = Proxy::query('SELECT id FROM `select` WHERE id=1', self::$link);
        $row    = Proxy::fetch_object($result, 'MySqlTest\\TestAsset\\FetchObjectClass', array(2));
        $this->assertInstanceOf('MySqlTest\\TestAsset\\FetchObjectClass', $row);
        $this->assertEquals(1, $row->id);
        $this->assertSame(2, $row->constructorVar);
    }

    // @todo depends query
    public function testShouldReturnFalseIfThereAreNoMoreRowsWhenUsingFetchObject()
    {
        $result = Proxy::query('SELECT id FROM `select` WHERE id=5000', self::$link);
        $this->assertFalse(Proxy::fetch_object($result));
    }

    // @todo depends query
    public function testShouldIncreaseTheInternalRowPointerWhenUsingFetchObject()
    {
        $result = Proxy::query('SELECT id FROM `select`', self::$link);
        $rowA   = Proxy::fetch_object($result);
        $rowB   = Proxy::fetch_object($result);

        $this->assertNotSame($rowA->id, $rowB->id);
    }

    // @todo depends query
    public function testShouldReturnTheLengthsOfARowFromQueryResult()
    {
        $result  = Proxy::query('SELECT id FROM `select` WHERE id=1', self::$link);
        $row     = Proxy::fetch_row($result);
        $lengths = Proxy::fetch_lengths($result);

        foreach ($row as $key => $value) {
            $this->assertSame(strlen($value), $lengths[$key]);
        }
    }

    // @todo depends query
    public function testShouldReturnFalseForTheLengthsOfARowFromQueryResultWhenNoResultHasBeenFetched()
    {
        $result  = Proxy::query('SELECT id FROM `select` WHERE id=1', self::$link);
        $lengths = Proxy::fetch_lengths($result);

        $this->assertFalse($lengths);
    }

    // @todo depends query num_rows
    public function testShouldProvideMetadataUntilNoMoreMetadataAreAvailable()
    {
        $result = Proxy::query('SELECT * FROM `metadata`', self::$link);
        for ($i = 0; $i < 6; ++$i) {
            $this->assertInstanceOf('stdClass', Proxy::fetch_field($result));
        }
        $this->assertFalse(Proxy::fetch_field($result));
    }

    // @todo depends query
    public function testShouldProvideSomeMetadataAboutAField()
    {
        $result = Proxy::query('SELECT * FROM `metadata`', self::$link);
        $meta   = Proxy::fetch_field($result);

        $this->assertObjectHasAttribute('name', $meta);
        $this->assertObjectHasAttribute('table', $meta);
        $this->assertObjectHasAttribute('max_length', $meta);
        $this->assertObjectHasAttribute('not_null', $meta);
        $this->assertObjectHasAttribute('primary_key', $meta);
        $this->assertObjectHasAttribute('unique_key', $meta);
        $this->assertObjectHasAttribute('multiple_key', $meta);
        $this->assertObjectHasAttribute('numeric', $meta);
        $this->assertObjectHasAttribute('blob', $meta);
        $this->assertObjectHasAttribute('type', $meta);
        $this->assertObjectHasAttribute('unsigned', $meta);
        $this->assertObjectHasAttribute('zerofill', $meta);

        return $result;
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideTheColumnNameFromAField($result)
    {
        $meta = Proxy::fetch_field($result, 0);
        $this->assertEquals('id', $meta->name);
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideTheTableNameBelongingToAField($result)
    {
        $meta = Proxy::fetch_field($result, 0);
        $this->assertEquals('metadata', $meta->table);
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideTheMaxLengthOfAField($result)
    {
        $meta = Proxy::fetch_field($result, 0);
        $this->assertEquals(0, $meta->max_length);
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideANotNullFlagForAField($result)
    {
        $meta = Proxy::fetch_field($result, 0);
        $this->assertEquals(1, $meta->not_null);

        $meta = Proxy::fetch_field($result, 1);
        $this->assertEquals(0, $meta->not_null);
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideAPrimaryKeyFlagForAField($result)
    {
        $meta = Proxy::fetch_field($result, 0);
        $this->assertEquals(1, $meta->primary_key);

        $meta = Proxy::fetch_field($result, 1);
        $this->assertEquals(0, $meta->primary_key);
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideAMultipleKeyFlagForAField($result)
    {
        $meta = Proxy::fetch_field($result, 0);
        $this->assertEquals(0, $meta->multiple_key);

        $meta = Proxy::fetch_field($result, 3);
        $this->assertEquals(1, $meta->multiple_key);
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideAUniqueKeyFlagForAField($result)
    {
        $meta = Proxy::fetch_field($result, 0);
        $this->assertEquals(0, $meta->unique_key);

        $meta = Proxy::fetch_field($result, 2);
        $this->assertEquals(1, $meta->unique_key);
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideANumericFlagForAField($result)
    {
        $meta = Proxy::fetch_field($result, 2);
        $this->assertEquals(1, $meta->numeric);

        $meta = Proxy::fetch_field($result, 3);
        $this->assertEquals(0, $meta->numeric);
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideABlobFlagForAField($result)
    {
        $meta = Proxy::fetch_field($result, 0);
        $this->assertEquals(0, $meta->blob);

        $meta = Proxy::fetch_field($result, 1);
        $this->assertEquals(1, $meta->blob);
    }

    public function testShouldProvideTheTypeForAField()
    {
        $result = Proxy::query('SELECT * FROM `metadata_types`', self::$link);
        $this->assertCurrentDatabase($GLOBALS['DB_DBNAME']);

        $expectedTypes = array('int', 'int', 'int', 'int', 'int', 'real', 'real', 'real', 'real', 'int', 'int', 'int',
            'date', 'datetime', 'timestamp', 'time', 'year', 'string', 'string', 'blob', 'blob', 'blob', 'blob',
            'string', 'string', 'blob', 'blob', 'blob', 'blob', 'string', 'string', 'geometry',  'geometry', 'geometry',
            'geometry', 'geometry', 'geometry', 'geometry', 'geometry',
        );

        foreach ($expectedTypes as $each) {
            $meta = Proxy::fetch_field($result);
            $this->assertEquals($each, $meta->type);
        }
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideAnUnsignedFlagForAField($result)
    {
        $meta = Proxy::fetch_field($result, 2);
        $this->assertEquals(1, $meta->unsigned);

        $meta = Proxy::fetch_field($result, 3);
        $this->assertEquals(0, $meta->unsigned);
    }

    /**
     * @depends testShouldProvideSomeMetadataAboutAField
     */
    public function testShouldProvideAZerofillFlagForAField($result)
    {
        $meta = Proxy::fetch_field($result, 2);
        $this->assertEquals(1, $meta->zerofill);

        $meta = Proxy::fetch_field($result, 3);
        $this->assertEquals(0, $meta->zerofill);
    }

    public function testShouldProvideVariousFieldFlags()
    {
        $result = Proxy::query('SELECT * FROM metadata', self::$link);

        $flags = array(
            'not_null primary_key auto_increment',
            'blob binary',
            'not_null unique_key unsigned zerofill',
            'not_null multiple_key',
            'not_null enum',
            'not_null binary timestamp'
        );
        foreach ($flags as $i => $expected) {
            $this->assertEquals($expected, Proxy::field_flags($result, $i));
        }
    }

    public function testShouldProvideFieldLength()
    {
        $result  = Proxy::query('SELECT * FROM metadata_types', self::$link);
        $lengths = array(4, 6, 9, 11, 20, 11, 12, 22, 22, 1, 1, 20, 10, 19, 19, 10, 4, 5, 55, 255, 65535, 16777215, -1,
                         1, 50, 255, 16777215, 65535, -1, 3, 3, -1, -1, -1, -1, -1, -1, -1, -1);

        foreach ($lengths as $i => $expected) {
            if ($expected != Proxy::field_len($result, $i)) {
                var_dump(($i+1) . ': ' . $expected . ' != ' . Proxy::field_len($result, $i));
            }
            //$this->assertEquals($expected, Proxy::field_len($result, $i));
        }
        $this->assertFalse(true);
    }

    public function testShouldProvideFieldName()
    {
        $result  = Proxy::query('SELECT * FROM metadata', self::$link);
        $lengths = array('id', 'blob', 'number', 'text', 'enum', 'timestamp');

        foreach ($lengths as $i => $expected) {
            $this->assertEquals($expected, Proxy::field_name($result, $i));
        }
    }

    public function testShouldProvideAnAliasAsFieldName()
    {
        $result  = Proxy::query('SELECT `id` AS `alias` FROM metadata', self::$link);
        $this->assertEquals('alias', Proxy::field_name($result, 0));
    }

    public function testMysql()
    {
        @mysql_connect('localhost', 'root', '');
        mysql_select_db('mysqltest');
        $result = mysql_query('SELECT * FROM metadata_types');
        $i = 0;
        for ($i = 0; $i < 39; ++$i) {
            var_dump(($i + 1) . ': ' . mysql_field_len($result, $i));
        }
    }

    public function assertCurrentDatabase($databaseName)
    {
        $result   = Proxy::query('SELECT DATABASE()', self::$link);
        $row      = Proxy::fetch_row($result);
        $this->assertSame(
            strtolower($databaseName),
            strtolower($row[0]),
            'Current database is ' . $row[0] . 'but should be ' . $databaseName
        );
    }

    public function assertDatabaseExists($databaseName)
    {
        $statement = self::$pdo->query("SHOW DATABASES LIKE '$databaseName'");
        $statement->execute();
        $this->assertEquals(1, $statement->rowCount(), 'Database ' . $databaseName . ' does not exist');
    }

    public function assertDatabaseNotExists($databaseName)
    {
        $statement = self::$pdo->query("SHOW DATABASES LIKE '$databaseName'");
        $statement->execute();
        $this->assertEquals(0, $statement->rowCount(), 'Database ' . $databaseName . ' does exist');
    }

    public function createDb($databaseName)
    {
        self::$pdo->query('CREATE DATABASE IF NOT EXISTS `' . $databaseName . '`');
    }

    public function dropDb($databaseName)
    {
        self::$pdo->query('DROP DATABASE `' . $databaseName . '`');
    }

    public function connectProxyWithGlobalData()
    {
        return Proxy::connect($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
    }
}
