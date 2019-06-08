---
title: Tutorial
description: PHP Development Tutorial with the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Tutorial

## Creating a new Project

Download and install [Composer](https://getcomposer.org/doc/00-intro.md), then run the following command to create a new project

```linux
$ composer create-project originphp/app blog
```

## Installing Requirements

OriginPHP requires a webserver and a database server, either MySQL or PostgreSQL. OriginPHP comes with a Dockerized
Development Environment, which is the best thing since sliced bread.

### Setting up the Dockerized Development Environment (DDE)

Install [Docker Desktop](https://www.docker.com/products/docker-desktop) then build the docker containers, this must be done from within the project folder. 

```linux
$ cd blog
$ docker-compose build
```

The container only needs to be built once, after this you will use the `up` and `down` commands to start and stop the docker container.

```linux
$ docker-compose up
```

Then open your web browser and go to [http://localhost:8000](http://localhost:8000)  which will show you a status page that all is working okay.


### Configure the Database Connection

Open the file `config/database.php.default` in your IDE, I recommend [Visual Studio Code](https://code.visualstudio.com/). Set the host, database, username and password as follows and then save a copy as `database.php`.

```php
ConnectionManager::config('default', [
    'host' => 'db', // Docker MySQL container
    'database' => 'blog',
    'username' => 'root',
    'password' => 'root',
    'engine' => 'mysql'
]);
```

> To access the MySQL server from within the Docker container, we need to use its name which is `db` and not `localhost`.


Lets create the database and test the configuration, run the following command to create the blog database.

```linux
$ bin/console db:create
```

### Accessing the Server (Linux Prompt)

Now the Docker containers are running, you will want to access the command line of the server.

```linux
$ docker-compose run app bash
```

## Start Coding

Lets create a Controller and a View.

```linux
$ bin/console generate controller Welcome index
```

This will create a few files

```linux
[ OK ] /var/www/src/Controller/WelcomeController.php
[ OK ] /var/www/src/blog/src/View/Welcome/index.ctp
[ OK ] /var/www/src/blog/tests/TestCase/Controller/WelcomeControllerTest.php
```

The Controller is where the logic is placed and the View is where the information that will be displayed to the user.

Open the `src/View/Welcome/index.ctp` and change the contents so it looks like this

```html
<h1>Welcome</h1>
<p>This is where interesting things happen</p>
```

Now lets set this has the home page open `config/routes.php` and change the following line

```php
Router::add('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
```

replacing with this

```php
Router::add('/', ['controller' => 'Welcome', 'action' => 'index']);
```

This is the routing file for web application. So now when you go [http://localhost:8000](http://localhost:8000) it will show you the welcome page instead of the status page.


### Building the App

First in your web browser goto [http://localhost:8000/articles/new](http://localhost:8000/articles/new). This will give you a missing controller error `ArticlesController class could not be found.` 

![Missing Controller Exception](/assets/images/missing-controller-exception.png)

Lets create a Controller

```linux
$ bin/console generate controller Articles
```

This will create the Articles controller

```php
namespace  App\Controller;
class ArticlesController extends AppController
{

}
```

Then go back to [http://localhost:8000/articles/new](http://localhost:8000/articles/new)

Now you will see a missing action exception error, this because the controller that was created is blank, and it does not have a public method called new.

![Missing Action Exception](/assets/images/missing-action-exception.png)

Edit the `/src/Controller/ArticlesController.php` file, so it looks like this

```php
namespace  App\Controller;
class ArticlesController extends AppController
{
    public function new(){

    }
}
```

Then go back to [http://localhost:8000/articles/new](http://localhost:8000/articles/new)

Now you will get a missing view exception error, this is because there is no view file.

![Missing View Exception](/assets/images/missing-view-exception.png)


Create a folder called `Articles` in the `View` folder, and save the file as `index.ctp`, e.g. `src/View/Welcome/index.ctp`

```html
<h1>New Article</h1>
```

Refresh the page, [http://localhost:8000/articles/new](http://localhost:8000/articles/new) and you should now see the New Article Title.

### Creating the Model

The next step is to create the Model so we can work with the database, you don't need to worry about setting up a table, the generator can do that for you because you are defining the schema.

```linux
$ bin/console generate model Article title:string body:text
```

This will output

```linux
[ OK ] /var/www/src/Model/Article.php
[ OK ] /var/www/tests/TestCase/Model/ArticleTest.php
[ OK ] /var/www/tests/Fixture/ArticleFixture.php
[ OK ] /var/www/db/migrate/20190606063934CreateArticleTable.php
```

Not only has it created the Model and test files, it has created a migration file so that you can create the table. 

### Running A Migration

Before you can use the migrations feature, you will need to create the migrations table in your database. Run the following command to create the table for you, it will load the schema from the `db/migrations.sql`.

```linux
$ bin/console db:schema:load migrations
```

If you look at the migration file `db/migrate/YYYYMMDDHHMMSSCreateArticleTable.php`, it will look like this

```php
class CreateArticleTableMigration extends Migration
{
    public function change()
    {
        $this->createTable('articles',[
            'title' => 'string',
            'body' => 'text',
            ]);
    }
}
```

Then to run the migration

```linux
$ bin/console db:migrate
```

Which will create the table and output:

```linux
CreateArticleTable [20190606063934]

 > CREATE TABLE articles (
 id INT NOT NULL AUTO_INCREMENT,
 title VARCHAR(255),
 body TEXT,
 PRIMARY KEY (id)
)

Migration Complete. 1 migrations in 0 ms
```

See the [Migrations Guide](/docs/development/migrations) for more information.

### Adding a Form

Now create a basic form for getting the input. Create the view for the new action.  The file for this would be `src/View/Articles/new.ctp`

> The <?= is the PHP shorthand for <?php echo 

```php
<h1>New Article</h1>
<?= $this->Form->create($article) ?>
<div>
    <?= $this->Form->label('title') ?>
    <?= $this->Form->text('title') ?>
</div>
<div>
    <?= $this->Form->label('body') ?>
    <?= $this->Form->textarea('body') ?>
</div>
<?= $this->Form->submit('Save') ?>
<?= $this->Form->end() ?>
```

In this tutorial I will showing you the basics, however the FormHelper has a `control` method, this creates a form input, wraps it up in a div (as used by most front end frameworks), adds the bootstrap classes, and error messages - if any. By using the control method instead the form would look completely different, but more on this in a moment.

See the [Form Helper](/docs/view/form-helper) for more information.

### Add Controller Code

Now add code to the Controller `/src/Controller/ArticlesController.php` to handle the form in the new method, it needs to take the POST data from the request and convert this to article [entity](/docs/model/entities) (a single record object). If the request is POST request, then it will save the record and redirect to the view action to show the article that it just created. It will set object in the view so that the FormHelper can use it.

```php
public function new()
{
    $article = $this->Article->new($this->request->data()); // create article entity (blank)

    if ($this->request->is('post')) {
        if ($this->Article->save($article)) {
            $this->Flash->success('Your article has saved');
            $this->redirect(['action'=>'view',$article->id]);
        } else {
            $this->Flash->error('Error saving your article');
        }
    }

    $this->set('article', $article); // set the article entity in the view
}
```

Go back to [http://localhost:8000/articles/new](http://localhost:8000/articles/new) to see this in action.

### Preventing Mass Assignment

Its very easy to manipulate forms using modern browsers, users can add fields and data to your forms before posting. It is possible they can post a value which might compromise your applications' integrity.

Change the first line which converts the request data into an article entity object, so that it passes an options array with the key `fields`. Regardless of what form inputs are submitted, only title and body will be accepted.

If you want to see the article object, then you can use `debug($article)` or `pr($article)` which will output values/objects in a nice format.

```php
public function new()
{
    $article = $this->Article->new($this->request->data(),[
            'fields'=> ['title','body']
        ]);;

    if ($this->request->is('post')) {
        if ($this->Article->save($article)) {
            $this->Flash->success('Your article has saved');
            $this->redirect(['action'=>'view',$article->id]);
        } else {
            $this->Flash->error('Error saving your article');
        }
    }

    $this->set('article', $article); // set the article entity in the view
}
```

### Viewing an Article

Open the articles controller and add the `view` method which will be used to show a article to a user.

```php
public function view($id)
{
    $article = $this->Article->get($id);
    $this->set('article', $article);
}
```

And now create the view file for this `src/View/Articles/view.ctp`

Open [http://localhost:8000/articles/new](http://localhost:8000/articles/new) and create an article, this
will redirect you to the view page.

### Listing all articles

Now open the controller again and add the `index` method

```php
public function index()
{
    $articles = $this->Article->find('all');
    $this->set('articles', $articles);
}
```

Now create the view file for this `src/View/Articles/index.ctp`

```php
<h1>Articles</h1>
<table>
  <tr>
    <th>Title</th>
    <th>Body</th>
    <th>&nbsp;</th>
  </tr>
  <?php foreach ($articles as $article): ?>
    <tr>
        <td><?= h($article->title) ?></td>
        <td><?= h($article->body) ?></td>
        <td><?= $this->Html->link('view', ['action' => 'view', $article->id]) ?></td>
    <tr>
 <?php endforeach; ?>
</table>
```

Open [http://localhost:8000/articles/index](http://localhost:8000/articles/index) to see the index view.

### Creating Links

So you have some basic actions such as creating and viewing an article as well listing all the articles in the database. So lets add a link from the home page

Open `src/View/Welcome/index.ctp` and change the contents so it looks like this

```html
<h1>Welcome</h1>
<p><?= $this->Html->link('My Blog', ['controller'=>'Articles']) ?></p>
```

Open `src/View/Articles/index.ctp` and add a link for creating a new article.

```php
<h1>Articles</h1>
<p><?= $this->Html->link('New article', ['action' => 'new']) ?></p>
<table>
  <tr>
    <th>Title</th>
    <th>Body</th>
    <th>&nbsp;</th>
  </tr>
  <?php foreach ($articles as $article): ?>
    <tr>
        <td><?= h($article->title) ?></td>
        <td><?= h($article->body) ?></td>
        <td><?= $this->Html->link('view', ['action' => 'view', $article->id]) ?></td>
    <tr>
 <?php endforeach; ?>
</table>
```

Go to the welcome page [http://localhost:8000](http://localhost:8000) and you now be able to access your blog via link.

### Add Validation

Let's add some validation you will need to edit the article Model, which can be found in `/src/Model/Article.php`. The validation rule should make sure that the article cannot be saved if there is not Article title.

```php
class Article extends AppModel
{
    public function initialize(array $config)
    {
        parent::initialize($config); // Always call the parent.
        $this->validate('title', 'notBlank');
    }
```

Now open the view file `src/View/Articles/new.ctp` again, change code to as follows, which has new lines to loop through the error messages and display them in a list. Note, this is just to show how it works, in moment I will show you how to use the `Form::control`, which does this all for you.

```php
<h1>New Article</h1>
<?php 
if($article->errors()){
    ?> 
    <h2>Validation Errors</h2> 
    <ul>
    <?php 
        foreach($article->errors() as $field => $messages) {
            foreach($messages as $message){
                ?>
                <li><strong><?= $field ?></strong> <?= $message ?></li>
                <?php
            }
        }
    ?>
    </ul>
    <?php
}  
?>
<?= $this->Form->create($article) ?>
<div>
    <?= $this->Form->label('title') ?>
    <?= $this->Form->text('title') ?>
</div>
<div>
    <?= $this->Form->label('body') ?>
    <?= $this->Form->textarea('body') ?>
</div>
<?= $this->Form->submit('Save') ?>
<?= $this->Form->end() ?>
```

Goto [http://localhost:8000/articles/new](http://localhost:8000/articles/new) and just press save without entering any data, you should get some validation errors.


### Form Control

The `FormHelper` control method wraps ups form elements in div, adds a label,classes and handles the validation. 
Open the new view `src/View/Articles/new.ctp` and change it as follows

```php
<h1>New Article</h1>
<?= $this->Form->create($article) ?>
<?= $this->Form->control('title') ?>
<?= $this->Form->control('body') ?>
<?= $this->Form->submit('Save',['class'=>'btn btn-primary']) ?>
<?= $this->Form->end() ?>
```

When you goto [http://localhost:8000/articles/new](http://localhost:8000/articles/new) you will see the difference, and if you try to save without entering data you will see how the validation errors are displayed.

### Editing Records

Open the articles controller `/src/Controller/ArticlesController.php` and add the edit method. This is slightly different since you need to load the article from the database, and then if its a post then you need to patch the article entity with the request data that was posted.


```php
public function edit($id=null)
{
    $article = $this->Article->get($id);

    if ($this->request->is('post')) {
        $article = $this->Article->patch($article,$this->request->data());
        if ($this->Article->save($article)) {
            $this->Flash->success('Your article has saved');
            $this->redirect(['action'=>'view',$article->id]);
        } else {
            $this->Flash->error('Error saving your article');
        }
    }

    $this->set('article', $article); // set the article entity in the view
}
```

Now create the view file `src/View/Articles/edit.ctp`, using the new view, just changing the title.

```php
<h1>Edit Article</h1>
<?= $this->Form->create($article) ?>
<?= $this->Form->control('title') ?>
<?= $this->Form->control('body') ?>
<?= $this->Form->submit('Save',['class'=>'btn btn-primary']) ?>
<?= $this->Form->end() ?>
```

And add an edit link to the index view `src/View/Articles/index.ctp`.

```php
<h1>Articles</h1>
<p><?= $this->Html->link('New article', ['action' => 'new']) ?></p>
<table>
  <tr>
    <th>Title</th>
    <th>Body</th>
    <th>&nbsp;</th>
  </tr>
    
  <?php foreach ($articles as $article): ?>
    <tr>
        <td><?= h($article->title) ?></td>
        <td><?= h($article->body) ?></td>
        <td><?= $this->Html->link('view', ['action' => 'view', $article->id]) ?> | <?= $this->Html->link('edit', ['action' => 'edit', $article->id]) ?></td>
    <tr>
 <?php endforeach; ?>
</table>
```


Open [http://localhost:8000/articles/index](http://localhost:8000/articles/index) and you will now have an edit link next to each record.

### Deleting Records

The last part, is the deleting of the records. You want to prevent people from calling a delete url directly (i.e a GET request).

```php
public function delete($id){
    $this->request->allowMethod(['post', 'delete']);
    $article = $this->Article->get($id);
    if ($this->Article->delete($article)) {
        $this->Flash->success('Article has been deleted');
    } else {
        $this->Flash->error('Article could not be deleted');
    }
    return $this->redirect(['action' => 'index']);
}
```

Goto [http://localhost:8000/articles/delete/1](http://localhost:8000/articles/delete/1) and you should get a method not allowed exception.

![Method Not Allowed Exception](/assets/images/method-not-allowed-exception.png)

Now edit the index view `src/View/Articles/index.ctp`, adding a link for deleting, but the link will be a post link (which is a link wrapped in a form)


```php
<h1>Articles</h1>
<p><?= $this->Html->link('New article', ['action' => 'new']) ?></p>
<table>
  <tr>
    <th>Title</th>
    <th>Body</th>
    <th>&nbsp;</th>
  </tr>
    
  <?php foreach ($articles as $article): ?>
    <tr>
        <td><?= h($article->title) ?></td>
        <td><?= h($article->body) ?></td>
        <td>
        <?= $this->Html->link('view', ['action' => 'view', $article->id]) ?> | 
        <?= $this->Html->link('edit', ['action' => 'edit', $article->id]) ?> |
        <?= $this->Form->postLink('delete', ['action' => 'delete', $article->id], ['confirm' => __('Are you sure you want to delete ?')]); ?>
        </td>
    <tr>
 <?php endforeach; ?>
</table>
```

Navigate to [http://localhost:8000/articles/index](http://localhost:8000/articles/index) and try the new delete link.

### Add Another Model

Run the generate command to create the model

```linux
$ bin/console generate model Comment article_id:integer name:string body:text
```

Run the migration to create the table

```linux
bin/console db:migrate
```

### Associate Models

Open the article model `src/Model/Article.php` and in the initialize method setup the association.

```php
public function initialize(array $config)
{
    parent::initialize($config);
    $this->validate('title', 'notBlank');
    $this->hasMany('Comment');
}
```

Open the comment `src/Model/Comment.php` and in the initialize method setup the association. 

```php
public function initialize(array $config)
{
    parent::initialize($config);
    $this->belongsTo('Comment');
}
```

Open the articles controller `/src/Controller/ArticlesController.php` and edit the view method, so that it retrieves associated records.

```php
public function view($id)
{
    $article = $this->Article->get($id,[
        'associated' => ['Comment']
    ]);
    $this->set('article', $article);
}
```

For more information see the [Associations Guide](/docs/model/associations).

### Create the Controller

```linux
$ bin/console generate controller Comments
```

Now add the `add` method to the comments controller file  `/src/Controller/CommentsController.php`.

```php
class CommentsController extends AppController
{
    public function add(){
        if($this->request->is('post')){
            $comment = $this->Comment->new($this->request->data());
            if($this->Comment->save($comment)){
                $this->Flash->success('Your comment has been saved');
            }
            else{
                $this->Flash->error('Your comment could not be added');
            }
            return $this->redirect(['controller'=>'Articles','action'=>'view',$comment->article_id]);
        }
    }
}
```

### Add the Comment Form

Edit `src/View/Articles/view.ctp` and adjust so it includes the comments form and the loop which lists all 
comments for this article.

```php
<h1><?= $article->title ?></h1>
<p><?= $article->body ?></p>

<h2>Comments</h2>
<strong>Add Comment</strong>
<?= $this->Form->create(null,[
        'url'=>['controller'=>'Comments','action'=>'add']
        ]); ?>
<?= $this->Form->control('name') ?>
<?= $this->Form->control('body')  ?>
<?= $this->Form->hidden('article_id',['value'=>$article->id]) ?>
<?= $this->Form->submit('Save comment') ?>
<?= $this->Form->end() ?>

<?php foreach ($article->comments as $comment): ?>
<p><strong><?= $comment->name ?>:</strong> <?= $comment->body ?></p>
<?php endforeach; ?>
```

Goto [http://localhost:8000/articles/index](http://localhost:8000/articles/index), and add an article then add comments to it.


### Scaffolding

You can generate a prototype for your application using the scaffolding feature, all it requires is that your database is setup and has tables.

Since in this tutorial you have already created the tables, we don't need to import the schema. But in future if you need too, then use the `db:schema:import` command first which will import the sql file from the db folder.

To generate the scaffolding for the articles and comments tables.

```linux
$ bin/console generate scaffold Article
$ bin/console generate scaffold Comment
```

Now goto [http://localhost:8000/articles/index](http://localhost:8000/articles/index), to see it in action. If you need to customize the scaffolding templates, copy the templates folder from the `vendor/originphp/framework` folder into your project folder, then customise as you see fit.

This concludes the tutorial, any suggestions how to make this better? Let me know.

