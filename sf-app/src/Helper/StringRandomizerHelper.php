<?php

namespace App\Helper;

/**
 * @author Écio Silva
 */
class StringRandomizerHelper
{

    /**
     * @link https://stackoverflow.com/a/31107425/2216637
     */
    public function random(int $length = 64, bool $includeSpecialChars = true): string
    {
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols = '@#$%=+().,/+-&!?;:';

        $keyspace .= $includeSpecialChars ? $symbols : '';

        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }

        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
    }

}