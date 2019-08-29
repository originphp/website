---
title: Testing
description: Testing Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Testing Your Apps

## Getting Ready for Testing

OriginPHP uses PHPUnit 7.5 for unit testing and this is already installed in the Docker container, just type in `phpunit` anywhere. If you are not using the Docker container you can install the composer package, which will install PHPUnit into the `vendor/bin` folder. 

```linux
$ composer require phpunit/phpunit 7.5
```

> A benefit of installing this using composer is that when you are developing your IDE will show you code hinting for the PHPUnit classes.

### Setting up the database

The first thing to do is to setup the test database configuration.

In your `config/database.php` add database configuration.

```php
    ConnectionManager::config('test', array(
    'host' => 'db', // Docker is db, or localhost or Ip address
    'database' => 'app_test',
    'username' => 'somebody',
    'password' => 'secret',
    ));
```

Make sure you have an up to date schema file `database/schema.php`. You can use the schema dump command, by default the database commands use the agnostic version `php`, this can be changed in your `config/application.php` or by passing the option `--type=sql`.

```linux
$ bin/console db:schema:dump
$ bin/console db:schema:dump --type=sql
```

When dumping an SQL version, the schema:dump command uses the SHOW CREATE TABLE for MySQL and the internal version for PostgreSQL, which include the create table information such as columns, constraints and indexes.

When you make changes to the db structure you  should run the `db:test:prepare` which drops and then recreates the test database, and then loads the schema in the test database.

```linux
$ bin/console db:test:prepare
```

### Conventions

When you create tests these will go in the `tests/TestCase` folder, and then depending upon the type it will be another sub folder and the filename should end with  `Test.php`

`tests/TestCase/Controller/BookmarksControllerTest.php`
`tests/TestCase/Model/BookmarkTest.php`
`tests/TestCase/Lib/MathLibraryTest.php`

When you create the test files, the filename should end with `Test.php` and they will extends either

- `\PHPUnit\Framework\TestCase` - To use PHPUnit directly without extra features of Origin such as fixtures
- `Origin\TestSuite\TestCase` - For testing everything else

## Fixtures

Fixtures are sets of sample data that you can use to test your app with, the data in the fixtures in inserted into the table for the fixture. When testing with PHPUnit, the `test` datasource is used by all models, see the `Setting up the database` section.

### Creating Fixtures

When you use create a model using the `generate` command, a fixture will automatically be created for you in the `tests\Fixture` folder.

If you need to manually create a fixture, you should create in the `tests\Fixture` starting with the model name
Create the fixtures in the `tests\Fixture` folder. A fixture for the Article model should be called `ArticleFixture`, this will use the `articles` table, unless you specify a different table using the `table` property method in the fixture.

```php
namespace App\Test\Fixture;

use Origin\TestSuite\Fixture;

class ArticleFixture extends Fixture
{
    public $records = [
        [
            'id' => 1,
            'title' => 'Article #1',
            'body' => 'Article body #1',
            'published' => 1,
            'created' => '2018-12-19 13:29:10',
            'modified' => '2018-12-19 13:30:20',
        ],
        [
            'id' => 2,
            'title' => 'Article #2',
            'body' => 'Article body #2',
            'published' => 1,
            'created' => '2018-12-19 13:31:30',
            'modified' => '2018-12-19 13:32:40',
        ],
        [
            'id' => 3,
            'title' => 'Article #3',
            'body' => 'Article body #3',
            'published' => 1,
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
                'published' => 1,
                'created' => date('Y-m-d H:i:s'],
                'modified' => date('Y-m-d H:i:s']
            ],
        );
        parent::initialize(); // always call parent
    }

```

### Loading Fixtures

To load a fixture, in your test case which extends `OriginTestCase` use the `fixtures` var.

```php
namespace App\Test\Model;

use Origin\TestSuite\OriginTestCase;

class BookmarkTest extends OriginTestCase
{
    public $fixtures = ['Bookmark'];
}
```

If you are want to load a fixture from a plugin, then add the plugin name with the dot notation to list, e.g. `MyPlugin.Bookmark`.


## Testing Models

You will create a test case class like this, defining the fixtures that you will use in testing (including models that are used by other models etc).

