<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 07/01/2019
 * Time: 10:46 AM
 */

namespace Service;


use Swoole\Table as SwooleTable;

class MetaStorage
{

    /** @var SwooleTable */
    private $ramStorage;
    /** @var bool */
    private $useRamSotrage;

    /**
     * MetaStorage constructor.
     */
    public function __construct($useRamSotrage = true)
    {
        $this->useRamSotrage = $useRamSotrage;
        if ($this->useRamSotrage) {
            $this->ramStorage = new SwooleTable(2);
            $this->ramStorage->column('data', SwooleTable::TYPE_STRING, 1024 * 1024 * 10);
            $this->ramStorage->create();
        }

    }

    public function fetch($metaName)
    {
        if ($this->useRamSotrage) {
            return $this->fetchInRam($metaName);
        }
        return $this->fetchInFile($metaName);
    }

    public function fetchInRam($metaName)
    {
        $metaName = base64_encode($metaName);
        $metaName = str_replace(".", "", $metaName);
        $metaName = str_replace("/", "", $metaName);
        $metaName = str_replace("\\", "", $metaName);
        $row = $this->ramStorage->get($metaName);
        if (!$row) {
            return null;
        }
        return unserialize(trim($row["data"]));
    }

    public function save($metaName, $data)
    {
        if ($this->useRamSotrage) {
            return $this->saveInRam($metaName, $data);
        }
        return $this->saveInFile($metaName, $data);
    }

    public function saveInRam($metaName, $data)
    {
        $metaName = base64_encode($metaName);
        $metaName = str_replace(".", "", $metaName);
        $metaName = str_replace("/", "", $metaName);
        $metaName = str_replace("\\", "", $metaName);
        return $this->ramStorage->set($metaName, ["data" => serialize($data)]);
    }


    public function fetchInFile($metaName)
    {
        $metaName = base64_encode($metaName);
        $metaName = str_replace(".", "", $metaName);
        $metaName = str_replace("/", "", $metaName);
        $metaName = str_replace("\\", "", $metaName);
        $str = file_get_contents(__DIR__ . "/../Var/" . $metaName);
        if ($str === false) {
            return null;
        }
        return unserialize($str);
    }

    public function saveInFile($metaName, $data)
    {
        $metaName = base64_encode($metaName);
        $metaName = str_replace(".", "", $metaName);
        $metaName = str_replace("/", "", $metaName);
        $metaName = str_replace("\\", "", $metaName);
        return file_put_contents(__DIR__ . "/../Var/" . $metaName, serialize($data));
    }
}