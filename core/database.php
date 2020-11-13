<?php

namespace core;

use core\parameters;

class database {
    /**
     *
     * @var type 
     */
    public $db;
    
    /**
     *
     * @var type 
     */
    public $host;
    
    /**
     *
     * @var type 
     */
    public $user;
    
    /**
     *
     * @var type 
     */
    public $password;
    
    /**
     *
     * @var type 
     */
    public $database;
    
    public function __construct(parameters $appParams) {
        $this->host = $appParams->get("db")['host'];
        $this->user = $appParams->get("db")['user'];
        $this->password = $appParams->get("db")['password'];
        $this->database = $appParams->get("db")['database'];
        
        if(!$GLOBALS['app']->database || (!$GLOBALS['app']->database->db && !$this->db)) {
            $this->connect();
        }
    }
    /*
     * Подчключение к БД
     */
    public function connect()
    {
        $this->db = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        if(!$this->db) {
            throw new \Exception("Ошибка подключения к базе данных. Проверьте настрйоки подключения в parameters.json");
        }
        
    }
    /*
     * Выполним SQL
     */
    public function execute($query)
    {
        
       if(!is_null($GLOBALS['app']->database->db)){
           $database = $GLOBALS['app']->database->db;
       } 
       else {
           $database = $this->db;
       }
       
       
       return mysqli_query ($database, $query);
    }
    
    /*
     * Получим данные первой строки
     */
    public function fetchOne($query)
    {
        $result = $this->execute($query);
        if(!$result) {
            throw new \Exception(mysqli_error($GLOBALS['app']->database->db));
        }
        else {
            
            return mysqli_fetch_assoc($result);
        }
        
    }
    
       
    /*
     * Получим данные всех строк
     */
    public function fetchAll($query)
    {
        $return = [];
        $result = $this->execute($query);
        if(!$result) {
            throw new \Exception(mysqli_error($GLOBALS['app']->database->db));
        }
        else {
            while($row = mysqli_fetch_assoc($result)){
                $return[]=$row;
            }
            return $return;
        }
    }
    /*
     * Ид последней вставки
     */
    public function getInsertId()
    {
       if(!is_null($GLOBALS['app']->database->db)){
           $database = $GLOBALS['app']->database->db;
       } 
       else {
           $database = $this->db;
       }
       
       return mysqli_insert_id($database);
    }
}