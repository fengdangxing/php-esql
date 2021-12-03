<?php

namespace fengdangxing\esql\Dsl;

use ONGR\ElasticsearchDSL\Search;

/**
 * @desc 聚合
 * @author 1
 * @version v2.1
 * @date: 2021/11/29
 * Class AggsDsl
 * @package common\es_new\lib\Dsl
 */
class AggDsl extends BaseDsl
{
    private $searchOb;


    public function __construct(Search $search)
    {
        $this->searchOb = $search;
    }

    public function addAggToSearch(array $sorts)
    {
        foreach ($sorts as $k => $val) {
            $this->searchOb->addAggregation($val);
        }

    }

    public function setNoList()
    {
        $this->searchOb->setSize(0);
        return $this;
    }

    public function addAggToTermsAgg($name, $field, array $aggs, $sort = [])
    {
        foreach ($aggs as $k => $val) {
            $this->searchOb->addAggregation($this->termsAggregation($name, $field, $val, $sort));
        }
    }
}
