---
title: Migrations
description: Migrations Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Migrations

Migrations are an easy way to alter your database, allowing you to deploy consistent
changes with the ability to rollback if needed.

Here is a sample migration to a create a table called products.

```php
class CreateProductsTableMigration extends Migration
{
    public function change(): void
    {
        $this->createTable('products',[
            'owner_id' => ['type' => 'integer','limit' => 10],
            'name' => 'string',
            'description' => 'text',
        ]);
    }
```

## Creating Migrations

Creating a migration is easy peasy, just run the migration command with a name of the class (a short description) in studly caps.

```linux
$ bin/console generate migration CreateProductTable
```

Migrations are automatically versioned by adding a `YYYYMMDDHHMMSS` prefix to the filename and are stored in `database/migrations`.

## How Migrations Works

Migration actions placed in `change` will automatically be reversed, in case something is not reversible in the `change` method you can place actions in `reversable` this will be called when the migration is rolled back.

Also there are `up` and `down` methods which can used instead of `change`, this is where you can customize what to do when migrating up or down, actions called in either `up` or `down` are not reversed.

For example

```php
class AddNameColumnToSuppliersMigration extends Migration
{
    public function up(): void
    {
        $this->addColumn('suppliers','name','string');
    }
    
    public function down(): void
    {
        $this->removeColumn('suppliers','name');
    }

```

To ensure there are no SQL issues, use either `change` or both up and down in a migration especially if working on the same tables. 

## Writing Migrations

After you have created a Migration using the `migration` command, the next step is to add the migration actions.

### Tables

#### Creating a Table

In the change method add the following code create a table:

```php
$this->createTable('products',[
        'name' => 'string',
        'description' => 'text',
    ]);
```

The key for each row is the column name and then use either a string which means the column type or you can pass an array with further options. For example.


```php
$this->createTable('products',[
        'name' => ['type' => 'integer','limit' => 10,'null' => false],
        'amount' => ['type' => 'decimal','precision' => 8,'scale' => 2],
        'status' => ['type' => 'string','default' => 'new']
    ]);
```

The columns types will map according to the database adapter, for example `string` would map to `varchar` in MySQL. The column types to use are `string`,`text`,`integer`,`bigint`,`float`,`decimal`,`datetime`,`date`,`time`,`binary` and `boolean`.

Column modifiers are `type`,`limit`,`default`,`null`, and `precision` and `scale` for decimal and float types. To set a field default to null, use `''`.

You can also pass a third argument of options using the following keys:

- id: default is true, whether to automatically create the primary key field
- primaryKey: default is `id` this is the name of the primary key field
- autoIncrement: this sets the auto increment or serial value. e.g. 10000
- engine: This is the storage engine used for the table for MySQL e.g InnoDB
- charset: The character set for the table in MySQL e.g. utf8
- collate: Sets the collate option for the table in MySQL. utf8_unicode_ci

```php
$options = ['engine' => 'InnoDB','autoIncrement' => 10000,'charset' => 'utf8','collate' => 'utf8_unicode_ci'];

$this->createTable('products',[
        'name' => ['type' => 'integer','limit' => 10,'null' => false],
        'amount' => ['type' => 'decimal','precision' => 8,'scale' => 2],
        'status' => ['type' => 'string','default' => 'new']
    ],$options);
```

#### Creating a join table

When you are working with `hasAndBelongsToMany` you will need a additional table to manage the relationship.

```php
$this->createJoinTable('contacts','tags');
```

You can also pass a third argument as an array of options, these are the same as for `createTable` which include `id`,`primaryKey` and `options`.


#### Dropping a Table

To drop a table

```php
$this->dropTable('articles')
```

#### Renaming a Table

To rename a table

```php
$this->rename('old_articles','articles');
```

### Columns

#### Adding a Column

To add a column just pass the table name, column name and column type.

```php
$this->addColumn('suppliers','name','string',['limit' => 255]);
```

The columns types will map according to the database adapter, for example `string` would map to `varchar` in MySQL. The column types to use are `string`,`text`,`integer`,`bigint`,`float`,`decimal`,`datetime`,`date`,`time`,`binary` and `boolean`.

Column modifiers are `type`,`limit`,`default`,`null`, and `precision` and `scale` for decimal and float types. To set a field default to null, set ['default' => ''].

If you are using MySQL and want to use `mediumtext` or `longtext`

```php
$this->addColumn('suppliers','description','text',['limit' => 16777215]); // medium text bytes
$this->addColumn('suppliers','description','text',['limit' => 4294967295]); // long text bytes
```

#### Changing Columns

