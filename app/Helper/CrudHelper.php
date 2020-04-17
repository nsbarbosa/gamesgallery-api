<?php

namespace App\Helper;

use Carbon\Carbon;

/**
 * Class CrudHelper
 * @package Modules\Platform\Core\Helper
 */
class CrudHelper
{
    const BELONGS_TO_MANY = 'Illuminate\Database\Eloquent\Relations\BelongsToMany';

    const BELONGS_TO = 'Illuminate\Database\Eloquent\Relations\BelongsTo';

    /**
     * Check if string start with
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Check if string end with
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }

    /**
     * Learn method type
     * @param $classname
     * @param $method
     * @return string
     */
    public static function learnMethodType($classname, $method)
    {
        $obj = new $classname;
        $type = get_class($obj->{$method}());

        return $type;
    }

    /**
     * Set created at & updated at to now in array
     * @param $array
     * @return mixed
     */
    public static function setDatesInArray($array)
    {
        $result = [];

        foreach ($array as $arr) {
            $arr['updated_at'] = Carbon::now();
            $arr['created_at'] = Carbon::now();

            $result[] = $arr;
        }
        return $result;
    }
}