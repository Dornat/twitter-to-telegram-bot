<?php

namespace App\Helpers;

use ArrayObject;
use BadMethodCallException;

/**
 * @method array_keys()
 */
class PipelinesMap extends ArrayObject
{
    public function __construct(array $array)
    {
        $result = [];

        foreach ($array as $item) {
            $result[$item[0]] = $item[1];
        }

        parent::__construct($result);
    }

    /**
     * Enable possibility to call any array_* function on an object of this class.
     */
    public function __call($func, $argv): mixed
    {
        if (!is_callable($func) || !str_starts_with($func, 'array_')) {
            throw new BadMethodCallException(__CLASS__ . '->' . $func);
        }

        return call_user_func_array($func, array_merge(array($this->getArrayCopy()), $argv));
    }
}
