<?php

namespace fengdangxing\esql;

/**
 * es字段类型-常用而已
 * @return ${TYPE_HINT}
 */
trait EsType
{
    //核心数据类型
    public static $text = 'text';
    public static $keyword = 'keyword';//关键字家庭，其中包括keyword，constant_keyword，和wildcard。

    public static $long = 'long';//带符号的64位整数，最小值-263，最大值263-1
    public static $integer = 'integer';//带符号的32位整数，最小值-231，最大值231-1
    public static $short = 'short';//带符号的16位整数，最小值-32768，最大值32767
    public static $byte = 'byte';//带符号的8位整数，最小值-128，最小值127
    public static $double = 'double';//双精度64位IEEE 754 浮点数
    public static $float = 'float';//单精度32位IEEE 754 浮点数

    public static $date = 'date';//日期类型，包括date和 date_nanos。
    public static $object = 'object';//插入|更新字段的值，值写成json对象的形式 搜索时，字段名使用点号连接
    public static $ip = 'ip';


    private static $fields;

    /**
     * 文本 会分词，然后进行索引 支持模糊、精确查询 不支持聚合
     * @param bool $is_analyzer
     * @param bool $index
     * @return array
     */
    public static function getText($is_analyzer = false, $index = true)
    {
        if ($is_analyzer) {
            static::addParamter('analyzer', 'ik_max_word');#ik中文分词器
        }

        static::addParamter('index', $index);
        return static::getBack(static::$text);
    }

    /**
     * 不进行分词，直接索引 支持模糊、精确查询 支持聚合
     * @return array
     */
    public static function getKeyword()
    {
        return static::getBack(self::$keyword);
    }


    /**
     * @return array
     */
    public static function getLong()
    {
        return static::getBack(self::$long);
    }

    /**
     * @return array
     */
    public static function getInteger()
    {
        return static::getBack(self::$integer);
    }

    /**
     * @return array
     */
    public static function getShort()
    {
        return static::getBack(self::$short);
    }

    /**
     * @return array
     */
    public static function getByte()
    {
        return static::getBack(self::$byte);
    }

    /**
     * @return array
     */
    public static function getDouble()
    {
        return static::getBack(self::$double);
    }

    /**
     * @return array
     */
    public static function getFloat()
    {
        return static::getBack(self::$float);
    }

    /**
     * @param string $format 格式
     * @return array
     */
    public static function getDate($format = "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis")
    {
        static::addParamter('format', $format);
        return static::getBack(self::$date);
    }

    /**
     * @return array
     */
    public static function getObject()
    {
        return static::getBack(self::$object);
    }

    /**
     * @return array
     */
    public static function getIp()
    {
        return static::getBack(self::$ip);
    }

    /**
     * @param $type
     * @return array
     */
    private static function getBack($type)
    {
        $back['type'] = $type;
        if (static::$paramter) {
            foreach (static::$paramter as $field => $value) {
                $back[$value[0]] = $value[1];
            }
            static::$paramter = [];
        }
        if (static::getFields()) {
            $back['fields'][static::getFields()[0]]['type'] = static::getFields()[1];
        }

        return $back;
    }

    private static $paramter;

    private static function addParamter($field, $value)
    {
        static::$paramter[] = array($field, $value);
    }

    /**
     * @return mixed
     */
    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * @param $name
     * @param $type
     */
    public static function setFields($name, $type)
    {
        self::$fields = [$name, $type];
    }
}
