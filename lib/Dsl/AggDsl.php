<?php

namespace fengdangxing\esql\Dsl;

use ONGR\ElasticsearchDSL\Aggregation\Pipeline\BucketSortAggregation;
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

    public function addAggToSearch($agg)
    {
        $this->searchOb->addAggregation($agg);
        return $this;

    }

    public function setNoList()
    {
        $this->searchOb->setSize(0);
        return $this;
    }

    public function addAggToTermsAgg($name, $field, array $aggs, $size, $sort = [])
    {
        $this->searchOb->addAggregation($this->termsAggregation($name, $field, $aggs, $size, $sort));
    }

    /**
     * @desc 聚合分页排序
     * @author 1
     * @version v2.1
     * @date: 2021/12/31
     * @param int $from
     * @param int $size
     * @return BucketSortAggregation
     */
    public function bucketSort($from = 0, $size = 10)
    {
        $Bucket = new BucketSortAggregation('bucket-sort');
        $Bucket->setParameters(['size' => $size, 'from' => $from]);
        return $Bucket;
    }
}
