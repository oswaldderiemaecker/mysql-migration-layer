<?php
namespace MySQL;

abstract class Adapter
{
    abstract public function affected_rows($link_identifier = null);
    abstract public function client_encoding($link_identifier = null);
    abstract public function close($link_identifier = null);
    abstract public function connect($server = null, $username = null, $password = null, $new_link = null);
    abstract public function create_db($database_name, $link_identifier = null);
    abstract public function data_seek($result, $row_number);
    abstract public function db_name($result, $row, $field = null);
    abstract public function db_query($database, $query, $link_identifier = null);
    abstract public function drop_db($database_name, $link_identifier);
    abstract public function errno($link_identifier = null);
    abstract public function error($link_identifier = null);
    abstract public function escape_string($unescaped_string);
    abstract public function fetch_array($result, $result_type = null);
    abstract public function fetch_assoc($result);
    abstract public function fetch_field($result, $field_offset = null);
    abstract public function fetch_lengths($result);
    abstract public function fetch_object($result, $class_name = 'stdClass', array $params = null);
    abstract public function fetch_row($result);
    abstract public function field_flags($result, $field_offset);
    abstract public function field_len($result, $field_offset);
    abstract public function field_name($result, $field_offset);
    abstract public function field_seek($result, $field_offset);
    abstract public function field_table($result, $field_offset);
    abstract public function field_type($result, $field_offset);
    abstract public function free_result($result);
    abstract public function get_client_info();
    abstract public function get_host_info($link_identifier = null);
    abstract public function get_proto_info($link_identifier = null);
    abstract public function get_server_info($link_identifier = null);
    abstract public function info($link_identifier = null);
    abstract public function insert_id($link_identifier = null);
    abstract public function list_dbs($link_identifier = null);
    abstract public function list_fields($database_name, $table_name, $link_identifier = null);
    abstract public function list_processes($link_identifier);
    abstract public function list_tables($database, $link_identifier = null);
    abstract public function num_fields($result);
    abstract public function num_rows($result);
    abstract public function pconnect($server = null, $username = null, $password = null, $client_flags = 0);
    abstract public function ping($link_identifier = null);
    abstract public function query($query, $link_identifier = null);
    abstract public function real_escape_string($unescaped_string, $link_identifier = null);
    abstract public function result($result, $row, $field = 0);
    abstract public function select_db($database_name, $link_identifier = null);
    abstract public function set_charset($charset, $link_identifier = null);
    abstract public function stat($link_identifier = null);
    abstract public function tablename($result, $i);
    abstract public function thread_id($link_identifier = null);
    abstract public function unbuffered_query($query, $link_identifier = null);
}
