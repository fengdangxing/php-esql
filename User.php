<?php
/**
 * @desc demo
 * @author 1
 * @version v2.1
 * @date: 2020/09/17
 * Time: 15:22
 */

namespace fengdangxing\user;

use common\es_new\lib\EsModel;
use common\es_new\lib\EsType;

/**
 * @desc es 用户模型类
 * @author 1
 * @version v2.1
 * @date: 2020/09/17
 * Class User
 * @package backend\models
 */
class User extends EsModel
{
    public function __construct()
    {
        $this->setHosts(['192.168.*.*']);
        $this->setUsername('elastic');
        $this->setPassword('222');
        $this->setIndex('dsl-test');
        $this->setType('_doc');
        $this->setMapping([
            'user_id' => EsType::getInteger(),
            'user_name' => EsType::getKeyword(),
        ]);
        parent::_init();
        parent::__construct();
    }
}
