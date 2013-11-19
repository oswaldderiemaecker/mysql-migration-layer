<?php
namespace MySql;


class MySqli extends Adapter
{
    /** @var \mysqli */
    protected $lastLinkIdentifier = null;

    /**
     * @param \mysqli $link
     * @return \mysqli
     * @throws \BadMethodCallException
     */
    protected function getLink(\mysqli $link = null)
    {
        if ($link instanceof \mysqli) {
            return $link;
        }

        if ($this->lastLinkIdentifier instanceof \mysqli) {
            return $this->lastLinkIdentifier;
        }

        $this->connect();

        if ($this->lastLinkIdentifier instanceof \mysqli) {
            return $this->lastLinkIdentifier;
        }

        throw new \BadMethodCallException('There has been no connection established.');
    }

    protected function ensureMySqliResult($result)
    {
        if (!$result instanceof \mysqli_result) {
            throw new \InvalidArgumentException('$result should be an instance of \\mysqli_result.');
        }
    }

    public function affected_rows($link_identifier = null)
    {
        return $this->getLink($link_identifier)->affected_rows;
    }

    public function client_encoding($link_identifier = null)
    {
        return $this->getLink($link_identifier)->character_set_name();
    }

    public function close($link_identifier = null)
    {
        return self::getLink($link_identifier)->close();
    }

    public function connect($server = null, $username = null, $password = null, $new_link = null)
    {
        $this->lastLinkIdentifier = new \mysqli($server, $username, $password);
        return $this->lastLinkIdentifier;
    }

    // @todo test
    public function create_db($database_name, $link_identifier = null)
    {
        return (bool) $this->getLink($link_identifier)->query(
            'CREATE DATABASE `' . $database_name . '`'
        );
    }

