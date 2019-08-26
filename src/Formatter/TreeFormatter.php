<?php
/**
 * Author: KEL
 * Email: gnefaijuw@gmail.com
 */

namespace Deathkel\DataFormatter\Formatter;


class TreeFormatter extends Formatter
{
    /**
     * primary id key
     * @var string
     */
    protected static $idKey = 'id';

    /**
     * parent id key
     * @var string
     */
    protected static $parentIdKey = 'parent_id';

    /**
     * children key in data generated
     * @var string
     */
    protected static $childrenKey = 'children';


    /**
     * build tree
     * @param array $elements origin data
     * @param int $parentId parent id value
     * @param array $extra extra value
     * @return array
     */
    public static function buildTree(array &$elements, $parentId = 0, &$extra = [])
    {
        $branch      = array();
        $idKey       = static::$idKey;
        $parentIdKey = static::$parentIdKey;
        $childrenKey = static::$childrenKey;
        $stdClass    = new \stdClass();
        foreach ($elements as $key => $element) {
            if ($element[$parentIdKey] == $parentId) {
                $_extra = $extra;
                $_info  = self::info($element, $_extra);
                if ($_info instanceof $stdClass) {
                    continue;
                }

                $children = self::buildTree($elements, $element[$idKey], $_extra);

                $_info[$childrenKey] = $children;

                $branch[] = $_info;

                unset($elements[$key]);
            }
        }
        return $branch;
    }
}