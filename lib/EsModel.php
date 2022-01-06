<?php

namespace fengdangxing\esql;

use Elasticsearch\ClientBuilder;

/**
 * @desc es模型基类
 * @author 1
 * @version v2.1
 * @date: 2021/11/30
 * Class Yii2ESql
 * @package common\es_new\lib
 */
class EsModel
{
    use ESql;
    use EsType;
    //子模型只要定义这几个参数
    private $index = '';//索引名称
    private $type = '';//文档名称
    private $mapping = [];//字段列表
    private $number_of_shards = 3;//定义分片个数
    private $number_of_replicas = 2;//定义副本个数
    private $max_result_window = 10000;//查询返回总个数

    private $hosts = [];//连接ips
    private $username = '';
    private $password = '';
    private $timezone = 'Asia/Shanghai';

    private $settings;

    /**
     * @var \Elasticsearch\Client
     */
    private static $clients;

    public function _init()
    {
        date_default_timezone_set($this->getTimezone());
        self::getDb($this->hosts, $this->username, $this->password);
    }

    /**
     *
     * @param $hosts
     * @param string $username
     * @param string $password
     * @return \Elasticsearch\Client
     * @access public
     */
    private static function getDb($hosts, $username = '', $password = '')
    {
        if (!self::$clients) {  //判断连接池中是否存在
            $builder = ClientBuilder::create();
            $builder->setHosts($hosts);
            //$builder->setHandler($handler);
            if ($username) $builder->setBasicAuthentication($username, $password);
            self::$clients = $builder->build();
        }
        //self::$connections->select(0);
        return self::$clients;
    }

    /**
     * Create this model's index
     */
    public function createIndex()
    {
        $params = [
            'index' => $this->getIndex(),
            'body' => [
                'mappings' => $this->getMapping(),
                'settings' => [
                    'number_of_shards' => $this->getNumberOfShards(),
                    'number_of_replicas' => $this->getNumberOfReplicas(),
                    //'max_result_window' => $this->getMaxResultWindow()
                ]
            ]
        ];
        $response = self::$clients->indices()->create($params);
        return $response['acknowledged'];
    }

    /**
     * Delete this model's index
     */
    public function deleteIndex()
    {
        $deleteParams = [
            'index' => $this->getIndex()
        ];
        $response = self::$clients->indices()->delete($deleteParams);
        return $response['acknowledged'];
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param array $mapping -二维数组['字段名' => '字段类型']
     */
    public function setMapping(array $mapping)
    {
        //"type":"inter",
        //"analyzer":"ik_max_word",
        //"search_analyzer":"ik_max_word"
        $mapping['@create_time'] = ["format" => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis", "type" => "date"];
        $mapping['@update_time'] = ["format" => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis", "type" => "date"];
        $this->mapping['properties'] = $mapping;
        $this->mapping['dynamic'] = false;
    }

    /**
     * @return int
     */
    public function getNumberOfShards()
    {
        return $this->number_of_shards;
    }

    /**
     * @param int $number_of_shards
     */
    public function setNumberOfShards($number_of_shards = 3)
    {
        $this->number_of_shards = $number_of_shards;
    }

    /**
     * @return int
     */
    public function getNumberOfReplicas()
    {
        return $this->number_of_replicas;
    }

    /**
     * @param int $number_of_replicas
     */
    public function setNumberOfReplicas($number_of_replicas = 2)
    {
        $this->number_of_replicas = $number_of_replicas;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param string $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param array $hosts
     */
    public function setHosts($hosts)
    {
        $this->hosts = $hosts;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function addData(array $data, $id)
    {
        $data['@create_time'] = date("Y-m-d H:i:s", time());
        $params['id'] = $id;
        $params['index'] = self::getIndex();
        $params['type'] = self::getType();
        $params['body'] = $data;
        self::$clients->create($params);
    }

    /**
     * @desc 更新一条文档内容
     * @author 1
     * @version v2.1
     * @date: 2021/12/10
     * @param array $data
     * @param $id
     * @param int $retry_on_conflict
     * @return array
     */
    public function updateOne(array $data, $id, $retry_on_conflict = 0)
    {
        $data['@update_time'] = date("Y-m-d H:i:s", time());
        $params['id'] = $id;
        $params['index'] = self::getIndex();
        $params['type'] = self::getType();
        $params['body'] = ['doc' => $data];
        if ($retry_on_conflict) $params['retry_on_conflict'] = $retry_on_conflict;
        return self::$clients->update($params);
    }

    /**
     * @desc 是否存在文档
     * @author 1
     * @version v2.1
     * @date: 2021/12/14
     * @param $id
     * @return bool
     */
    public function existsDoc($id): bool
    {
        $params['id'] = $id;
        $params['index'] = self::getIndex();
        $params['type'] = self::getType();
        return self::$clients->exists($params);
    }

    /**
     * @desc 获取连接
     * @author 1
     * @version v2.1
     * @date: 2021/12/14
     * @return \Elasticsearch\Client
     */
    public function getClients()
    {
        return self::$clients;
    }

    /**
     * @desc 执行dsl语句
     * @author 1
     * @version v2.1
     * @date: 2021/12/14
     * @param array $field
     * @return mixed
     */
    public function queryDsl(array $field = [])
    {
        $dsl = $this->getDsl()->dsl;
        $params = [
            'index' => $this->index,
            'body' => $dsl,
            '_source_includes' => $field
        ];
        $response = self::$clients->search($params);
        return $response;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @desc 更新设置
     * @author 1
     * @version v2.1
     * @date: 2022/01/06
     * @return mixed
     */
    public function updateSettings()
    {
        $deleteParams = [
            'index' => $this->getIndex(),
            'body' => [
                'max_result_window' => 100000000
            ]
        ];
        $response = self::$clients->indices()->putSettings($deleteParams);
        return $response['acknowledged'];
    }

    /**
     * @return int
     */
    public function getMaxResultWindow()
    {
        return $this->max_result_window;
    }

    /**
     * @param int $max_result_window
     */
    public function setMaxResultWindow($max_result_window)
    {
        $this->max_result_window = $max_result_window;
    }
}
