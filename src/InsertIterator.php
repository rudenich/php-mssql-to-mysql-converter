<?php
namespace DatabaseConverter;

class InsertIterator implements \Iterator
{
    public $pageSize;
    public $data;
    private $_currentPage=1;
    private $_totalPages=0;



    public function current()
    {
        return array_slice($this->data,($this->_currentPage-1)*$this->pageSize,$this->pageSize);
    }

    public function next()
    {
        $this->_currentPage++;
    }

    public function key()
    {
        return $this->_currentPage;
    }

    public function valid()
    {
        return $this->_currentPage<=$this->_totalPages;
    }

    public function rewind()
    {
        $this->_currentPage = 1;
        $this->_totalPages =  ceil(count($this->data)/$this->pageSize);
    }
}