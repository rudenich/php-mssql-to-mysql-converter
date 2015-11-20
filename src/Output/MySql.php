<?php
namespace DatabaseConverter\Output;

class MySql{
    private $_descriptor;

    /** @var  \PDO */
    private $_pdo;
    public function __construct(\PDO $pdo){
        $this->_pdo = $pdo;
        $t = [
            'dsn' => 'mysql:host=localhost;dbname=mssql',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ];
    }
    public function process($data){
        $this->_pdo->query($data);

    }
}