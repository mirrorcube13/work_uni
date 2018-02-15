<?php

namespace PTS\Models\Prototype;

use PTS\Core\DB;

trait Model
{
    protected static $class;

    protected static $attribute_names;

    protected static $db;

    protected static $pluckField;

    protected function __construct(){}

    public static function create(array $attributes = [])
    {
        $model = new self();
        $model->initAttributes();
        if (!empty($attributes)){
            foreach ($attributes as $key => $value){
                $model->attributes[$key] = $value;
            }
        }

        return $model;
    }

    /**
     * @param null $field | array [field_name => value]
     * @param null $value
     * @return Model
     */
    public static function load($field = null, $value = null) : self
    {
        $empty = new self();
        self::$db = DB::getObject();

        self::$db->select($empty->table);
        if (!is_null($field) && !is_null($value)) self::$db->where($field, $value);
        elseif (is_array($field) && is_null($value)){

            foreach ($field as $field_name => $val){
                self::$db->where($field_name, $val);
            }
        }

        return $empty;
    }

    public function orderBy(string $field, string $order = 'ASC'){
        self::$db->orderBy($field, $order);
        return $this;
    }

    public function get() : array {
        $records = self::$db->get();

        if (!empty($records)){
            $models = [];
            foreach ($records as $record){
                $models []= self::create($record);
            }
            return $models;
        }
        return [];
    }

    public function paginate(int $limit = 15, int $page = 0) {
        if ($page > 0) {
            self::$db->limit(($limit * $page), $limit);
        } else {
            self::$db->limit($limit);
        }

        return $this->get();
    }

    /**
     * Gets attributes of model which are field names in database table
     */
    protected function initAttributes()
    {
        if ( !(self::$class === get_class($this)) || !(isset(self::$attribute_names)) ) {
            self::$attribute_names = DB::getObject()->getColumns($this->table);
            self::$class = get_class($this);
        }

        foreach (self::$attribute_names as $col_name){
            $this->attributes[$col_name] = null;
        }
    }

    /**
     * Finds model by primary key
     * @param $value
     * @return Model
     * @throws \Exception
     */
    public static function find($value) : self
    {
        $tmpModel = new self();
        $model_attr = DB::getObject()->select($tmpModel->table)->where($tmpModel->primary_key, $value)->first();
        $model = self::create($model_attr);
        return $model;
    }

    public function saveToDb()
    {
        $attributes = [];
        if (!empty($this->hasDefault)){
            foreach ($this->attributes as $key => $val){
                if ( (!in_array($key, $this->hasDefault)) || !(is_null($this->attributes[$key])) ) {
                    $attributes[$key] = $val;
                }
            }
        }else $attributes = $this->attributes;

        if (is_array($this->primary_key)){
            $primaryFields = [];

            if (empty($this->primary_key)) throw new \Exception(str_replace('{class}', get_class($this), self::PRIMARY_KEY_NOT_SET));
            foreach ($this->primary_key as $field){
                if (array_key_exists($field, $this->attributes)){
                    $primaryFields [$field] = $this->attributes[$field];
                }
            }

            if (empty($primaryFields)) throw new \Exception(str_replace('{class}', get_class($this), self::PRIMARY_KEY_NOT_SET));

            $models = self::load($primaryFields)->get();
            if (empty($models)){
                DB::getObject()->insert($this->table, $attributes);

            }elseif (count($models) == 1){

                DB::getObject()->update($this->table, $attributes);
                foreach ($primaryFields as $field => $val){
                    DB::getObject()->where($field, $val);
                }
                DB::getObject()->execute();

            }else {
                $text = self::PRIMARY_KEY_NOT_UNQ;

                if (isset($this->table)) {
                    $text = str_replace('{table}', $this->table, $text);
                }else{
                    $text = str_replace('{table}', get_class($this), $text);
                }
                $text = str_replace('{key}', implode(', ', $this->primary_key), $text);

                throw new \Exception($text);
            }

        }elseif (is_string($this->primary_key)){
            try{
                self::find($this->attributes[$this->primary_key]);
            }catch (\Exception $e){
                DB::getObject()->insert($this->table, $attributes);
                $this->attributes[$this->primary_key] = DB::getObject()->getLastId();
                return $this;
            }

            DB::getObject()
                ->update($this->table, $attributes)
                ->where($this->primary_key, $this->attributes[$this->primary_key])->execute();
        }

        return $this;

    }

    public function delete()
    {
        if (empty($this->primary_key)) throw new \Exception(str_replace('{class}', get_class($this), self::PRIMARY_KEY_NOT_SET));

        $db = DB::getObject();
        if (is_string($this->primary_key)){
            $db->delete($this->table)->where($this->primary_key, $this->attributes[$this->primary_key])->execute();

        }elseif (is_array($this->primary_key)){
            $db->delete($this->table);
            foreach ($this->primary_key as $keyField){
                $db->where($keyField, $this->attributes[$keyField]);
            }
            $db->execute();
        }
    }

    public static function pluck(string $field)
    {
        self::$pluckField = $field;
        $tmp = new self();
        self::$db = DB::getObject();
        self::$db->select($tmp->table, [$field]);

        return $tmp;
    }

    public function where(string $field, $value, string $operator = '=', string $condition = 'AND'){
        self::$db->where($field, $value, $operator, $condition);

        return $this;
    }

    public function getConnection()
    {
        return self::$db;
    }

    public function getAsArray() : array {
        $array = self::$db->get();
        if (isset(self::$pluckField)){
            $values = [];
            foreach ($array as $record){
                $values []= $record[self::$pluckField];
            }
            self::$pluckField = null;
            return $values;
        }
        return $array;
    }
}