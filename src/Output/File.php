<?php
namespace DatabaseConverter\Output;

class File{
    private $_descriptor;

    public function __construct(){
        $this->_descriptor = fopen(getcwd().'/dump.sql','w+');
    }
    public function process($data){
        $data = $data."\n\n\n";
        fputs($this->_descriptor, $data);
        //fwrite($this->_descriptor,$data,mb_strlen($data,'utf8'));

    }
    public function __destruct(){
        fclose($this->_descriptor);
    }
}