<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 07/01/2019
 * Time: 10:46 AM
 */

namespace Service;


class MetaStorage
{

    public static function fetch($metaName)
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

    public static function save($metaName, $data)
    {
        $metaName = base64_encode($metaName);
        $metaName = str_replace(".", "", $metaName);
        $metaName = str_replace("/", "", $metaName);
        $metaName = str_replace("\\", "", $metaName);
        return file_put_contents(__DIR__ . "/../Var/" . $metaName, serialize($data));
    }
}