```php
namespace App\Test\Model;

use Origin\TestSuite\OriginTestCase;
use Origin\Model\Entity;

class BookmarkTest extends OriginTestCase
{
    public $fixtures = ['Bookmark'];

    // this is called when the testcase is constructed
    public function initialize()
    {
        
    }

    // alias for PHPunit setUp in the OriginTestCase
    public function startup()
    {
        parent::startup(); // remember parent
        $this->loadModel('Bookmark');
    }

    // example assertion
    public function testHasBookmarks(){
        $this->assertTrue($this->Bookmkark->hasBookmarks());
    }

    // alias for PHPunit tearDown in the OriginTestCase
    public function shutdown()
    {
        parent::shutdown(); // remember parent
    }

}

```

### Mocking Models

To mock models extend your Test case by `OriginTestCase` and then call the `getMockForModel` method. When the Model is mocked, it will also be added to the model registry. Remember if use the `tearDown` method in your test case, then call `parent::tearDown()`;

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

## Testing Behaviors

Lets say you created a Behavior to translate records after they have been retrieved

```php
namespace App\Test\Model\Behavior;

use Origin\Model\Model;
use Origin\Model\ModelRegistry;
use App\Model\Behavior\DuckBehavior;
use Origin\TestSuite\OriginTestCase;

class TranslateBehaviorTest extends OriginTestCase
{
    /**
    * Use fixture to import data
    *
    * @var array
    */
    public $fixtures = ['Article'];

    public function startup()
    {
        parent::startup();

        $this->Article = ModelRegistry::get('Article');
        $this->Translate = new TranslateBehavior($this->Article,[
            'language'=>'es'
            ]);
    }

    public function testSomething()
    {
        $article  = $this->Article->findById(1000);
        $this->assertEquals('Hola Mundo',$article->title); // was hello world
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
        $this->assertInstanceOf('Origin\Mailer\Email', $Email); // check returning this

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

## Testing Controllers

In the past testing controllers required quite a bit of code, however we have opted to use custom assertion methods and a request method which requires minimal input or config, to reduce the ifs and issets etc.

The things you should test for:

- was the request successful?
- was the user redirected to the right page (if any)
- is the response body correct
- are cookie, session values and view vars correct,

In your controller test case add the `IntegrationTestTrait`

```php
use Origin\TestSuite\OriginTestCase;
use Origin\TestSuite\IntegrationTestTrait;

class BookmarksControllerTest extends OriginTestCase
{
    use IntegrationTestTrait;

    // this is called when the testcase is constructed
    public function initialize()
    {

    }

    // alias for PHPunit setUp in the OriginTestCase
    public function startup()
    { 
    }

    public function testIndex(){
        $this->get('/bookmarks/index');
        $this->assertResponseOk();
        $this->assertResponseContains('<h2>Bookmarks</h2>');
    }

    // alias for PHPunit setUp in the OriginTestCase
    public function shutdown()
    { 
    }
}
```

### Requests

You can test various requests

#### Get

This will GET the url (get request)

```php
$this->get('/bookmarks/index');
```

#### Post

This will send a POST request using an array of data

```php
$this->post('/bookmarks/index',['title'=>'bookmark name']);
```

#### Delete

This will send a DELETE request

```php
$this->delete('/bookmarks/delete/1234');
```

You can also send PUT and PATCH requests.

#### Put

To send a request as a PUT request

```php
$this->put('/bookmarks/index',['title'=>'bookmark name']);
```

#### patch(string $url,array $data)

To send a request as a PATCH request

```php
$this->patch('/bookmarks/index',['title'=>'bookmark name']);
```

### Assertion Methods

The first thing to check is the response code

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

// Short cuts for response codes
$this->assertResponseNotFound(); // 404 - Not Found

$this->assertResponseBadRequest(); // 400 - Bad Request (Failure - client side problem)

$this->assertResponseForbidden(); // 403 - Forbidden (For application level permisions)

$this->assertResponseUnauthorized(); // 401 - Unauthorized
```

To check response contents

```php

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

```

To check headers

