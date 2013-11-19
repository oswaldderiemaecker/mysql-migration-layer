<?php
namespace MySQLTest\TestAsset;


class FetchObjectClass
{
    public $constructorVar;
    public $id;

    public function __construct($constructorVar)
    {
        $this->constructorVar = $constructorVar;
    }
} 