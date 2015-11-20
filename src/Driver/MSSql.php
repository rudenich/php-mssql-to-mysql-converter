<?php
namespace DatabaseConverter\Driver;
use \PDO ;
class MSSql
{
    private $_pdo;

    public function __construct($serverName, $db, $login, $pass)
    {
        try {
            $this->_pdo = new PDO ("dblib:host=$serverName:1433;dbname=$db", "$login", "$pass");
        } catch (PDOException $e) {
            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            exit;
        }
    }


    public function getTableList()
    {
        $sql = "SELECT * FROM information_schema.tables where TABLE_TYPE='BASE TABLE' ";
        return $this->_pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll();
    }


    public function getTableColumns($tableName)
    {
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS  WHERE  TABLE_NAME='{$tableName}';";
        $data =  $this->_pdo->query($sql, PDO::FETCH_ASSOC)->fetchAll();
        $result = array();
        foreach($data as $item){
            $result[$item['COLUMN_NAME']] = $item;
        }
        return $result;
    }

    public function getItems1($table, $orderField, $from = 0, $limit = 500)
    {
        $sql = <<<SSS
;
WITH MYDATA AS
(
    SELECT *,
    ROW_NUMBER() OVER (ORDER BY {$orderField}) AS 'RowNumber'
    FROM {$table}
)
SELECT *
FROM MYDATA
WHERE RowNumber BETWEEN {$from} AND {$limit};
SSS;

        $query = $this->_pdo->query($sql, PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }


    public function getItems($table,$where,$orderField, $page = 1, $pageSize = 1000)
    {
        if($where!=''){
            $where = "WHERE ".$where;
        }
        $sql = <<<SSS
DECLARE @RowsPerPage INT = {$pageSize}, @PageNumber INT = {$page}
SELECT *
FROM {$table}
{$where}
ORDER BY {$orderField}
OFFSET (@PageNumber-1)*@RowsPerPage ROWS
FETCH NEXT @RowsPerPage ROWS ONLY;
SSS;

        $query = $this->_pdo->prepare($sql);
        $res = $query->execute();
        $query->nextRowset();
        $data =  $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $data;
    }

    public function totalCount($table){
        $sql = "SELECT COUNT(*) FROM {$table}";
        $res = $this->_pdo->query($sql)->fetch(PDO::FETCH_NUM);
        return (int) array_shift($res);
    }
}