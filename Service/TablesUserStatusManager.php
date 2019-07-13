<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 05/01/2019
 * Time: 10:50 AM
 */

namespace Service;

class TablesUserStatusManager
{

    private $tablesUserStatus = [];
    /** @var MetaStorage */
    private $metaStorage = null;

    /**
     * TablesUserStatusManager constructor.
     * @param MetaStorage $metaStorage
     */
    public function __construct(MetaStorage $metaStorage)
    {
        $this->metaStorage = $metaStorage;
    }

    public function restoreTablesUserStatus()
    {
        $this->tablesUserStatus = $this->metaStorage->fetch("tablesUserStatus");
        if (!$this->tablesUserStatus) {
            $this->tablesUserStatus = [];
        }
    }

    /**
     * @param $tableId
     * @param $nickname
     * @param array $cellPosition
     */
    public function updateStatus($tableId, $nickname, $cellPosition)
    {
        $this->restoreTablesUserStatus();

        if (!isset($this->tablesUserStatus[$tableId])) {
            $this->tablesUserStatus[$tableId] = [];
        }
        if (!isset($this->tablesUserStatus[$tableId][$nickname])) {
            $this->tablesUserStatus[$tableId][$nickname] = [];
        }
        if (!isset($this->tablesUserStatus[$tableId][$nickname]["cellPosition"])) {
            $this->tablesUserStatus[$tableId][$nickname]["cellPosition"] = "";
        }

        $this->tablesUserStatus[$tableId][$nickname]["cellPosition"] = $cellPosition;

        $this->metaStorage->save("tablesUserStatus", $this->tablesUserStatus);
    }

    /**
     * @param $tableId
     * @return array
     */
    public function getTableUserStatus($tableId)
    {
        $this->restoreTablesUserStatus();

        if (!isset($this->tablesUserStatus[$tableId])) {
            $this->tablesUserStatus[$tableId] = [];
        }

        $tablesUserStatus = [];
        foreach ($this->tablesUserStatus[$tableId] as $nickname => $userStatus){
            $tablesUserStatus[] = [
                "nickname" => $nickname,
                "userStatus" => $userStatus
            ];
        }

        $this->metaStorage->save("tablesUserStatus", $this->tablesUserStatus);

        return $tablesUserStatus;
    }

}