Changing columns in a table is pretty straight forward.

```php
$this->changeColumn('suppliers','name','string',['limit' => 80]);
```

The column types are `string`,`text`,`integer`,`bigint`,`float`,`decimal`,`datetime`,`date`,`time`,`binary` and `boolean`. The column modifiers are `type`,`limit`,`default`,`null`, `precision` and `scale`.

#### Removing a Column

```php
$this->removeColumn('suppliers','name');
```

#### Renaming Columns

To rename a column

```php
$this->renameColumn('suppliers','old_name','new_name');
```

### Indexes

#### Adding an Index

To create an index on a single column

```php
$this->addIndex('customers','account_number')
```

To create an index on two or more columns:

```php
$this->addIndex('customers',['owner_id','tenant_id']);
```

When indexes are created the index name defaults to `table_column_name_index` you can change this
by passing an array with the `name` key as the third argument.

For example:

```php
$this->addIndex('customers','account_number',['name' => 'my_index_name']);
```

If you want to add a FULLTEXT index (MySQL index)

```php
$this->addIndex('articles','title',['type' => 'fulltext']);
```

#### Removing Indexes

To remove an index using the index name generated by  `addIndex` use:

```php
$this->removeIndex('customers','account_number');
$this->removeIndex('customers',['owner_id','tenant_id']);
```

To remove an index using the index name

```php
$this->removeIndex('customers',['name' => 'index_name']);
```

#### Listing Indexes

To get a list of indexes

```php
$indexes = $this->indexes('articles');
```

### Foreign Keys

#### Adding a Foreign Key

If you are following the conventions and your foreign keys end with `_id`, and the primary key in tables are `id`. Then 
you can create a foreign key by just using the two table names. The name for the foreign key will generated automatically and will look like `fk_origin_c05a10b6`.

```php
$this->addForeignKey('accounts','users');
```

If you have not or need to customize this you can pass an options array with `column` and/or `primaryKey` keys.

```php
$this->addForeignKey('articles','users',[
    'column' => 'author_id', // column in articles table
    'primaryKey' => 'lng_id' // column in users table
    ]);
```

You can also set options for on delete and on update. See [Referential Actions](https://dev.mysql.com/doc/refman/8.0/en/create-table-foreign-keys.html#foreign-keys-referential-actions)

- cascade
- restrict
- setNull
- setDefault
- noAction

For example:

```php
$this->addForeignKey('articles','users', [
    'update' => 'cascade',
    'delete' => 'setNull'
    ]);
```

#### Removing Foreign Keys

Again, if you followed conventions then to remove a foreign key you only need to supply the two table names.

```php
$this->removeForeignKey('accounts','users');
```

You can also remove by column or constraint name.

```php
$this->removeForeignKey('articles',['column' => 'owner_id']);
$this->removeForeignKey('accounts',['name' => 'fk_origin_1234567891'];
```

#### Listing Foreign Keys

To get a list of foreign keys

```php
$foreignKeys = $this->foreignKeys('articles');
/*
$foreignKeys = [
    [
        'table' => 'articles',
        'column' => 'author_id',
        'name' => 'fk_origin_12345',
        'referencedTable' => 'users',
        'referencedColumn' => 'id',
        'update' => 'noAction',
        'delete' => 'noAction'
    ]
];
*/

```



### Custom SQL Queries

If you need to run custom SQL queries you can, but we can't magically reverse them. You can only use the `execute` method in the `up` and `down` methods. If you use other methods of migration in the `up` and `down` methods, they wont be reversed automatically.

```php
class CreateProductsTableMigration extends Migration
{
    public function up(): void
    {
        $this->execute($sqlThatDoesSomething);
    }
     public function down(): void
    {
        $this->execute($sqlThatUndoesWhatUpDid);
    }

```

### Irreversible Migrations

Sometimes you might delete data or do something else that is not reversible, in that case you should throw a `IrreversibleMigrationException`.

```php
class CreateProductsTableMigration extends Migration
{
    public function reversable(): void // or use down if you are using up
    {
        $this->throwIrreversibleMigrationException();
    }

```

## Running Migrations

Before you run migrations for the very first time you need to create the migrations table in your database, simply run the following command to do this for you.

```linux
$ bin/console db:schema:load migrations
```

When you run the db migrate command it will check for migrations which have not be applied.

```linux
$ bin/console db:migrate
```

To rollback the last migration (this can be run multiple times)

```linux
$ bin/console db:rollback
```

If you want to migrate to a specific version or rollback, add the version number to the command, its really that simple.

```linux
$ bin/console db:migrate 20190511111934
```