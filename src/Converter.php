<?php

namespace DatabaseConverter;

class Converter
{
    private $_mssqlDriver;
    private $_converter;
    private $_outpotProcessor;
    private $_sqlIterator;
    private $_arrayIterator;

    private $_tables=array();

    public $_where='';
    public function setMssqlDriver($value)
    {
        $this->_mssqlDriver = $value;
    }

    public function getMssqlDriver()
    {
        return $this->_mssqlDriver;
    }

    public function setConverter($value)
    {
        $this->_converter = $value;
    }

    public function getConverter()
    {
        return $this->_converter;
    }

    public function setOutputProcessor($value){
        $this->_outpotProcessor = $value;
    }
    public function getOutputProcessor(){
        return $this->_outpotProcessor;
    }

    public function setSqlIterator($value){
        $this->_sqlIterator = $value;
    }

    public function getSqlIterator(){
        return $this->_sqlIterator;
    }

    public function setArrayIterator($value){
        $this->_arrayIterator = $value;
    }

    public function getArrayIterator(){
        return $this->_arrayIterator;
    }




    public function loadSchema(){
        $tables = $this->_mssqlDriver->getTableList();
        foreach($tables as $item){
            $tableName = $item['TABLE_NAME'];
            $this->_tables[$tableName] = $this->_mssqlDriver->getTableColumns($tableName);
        }
    }


    public function processOutput($data){
        if($this->_outpotProcessor!=null){
            $this->_outpotProcessor->process($data);
        }
    }

    public function test()
    {
        $res = array();
        foreach ($this->_tables as $key=>$item) {
            $res[] = $this->generateCreate($key);
        }
        echo implode("\n", $res);
    }

    public function generateInsert($tableName)
    {
        $columns = $this->_tables[$tableName];
        $columnNames = array_column($columns, 'COLUMN_NAME');
        $column = array_shift($columnNames);
        $this->_sqlIterator->table = $tableName;
        $this->_sqlIterator->field = $column;
        foreach($this->_sqlIterator as $page=>$dataSet){
            echo "{$tableName} page {$page};\n";
            $this->_arrayIterator->data = $dataSet;
            foreach($this->_arrayIterator as $items){
                $this->generateInsertBlock($tableName,$items);
            }
        }



    }

    public function generateInsertBlock($table,$data){
        $columns = $this->_tables[$table];
        $columnNames = array_column($columns, 'COLUMN_NAME');
        $head = $this->generateInsertHead($table, $columnNames);

        $bodyItems = array();
        foreach ($data as $item) {
            $bodyItems[] = $this->generateInsertData($item,$table);
        }
        $body = implode(",\n", $bodyItems) . ";";
        $result = $head.$body;
        $this->processOutput($result);
    }

    public function generateInsertHead($tableName, $columns)
    {
        $columnsRes = array();
        foreach($columns as $col){
            $columnsRes[] = $this->_converter->convertName($col);
        }
        $columnsStr = implode(',', $columnsRes);
        $sql = "INSERT INTO `{$tableName}` ($columnsStr) VALUES \n";
        return $sql;
    }

    public function generateInsertData($data,$tableName)
    {
        //array_pop($data);

        $columns = $this->_tables[$tableName];

        $prepare = array();
        foreach ($data as $key=>$item) {
            $value = $this->_converter->convertData(trim(mb_convert_encoding($item, 'utf8', 'cp1251')),$columns[$key]['DATA_TYPE']);
            $value = addslashes($value);
            $prepare[] = "'" .$value. "'";
        }
        $dataStr = implode(",", $prepare);
        $sql = "({$dataStr})";
        return $sql;
    }

    public function generateCreate($tableName)
    {
        $columns = $this->_tables[$tableName];
        $columnsArray = $this->generateColumns($columns);
        $sql = $this->generateTableCreate($tableName, $columnsArray);
        $this->processOutput($sql);
    }

    public function generateTableCreate($name, $columns)
    {
        $columns[] = "PRIMARY KEY (id)";
        $columnsql = implode(",\n", $columns);
        $sql = <<<TTT
CREATE TABLE {$name}(
         id                 int(11) AUTO_INCREMENT NOT NULL,
         {$columnsql}
) ENGINE = InnoDB
  COLLATE utf8_unicode_ci;

TTT;
        return $sql;
    }

    public function generateColumns($columns)
    {
        $columnsSql = array();
        foreach ($columns as $item) {
            $columnsSql[] = $this->_converter->convert($item);
        }
        return $columnsSql;
    }

}