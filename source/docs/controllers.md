---
title: Controller
description: Controller Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Controllers

## What is a Controller

Controller is the C in MVC (Model View Controller). When a request is made, it is passed to the router then the router determines which controller to use. Most applications will receive the request, then get, create and or save data to the database through a model and then use a view to create the output to display to the user. It is considered a good practice to keep the controllers skinny, and should not contain business logic, the models should contain that.

## Controller Conventions

The name of the controller. should be in plural camel case and it needs to end with Controller. For example, `UsersController`,`UserProfilesController` and `BookmarksController`. It is important that you follow the conventions so that you can use the default routing, you can always customise the routing rules later.

## Controller Methods and Actions

When you create a controller it will extend the `ApplicationController` and save this to the `app/Http/Controller` folder. Your controller class will contain methods just like any other class, but only public methods will be treated as routeable actions.  When your application receives a request, the router will determine which controller and action to use, and then create an instance of the controller and run the action.

```php
namespace app\Http\Controller;

class ContactsController extends ApplicationController {
  public function view($id)
  {

  }
}
```

For example, if a user wants to create a new contact and in your application they would go to  `/contacts/create`, this will load the `ContactsController` and run the `create` method, this will then automatically render the `/app/Http/View/Contacts/create.ctp` unless you tell it otherwise. In the method we will create a Contact entity, which is a object which represents a single row of data, and then we will send it to the view using `set`. We do this by calling the `new` method on the Contact model.

```php
class ContactsController extends ApplicationController {
  public function new()
  {
      $contact = $this->Contact->new();

      $contact->first_name = 'James';
      $contact->email = 'james@example.com';

      $this->set('contact',$contact);
  }
}
```

Any methods which are not supposed to be actions, should be set to `private` or `protected`.

If you want to add logic when the controller is created, you can do so in the `initialize` method.

Remember, you can use the code generation tool to create the Controller and ControllerTest for you.

```linux
$ bin/console generate controller Products
```

For more information see [code generation]((/docs/development/code-generation))

## Models

By default the model for the controller (the singular name) will be lazy loaded when you call for it. However if you need to load a different model then you should call the `loadModel` method.

For more information see our [Models Guide](/docs/models).

```php
$AnotherModel = $this->loadModel('Product');
```

## Request

When a request is made, a request is object is injected into the controller. GET, POST and FILES parameters are parsed and it also provides some functions to check the type of request or only allow a certain type of request.

### Params

Lets look at this request:

```
GET /bookmarks/view/1024
```

You will need to get the params array from the request object, and this will contain information about the controller, action, the passed arguments (args), named parameters, which route was matched and the plugin name.

```php
print_r($this->request->params());
/*
Array
(
    [controller] => Bookmarks
    [action] => view
    [args] => Array
        (
            [0] => 1024
        )

    [named] => Array
        (
        )

    [route] => /:controller/:action/*
    [plugin] => 
*/
)
```

This is an example of what named parameters looks like in a request.
```
GET /books/index/sort:desc/page:100
```

```php
print_r($this->request->params('named'));
/*
Array
(
 [sort] => desc
 [page] => 100
)
*/
```

### Query

Query parameters are also accessed through the request object.

```
GET /books/index?sort=asc&page=101
```

```php
print_r($this->request->query());
/*
Array
(
 [sort] => asc
 [page] => 1001
)
*/
```

## Post Data

Post data is data which has been posted, we have taken this from the $_POST variable.
```html
<input type="text" name="first_name" value="James" />
<input type="text" name="email" value="james@example.com" />
```
```php
print_r($this->request->data());
/*
Array
(
 [first_name] => 'James'
 [email] => 'james@example.com'
)
*/
```

## Custom Parameters

You can configure routes to return the some of the parameters as keys in the `params` array. You can do
this by modifying the `config/routes.php` to include something like this:

```php
Router::add('/:controller/:action/:id');
/*
Array
(
 [controller] => 'YourController'
 [action] => 'some_action',
 [id] => 1234
)
*/
```

See the [routing guide](/docs/development/routing) for more information on routing setup.

## Components

Components are objects which can be shared between controllers. The framework comes with a number of components and you can also build your own. To load helpers call the `loadComponent` method in the `initialize` method of your controller.

```php
public function initialize() : void
{
    $this->loadComponent('Security');
}
```

