---
title: Testing
description: Testing Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Testing Your Apps

## Setting up the database for testing

OriginPHP uses PHPUnit 7.x for unit testing and this is already installed in the Docker container, just type in `phpunit` anywhere. If you are not using the Docker container you can install the composer package, which will install PHPUnit into the `vendor/bin` folder. 

```linux
$ composer require phpunit/phpunit ^7
```
> A benefit of installing this using composer is that when you are developing your IDE will show you code hinting for the PHPUnit classes.

The first thing to do is to create a test database, and setup the test database configuration.

In your `config/database.php` add database configuration.

```php
    ConnectionManager::config('test', array(
    'host' => 'db', // Docker is db, or localhost or Ip address
    'database' => 'app_test',
    'username' => 'somebody',
    'password' => 'secret',
    ));
```

Lets create the database, the db shell will take the settings from the connection manager and use that
to connect to db server and create the database.

```linux
$ bin/console db:create --datasource=test
```

### Conventions

When you create tests these will go in the `tests/TestCase` folder, and then depending upon the type it will be another sub folder and the filename should end with  `Test.php`

`tests/TestCase/Controller/BookmarksControllerTest.php`
`tests/TestCase/Model/BookmarkTest.php`
`tests/TestCase/Lib/MathLibraryTest.php`

When you create the test files, the filename should end with `Test.php` and they will extends either
- `\PHPUnit\Framework\TestCase` - To use PHPUnit directly without extra features of Origin such as fixtures
- `Origin\TestSuite\TestCase` - For testing everything else

### Testing Models

You will create a test case class like this, defining the fixtures that you will use in testing (including models that are used by other models etc).

```php
namespace App\Test\Model;

use Origin\TestSuite\OriginTestCase;
use Origin\Model\ModelRegistry;
use Origin\Model\Entity;

class BookmarkTest extends OriginTestCase
{
    public $fixtures = ['Bookmark'];

    public function setUp()
    {
        $this->Bookmark = ModelRegistry::get('Bookmark');
        parent::setUp();
    }

    public function tearDown(){
        parent::tearDown(); # Important
    }

}

```

If you are want to load a fixture from a plugin, then add the plugin name with the dot notation to list, e.g. `MyPlugin.Bookmark`.

Create the fixtures in the `tests\Fixture` folder. You are most likely going to be testing existing models, so we can import the schema easily.

```php
namespace App\Test\Fixture;

use Origin\TestSuite\Fixture;

class ArticleFixture extends Fixture
{
    public $import = ['model' =>'Article']
}

```

To set some test data set the `records` property.

```php
namespace App\Test\Fixture;

use Origin\TestSuite\Fixture;

class ArticleFixture extends Fixture
{
    public $import = ['model' =>'Article'];

    public $records = [
        [
            'id' => 1,
            'title' => 'Article #1',
            'body' => 'Article body #1',
            'published' => '1',
            'created' => '2018-12-19 13:29:10',
            'modified' => '2018-12-19 13:30:20',
        ],
        [
            'id' => 2,
            'title' => 'Article #2',
            'body' => 'Article body #2',
            'published' => '1',
            'created' => '2018-12-19 13:31:30',
            'modified' => '2018-12-19 13:32:40',
        ],
        [
            'id' => 3,
            'title' => 'Article #3',
            'body' => 'Article body #3',
            'published' => '1',
            'created' => '2018-12-19 13:33:50',
            'modified' => '2018-12-19 13:34:59',
        ],
    );
}
```

Sometimes you will want to use dynamic data, in this case you will modify the data using the `initialize` method.

```php
    public function initialize() {
        $this->records = [
            [
                'id' => 1,
                'title' => 'First Article',
                'body' => 'Article body goes here',
                'published' => '1',
                'created' => date('Y-m-d H:i:s'],
                'modified' => date('Y-m-d H:i:s'],
            ],
        );
        parent::initialize(); // always call parent
    }

```

You can also manually specify the schema, the type field using our own mapping designed to be database agnostic. The types are `string`,`text`,`integer`,`bigint`,`float`,`decimal`,`datetime`,`date`,`time`,`binary` and `boolean`.

