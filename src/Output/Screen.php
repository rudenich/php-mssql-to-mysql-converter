<?php
namespace DatabaseConverter\Output;
class Screen{
    public function process($data){
        echo "<pre>".$data."</pre>";
    }
}