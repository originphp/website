---
title: Query Objects
description: Query Object Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Query Object

Query Objects are used to package complex database queries into objects that only have one method `execute` and returns a result set.

It is based upon the [Query Object Pattern](https://www.martinfowler.com/eaaCatalog/queryObject.html). Query Objects are not used for wrapping up every single query, they only for complex queries that might be bloating your `Repository`.

## Creating a Query Object

To generate a `Query Object`

```linux
$ bin/console generate query BooleanSearch
```

This will create two files

```linux
[ OK ] /var/www/app/Model/Query/BooleanSearchQuery.php
[ OK ] /var/www/tests/TestCase/Model/Query/BooleanSearchQueryTest.php
```

## Usage

This is an example of a non-standard query which might get more complex in the future

```php
namespace App\Model\Query;

use Origin\Model\Query\QueryObject;
use Origin\Model\Model;

class BooleanSearchQuery extends QueryObject
{
    protected function initialize(Model $Article): void
    {
        $this->Article = $article;
    }

    public function execute(string $keywords): ?array
    {
        $filtered = preg_replace('/[^a-z0-9* \p{L}]/ui', '', $keywords);
        $keywords = explode(' ', $filtered);
        $keywords = '+' . implode(' +', $keywords);

        $statement = 'SELECT id FROM topics WHERE MATCH(title,body) AGAINST (:query IN BOOLEAN MODE)';

        return $this->Article->query($statement, ['query' => $keywords]);
    }
}
```

## Executing Query Objects

When you create the instance of your `Query Object` you need to inject the dependency, in this case the article `Model`.

In your `Repository` you could implement like this:

```php
use App\Model\Query\BooleanSearchQuery;

class ArticlesRepository extends Repository
{
    public function search(string $keywords): ?array
    {
       return (new BooleanSearchQuery($this->Article))->execute($keywords);
    }
}
```

## Batch Insert Query

OriginPHP comes with the `BatchInsertQuery` query object, this makes it easy to batch insert multiple records as one query, this provides MySQL performance increases when saving multiple records, such as importing.

```php
use Origin\Model\Query\BatchInsertQuery;

$records = [
    ['title' => 'Article #1'],
    ['title' => 'Article #2'],
];

(new BatchInsertQuery($this->Article))->execute($records);
```

For security reasons placeholders are used in query but there is limit on the number that can be used depending upon your database server, so you can chunk records into larger groups and still see a significant increase in performance in database writing when adding 1000s of records.

```php
use Origin\Model\Query\BatchInsertQuery;

$chunks = array_chunk($records,1000);

$batchInsertQuery = new BatchInsertQuery($this->Article);
foreach($chunks as $chunk){
    $batchInsertQuery->execute($chunk);
}
```