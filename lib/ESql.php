<?php

namespace fengdangxing\esql;

use fengdangxing\esql\Dsl\AggDsl;
use fengdangxing\esql\Dsl\BoolDsl;
use fengdangxing\esql\Dsl\SortDsl;
use ONGR\ElasticsearchDSL\Search;

trait ESql
{
    private $searchOb;
    private $bool;
    public $dsl;

    public function __construct()
    {
        $this->searchOb = new Search();
        $this->bool = new BoolDsl($this->searchOb);
    }

    /**
     * 必须等于条件
     * @param array $where
     * @return ESql
     */
    public function mustTerm(array $where)
    {
        if (empty($where)) {
            return $this;
        }
        $query = [];
        foreach ($where as $key => $value) {
            $query[] = $this->bool->term($key, $value);
        }
        $this->bool->addMustToBool($query);
        return $this;
    }

    /**
     * 必须等于多值条件
     * @param array $where
     * @return ESql
     */
    public function mustTerms(array $where)
    {
        if (empty($where)) {
            return $this;
        }
        $query = [];
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $query[] = $this->bool->terms($key, $value);
            }
        }
        $this->bool->addMustToBool($query);
        return $this;
    }

    /**
     * 必须包含条件
     * @param array $where
     * @return ESql
     */
    public function mustMatch(array $where)
    {
        if (empty($where)) {
            return $this;
        }
        $query = [];
        foreach ($where as $key => $value) {
            $query[] = $this->bool->match($key, $value);
        }
        $this->bool->addMustToBool($query);
        return $this;
    }

    /**
     * 必须不等于条件
     * @param array $where
     * @return ESql
     */
    public function mustNotTerm(array $where)
    {
        if (empty($where)) {
            return $this;
        }
        $query = [];
        foreach ($where as $key => $value) {
            $query[] = $this->bool->term($key, $value);
        }
        $this->bool->addMustNotToBool($query);
        return $this;
    }

    /**
     * 必须不等于条件
     * @param array $where
     * @return ESql
     */
    public function mustNotTerms(array $where)
    {
        if (empty($where)) {
            return $this;
        }
        $query = [];
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $query[] = $this->bool->terms($key, $value);
            }
        }
        $this->bool->addMustNotToBool($query);
        return $this;
    }

    /**
     * 必须范围查询
     * @param array $where
     * @return ESql
     * //gt 大于
     * //gte 大于等于
     * //lt 小于
     * //lte 小于等于
     * //["gte"=>20,"lt"=>1]
     * @throws \Exception
     */
    public function mustRange(array $where)
    {
        if (empty($where)) {
            return $this;
        }
        $query = [];
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $query[] = $this->bool->range($key, $value);
            } else {
                throw new \Exception('必须为数组');
            }
        }
        $this->bool->addMustToBool($query);
        return $this;
    }

    /**
     * 功能描述
     * @param $path
     * @param $EsModel | 当前模型类
     * @return $this
     */
    public function mustNested($path, $EsModel)
    {
        $EsModel->bool->addBoolToNested($path);
        $this->bool->addMustNested($EsModel->bool->nestedQuery);
        return $this;
    }

    /**
     * 或者条件-包含查询
     * @param array $where
     * @return ESql
     * @throws \Exception
     */
    public function shouldTerm(array $where)
    {
        if (empty($where)) {
            return $this;
        }
        $query = [];
        foreach ($where as $key => $value) {
            $query[] = $this->bool->term($key, $value);
        }
        $this->bool->addShouldToBool($query);
        return $this;
    }

    /**
     * 或者条件-包含查询
     * @param array $where
     * @return $this
     */
    public function shouldTerms(array $where)
    {
        if (empty($where)) {
            return $this;
        }
        $query = [];
        foreach ($where as $key => $value) {
            $query[] = $this->bool->terms($key, $value);
        }
        $this->bool->addShouldToBool($query);
        return $this;
    }

    /**
     * 或者条件-范围查询
     * @param array $where
     * @return ESql
     * @throws \Exception
     */
    public function shouldRange(array $where)
    {
        if (empty($where)) {
            return $this;
        }
        $query = [];
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $query[] = $this->bool->range($key, $value);
            } else {
                throw new \Exception('必须为数组');
            }
        }
        $this->bool->addShouldToBool($query);
        return $this;
    }

    public function orderBy(array $orders)
    {
        if (empty($orders)) {
            return $this;
        }
        $sort = new SortDsl($this->searchOb);
        $order = [];
        foreach ($orders as $field => $value) {
            $order[] = $sort->sort($field, $value);
        }

        $sort->addSortToSearch($order);
        return $this;
    }

    public function page($index = 0, $size = 10)
    {
        $this->bool->setPage($index, $size);
        return $this;
    }

    /**
     * 统计不显示聚合列表
     */
    public function notList()
    {
        $agg = new AggDsl($this->searchOb);
        $agg->setNoList();
        return $this;
    }

    /**
     * 单个分桶
     * @param $name
     * @param $field
     * @param array $aggs
     * @param int $size
     * @param array $sort
     * @return ESql
     */
    public function groupBy($name, $field, array $aggs, $size = 1000000, $sort = [])
    {
        $agg = new AggDsl($this->searchOb);
        $agg->addAggToTermsAgg($name, $field, $aggs, $size, $sort);
        return $this;
    }

    /**
     * 聚合分页
     * @param int $from
     * @param int $size
     * @return \ONGR\ElasticsearchDSL\Aggregation\Pipeline\BucketSortAggregation
     */
    public function groupPage($from = 0, $size = 10)
    {
        $agg = new AggDsl($this->searchOb);
        return $agg->bucketSort($from, $size);
    }

    public function sum($name, $field, $isGroupBy = false)
    {
        $agg = new AggDsl($this->searchOb);
        if ($isGroupBy) {
            return $agg->sum($name, $field);
        }
        $agg->addAggToSearch($agg->sum($name, $field));
        return $this;
    }

    public function count($name, $field, $isGroupBy = false)
    {
        $agg = new AggDsl($this->searchOb);
        if ($isGroupBy) {
            return $agg->cardinality($name, $field);
        }
        $agg->addAggToSearch($agg->cardinality($name, $field));
        return $this;
    }

    /**
     * 统计指标数值
     * @param $name
     * @param $field
     * @param bool $isGroupBy
     * @return $this|\ONGR\ElasticsearchDSL\Aggregation\Metric\ExtendedStatsAggregation
     */
    public function extended_stats($name, $field, $isGroupBy = false)
    {
        $agg = new AggDsl($this->searchOb);
        if ($isGroupBy) {
            return $agg->extended_stats($name, $field);
        }
        $agg->addAggToSearch($agg->extended_stats($name, $field));
        return $this;
    }

    private function allBool()
    {
        $this->bool->addBoolToSearch();
    }

    public function getDsl($debug = false)
    {
        $this->allBool();
        $this->dsl = $this->searchOb->toArray();
        if (!empty($this->dsl)) {
            $this->dsl["track_total_hits"] = true;
        }
        if ($debug) {
            echo json_encode($this->dsl);
            exit;
        }
        return $this;
    }
}
