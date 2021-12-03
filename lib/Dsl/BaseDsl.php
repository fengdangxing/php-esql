<?php

namespace fengdangxing\esql\Dsl;

use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Matrix\MaxAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Metric\AvgAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Metric\CardinalityAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Metric\ExtendedStatsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Metric\MinAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Metric\SumAggregation;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermsQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;

class BaseDsl
{
    //查询
    public $matchAll;
    public $match;//全文搜索或者精确查询
    public $multiMatch;//一个字段多条件查询
    public $range;//范围查询
    public $term;//精确查询
    public $terms;//一个字段 多条件匹配精确查询

    public function matchAll($params = ['field' => 'value'])
    {
        return new MatchAllQuery($params);
    }

    public function match($field, $value)
    {
        return new MatchQuery($field, $value);
    }

    public function multiMatch($field, $value)
    {
        return new MultiMatchQuery($field, $value);
    }

    public function range($field, $value = [])
    {
        //gt 大于
        //gte 大于等于
        //lt 小于
        //lte 小于等于
        //["gte"=>20,"lt"=>1]
        return new RangeQuery($field, $value);
    }

    public function term($field, $value)
    {
        return new TermQuery($field, $value);
    }

    public function terms($field, $value = [])
    {
        return new TermsQuery($field, $value);
    }

    //排序
    public function sort($field, $order)
    {
        return new FieldSort($field, $order);
    }

    //聚合
    //指标
    public function max($name, $field)
    {
        $aggregation = new MaxAggregation($name, $field);
        //$aggregation->setField($field);
        //$aggregation->addParameter('filter', ['aa' => 11]);
        return $aggregation;
    }

    public function avg($name, $field)
    {
        $aggregation = new AvgAggregation($name, $field);
        //$aggregation->setField($field);
        return $aggregation;
    }

    public function min($name, $field)
    {
        $aggregation = new MinAggregation($name, $field);
        //$aggregation->setField($field);
        return $aggregation;
    }

    public function sum($name, $field)
    {
        $aggregation = new SumAggregation($name, $field);
        $aggregation->setField($field);
        return $aggregation;
    }

    public function extended_stats($name, $field)
    {
        $aggregation = new ExtendedStatsAggregation($name, $field);
        //$aggregation->setField($field);
        return $aggregation;
    }

    public function cardinality($name, $field)
    {
        $aggregation = new CardinalityAggregation($name);
        $aggregation->setField($field);
        return $aggregation;
    }

    //分桶(分组)
    public function termsAggregation($name, $field, $agg, $sort = [])
    {
        $TermAggregation = new TermsAggregation($name);
        $TermAggregation->setField($field);
        if (!empty($sort)) {
            $TermAggregation->setParameters(['order' => $sort]);//增加排序 ['_count' => 'desc']
        }
        if ($agg) {
            $TermAggregation->addAggregation($agg);
        }
        return $TermAggregation;
    }
}
