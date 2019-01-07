<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 05/01/2019
 * Time: 10:35 AM
 */
namespace Service;

class TablesManager
{

    /** @var TablesUserStatusManager */
    private $tablesUserStatusManager = null;

    /**
     * @var [][]array
     */
    private $tables = [];

    /**
     * TablesManager constructor.
     * @param TablesUserStatusManager $tablesUserStatusManager
     */
    public function __construct($tablesUserStatusManager)
    {
        $this->tablesUserStatusManager = $tablesUserStatusManager;
    }

    private function createFixedArray($x, $y)
    {
        $array = [];
        for($i = 0; $i< $x; $i++) {
            $array[$i] = [];
            for($j = 0; $j< $y; $j++) {
                $array[$i][$j] = "&nbsp;&nbsp;";
            }
        }
        return $array;
    }


    public function restoreTables()
    {
        $this->tables = MetaStorage::fetch("tables");
        if (!$this->tables) {
            $this->tables = [];
        }
    }


    const X_MAX = 100;
    const Y_MAX = 100;

    public function createTable($tableId = 0)
    {
        $this->restoreTables();

        if (!$tableId) {
            $tableId = time() . rand(100000, 999999);
            $tableId = intval($tableId);
        }

        $this->tables[$tableId] = $this->createFixedArray(TablesManager::X_MAX, TablesManager::Y_MAX);

        MetaStorage::save("tables", $this->tables);
        return $tableId;
    }

    /**
     * @param string $tableId
     * @param array $cellPosition
     * @param string $value
     */
    public function updateTable($tableId, $cellPosition, $value)
    {
        $this->restoreTables();

        $x = intval($cellPosition[0]);
        if ($x < 0) {
            $x = 0;
        }
        if ($x > TablesManager::X_MAX) {
            $x = TablesManager::X_MAX;
        }
        $y = intval($cellPosition[1]);
        if ($y < 0) {
            $y = 0;
        }
        if ($y > TablesManager::Y_MAX) {
            $y = TablesManager::Y_MAX;
        }

        if (!isset($this->tables[$tableId][$x][$y])) {
            throw new \RuntimeException("cell not exist");
        }
        $this->tables[$tableId][$x][$y] = $value;

        MetaStorage::save("tables", $this->tables);
    }

    /**
     * @param $tableId
     * @return array[]
     */
    public function getTable($tableId)
    {
        $this->restoreTables();

        if (!isset($this->tables[$tableId])) {
            throw new \RuntimeException("table not exist");
        }

        $data = [
            "tableId" => $tableId,
            "table" => $this->tables[$tableId],
            "tableUserStatus" => $this->getTableUserStatus($tableId),
        ];
        return $data;
    }


    public function getTableIdList()
    {
        return array_keys($this->tables);
    }


    public function isTableExist($tableId)
    {
        return isset($this->tables[$tableId]);
    }

    /**
     * @return TablesUserStatusManager
     */
    public function getTablesUserStatusManager()
    {
        return $this->tablesUserStatusManager;
    }

    public function updateStatus($tableId, $nickname, $cellPosition)
    {
        $this->getTablesUserStatusManager()->updateStatus($tableId, $nickname, $cellPosition);
    }

    public function getTableUserStatus($tableId)
    {
        return $this->getTablesUserStatusManager()->getTableUserStatus($tableId);
    }
}