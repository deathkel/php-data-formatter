<?php
/**
 * Author: KEL
 * Email: gnefaijuw@gmail.com
 */

namespace Deathkel\DataFormatter\Formatter;

interface CustomType
{
    public static function item($item, &$extra = []);
}