Here is an example:

```php
namespace App\Test\Fixture;

use Origin\TestSuite\Fixture;

class ArticleFixture extends Fixture
{
    public $schema = [
         'id' => ['type' => 'primaryKey'],
         'title' => [
           'type' => 'string',
           'length' => 255,
           'null' => false,
         ],
         'body' => 'text',
         'published' => [
           'type' => 'integer',
           'default' => '0',
           'null' => false,
         ],
         'created' => 'datetime',
         'modified' => 'datetime',
     );
}

```

You can generate the schema from your existing database using the following command:

```linux
$ bin/console db:schema:dump --type:php
```

This will create a folder in your config folder, called schema with a PHP file for each table. You can run this anytime you make changes, but you will need to update the fixture file separately. 
When you are first developing your app, using the import method makes the most sense, since it will just your current database at all times. As you start to get into beta and production, you can code the field data into the fixtures - but it is up to you.

### Mocking Models

To mock models extend your Test case by either `OriginTestCase` and then call the `getMockForModel` method. When the Model is mocked, we also add this to model registry. Remember if use the `tearDown` method in your test case, then call `parent::tearDown()`;

To get a mock model with the find method stubbed.

```php
$mock = $this->getMockForModel('Bookmark',['find']);
```

You can also pass an array of options, which are passed to the model constructor such as className etc.

```php

class BookmarkTest extends OriginTestCase
{
    public function testSomething(){

        $model = $this->getMockForModel('Bookmark', ['something']);

        $model->expects($this->once())
            ->method('something')
            ->will($this->returnValue(true));
  
        $model->doSomething();
    }

}

```

## Testing Private or Protected Methods or Properties

There will be times you will want to test that protected or private methods or property, we have included a `TestTrait` to make this very easy.  There is a big debate on whether this should be done or just test the public methods and properties. I think it should be down to the specific case, for example if you look at our Email test, I wanted more control and each method to have its own test, I find this easier to write, manage and maintain.

```php
    public function testFrom()
    {
        $Email = new MockEmail();
        $Email = $Email->from('james@originphp.com');
        $this->assertInstanceOf('Origin\Utility\Email', $Email); // check returning this

        $property = $Email->getProperty('from'); # TestTrait
        $this->assertEquals(['james@originphp.com',null], $property);

        $Email = $Email->from('james@originphp.com', 'James');

        $property = $Email->getProperty('from'); # TestTrait
        $this->assertEquals(['james@originphp.com','James'], $property);
    }
```

An example of how you might use this:

```php

use Origin\TestSuite\TestTrait;

class BookmarkTest extends OriginTestCase
{
    use TestTrait;
    public function testPrivateProperty(){

        $privateProperty = $this->getProperty('hidden');
        ..
    }
}

```

There are 3 functions in the `TestTrait`

### Call Method

This will call a any method, private or protected, the second argument is an array of arguments that will be used
when calling the method.

For example:

```php
$result = $this->callMethod('doSomething',[$user,$password])
```

### Get Property

This will get any property of the object

For example:

```php
$result = $this->getProperty('id');
```

### Set Property

This will set any property of the object

For example:

```php
$result = $this->setProperty('id',1234);
```

# Testing Controllers

In the past testing controllers required quite a bit of code, however we have opted to use custom assertations and a request method which requires minimal input or config, to reduce the ifs and issets etc.

In your controller test case add the `IntegrationTestTrait`

```php
use Origin\TestSuite\OriginTestCase;
use Origin\TestSuite\IntegrationTestTrait;
class BookmarksControllerTest extends OriginTestCase
{
    use IntegrationTestTrait;
    public function testIndex(){
        $this->get('/bookmarks/index');
        $this->assertResponseOk();
        $this->assertResponseContains('<h2>Bookmarks</h2>');
    }
}
```

## Testing requests

You can test various requests

### Get

This will GET the url (get request)

```php
$this->get('/bookmarks/index');
```

### Post

This will send a POST request using an array of data

