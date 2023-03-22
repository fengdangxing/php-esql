<?php

namespace fengdangxing\esql\Dsl;

use ONGR\ElasticsearchDSL\Search;

/**
 * 排序
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