```php

// Check a header exists and its value is correct
$this->assertHeader('Content-Type', 'application/pdf');

// Check a header and its value contains a string
$this->assertHeaderContains('Content-Type', 'html');

// Check that header exists and its value does not contain a string
$this->assertHeaderNotContains('Content-Type', 'html');

```

To check a cookie value

```php
$this->assertCookie('name', 'value');
```

### Other methods

#### Session

Write data to session for the next request, one example is to test applications that require to be logged in.

```php
    $this->session(['Auth.User.id' =>1000]);
```

#### Header

Set headers for the next request

```php
    $this->header('PHP_AUTH_USER','james@originphp.com');
    $this->header('PHP_AUTH_PW','secret');
```

#### env

If you need to modify the $_SERVER variable for testing then can set using env

```php
    $this->env('SOME_ENV_NAME','value');
```

#### controller()

Returns the controller from the last request

#### request()

Returns the request object from the last request

#### response()

Returns the response object from the last request


## Testing Components

This is an example how you might test a component.


```php
namespace App\Test\Controller\Component;

use App\Controller\Component\MathComponent;
use Origin\Controller\Controller;
use Origin\Http\Request;
use Origin\Http\Response;

// A fake controller
class DummyController extends Controller
{
    public function initialize()
    {
        $this->loadComponent('Math');
    }
}

class MathComponentTest extends OriginTestCase
{
    // alias for PHPunit setUp in the OriginTestCase
    public function startup();
    {
        parent::startup();
        $request = new Request();
        $response =  new Response();
        $controller = new DummyController($request,$response);
        $this->MathComponent = $controller->Math;
    }

    public function testSum(){
        $this->assertEquals(2,$this->MathComponent->sum(1,1));
    }

}

```

## Testing View Helpers

Here is an example on how you might test a View Helper.

```php
namespace App\View\Helper;

use Origin\View\View;
use Origin\Http\Request;
use Origin\Http\Response;
use Origin\Controller\Controller;
use Origin\TestSuite\OriginTestCase;

use App\View\Helper\TagHelper;

class TagHelperTest extends OriginTestCase
{
    /**
    * @var \App\View\Helper\TagHelper
    */
    protected $Tag = null;

    public function startup()
    {
        parent::startup();
        $controller = new Controller(new Request(), new Response());
        $view = new View($controller);
        $this->Tag = new TagHelper($view);
    }

    public function testSum()
    {
        $expected = '<div>hello</div>'
        $this->assertEquals($expected, $this->Tag->div('hello'));
    }
}
```

## Testing Console Commands

Like with `Controllers` there is a console integration test trait which makes testing `Commands` a breeze.

```php
use Origin\TestSuite\OriginTestCase;
use Origin\TestSuite\ConsoleIntegrationTestTrait;

class CronCommandTest extends OriginTestCase
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

### Assertion Methods

Lets look at the Assertion methods and how they can be used.

If everything went well then

```php
$this->assertExitSuccess(); // Asserts that the script was run without any problems
$this->assertOutputContains('needle'); // checks that the output contains a string
$this->assertOutputNotContains('needle'); // checks that the output does not contains a string
$this->assertOutputEmpty(); // asserts there was no output
$this->assertOutputRegExp('/needle/');
```

If you want to test there was an error and that the error or warning sent to the screen contains certain text.

```php
$this->assertExitError(); //Asserts that an error was encounterd. 
$this->assertErrorContains('needle'); // Checks the error message contains a string
$this->assertErrorNotContains('needle'); // Checks the error message contains a string
$this->assertErrorRegExp('/needle/');
```

### Getting the output

When writing or debugging tests, you will need to see the output from the console command, as this is buffered, it is not sent to the screen however you get this with ease from within your test.

```php
$stdout = $this->output(); // stdout - standard output
$stderr = $this->error(); // stderr - errors
```

### Accessing the command

If you need the Command object.

```php
$command = $this->command();
```

## Testing Middleware

In the example below you will test Middleware, which sets the response body to foo. 

```php
namespace App\Test\Middleware;

use Origin\Http\Request;
use Origin\Http\Response;
use App\Middleware\FooMiddleware;
use Origin\TestSuite\OriginTestCase;