```php
$this->post('/bookmarks/index',['title'=>'bookmark name']);
```

### Delete

This will send a DELETE request

```php
$this->delete('/bookmarks/delete/1234');
```

You can also send PUT and PATCH requests.

### Put

To send a request as a PUT request

```php
$this->put('/bookmarks/index',['title'=>'bookmark name']);
```

### patch(string $url,array $data)

To send a request as a PATCH request

```php
$this->patch('/bookmarks/index',['title'=>'bookmark name']);
```

## Custom Assertations

```php

// Checks that response is 2xx
$this->assertResponseOk();

// Checks that response is 4xx
$this->assertResponseError();

// Checks that response is 2xx/3xx
$this->assertResponseSuccess();

// Checks that response is 5xx
$this->assertResponseFailure();

// Checks for a specific response code
$this->assertResponseCode(401);

// Check Response Content

$this->assertResponseEquals('{ "name":"James", "email":"james@originphp.com"}');

$this->assertResponseNotEquals('{ "error":"something went wrong"}');

$this->assertResponseContains('<h1>Some Title</h1>');

$this->assertResponseNotContains('please login');

$this->assertResponseEmpty();

$this->assertResponseNotEmpty();

// Check there was no redirect
$this->assertNoRedirect(); 

$this->assertRedirect(['controller'=>'users','action'=>'login']);

$this->assertRedirectContains('/users/view');

$this->assertRedirectNotContains('/users/login');

// Check Headers 
$this->assertHeaderContains('Content-Type', 'application/pdf');
$this->assertHeaderNotContains('Cache-Control', 'no-cache, must-revalidate');

```

## Other methods

### Session

Write data to session for the next request, one example is to test applications that require to be logged in.

```php
    $this->session(['Auth.User.id' =>1000]);
```

### Header

Set headers for the next request

```php
    $this->header('PHP_AUTH_USER','james@originphp.com');
    $this->header('PHP_AUTH_PW','secret');
```

### env

Sets server enviroment $_SERVER

```php
    $this->env('HTTP_ACCEPT_LANGUAGE','en');
```

### controller()

Returns the controller from the last request

### request()

Returns the request object from the last request

### response()

Returns the response object from the last request

## Testing Console Commands

Like with `Controllers` there is a console integration test trait which makes testing `Commands` a breeze.

```php
use Origin\TestSuite\OriginTestCase;
use Origin\TestSuite\ConsoleIntegrationTestTrait;

class CronShellTest extends OriginTestCase
{
    use ConsoleIntegrationTestTrait;

    public function testCronDaily()
    {
        $this->exec('cron daily'); // the same bin/console cron daily
        $this->assertExitSuccess();
        $this->assertOutputContains('nothing to run');
    }

}

```

To see what output was generated you can add `debug($this->output())` after calling the exec to see the standard output or `debug($this->errorOutput())` to see output in the stderr (ie. the errors).

When testing interactive commands (or where the user is asked for confirmation etc) through `$io->ask()` or `$io->askChoice()` then you will need to send the data when calling the `exec` method.

For example, if the console commands asks for a username and then asks to confirm.

```php
$this->exec('create-user',['james','y']);
```

This will send the name and then after that y to confirm

### Custom Assertations

Lets look at the assertations and how they can be used.

If everything went well then

```php
$this->assertExitSuccess(); // Asserts that the script was run without any problems
$this->assertOutputContains('needle'); // checks that the output contains a string
$this->assertOutputEmpty(); // asserts there was no output
```

If you want to test there was an error and that the error or warning sent to the screen contains certain text.

```php
$this->assertExitError(); //Asserts that an error was encounterd. 
$this->assertErrorContains('needle'); // Checks the error message contains a string

```

### Getting the output

When writing or debugging tests, you will need to see the output from the console command, as this is buffered, it is not sent to the screen however you get this with ease from within your test.

```php
$stdout = $this->output(); // standard output
$stderr = $this->errorOutput(); // Errors
```

### Accessing the command

If you need to access the shell

```php
$command = $this->command();
```