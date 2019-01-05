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

    /**
     * @param $tableId
     * @param $nickname
     * @param array $cellPosition
     */
    public function updateStatus($tableId, $nickname, $cellPosition)
    {
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
    }

    /**
     * @param $tableId
     * @return array
     */
    public function getTableUserStatus($tableId)
    {
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

        return $tablesUserStatus;
    }

}