The following Components come with OriginPHP:

- [Auth Component](/docs/controller/auth-component)
- [Session Component](/docs/controller/session-component)
- [Cookie Component](/docs/controller/cookie-component)
- [Flash Component](/docs/controller/flash-component)

For more information on this see the [components guide](/docs/controller/components).

## Rendering Views

By default, all views in your controller are rendered as html. Rendering takes place automatically for the controller and action. So if you if the user requests `/contacts/show/1` it will load the `View/Contacts/show.ctp` file.

One of the jobs of the controller is to send data to view, so the view can display this.

To send the data to the view, use the `set` method.

```php
class ContactsController extends ApplicationController
{
    public function view($id)
    {
        $user = $this->User->get($id);
        $this->set('user',$user);
    }
}
```

If the client requests with a header or extension for json or xml then the default format will be the changed for automatic rendering if serialzable data has been set. This makes the code easy and clean when working with multiple formats from the same controller. If serialize is not set then the view file will be rendered.

```php
class ContactsController extends ApplicationController
{
    public function view($id)
    {
        $user = $this->User->get($id);
        $this->set('user',$user);
        $this->serialize('user');
    }
}
```

Now if you go to `https://localhost:8000/contacts/view/10.json` it will display the result in json format, it works for xml as well.

You can also set an array of var keys to be serialized.

```php
$this->serialize(['user','profile']);
```

More information on how views work can be found in the [views guide](/docs/views).

### JSON Views

You can quickly and easily render JSON data using results returned from find or get operations, arrays of data and strings. The controller will automatically call the `toJson` on the objects.

```php
class ContactsController extends ApplicationController
{
    public function view($id)
    {
        $user = $this->User->get($id);
        $this->render(['json'=>$user]);
    }
}
```

You can also set the status code, this is handy when dealing with errors.

```php
class ContactsController extends ApplicationController
{
    public function view($id = null)
    {
        $json = [
            'errors'=>[
                'message' =>'Not Found'
            ]
        ];
        $this->render(['json'=>$json,'status'=>404]);
    }
}
```

 Remember there are quite a lot of status codes, including `418 I am a teapot`, many of the large enterprises who have professional apis only work with a small subset, these are a suggestion of the ones
 which you should remember.

| Status Code     | Definition                                                                                                |
| ----------------|---------------------------------------------------------------------------------------------------------- |
| 200             | OK (Success)                                                                                              |
| 400             | Bad Request (Failure - client side problem)                                                               |
| 500             | Internal Error (Failure - server side problem)                                                            |
| 401             | Unauthorized                                                                                              |
| 404             | Not Found                                                                                                 |
| 403             | Forbidden (For application level permissions)                                                             |

### XML Views

To render a xml view, just pass a result from the database, an array or a xml string. Data is converted using the XML utility. If you need to wrap some data in cdata tags, then make sure to include `use Origin\Utility\Xml` at the top of your file so you can call it directly.

```php
use Origin\Utility\Xml;
class PostsController extends ApplicationController
{
    public function lastest()
    {
        $data = [
           'post' => [
               '@category' => 'how tos', // to set attribute use @
               'id' => 12345,
               'title' => 'How to create an XML block',
               'body' =>  Xml::cdata('A quick brown fox jumps of a lazy dog.'),
                'author' => [
                    'name' => 'James'
                  ]
              ]
         ];
        $this->render(['xml'=>$data]);
    }
}
```

Here is another example using data returned from the find operation.

```php
class ContactsController extends ApplicationController
{
    public function all()
    {
        $results = $this->Contacts->find('all');
        $this->render(['xml'=>$results,'status'=>200]);
    }
}
```

## Callbacks

The Controller has callbacks which are run before and after actions, and even in-between such as before rendering or before redirecting. If you want the callbacks to be run in every controller, then add them to the `ApplicationController` and all child controllers will run this. Just remember to call the parent one as well.

### Before Action

This is called before the action on the controller (but after initialize), here you can access or modify request data, check user permissions or session data. If you need too you can even stop the action from continuing by throwing an exception or redirecting to somewhere else.

```php
class PostsController extends ApplicationController
{
    public function beforeAction()
    {
        if($this->Auth->isLoggedIn()){
            $this->Flash->info('Welcome back');
        }
    }
}
```