    public function data_seek($result, $row_number)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        return $result->data_seek($row_number);
    }

    // @todo test
    public function db_name($result, $row, $field = null)
    {
        if ($field === null) {
            $field = 0;
        }
        return $this->result($result, $row, $field);
    }

    // @todo test
    public function db_query($database, $query, $link_identifier = null)
    {
        if (!$this->select_db($database, $link_identifier)) {
            return false;
        }
        return $this->query($query, $link_identifier);
    }

    // @todo test
    public function drop_db($database_name, $link_identifier)
    {
        return (bool) $this->getLink($link_identifier)->query(
            'DROP DATABASE `' . $database_name . '`'
        );
    }

    public function errno($link_identifier = null)
    {
        return $this->getLink($link_identifier)->errno;
    }

    public function error($link_identifier = null)
    {
        return $this->getLink($link_identifier)->error;
    }

    public function escape_string($unescaped_string)
    {
        return $this->getLink()->escape_string($unescaped_string);
    }

    public function fetch_array($result, $result_type = null)
    {
        switch ($result_type) {
            case 1:
                $result_type = MYSQLI_ASSOC;
                break;
            case 2:
                $result_type = MYSQLI_NUM;
                break;
            default:
                $result_type = MYSQLI_BOTH;
                break;
        }

        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        $result = $result->fetch_array($result_type);
        return $result === null
             ? false
             : $result;
    }

    public function fetch_assoc($result)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        $result = $result->fetch_assoc();
        return $result === null
            ? false
            : $result;
    }

    public function fetch_field($result, $field_offset = null)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */

        if ($field_offset !== null) {
            $this->field_seek($result, $field_offset);
        }

        $mysqliMetadata = $result->fetch_field();
        if (!$mysqliMetadata) {
            return false;
        }

        $typesMap = array(
            1 => 'int',
            2 => 'int',
            3 => 'int',
            4 => 'real',
            5 => 'real',
            7 => 'timestamp',
            8 => 'int',
            9 => 'int',
            10 => 'date',
            11 => 'time',
            12 => 'datetime',
            13 => 'year',
            16 => 'int',
            246 => 'real',
            252 => 'blob',
            253 => 'string',
            254 => 'string',
            255 => 'geometry',
        );

        $metadataResult = array(
            'name'         => $mysqliMetadata->orgname,
            'table'        => $mysqliMetadata->orgtable,
            'max_length'   => $mysqliMetadata->max_length,
            'not_null'     => intval($mysqliMetadata->flags & 1),
            'primary_key'  => (int) (bool) ($mysqliMetadata->flags & 2),
            'unique_key'   => (int) (bool) ($mysqliMetadata->flags & 4),
            'multiple_key' => (int) (bool) ($mysqliMetadata->flags & 8),
            'numeric'      => (int) (bool) ($mysqliMetadata->flags & 32768),
            'blob'         => (int) (bool) ($mysqliMetadata->flags & 16),
            'type'         => $typesMap[$mysqliMetadata->type],
            'unsigned'     => (int) (bool) ($mysqliMetadata->flags & 32),
            'zerofill'     => (int) (bool) ($mysqliMetadata->flags & 64),
        );

        return (object) $metadataResult;
    }

    public function fetch_lengths($result)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        return $result->lengths === null
             ? false
             : $result->lengths;
    }

    public function fetch_object($result, $class_name = 'stdClass', array $params = null)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        if (is_array($params)) {
            $result = $result->fetch_object($class_name, $params);
        } else {
            $result = $result->fetch_object($class_name);
        }
        return $result === null
             ? false
             : $result;
    }

    public function fetch_row($result)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        $result = $result->fetch_row();
        return $result === null
            ? false
            : $result;
    }

    public function field_flags($result, $field_offset)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        $data    = $result->fetch_field_direct($field_offset);
        $flags   = $data->flags;
        $result  = array();
        $flagMap = array(
            1 => 'not_null',
            2 => 'primary_key',
            4 => 'unique_key',
            8 => 'multiple_key',
            16 => 'blob',
            32 => 'unsigned',
            64 => 'zerofill',
            128 => 'binary',
            256 => 'enum',
            512 => 'auto_increment',
            1024 => 'timestamp'
        );

        foreach ($flagMap as $flagBit => $flag) {
            if ($flags & $flagBit) {
                $result[] = $flag;
            }
        }

        return implode(' ', $result);
    }

    public function field_len($result, $field_offset)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        $data = $result->fetch_field_direct($field_offset);
        if ($data->charsetnr != 33) {
            return $data->length;
        }
        var_dump(($field_offset + 1) . ' ' .$data->name . ': ' . $this->fetch_field($result, $field_offset)->type);
        if ($this->fetch_field($result, $field_offset)->type != 'string') {
            return $data->length;
        }
        return $data->length / 3;
    }

    public function field_name($result, $field_offset)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        $data = $result->fetch_field_direct($field_offset);
        return $data->name;
    }

    public function field_seek($result, $field_offset)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        return $result->field_seek($field_offset);
    }

    public function field_table($result, $field_offset)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        $data = $result->fetch_field_direct($field_offset);
        return isset($data['table']) ? $data['table'] : $data['orgtable'];
    }

    public function field_type($result, $field_offset)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        $data = $result->fetch_field_direct($field_offset);
        return $data['type'];
    }

    public function free_result($result)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        return $result->free();
    }

    public function get_client_info()
    {
        return mysqli_get_client_info();
    }

    public function get_host_info($link_identifier = null)
    {
        return $this->getLink($link_identifier)->client_info;
    }

    public function get_proto_info($link_identifier = null)
    {
        return $this->getLink($link_identifier)->protocol_version;
    }

    public function get_server_info($link_identifier = null)
    {
        return $this->getLink($link_identifier)->server_info;
    }

    public function info($link_identifier = null)
    {
        return $this->getLink($link_identifier)->info;
    }

    public function insert_id($link_identifier = null)
    {
        return $this->getLink($link_identifier)->insert_id;
    }

    // @todo test
    public function list_dbs($link_identifier = null)
    {
        return $this->query('SHOW DATABASES', $link_identifier);
    }

    // @todo test
    public function list_fields($database_name, $table_name, $link_identifier = null)
    {
        return $this->query('SHOW COLUMNS FROM `' . $database_name . '`.`' . $table_name . '`', $link_identifier);
    }

    // @todo überarbeiten!!!
    public function list_processes($link_identifier)
    {
        return $this->getLink($link_identifier)->thread_id;
    }

    public function list_tables($database, $link_identifier = null)
    {
        return $this->query('SHOW TABLES FROM `' . $database . '`', $link_identifier);
    }

    public function num_fields($result)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        return $result->field_count;
    }

    public function num_rows($result)
    {
        $this->ensureMySqliResult($result);
        /* @var $result \mysqli_result */
        return $result->num_rows;
    }

    // @todo handle flags
    public function pconnect($server = null, $username = null, $password = null, $client_flags = 0)
    {
        if ($server !== null) {
            $server = 'p:' . $server;
        }
        return $this->connect($server, $username, $password);
    }

    public function ping($link_identifier = null)
    {
        return $this->getLink($link_identifier)->ping();
    }

    public function query($query, $link_identifier = null)
    {
        return $this->getLink($link_identifier)->query($query);
    }

    public function real_escape_string($unescaped_string, $link_identifier = null)
    {
        return $this->getLink($link_identifier)->real_escape_string($unescaped_string);
    }

    // @todo see http://de2.php.net/manual/en/function.mysql-result.php field parameter
    public function result($result, $row, $field = 0)
    {
        $this->data_seek($result, $row);
        $result = $this->fetch_array($result);
        return $result[$field];
    }

    public function select_db($database_name, $link_identifier = null)
    {
        return $this->getLink($link_identifier)->select_db($database_name);
    }

    public function set_charset($charset, $link_identifier = null)
    {
        return $this->getLink($link_identifier)->set_charset($charset);
    }

    public function stat($link_identifier = null)
    {
        return $this->getLink($link_identifier)->stat();
    }

    public function tablename($result, $i)
    {
        return $this->result($result, $i, 0);
    }

    public function thread_id($link_identifier = null)
    {
        return $this->getLink($link_identifier)->thread_id;
    }

    public function unbuffered_query($query, $link_identifier = null)
    {
        return $this->getLink($link_identifier)->query($link_identifier, MYSQLI_USE_RESULT);
    }
}
