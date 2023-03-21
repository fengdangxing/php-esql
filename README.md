# elasticsearch-dsl
```php
#demo
$es = new User();
$es->createIndex();//创建索引
$id = 1;//文档id
$es->addData(['user_id' => 1, 'user_name' => 'ffff'], $id);//添加文档
$es->updateOne(['user_id' => 1, 'user_name' => 'ffff'], $id);//更新文档文档
$es
 #精准查询条件
 ->mustTerm(['user_id' => 1, 'user_name' => 'ffff'])
 #条件条件 
 #gt: > 大于  
 #lt: < 小于  
 #gte: >= 大于或等于
 #lte: <= 小于或等于
 ->mustRange(['user_id' => ['gt', 0]])
 #desc/asc 排序 
 ->orderBy(['user_id' => 'desc'])
 #分组 第一个参数=名称 第二个参数=字段 不分页
 ->groupBy('term_user_id', 'user_id', [$es->count('count', 'user_id', true)]);
 #聚合分页
 ->groupBy('term_user_id', 'user_id', [$es->count('count', 'user_id', true),$es->groupPage()]);
 #多个聚合
 ->groupBy('term_1', 'user_1', [$es->count('count', 'user_1', true),$es->groupPage()]);
 ->groupBy('term_2', 'user_2', [$es->count('count', 'user_2', true),$es->groupPage()]);

#执行dsl
$result = $es->queryDsl();
$es->getDsl(true);//打印dsl语句
var_dump($result);