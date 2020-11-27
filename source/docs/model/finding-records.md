---
title: Models - Finding Records
description: Finding Records Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Finding Records

The examples below will relate to the following models and because the tables are setup using conventions, we don't need to pass parameters.

```php
class Article extends ApplicationModel
{
  protected function initialize(array $config): void
  {
    $this->hasOne('Author');
    $this->hasMany('Comment');
  }
}
```

```php
class Author extends ApplicationModel
{
  protected function initialize(array $config): void
  {
    $this->belongsTo('Article');
  }
}
```

```php
class Comment extends ApplicationModel
{
  protected function initialize(array $config): void
  {
    $this->belongsTo('Article');
  }
}
```

## Retrieving data from the database

To retrieve data from the database, the model class provides a function called find which will read the data
from the database and then transform it into objects which you can work with.

### Getting a single record using the primary key

With `get` you can get a single record, also called an `Entity`. See the [Entities guide](/docs/model/entities) for information on these objects.

```php
$article = $this->Article->get(1000);
echo $article->title;
```
When you use `get` if the record is not found, it will throw a `RecordNotFoundException`.

### First Finder

The first finder will return a single record as an entity object, the difference between this and get is that it will not throw an exception if a record is not found. In fact the get method uses the `first` finder.

```php
$article = $this->Article->find('first');
echo $article->title;
echo $article->author->name;
```

You can also pass an array of options, which will be explained in the *Find Options* chapter. If no result is found it will return `null`.

If you need the result to be an array form then you can call `toArray` on the result.

You can also call the first method

```php
$article = $this->Article->first();
```

### All Finder

The all finder will return multiple records as a special collection object.

```php
$articles = $this->Article->find('all');
foreach($articles as $article){
  echo $article->title;
  foreach($article->comments as $comment){
    echo $comment->description;
  }
}
```

You can also pass an array of options. If no results are found it will return an empty array.

If you need the results to be an array form then you can call `toArray` on the results object.

You can also call the all method

```php
$article = $this->Article->all();
```

### List Finder

The list finder will return an array of selected data. If you specify a 3rd field the data will be grouped.

```php
    $list = $this->Article->find('list', ['fields' => ['id']]); // ['a','b','c']
    $list = $this->Article->find('list', ['fields' => ['id','title']]); // ['a' => 'b']
    $list = $this->Article->find('list', ['fields' => ['id','title','category']]); // ['c' => ['a' => 'b']
```
### Count Finder

The count finder will return a count based upon criteria that you have supplied.

```php
$count = $this->Article->find('count', [
    'conditions' => [
        'owner_id' => 1234,
        'status' => ['Published']
        ]
    );
```

## Conditions
To set conditions for the `find` or `get` you need to pass an array with the key `conditions`, which itself should be an array. When you fetching associated data, if you don't add an alias prefix to the field name it will be assumed that the condition is for the current model.

```php
$conditions = ['id' => 1234];
$result = $this->Article->find('first', ['conditions' => $conditions]);
```

### Equals

You can use either a string or an array if you want to search multiple.

```php
  $conditions = ['title' => 'How to write an article'] // title = "How to write an article"
  $conditions = ['authors.name' => 'James']; // authors.name = "James"
  $conditions = ['author_id' => [1000,1001,1002];  // author_id IN (1000,1001,1002)
```

### Not Equals

You can use either a string or an array if you want to search multiple.

```php
  $conditions = ['title !=' => 'How to write an article'] // title != "How to write an article"
  $conditions = ['authors.name !=' => 'James']; // authors.name = "James"
  $conditions = ['author_id !=' => [1000,1001,1002];  // author_id NOT IN (1000,1001,1002)
```


### Comparing

To compare two fields

```php
 $conditions = ['articles.created = articles.modified'];
```

### Arithmetic

To check field values, such as greater, less than etc.

```php
 $conditions = ['rating >' => 5];
 $conditions = ['rating <' => 10];
 $conditions = ['rating >=' => 5];
 $conditions = ['rating <=' => 10];
```

### Between

To use between

```php
 $conditions = ['rating BETWEEN' => [5,10]];
 $conditions = ['rating NOT BETWEEN' => [5,10]];
```

### Like

```php
 $conditions = ['authors.name LIKE' =>'Tony%'];
 $conditions = ['authors.name NOT LIKE' =>'%Tom%'];
```

### And,OR and NOT

To create more complex queries you can use and or and not.

```php
$conditions = [
    'authors.name' => 'James',
    'OR' => [
      'articles.title LIKE' => 'how to%',
      'articles.status' => 'Published'
    ]
]
```
This would generate something like this this:

```sql
authors.name = 'James' AND (articles.title LIKE 'how to%' OR articles.status = 'Published')
```

If you wanted to search using the same fields you can put each condition in its own array.

```php
$conditions = [
    'authors.name' => 'James',
    'OR' => [
      ['articles.title LIKE' => 'how to%'],
      ['articles.title LIKE' => '100 Ways to%'],
    ]
]
```

You can also nest multiple OR, AND queries using arrays. 

Lets say you want to search by article title or author:

```php
  $conditions = [
    ['OR' => [
      ['title LIKE' => '%how to%'],
      ['title LIKE' => '%100 ways to%'],
      ]],
    ['OR' => [
        ['authors.name LIKE' => '%tony%'],
        ['authors.name LIKE' => '%claire%'],
    ]],
    ];
```

## Find Options

### Conditions

The conditions key is what is used to generate the sql queries. 

```php
$conditions = ['id' => 1234];
$result = $this->Article->find('first', ['conditions' => $conditions]);
```

You should always try to add the model alias to the field when working with associated data or joining tables.

