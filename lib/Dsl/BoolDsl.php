<?php

namespace fengdangxing\esql\Dsl;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Search;

/**
 * @desc bool过滤器
 * @author 1
 * @version v2.1
 * @date: 2021/11/29
 * Class BoolDsl
 * @package common\es_new\lib\Dsl
 */
class BoolDsl extends BaseDsl
{
    /*must 文档 必须 匹配这些条件才能被包含进来。
    must_not 文档 必须不 匹配这些条件才能被包含进来。
    should 如果满足这些语句中的任意语句，将增加 _score ，否则，无任何影响。它们主要用于修正每个文档的相关性得分。
    filter 必须 匹配，但它以不评分、过滤模式来进行。这些语句对评分没有贡献，只是根据过滤标准来排除或包含文档。*/
    private $searchOb;

    private $boolQuery;

    public $nestedQuery;

    public function __construct(Search $search)
    {
        $this->searchOb = $search;
        $this->boolQuery = new BoolQuery();
    }

    public function addFilterToBool(array $queryDsl)
    {
        foreach ($queryDsl as $k => $val) {
            $this->boolQuery->add($val, BoolQuery::FILTER);
        }
        return $this;
    }

    public function addMustToBool(array $queryDsl)
    {
        foreach ($queryDsl as $k => $val) {
            $this->boolQuery->add($val, BoolQuery::MUST);
        }
        return $this;
    }

    public function addMustNotToBool(array $queryDsl)
    {
        foreach ($queryDsl as $k => $val) {
            $this->boolQuery->add($val, BoolQuery::MUST_NOT);
        }
        return $this;
    }

    public function addShouldToBool(array $queryDsl)
    {
        foreach ($queryDsl as $k => $val) {
            $this->boolQuery->add($val, BoolQuery::SHOULD);
        }
        return $this;
    }
    public function addMustNested($nested)
    {
        $this->boolQuery->add($nested, BoolQuery::MUST);
        return $this;
    }

    public function addBoolToSearch()
    {
        $this->searchOb->addQuery($this->boolQuery);
        return $this;
    }

    public function addQueryToSearch(array $queryDsl)
    {
        foreach ($queryDsl as $k => $val) {
            $this->searchOb->addQuery($val);
        }
        return $this;
    }

    public function addBoolToNested($path)
    {
        $this->nestedQuery = $this->nestedQuery($path, $this->boolQuery);
        return $this;
    }

    public function setPage($from = 0, $size = 25)
    {
        $this->searchOb->setFrom($from);
        $this->searchOb->setSize($size);
        return $this;
    }
}
