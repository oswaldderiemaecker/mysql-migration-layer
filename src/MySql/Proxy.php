<?php
namespace MySQL;

class Proxy
{
    /** @var Adapter */
    protected static $adapter;

    public static function setAdapter(Adapter $layer)
    {
        self::$adapter = $layer;
    }

    /** @return Adapter */
    protected static function getAdapter()
    {
        if (! self::$adapter instanceof Adapter) {
            self::$adapter = new MySQLi();
        }
        return self::$adapter;
    }

    public static function affected_rows($link_identifier = null)
    {
        return self::getAdapter()->affected_rows($link_identifier);
    }

    public static function client_encoding($link_identifier = null)
    {
        return self::getAdapter()->client_encoding($link_identifier);
    }

    public static function close($link_identifier = null)
    {
        return self::getAdapter()->close($link_identifier);
    }

    public static function connect($server = null, $username = null, $password = null, $new_link = null)
    {
        return self::getAdapter()->connect($server, $username, $password, $new_link);
    }

    public static function create_db($database_name, $link_identifier = null)
    {
        return self::getAdapter()->create_db($database_name, $link_identifier);
    }

    public static function data_seek($result, $row_number)
    {
        return self::getAdapter()->data_seek($result, $row_number);
    }

    public static function db_name($result, $row, $field = null)
    {
        return self::getAdapter()->db_name($result, $row, $field);
    }

    public static function db_query($database, $query, $link_identifier = null)
    {
        return self::getAdapter()->db_query($database, $query, $link_identifier);
    }

    public static function drop_db($database_name, $link_identifier)
    {
        return self::getAdapter()->drop_db($database_name, $link_identifier);
    }

    public static function errno($link_identifier = null)
    {
        return self::getAdapter()->errno($link_identifier);
    }

    public static function error($link_identifier = null)
    {
        return self::getAdapter()->error($link_identifier);
    }

    public static function escape_string($unescaped_string)
    {
        return self::getAdapter()->escape_string($unescaped_string);
    }

    public static function fetch_array($result, $result_type = null)
    {
        return self::getAdapter()->fetch_array($result, $result_type);
    }

    public static function fetch_assoc($result)
    {
        return self::getAdapter()->fetch_assoc($result);
    }

    public static function fetch_field($result, $field_offset = null)
    {
        return self::getAdapter()->fetch_field($result, $field_offset);
    }

    public static function fetch_lengths($result)
    {
        return self::getAdapter()->fetch_lengths($result);
    }

    public static function fetch_object($result, $class_name = 'stdClass', array $params = null)
    {
        return self::getAdapter()->fetch_object($result, $class_name, $params);
    }

    public static function fetch_row($result)
    {
        return self::getAdapter()->fetch_row($result);
    }

    public static function field_flags($result, $field_offset)
    {
        return self::getAdapter()->field_flags($result, $field_offset);
    }

    public static function field_len($result, $field_offset)
    {
        return self::getAdapter()->field_len($result, $field_offset);
    }

    public static function field_name($result, $field_offset)
    {
        return self::getAdapter()->field_name($result, $field_offset);
    }

    public static function field_seek($result, $field_offset)
    {
        return self::getAdapter()->field_seek($result, $field_offset);
    }

    public static function field_table($result, $field_offset)
    {
        return self::getAdapter()->field_table($result, $field_offset);
    }

    public static function field_type($result, $field_offset)
    {
        return self::getAdapter()->field_type($result, $field_offset);
    }

    public static function free_result($result)
    {
        return self::getAdapter()->free_result($result);
    }

    public static function get_client_info()
    {
        return self::getAdapter()->get_client_info();
    }

    public static function get_host_info($link_identifier = null)
    {
        return self::getAdapter()->get_host_info($link_identifier);
    }

    public static function get_proto_info($link_identifier = null)
    {
        return self::getAdapter()->get_proto_info($link_identifier);
    }

    public static function get_server_info($link_identifier = null)
    {
        return self::getAdapter()->get_server_info($link_identifier);
    }

    public static function info($link_identifier = null)
    {
        return self::getAdapter()->info($link_identifier);
    }

    public static function insert_id($link_identifier = null)
    {
        return self::getAdapter()->insert_id($link_identifier);
    }

    public static function list_dbs($link_identifier = null)
    {
        return self::getAdapter()->list_dbs($link_identifier);
    }

    public static function list_fields($database_name, $table_name, $link_identifier = null)
    {
        return self::getAdapter()->list_fields($database_name, $table_name, $link_identifier);
    }

    public static function list_processes($link_identifier)
    {
        return self::getAdapter()->list_processes($link_identifier);
    }

    public static function list_tables($database, $link_identifier = null)
    {
        return self::getAdapter()->list_tables($database, $link_identifier);
    }

    public static function num_fields($result)
    {
        return self::getAdapter()->num_fields($result);
    }

    public static function num_rows($result)
    {
        return self::getAdapter()->num_rows($result);
    }

    public static function pconnect($server = null, $username = null, $password = null, $client_flags = 0)
    {
        return self::getAdapter()->pconnect($server, $username, $password, $client_flags);
    }

    public static function ping($link_identifier = null)
    {
        return self::getAdapter()->ping($link_identifier);
    }

    public static function query($query, $link_identifier = null)
    {
        return self::getAdapter()->query($query, $link_identifier);
    }

    public static function real_escape_string($unescaped_string, $link_identifier = null)
    {
        return self::getAdapter()->real_escape_string($unescaped_string, $link_identifier);
    }

    public static function result($result, $row, $field = 0)
    {
        return self::getAdapter()->result($result, $row, $field);
    }

    public static function select_db($database_name, $link_identifier = null)
    {
        return self::getAdapter()->select_db($database_name, $link_identifier);
    }

    public static function set_charset($charset, $link_identifier = null)
    {
        return self::getAdapter()->set_charset($charset, $link_identifier);
    }

    public static function stat($link_identifier = null)
    {
        return self::getAdapter()->stat($link_identifier);
    }

    public static function tablename($result, $i)
    {
        return self::getAdapter()->tablename($result, $i);
    }

    public static function thread_id($link_identifier = null)
    {
        return self::getAdapter()->thread_id($link_identifier);
    }

    public static function unbuffered_query($query, $link_identifier = null)
    {
        return self::getAdapter()->unbuffered_query($query, $link_identifier);
    }
}