### After Action

This is called after the controller action has been run and the view has been rendered, but before the response has been sent to the client.

```php
class PostsController extends ApplicationController
{
    public function afterAction()
    {
        $this->doSomething();
    }
}
```

### Other Filters

There are two other filters in the controllers that you can use, and these are `beforeRender` and `beforeRedirect`.

## Redirecting

A common thing to do from a controller is to redirect. To redirect to a different url use the redirect method. You can pass either a string or an array.

```php
$this->redirect('/thanks');
$this->redirect('https://www.wikipedia.org');
```

You can also use an array, if you dont specify a controller, it will assume you want to redirect to the same controller. The array function here for redirect works the same way elsewhere in the framework when using an array for a URL.

```php
$this->redirect([
  'controller' => 'users',
  'action' => 'view',
  1024
]);
```

To use a query string, pass a `?` key with the array.

```php
$this->redirect([
  'controller' => 'users',
  'action' => 'view',
  1024,
  '?' => ['sort'=>'ASC','page'=>1]
]);
```

You can also use `#` for fragments, to scroll to a part of the page.

```php
['action'=>'index','#'=>'bottom']
```

OriginPHP also supports named parameters.

```php
$this->redirect([
  'controller' => 'orders',
  'action' => 'confirmation',
  'product' => 'ebook',
  'quantity' => 5
]);
```

which will generate a URL like

`/orders/confirmation/product:ebook/quantity:5`


## Logging

Logs are stored in `logs` and make it easy to debug and keep track of what is going on.
 OriginPHP uses a minimalistic file logger based upon the PSR 3 standard.

Each line in the log includes the date, channel, type of message and the message itself. 


```php
use Origin\Log\Log;
Log::error('something has gone wrong',['channel'=>'controller']);
```

That will produce a line like this in the log:

```
[2019-03-10 13:37:49] controller ERROR: something has gone wrong.
```

You can also use place holders

```php
Log::warning('{key} was null',['key'=>'foo']);
```

This will produce a line like this in the log:

```
[2019-03-10 14:25:50] application WARNING: foo was null.
```

You can call the following logging methods on the Logger object:

| Method            | Use case                                                                                          |
| ------------------|-------------------------------------------------------------------------------------------------- |
| debug             | Detailed debug information                                                                        |
| info              | Interesting events.                                                                               |
| notice            | Normal but significant events.                                                                    |
| warning           | Exceptional occurrences that are not errors                                                       |
| error             | Runtime errors that do not require immediate action but should typically be logged and monitored. |
| critical          | Critical conditions or events.                                                                    |
| alert             | Actions that must be taken immediately.                                                           |
| emergency         | The system is unusable.                                                                           |

## Paginating Records

When you are displaying multiple records to users, you will want paginate them, this is done in the background using the `PaginationComponent` and the `PaginationHelper`.

From the controller action that you want use pagination, call the controller method `paginate` this will load the component and helper, and paginate the records for you.


````php 
class BookmarksController extends ApplicationController
{
    // Default pagination settings to be used by all actions
    public $paginate = [
        'limit' => 20,
    ];

    function index(){
         $this->set('bookmarks', $this->paginate('Bookmark'));
    }
}
````

By default it will look at the default pagination settings in the controller paginate attribute. But you can also pass settings through the paginate method.

You can pass an array with the following keys, which are the same as used in Models.

- **fields**: is an array of fields that you want to return in the query
- **order**: is either a string or an array of how you want the data to be ordered.
- **group**: is for the database group query results.
- **limit**: this sets how many rows are returned.
- **callbacks**: If this is set to true, before or after then it will call the model callbacks.
- **associated**: An array of models that you want to load associated data for. You can also pass the model as key and array of config, e.g ['Tag'=>['fields'=>$field,'conditions'=>$conditions]]
- **joins**:  An array of join settings to join a table.

```php
    $settings = [
         'joins' =  [
            [
                'table' => 'authors',
                'alias' => 'authors',
                'type' => 'LEFT', 
                'conditions' => ['authors.id = articles.author_id']
            ]
    ];
```

### Initializer Trait

Controllers have the `InitializerTrait` enabled, enables you to use an initialization method on traits. See the [InitializerTrait guide](/docs/initializer-trait) for more information.