class FooMiddlewareTest extends OriginTestCase
{
    /**
    * @var \Origin\Http\Request
    */
    protected $request = null;

    /**
    * @var \Origin\Http\Response
    */
    protected $response = null;

    public function startup()
    {
        parent::startup();
        $this->request = new Request();
        $this->response = new Response();
    
        // Invoke the middleware
        $middleware = new FooMiddleware();
        $middleware($this->request, $this->response);
    }

    public function testAbc()
    {
        $this->assertContains('foo', $this->response->body());
    }
}
```

If you created a complicated Middleware or want to test at different stages

```php
   public function testHandle()
    {
        $middleware = new FooMiddleware();
        $middleware->startup($request); // handles request
        // now check the request object
    }

    public function testResponseProcess(){
        $middleware = new FooMiddleware();
        $middleware->startup($request); // handles request
        $middleware->shutdown($request,$response); // handles response
        // now check the response object
    }
```

## Testing Jobs

To test Jobs, make sure you have configured a test Queue connection in your `config/application.php`. When the test is run the `test` queue connection will be used.

```
use Origin\Job\Queue;
Queue::config('default', [
    'engine' => 'database',
    'connection' => 'test',
]);
```

Also make you have you added the queue schema to your `database/schema.php` file, then when you run the `db:test:prepare` command this will be setup.  If you have upgraded from an older version make sure you are using the recent version of [queue.php](https://github.com/originphp/app/blob/master/database/queue.php) 

If not you can import

```linux
$ bin/console db:schema:load queue --datasource=test
```

A test for a Job would look something like this

```php
namespace App\Test\Job;
use App\Job\CreateUserDirectoryJob;
use Origin\TestSuite\OriginTestCase;

class CreateUserDirectoryJobTest extends OriginTestCase
{
    // alias for PHPunit setUp in the OriginTestCase
    public function startup()
    {
        parent::startup(); 
        $this->loadModel('User');
    }

    public function testExecute()
    {
        $user = $this->User->find('first');
        $result = (new CreateUserDirectoryJob())->dispatchNow($user);
        $this->assertTrue($result);
    }
}
```

## Testing Mailers

To test a Mailer, make sure you have configured a test Email account in your `application.php`, this can be a real account or debug, which does not send anything.

```
use Origin\Mailer\Email;
Email::config('test',['engine'=>'Test']);
```

A test would look something like this

```php
namespace App\Test\Mailer;

use Origin\TestSuite\OriginTestCase;
use App\Mailer\SendWelcomeEmailMailer;

class SendWelcomeEmailMailerTest extends OriginTestCase
{
    public function startup()
    {
        parent::startup();
        $this->loadModel('User');
    }

    public function testExecute()
    {
        $user = $this->User->find('first', ['conditions' => ['id' => 1000]]);

        $message = (new SendWelcomeEmailMailer())->dispatch($user);
        $this->assertContains('To: somebody@example.com', $message->header());
        $this->assertContains('From: Name <demo@example.com>', $message->header());
        $this->assertContains('Hi Tony,', $message->body());
    }
}

```

## Testing Services

To test Services (Service Objects)

```php
use Origin\TestSuite\OriginTestCase;
use App\Service\CreateUserService;

class CreateUserServiceTest extends OriginTestCase
{
    public function startup()
    {
        parent::startup();
        $this->loadModel('User');
    }

     public function testExecute()
    {
        $result = (new CreateUserService($this->User))->dispatch(['foo'=>'bar']);
        $this->assertTrue($result->success);
    }
}
```

## Code Coverage

The Dockerized Development Environment comes with PHPUnit and XDebug pre-installed so you can run code coverage easily.

Even though xdebug is installed, it is not enabled because it causes performance issues. You need to enable this after each time you start the container and want to run the coverage.

```linux
$ echo 'zend_extension="/usr/lib/php/20170718/xdebug.so"' >> /etc/php/7.2/cli/php.ini
$ echo 'xdebug.default_enable=0' >> /etc/php/7.2/cli/php.ini
```

You can generate code coverage with the following command:

```linux
$ phpunit --coverage-html /var/www/public/coverage
```

You can then access this by visiting [http://localhost:8000/coverage/index.html](http://localhost:8000/coverage/index.html).