<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $guarded = [];

    // static function baseValidate(array $post, array $rule)
    // {
    //     /** @var $val \Illuminate\Contracts\Validation\Validator|\Illuminate\Contracts\Validation\Factory */
    //     $val = \Illuminate\Support\Facades\Validator::make($post, $rule);

    //     if ($val->fails())
    //     {
    //         return [SUCCESSFUL => false, MESSAGE => 'Validation failed: '.getFirstValue($val->errors()->toArray()), 'val' => $val,
    //             'error_msg' => json_encode($val->errors()->toArray()) ];
    //     }

    //     return ret(TRUE, '');
    // }

    // static function getCount($table, $queryArr = [])
    // {
    //     $sql = "SELECT COUNT(id) AS query_name FROM $table";

    //     if($queryArr) $sql .= static::buildSqlQuery($queryArr);

    //     $superArray = static::pdoQuery($sql, $queryArr);

    //     return Arr::get($superArray, 'query_name', 0);
    // }

    // static function getSum($columnName, $table, $queryArr = [])
    // {
    //     $sql = 'SELECT SUM('.$columnName.") AS query_name FROM $table";

    //     if($queryArr) $sql .= static::buildSqlQuery($queryArr);

    //     $superArray = static::pdoQuery($sql, $queryArr);

    //     return Arr::get($superArray, 'query_name', 0);
    // }

    // static function buildSqlQuery($arr)
    // {
    //     $i = 0;
    //     $sql = '';
    //     foreach ($arr as $columnName => $value)
    //     {
    //         if($i === 0) $sql .= " WHERE ";

    //         else $sql .= " AND ";

    //         $sql .= " $columnName = :$columnName";

    //         $i++;
    //     }
    //     return $sql;
    // }

    // static function pdoQuery($query, $binders)
    // {
    //     $superArray = null;

    //     $pdo = (new BaseModel())->getConnection()->getPdo();
    //     try
    //     {
    //         $stmt = $pdo->prepare($query);

    //         foreach ($binders as $key => $value)
    //         {
    //             //                echo "Key = $key, value = $value <br />";
    //             $stmt->bindValue($key, $value);
    //         }
    //         // 	       die($stmt->queryString);
    //         $stmt->execute();

    //         $superArray = $stmt->fetch(\PDO::FETCH_ASSOC);

    //         return $superArray;

    //     } catch (\Exception $e) { dDie($query . '*****'. $e->getMessage()); }

    //     return $superArray;
    // }
}