```php
$conditions = ['articles.id' => 1234];
$result = $this->Article->find('first', ['conditions' => $conditions]);
```

### Fields

By default all fields are returned for each model, even if you don't use them. You can reduce the load on the server by selecting just the fields that you need.

```php
$result = $this->Article->find('first', ['fields' => ['id','title','author_id']]);
```

To use also pass DISTINCT, MIN and MAX etc. When using those database functions remember to include AS then a unique field name.

```php
$conditions = ['fields' => ['DISTINCT (authors.name) AS author_name','title']];
```

### Order

Use this option to order the results by fields. 


```php
$result = $this->Article->find('all', [
  'order' => ['title DESC']
  ]); // ORDER BY articles.title DESC
```


```php
$result = $this->Article->find('all', [
  'order' => ['title' => 'DESC']
  ]); // ORDER BY articles.title DESC
```

You can also sort by multiple fields

```php
$result = $this->Article->find('all', [
  'order' => ['title','created ASC']
  ]); // ORDER BY articles.title, articles.created ASC
```

Make sure you add the alias prefix e.g. `articles.` to the field if you are working with associated data. The order option can be a string or an array.

```php
$result = $this->Article->find('all', [
  'order' => 'authors.created DESC'
  ]); // ORDER BY authors.created DESC
```

You can set the default sort order for a model in the model property `order`, any calls to find without order will use this as the natural order.

### Group

To run a group by query, any aliased fields that don't exist in the table will be added as a property to the entity of the current model regardless if it took the data from another table.

```php
$result = $this->Article->find('all', [
  'fields' => ['COUNT(*) as total','category'],
  'group' => 'category'
  ]);
```

This will return something like this

```php
[0] => Origin\Model\Entity Object
        (
            [category] => 'How To'
            [total] => 2
        )
```

### Limit

Limit is basically what it says it does, it limits the number of results.

```php
$result = $this->Article->find('all', ['limit' => 5]);
```

### Having

You can use having to create filters for a group of rows or aggregates.

```php
$result = $this->Order->find('all', [
  'group' => 'order_date',
  'having' => ['total >' => 1000]
  ]);
```

### Locking (SELECT FOR UPDATE)

When you to find a record and lock it for updating, this will execute a select for update statement.

```php
$result = $this->Article->find('first', ['lock' => true]);
```


## Eager Loading Associations

Associated records are not fetched unless you tell find to do so. Providing you have set up the relationships e.g. `hasMany`,`hasOne`,`belongsTo`,`hasAndBelongsToMany`, you will be able to fetch related data by passing array with the models that you want to fetch data for.

```php
$result = $this->Article->find('first', [
  'associated' => ['Author','Comment']
  ]);
```

By default all fields for each the associated models will be fetched (or if you have configured the association to return only certain fields by default) unless you tell it otherwise.

NOTE: If you limit the fields that are returned, you must always ensure that `foreignKey` is  present, if not the final results wont include the records.

```php
$result = $this->Article->find('first', [
  'associated' => [
    'Author' => [
      'fields' => ['id','name','email']
      ]
    ]
  ]);
```

## Joining Tables

Sometimes you might want to do your own joins, this can easily be done by using the `joins` option when finding. This option should be an array of arrays, since you do multiple joins.

```php
  $conditions['joins'][] = [
    'table' => 'authors',
    'alias' => 'authors',
    'type' => 'LEFT' , // this is defualt,
    'conditions' => [
      'authors.id = articles.author_id'
    ]
   ];
```

## Disabling Callbacks

By default callbacks are enabled, you can disable them by passing false, then the `beforeFind` and `afterFind` will not be called.

```php
$result = $this->Article->find('first', ['callbacks' => false]);
```

## FindBys

FindBys are convenience methods find records by a set of conditions.

```php
$article = $this->Article->findBy(['title' => 'foo']);
$articles = $this->Article->findAllBy(['category' => 'new']);
```

## Aggregates

These functions are used for calculations and support the same options as the `find` method:

```php
$count = $this->Article->count();
$count = $this->Article->count('all', $options); // all is alias for *
$count = $this->Article->count('DISTINCT articles.id');

$avg = $this->Article->average('score');
$min = $this->Article->minimum('score');
$max = $this->Article->maximum('score');
$sum = $this->Article->sum('score');
```

These methods also work with group queries, which will return an associative array.

```php
$result = $this->Article->count('all', ['group' => 'category']);
/*
Array
(
    [0] => Array
        (
            [count] => 128
            [category] => Foo
        )

    [1] => Array
        (
            [count] => 64
            [category] => Bar
        )
)
*/
```

## Finding out if a record exists

If you need to find a record exists with a primary key you can use the `exists` method.

```php
$result = $this->Article->exists(1024);
```

## Batch processing for large datasets

> By the sort order is always by the primary key in ascending order

If you have thousands of records and you need to run a query on them you can do so like this to limit each
query to a certain number of records

```php
foreach($this->Article->findInBatches() as $articles){
  // a collection of articles is returned
}
```

The following options are supported

- size: default:1000 the size of the batch
- start: the primary key where to start, if wanted
- finish: the primary key where to end, if wanted

The `findInBatches` returns a collection of results, but you can also use `findEach` which uses the `findInBatches` and yields an entity for each result.


```php
foreach($this->Article->findEach() as $article){
  // an article is returned
}
```

## Running Raw SQL queries

If you need to carry out a raw SQL query the you can use the `query` method.

```php
$result = $this->Article->query('SELECT name from articles');
```

To securely pass values when using sql statements, pass an array with key value pairs.

```php
$result = $this->User->query('SELECT name FROM users WHERE id = :id', ['id' => 1234]);
```