<?php

namespace fengdangxing\lib\Dsl;

use ONGR\ElasticsearchDSL\Search;

/**
 * @desc 排序
 * @author 1
 * @version v2.1
 * @date: 2021/11/29
 * Class SortDsl
 * @package common\es_new\lib\Dsl
 */
class SortDsl extends BaseDsl
{
    private $searchOb;


    public function __construct(Search $search)
    {
        $this->searchOb = $search;
    }

    public function addSortToSearch(array $sorts)
    {
        foreach ($sorts as $k => $val) {
            $this->searchOb->addSort($val);
        }

    }
}
