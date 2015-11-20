<?php
namespace DatabaseConverter;

class SqlIterator implements \Iterator
{
    public $table;
    public $field;
    public $pageSize;
    private $_driver;

    private $currentPage = 1;
    private $totalPages;

    public $where='';

    public function setDriver($value)
    {
        $this->_driver = $value;
    }

    public function getDriver()
    {
        return $this->_driver;
    }


    public function current()
    {
        return $this->_driver->getItems($this->table,$this->where,$this->field,$this->currentPage,$this->pageSize);
    }

    public function next()
    {
        $this->currentPage++;
    }

    public function key()
    {
        return $this->currentPage;
    }

    public function valid()
    {
        return $this->currentPage<=$this->totalPages;
    }

    public function rewind()
    {
        $this->currentPage = 1;
        $count = $this->_driver->totalCount($this->table);
        $this->totalPages =  ceil($count/$this->pageSize);
    }
}