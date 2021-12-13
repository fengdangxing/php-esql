<?php

namespace common\es_new\lib;

use common\es_new\lib\Dsl\AggDsl;
use common\es_new\lib\Dsl\BoolDsl;
use common\es_new\lib\Dsl\SortDsl;
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
     * @desc 必须等于条件
     * @author 1
     * @version v2.1
     * @date: 2021/11/30
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
     * @desc 必须等于多值条件
     * @author 1
     * @version v2.1
     * @date: 2021/11/30
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
            $query[] = $this->bool->terms($key, $value);
        }
        $this->bool->addMustToBool($query);
        return $this;
    }

    /**
     * @desc 必须包含条件
     * @author 1
     * @version v2.1
     * @date: 2021/11/30
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
     * @desc 必须不等于条件
     * @author 1
     * @version v2.1
     * @date: 2021/11/30
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
     * @desc 必须范围查询
     * @author 1
     * @version v2.1
     * @date: 2021/11/30
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
     * @desc 功能描述
     * @author 1
     * @version v2.1
     * @date: 2021/12/13
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
     * @desc 或者条件-包含查询
     * @author 1
     * @version v2.1
     * @date: 2021/12/01
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
     * @desc 或者条件-范围查询
     * @author 1
     * @version v2.1
     * @date: 2021/12/01
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
     * @desc 统计不显示聚合列表
     * @author 1
     * @version v2.1
     * @date: 2021/12/01
     */
    public function notList()
    {
        $agg = new AggDsl($this->searchOb);
        $agg->setNoList();
        return $this;
    }

    public function groupBy($name, $field, array $index, $sort = [])
    {
        $agg = new AggDsl($this->searchOb);
        $agg->addAggToTermsAgg($name, $field, $index, $sort);
        return $this;
    }

    public function max($name, $field, $isGroupBy = false)
    {
        $agg = new AggDsl($this->searchOb);
        if ($isGroupBy) {
            return $agg->max($name, $field);
        }
        $agg->max($name, $field);
        return $this;
    }

    public function count($name, $field, $isGroupBy = false)
    {
        $agg = new AggDsl($this->searchOb);
        if ($isGroupBy) {
            return $agg->cardinality($name, $field);
        }
        $agg->cardinality($name, $field);
        return $this;
    }

    public function extended_stats($name, $field, $isGroupBy = false)
    {
        $agg = new AggDsl($this->searchOb);
        if ($isGroupBy) {
            return $agg->extended_stats($name, $field);
        }
        $agg->extended_stats($name, $field);
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
        if ($debug) {
            echo json_encode($this->dsl);
            exit;
        }
        return $this;
    }
}
