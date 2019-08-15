---
title: Elasticsearch
description: Elasticsearch Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Elasticsearch

If you need to improve performance of your database or implement more advanced searching you case use Elasticsearch. OriginPHP makes it ultra simple to implement.

> To use the Elasticsearch features you will need to install Elasticsearch 7.0 or greater.

## Configuration

Here I will cover the configuration of Elasticsearch, the installation instructions for Elasticsearch with Docker are at the bottom of this guide.

### Connection

First you need to configure the Elasticsearch connection. In your configuration file `config/application.php` add the following

```php
use Origin\Utility\Elasticsearch;

Elasticsearch::config('default', [
    'host' => 'elasticsearch', // or 127.0.0.1 if not using the docker version
    'port' => 9200,
    'timeout' => 400
    'ssl' => false
]);
```

### Model

Load the Elasticsearch Behavior, in the `initialize` method of the models that you want to implement Elasticsearch in.

```php
public function initialize(array $config)
{
    $this->loadBehavior('Elasticsearch');
}
```

If you want to use a different connection, then you pass the `connection` key when loading the Elasticsearch Behavior.

```php
$this->loadBehavior('Elasticsearch',[
    'connection'=>'other'
    ]);
```

### Searching

Whenever you create or delete a record the Elasticsearch index will be updated.
To carry out a search use the model, which will have new methods from the Behavior.

To search using keywords or a [query string](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html) on columns in your index

```php
$results = $this->Post->search('Top Frameworks 2019');
$results = $this->Post->search('"Top Frameworks 2019"');
$results = $this->Post->search('title:how to');
$results = $this->Post->search('+framework +php -draft');
```

If you want to carry out a custom search using [Elasticsearch query DSL](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html) (Domain Specific Language), you can do like this

```php
$query = [
    'query' => [
        'multi_match' => [
            'query' => 'php framework',
            'fields' => ['title','body']
            ]
        ]
    ];
$results = $this->Post->search($query);
```

### Customizing Search

If you want to customize the search function or use custom searches from within your model you can do it like this:

```php

public function search(string $keywords)
{
    return $this->Elasticsearch->search([
        'query' => [
            'multi_match' => [
                'query' => $keywords,
                'fields' => ['title','body']
                ]
            ]
    ]);
}

```

### Mapping

By default, OriginPHP dynamically maps each column to Elasticsearch, all columns for the model will be stored in the index unless you tell it otherwise.

To manually map columns for the indexes, in your model call the index method for each column that you want to index. You can optionally pass an options array which takes settings from Elasticsearch [mapping types](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html).


```php
class Article extends AppModel
{
    public function initialize(array $config)
    {
        $this->loadBehavior('Elasticsearch');

        // To index columns
        $this->index('title',['type'=>'keyword','analyzer'=>'english']);
        $this->index('body'); // this will dynamically map
        
        // To set index settings
        $this->indexSettings(['number_of_shards' => 1]); 
    }
}
```

You will then need to run the `elasticsearch:index` command telling which models recreate the indexes on with new settings, the data will then be added back to the new indexes.

```linux
$ bin/console elasticsearch:index Post Comment
```

> The only time indexes are created with your settings is when this command is run, if not Elasticsearch creates the index if it does not exist with default settings.

## Installing Elastic Search

Add the following to your `docker-compose.yml` file.

```yml
  elasticsearch:
    image: docker.elastic.co/elasticsearch/elasticsearch:7.3.0
    container_name: elasticsearch
    environment:
      - discovery.type=single-node
    volumes:
      - es-data:/usr/share/elasticsearch/data
    ports:
      - 9200:9200
```

And under the `volumes:` node add

```yml
    es-data:
      driver: local
```

The next time you run `docker-compose up` the Elasticsearch container will created.

For information on how to install on Ubuntu server see this [article](https://linuxize.com/post/how-to-install-elasticsearch-on-ubuntu-18-04/).