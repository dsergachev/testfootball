<?php

namespace core;

use core\app;
use core\database;

abstract class model extends database
{
    public $table;
    private $attributes;
    
    /**
     * Найдем одну запись
     * 
     * $params - массив вида
     * 
     * [['=','id','1'],['like','name','вася']]
     * 
     * Будет преобразовано в where id=1 and name like '%вася%'
     * 
     * @param array $params 
     */
    public function findOne(array $params)
    {
        $paramsConverted = $this->paramsToWhere($params);
        $sql = "select * from ".$this->table." where ".$paramsConverted." limit 1;";
        return $this->getSingleObject($this->fetchOne($sql));
        
    }
    
    public function findAll(array $params=[],$fields='*')
    {
        if(count($params)>0) {
            $paramsConverted = $this->paramsToWhere($params);
        } else {
            $paramsConverted = 'id>0';
        }
        $sql = "select $fields from ".$this->table." where ".$paramsConverted.";";
        if($fields=='*') {
            return $this->getAllObjects($this->fetchAll($sql));
        } else {
            if(count(explode(',', $fields))>1)
            {
                return $this->fetchAll($sql);
            } else {
                return $this->toPlainArray($fields, $this->fetchAll($sql));
            }
        }
    }
    
    public function toPlainArray($field, $fetches) 
    {
        $return = [];
        foreach ($fetches as $fetch) {
            $return[] = $fetch[$field];
        }
        return $return;
    }
    
    public function toAggregatedValue($fetches)
    {
        return $fetches['aggregate'];
    }
    
    public function save($update=false)
    {
    if(!$update) {
        $this->prepareForSave();
    }
                
        $sql = 'insert into '.$this->table." (".implode(',', $this->attributes).") values (".$this->attributesToValue().") on duplicate key update ".$this->attributesToSet();
        
        if($this->execute($sql))
        {
            $this->id=$this->getInsertId();
        }
        else {
            throw new \Exception(mysqli_error($GLOBALS['app']->database->db));
        }
    }
    
    private function attributesToValue()
    {
        $valuesArray = [];
        foreach($this->attributes as $attributeName){
            $valuesArray[]=  (is_integer($this->$attributeName) || is_float($this->$attributeName))?$this->$attributeName:"'".$this->$attributeName."'";
        }
        return implode(', ', $valuesArray);
    }
    
     private function attributesToSet()
    {
        $valuesArray = [];
        foreach($this->attributes as $attributeName){
            $valuesArray[]=  $attributeName."=".((is_integer($this->$attributeName) || is_float($this->$attributeName))?$this->$attributeName:"'".$this->$attributeName."'");
        }
        return implode(', ', $valuesArray);
    }
    
    private function prepareForSave()
    {
        if(array_search('id',$this->attributes)>0) {
             unset($this->attributes[array_search('id',$this->attributes)]);
         }
    }
    
    private function prepareForSelect()
    {
        $this->attributes[]='id';
    }
    
    private function convertFetchToAttributes($fetchArray)
    {
        foreach ($fetchArray as $fetchElementKey=>$fetchElementValue){
            $this->$fetchElementKey=$fetchElementValue;
        }
    }
    
    private function getSingleObject($fetchArray)
    {
        if(is_null($fetchArray)) return null;
        
        $this->convertFetchToAttributes($fetchArray);
        return $this;
    }
    
    private function getAllObjects($fetchArray)
    {
        if(is_null($fetchArray)) return null;
        
        $classname = get_class($this);
        
        $return = [];
         foreach ($fetchArray as $fetchLine) {
            $newObject = new $classname;
            $this->convertFetchToAttributes($fetchLine);
            foreach ($this->attributes as $attributeKey) {
                $newObject->$attributeKey = $this->$attributeKey;
            }
            $return[] = $newObject;
        }
    
        return $return;
        
    }
    
    public function ref($modelName, $property, $reference)
    {
        $model = $GLOBALS['app']->getModel($modelName,[['=',$reference,$this->$property]]);
        return $model;
    }

    public function aggregate($aggregateFunction, $aggreagateProperty)
    {
        $sql = "select $aggregateFunction($aggreagateProperty) as aggregate from ".$this->table;
        return $this->toAggregatedValue($this->fetchOne($sql));
    }



    public function __call($name, $arguments) {
        
        ;
    }
    
    private function paramsToWhere(array $params)
    {
        $where = [];
        foreach($params as $param){
            if(count($param)!=3) {
                throw new \Exception('param должен иметь ровно три элемента, условие, свойство и значение');
            }
            $where[] = $param[1]." ".$param[0]." ".($param[0]=='like'?"'%".$param[2]."%'":"'".$param[2]."'");
        }
        return implode(' AND ', $where);
    }

    abstract public function attributes();
    
    public function __construct(parameters $appParams=null, array $fetchParameters=[]) {
        
        if(is_null($appParams)) {
            $appParams=app::parameters();
        }
        
        $this->attributes = $this->attributes();
        $this->prepareForSelect();
        
        parent::__construct($appParams);
        
        if(!empty($fetchParameters))
        {
            $this->findOne($fetchParameters);
        }
        
    }
    
    
    
}

