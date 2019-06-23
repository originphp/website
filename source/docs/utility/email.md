---
title: Email Utility
description: Email Utility Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Email

The email class enables you to send emails easily through SMTP.


## Email Configuration

You setup your email accounts in your `config/email.php` we have created a template for you, just rename the file and fill your details.

```php
use Origin\Utility\Email;

Email::config(
    'default',[
        'host' => 'smtp.example.com',
        'port' => 25,
        'username' => 'demo@example.com',
        'password' => 'secret',
        'timeout' => 5,
        'ssl' => true,
        'tls' => false
        ]
    );
```

The keys for the config are as follows:

- *host*: this is smtp server hostname
- *port*: port number default 25
- *username*: the username to access this SMTP server
- *password*: the password to access this SMTP server
- *ssl*: default is false, set to true if you want to connect via SSL
- *tls*: default is false, set to true if you want to enable TLS
- *timeout*: how many seconds to timeout
- *domain*: When we send the HELO command to the sever we have to identify your hostname, so we will use localhost or HTTP_SERVER var if client is not set.
- *debug*: If set and is true the headers and message is rendered and returned (without sending via SMTP)

You can also pass keys such as `from`,`to`,`cc`,`bcc`,`sender` and `replyTo` this pass the data to its functions either as string if its just an email or an array if you want to include a name. Remember if you are going to automatically cc or bcc somewhere, then you have to next call addBcc or addCc to ensure that you don't overwrite this.

For example

```php
    [
        'from' => 'james@originphp.com'
        'bcc' => ['someone@origin.php', 'Someone']
    ]
```

If a config for `default` is found this will be used unless you specify something else with the `account`.

## Sending Emails

The default email sending behavior is to send a HTML message with a text version, which is a best practice and reduces the risk of your email ending up in spam folders. If you don't provide a text version of the message, the Email utility will automatically convert your HTML into a text version.

Email templates are stored in the `View/Email` folder, use the template method to set the name and use the set method to send variables to the templates.

To send an email (with both html and text versions):

```php
use Origin\Utility\Email;
$Email = new Email();
$Email->to('somebody@originphp.com')
    ->from('me@originphp.com')
    ->subject('This is a test')
    ->template('welcome')
    ->set(['first_name'=>'Frank'])
    ->send();
```

The above example will load the `View/Email/html/welcome.ctp` and if the text version exists, that too from `View/Email/text/welcome.ctp`. If the text version does not exist then it will create a text version from the html message.

To change the format of the email to either `text` or `html` only, you need to set the format.

```php
use Origin\Utility\Email;
$Email = new Email();
$Email->to('somebody@originphp.com')
    ->from('me@originphp.com')
    ->subject('This is a test')
    ->template('welcome')
    ->set(['first_name'=>'Frank'])
    ->format('text')
    ->send();
```

Here is how you use variables in the email templates:

```php
// View/Email/html/welcome.ctp
<p>Hi <?= $first_name ?></p>
<p>How is your day so far?</p>
```

The template method also accepts plugin syntax, so to load a template from a plugin  folder just add the plugin name followed by a dot then the template name.

```php
use Origin\Utility\Email;
$Email = new Email();
$Email->to('somebody@originphp.com')
    ->from('me@originphp.com')
    ->subject('This is a test')
    ->template('ContactManager.reset_password')
    ->send();
```


## Sending emails without Templates

### Send an Email

To send an email with both html and text versions:

```php
use Origin\Utility\Email;

    $Email = new Email();
    $Email->to('somebody@originphp.com')
        ->from('me@originphp.com')
        ->subject('This is a test')
        ->textMessage('This is the text content')
        ->htmlMessage('<p>This is the html content</p>')
        ->send();
```

### Sending HTML Only Email

To send a HTML only email, you need to tell the Email utility use the html format.

```php
use Origin\Utility\Email;

    $Email = new Email();
    $Email->to('somebody@originphp.com')
        ->from('me@originphp.com')
        ->subject('This is a test')
        ->htmlMessage('<p>This is the html content</p>')
        ->format('html')
        ->send();
```

### Sending Text Email

To send a text email, you need to tell the Email utility use the text format.

```php
use Origin\Utility\Email;
$Email = new Email();
$Email->to('somebody@originphp.com')
    ->from('me@originphp.com')
    ->subject('This is a test')
    ->textMessage('This is the text content')
    ->format('text')
    ->send();
```

## Switching Email Accounts

To change the email account (accounts are setup using the `Email::config()` usually in the `config/email.php`)

```php
use Origin\Utility\Email;
$Email = new Email();
$Email->to('somebody@originphp.com')
    ->from('me@originphp.com')
    ->subject('This is a test')
    ->textMessage('This is the text content')
    ->account('gmail')
    ->send();
```

You can also setup the config during the creation of the Email object.

```php
use Origin\Utility\Email;
$config = [ 
    'host' => 'smtp.gmail.com',
    'port' => 465,
    'username' => 'email@gmail.com',
    'password' => 'secret',
    'ssl' => true,
    'tls' => false
];
$Email = new Email($config);
```

## Adding Attachments

To add attachments

```php
use Origin\Utility\Email;
$Email = new Email();
$Email->to('somebody@originphp.com')
    ->from('me@originphp.com')
    ->subject('This is a test')
    ->textMessage('This is the text content')
    ->addAttachment($filename1)
    ->addAttachment($filename2,'Logo.png')
    ->format('text')
    ->send();
```

