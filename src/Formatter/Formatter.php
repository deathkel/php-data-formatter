<?php
/**
 * Author: KEL
 * Email: gnefaijuw@gmail.com
 */

namespace Deathkel\DataFormatter\Formatter;

class Formatter
{
    /**
     * fields wanted
     * @var array
     */
    protected static $fields = [];

    /**
     * key case: camelCase | bigCamelCase | snakeCase | null
     * @var null
     */
    protected static $keyCase = null;

    const CAMEL_CASE = 'camelCase';
    const BIG_CAMEL_CASE = 'bigCamelCase';
    const SNAKE_CASE = 'snakeCase';

    protected static function item($key, $item, $type, &$info = null, &$extra = [])
    {
        switch ($type) {
            case Type::BOOL:
                $item = (bool)$item;
                break;
            case Type::INT:
                $item = (int)$item;
                break;
            case Type::STRING:
                $item = (string)$item;
                break;
            case Type::FLOAT:
                $item = (float)$item;
                break;
            default:
                if (class_exists($type)) {
                    $params = [$item, &$extra];
                    if (is_subclass_of($type, CustomType::class)) {
                        $item = call_user_func_array($type . '::item', $params);
                    } elseif (is_subclass_of($type, Formatter::class)) {
                        $item = call_user_func_array($type . '::info', $params);
                    }
                }
                break;
        }
        return $item;
    }

    /**
     * format key by keyCase or type['as']
     * @param $key
     * @param array $typeOpt
     * @return mixed|string
     */
    protected static function _formatKeyName($key, $typeOpt = [])
    {
        if (isset($typeOpt['as']) && $typeOpt['as']) {
            $keyName = $typeOpt['as'];
            return $keyName;
        }

        switch (static::$keyCase) {
            case static::CAMEL_CASE:
                $keyName = str2Camel($key);
                break;
            case static::BIG_CAMEL_CASE:
                $keyName = str2BigCamel($key);
                break;
            case static::SNAKE_CASE:
                $keyName = str2Snake($key);
                break;
            default:
                $keyName = $key;
        }

        return $keyName;
    }

    /**
     * format a single data
     * @param $info
     * @param $extra
     * @return array|\stdClass
     */
    public static function info($info, &$extra = [])
    {
        $result = [];
        if ($info) {
            foreach (static::$fields as $key => $typeOpt) {
                $isOptional = false;
                $isRepeated = false;
                $keyName    = self::_formatKeyName($key, $typeOpt);

                if (is_array($typeOpt)) {
                    $type = isset($typeOpt['type']) ? $typeOpt['type'] : '';
                    if (isset($typeOpt['optional']) && $typeOpt['optional']) {
                        $isOptional = true;
                    }
                    if (isset($typeOpt['repeated']) && $typeOpt['repeated']) {
                        $isRepeated = true;
                    }
                } else {
                    $type = $typeOpt;
                }

                if (array_key_exists($key, $info) || !$isOptional) {
                    if (is_object($info)) {
                        $item = isset($info->$key) ? $info->$key : ($isRepeated ? [] : '');
                    } else {
                        $item = array_key_exists($key, $info) ? $info[$key] : ($isRepeated ? [] : '');
                    }
                    if ($isRepeated && (is_array($item) || $item instanceof \Iterator)) {
                        $result[$keyName] = [];

                        foreach ($item as $_item) {
                            $result[$keyName][] = static::item($key, $_item, $type, $info, $extra);
                        }
                    } else {
                        $result[$keyName] = static::item($key, $item, $type, $info, $extra);
                    }
                }
            }
        }
        return $result ? $result : new \stdClass();
    }

    /**
     * format a data list
     * @param $data
     * @param $extra
     * @return array
     */
    public static function data($data, $extra = [])
    {
        $result = [];
        if ($data) {
            $std = new \stdClass();
            foreach ($data as $info) {
                $_info = static::info($info, $extra);
                if ($_info && !($_info instanceof $std)) {
                    $result[] = $_info;
                }
            }
        }
        return $result;
    }
}