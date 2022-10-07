<?php
namespace ff\database;

use XiangYu2038\WithXy\collection;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use XiangYu2038\WithXy\WithXy;
class Model extends EloquentModel
{
    use WithXy;

    public static $fields = [];//这里一定要用static
    protected static $column = [];//这里一定要用static

    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    public static function fields(){
        $static = new static();
        if(!self::$fields[$static->table]){
            $fields = DB::select('select column_name,column_comment from information_schema.columns where table_name="pre_'.$static->table.'"');
            self::$fields[$static->table] = $fields;
        }

        return self::$fields[$static->table];
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
    public  function column()
    {

        if(!self::$column[$this->table]){

            $fields = array_flip(array_column(self::fields(),'column_name'));

            $myfunction = function () {
                return '';
            };


            self::$column[$this->table] = array_map($myfunction, $fields);
        }

        // dd(__LINE__);

        return self::$column[$this->table];

    }





}
