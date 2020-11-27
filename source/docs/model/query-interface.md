---
title: Query Interface Guide
description: Query Interface Guide Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Query Interface

You can query the database using the model query interface, when you loop through the query object it will execute the SQL statement.

## Accessing the query object

Call `where` or `select` on the model and it will return a query object.

```php
// looping through the query will execute the statement
$query = $this->Article->where(['published' => true]);
foreach($query as $article){
  //
}
```

```php
$query = $this->Article->select(['id','title','description']);
foreach($query as $article){
  //
}
```

## Executing the Query

You can also manually execute the query without looping through it by calling `first`,`all`,`sum`,`count`,`average`,`minimum` or `maximum` on the query object.

```php
$article = $this->Article->select(['id','title','description'])->first();
$articles = $this->Article->select(['id','title','description'])->all();
```

## Query Methods

### select

> Can also be called on the model to return the query object

This selects the fields that you want, if you are joining a table (See below) then you will need to add
the table alias to the fields from the other tables.

```php
$this->Article->where(['id' => 1234])
              ->select(['id','title','description']);
```

> You also must include the foreignKeys if not fields will not be returned from the join table.

```php
$this->Article->where(['id' => 1234])
              ->select(['id','title','description','author_id','authors.id','author.name']); // 'author_id','authors.id' important
```

### Where

> Can also be called on the model to return the query object

This selects the fields that you want, if you are joining a table (See below) then you will need to add
the table alias to the fields from the other tables.

```php
$this->Article->select(['id','title','description'])
              ->where(['id' => 1234]);
```

> Remember to include the foreign key when joining tables, if not fields will not be returned from the join table.

```php
$this->Article->select(['id','title','description','author_id','authors.id','author.name']) 
              ->where(['id' => 1234]);
```

### Group

To use group in the query

```php
$this->Article->select(['count(DISTINCT id) as count','category'])
              ->group(['category']);
/*
Origin\Model\Collection Object
(
    [0] => Origin\Model\Entity Object
        (
            [category] => Development
            [count] => 1
        )
    [1] => Origin\Model\Entity Object
        (
            [category] => Search
            [count] => 2
        )
)
*/
```

### Having

To use having

```php
$this->Article->select(['count(id) as count','category'])
              ->group(['category'])
              ->having(['count >' => 1]);
/*
Origin\Model\Collection Object
(
    [0] => Origin\Model\Entity Object
        (
            [category] => Search
            [count] => 2
        )
)
*/
```

### Order

To set the order

```php
$this->Article->select(['id','title'])
              ->order(['id ASC']);
```

You can also use key/value pair

```php
$this->Article->select(['id','title'])
              ->order(['id' => 'ASC']);
```

To order on multiple columns is also easy

```php
$this->Article->select(['id','title'])
              ->order(['id','title ASC']);
```

### Distinct

To run a distinct query

```php
$this->Article->select(['id','title'])
               >distinct();
```

### Limit

To limit the results

```php
$query = $this->Article->where(['category' => 'foo'])->limit(5);
```

### Offset

To set an offset 

```php
$this->Article->where(['category' => 'foo'])
              ->limit(5)
              ->offset(20);
```

### Locking Records

To run a SELECT FOR UPDATE to lock a record or records for updating.

```php
$this->Article->select(['id','title'])
              ->lock();
```

### With

If you want to get a record with associated data using the configured relations such as `belongsTo` and `hasMany`

```php
$this->Article->where(['category' => 'foo'])
              ->with(['Author','Comment']);
```

You can also get nested info

```php
$this->Article->where(['category' => 'foo'])
              ->with(['Author' => ['Profile','Address'],'Comment']);
```

### Join

You can manually a join table and then select the fields that you want. This expects that you have followed conventions and your table name is underscore lower case plural and the foreign key would be `author_id` and the primary key is `id`.

```php
$this->Article->select(['id','title','authors.id','authors.name'])
              ->join('authors'); // table name
```

To manually create a join

```php

$this->Article->select(['id','title','authors.id','authors.name'])
              ->join([
                'table' => 'authors',
                'alias' => 'authors',
                'type' => 'LEFT', // LEFT|INNER|FULL|RIGHT
                'conditions' => [
                  'articles.author_id = authors.id'
                ]]);
```

### Count

To execute the query and get a count

```php
$this->Article->where(['id >' => 1000 ])
              ->count();
```

### Average

To execute the query and get an average value of a column

```php
$this->Article->where(['category' => ['new','draft']])
              ->average('column_name');
```

### Sum

To execute the query to calculate the sum of a column

```php
$this->Article->where(['created <' => now()])
              ->sum('column_name');
```

### Minimum

To execute the query and get the minimum value of a column

```php
$this->Article->where(['id !=' => 1000 ])
              ->minimum('column_name');
```

### Maximum

To execute the query and get the maximum value of a column

```php
$this->Article->where(['user_id' => 1000 ])
              ->maximum('column_name');
```


## Chunk

If you have thousands of records and you need to run a query on them you can do so like this to limit each
query to a certain number of records, this will return a generator which can be used in `foreach`

```php
$chunks = $this->Article->where(['category' =>'pending'])
                        ->chunk(200);
foreach($chunks as $chunk){

}
```

## Each

The `each` method uses `chunk`, and yields a record for each result instead of chunks.

```php
$articles = $this->Article->where(['category' =>'pending'])->each();

foreach($articles as $article){

}
```

By default it will use `1000` records per chunk, however you can change this

```php
$this->Article->where(['category' =>'pending'])->each(50);
```

