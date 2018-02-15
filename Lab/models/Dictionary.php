<?php

namespace PTS\Models;

class Dictionary extends Model
{
    use Prototype\Model;

    protected $table = 'dictionary';

    protected $primary_key = 'd_id';

    public static function getDictionaryValues(array $words)
    {
        $loader = self::load();
        $loader->getConnection()->whereIn('d_name', $words);
        $cvValues = $loader->get();

        foreach ($cvValues as $key => $record){
            $cvValues [$record['d_name']] = $record['d_value'];
            unset($cvValues[$key]);
        }

        return $cvValues;
    }
}
