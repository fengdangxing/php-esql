# php-elasticsearch-sql
ElasticsearchSql 基类即可
```php
#效果demo
$es = new User();
$es->createIndex();//创建索引
$es->addData(['user_id' => 1, 'user_name' => 'ffff'], 1);//添加文档

$es->mustTerm(['user_id' => 1, 'user_name' => 'ffff'])//must term 条件
 ->mustRange(['user_id' => ['gt', 0]])
 ->orderBy(['user_id' => 'desc'])
 ->groupBy('term_user_id', 'user_id', [$es->count('count', 'user_id', true)]);
$result = $es->queryDsl();
$es->getDsl(true);//打印dsl语句
var_dump($result);

#聚合分页
->groupBy('term_user_id', 'user_id', [$es->count('count', 'user_id', true),$es->groupPage()]);
 
#多个聚合
->groupBy('term_1', 'user_1', [$es->count('count', 'user_1', true),$es->groupPage()]);
->groupBy('term_2', 'user_2', [$es->count('count', 'user_2', true),$es->groupPage()]);
