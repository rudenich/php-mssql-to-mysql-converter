<?php
namespace DatabaseConverter\Adapter;
class MSSqlToMYSql{
    private  $_map = array(
        'Родитель1'=>'parent1',
        'Родитель2'=>'parent2',
        'Родитель3'=>'parent3',
        'Родитель4'=>'parent4',
        'Родитель5'=>'parent5'
    );
    public function convert($column){
        $type = $column['DATA_TYPE'];
        $name = $this->convertName($column['COLUMN_NAME']);
        $isNull = $column['IS_NULLABLE']=='YES'?1:0;
        $default = $column['COLUMN_DEFAULT']=='NULL'?'':$column['COLUMN_DEFAULT'];
        $methodName = "convert".ucfirst($type);
        $res =  $this->$methodName($name,$column);
        if(!$isNull){
            $res.=" NOT NULL ";
        }
        if($default!=''){
            $res.="DEFAULT {$default}";
        }
        return $res;
    }
    public function convertName($name){
        $name = mb_convert_encoding($name,'utf-8','cp1251');
        if(isset($this->_map[$name])){
            $name = $this->_map[$name];
        }
        return $name;
    }

    private function convertMoney($name,$data){
        $precision = $data['NUMERIC_PRECISION'];
        $scale = $data['NUMERIC_SCALE'];
        return "{$name} NUMERIC({$precision},{$scale}) ";
    }



    private function convertDatetime($name,$data){
        return "{$name} DATETIME ";
    }

    private function convertFloat($name,$data){
        $precision = $data['NUMERIC_PRECISION'];
        $scale = $data['NUMERIC_SCALE'];
        return "{$name} FLOAT ({$precision},1) ";
    }

    private function convertInt($name,$data){
        $precision = $data['NUMERIC_PRECISION'];
        return "{$name} INT({$precision}) ";
    }

    private function convertSmallint($name,$data){
        $precision = $data['NUMERIC_PRECISION'];
        return "{$name} SMALLINT({$precision}) ";
    }

    private function convertBit($name){
        return "{$name} TINYINT(1) ";
    }

    private function convertNchar($name,$data){
        $length = $data['CHARACTER_MAXIMUM_LENGTH'];
        return "{$name} VARCHAR ($length) ";
    }

    private function convertChar($name,$data){
        $length = $data['CHARACTER_MAXIMUM_LENGTH'];
        return "{$name} VARCHAR ($length) ";
    }

    private function convertVarchar($name,$data){
        $length = $data['CHARACTER_MAXIMUM_LENGTH'];
        return "{$name} VARCHAR ($length) ";
    }

    private function convertNvarchar($name,$data){
        $length = $data['CHARACTER_MAXIMUM_LENGTH'];
        return "{$name} VARCHAR ($length) ";
    }

    public function convertData($value,$type){
        $method = 'convertData'.ucfirst($type);
        if(method_exists($this,$method)){
            $result = $this->$method($value);
        }else{
            $result = $value;
        }

        return $result;
    }


    private function convertDataDatetime($value){
        $dateTime = new \DateTime($value);
        return $dateTime->format("Y-m-d H:i:s");
    }
}