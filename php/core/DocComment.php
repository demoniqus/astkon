<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 31.12.18
 * Time: 17:11
 */

namespace Astkon;

use Reflector;

abstract class DocComment
{
    /**
     * Извлекает из DocComment все @параметры и возвращает из них массив.
     * Если в DocComment несколько одинаковых ключей, их значения будут собраны в массив
     * @param Reflector $reflector
     * @return array
     */
    public static function extractDocCommentParams(Reflector $reflector) {
        $_items = array_filter(
            explode(GlobalConst::NewLineChar, $reflector->getDocComment()),
            function($line){
                return mb_strpos($line, '@') !== false;
            }
        );
        $_items = array_map(
            function($line){
                while (mb_substr($line = trim($line), 0, 1) === '*') {
                    $line = mb_substr($line, 1);
                }
                return $line;
            },
            $_items
        );
        $_items = array_filter(
            $_items,
            function($line){
                return !!preg_match('/^@[a-z_]/i', $line);
            }
        );

        $items = array();
        array_walk($_items, function($item) use (&$items) {
            $segments = explode(' ', $item, 2);
            if (count($segments) < 2) {
                $segments[] = null;
            }
            list($key, $value) = $segments;
            $key = mb_substr($key, 1);
            if (!array_key_exists($key, $items)) {
                $items[$key] = array();
            }
            $items[$key][] = $value;
        });
        foreach ($items as $key => $value) {
            if (count($value) === 1) {
                $items[$key] = $value[0];
            }
        }
        return $items;
    }

    /**
     * @param Reflector $reflector
     * @param string $itemName
     * @return array|string|null
     */
    public static function getDocCommentItem (Reflector $reflector, string $itemName) {
        $docCommentParams = self::extractDocCommentParams($reflector);
        if (!is_array($docCommentParams)) {
            $docCommentParams = array();
        }
        return array_key_exists($itemName, $docCommentParams) ? $docCommentParams[$itemName] : null;
    }
}