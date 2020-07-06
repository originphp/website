---
title: Mailbox
description: Mailbox Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Mailbox

Mailbox enables you receive and process emails in your OriginPHP application. Mailboxes are like controllers which can accept email through email piping or by downloading emails through IMAP or POP3. Email messages are routed to the appropriate controller and parsed into a `Mail` object which allows to easily work with email messages. The `Mail` object also has built in `autoresponder` and `bounce` detection.

## Installation

Mailbox requires the `mailparse` and `imap` php extensions.

To install these extensions first update your system

```linux
$ sudo apt-get update
```

To install the PHP mailparse and IMAP extensions

```linux
$ sudo apt-get install php-mailparse php-imap
```

Load the database schema

```linux
$ bin/console db:schema:load mailbox
```

Mailbox uses [Queues](/docs/queue) to clean up the incoming emails, so if you have not loaded the schema for this then run

```linux
$ bin/console db:schema:load queue
```

## Mailbox Controllers

### Creating a Route 

First you need to create a route for your mailbox in your `config/mailbox.php` and then edit your `config/bootstrap.php` to load that file. 

Lets say you to route emails that are sent to `support@domain.com` to this mailbox.

```php
Mailbox::route('/^support@/i'', 'Support');
```

### Creating a Mailbox

```linux
$ bin/console generate mailbox Support
```

This will create the Mailbox

```php
namespace App\Mailbox;

use Origin\Mailbox\Mailbox;
use Exception;

class SupportMailbox extends Mailbox
{
    protected function initialize(): void
    {
        $this->onError('errorHandler');
    }

    protected function process() : void
    {
        // extract ticket number from email subject
        if(preg_match('/ticket: ([\d]{10}+)/i',$this->mail->subject,$matches){
            $ticketId = $matches[1];
            // do something with this
        }
    }

    protected function errorHandler(Exception $exception) : void
    {
    }
}

```

### Mail Object

When an email comes it it will be parsed into a `Mail` object

#### Properties

- to - a string or array of email addresses
- subject
- cc - a string or array of email addresses
- bcc - a string or array of email addresses
- from - an email address
- sender - an email address
- replyTo - an email address
- returnPath - an email address
- header - raw header
- body - raw body
- decoded - this is the decoded body, if the message has multiple parts it will get the highest priority one. (auto detected)
- textPart - gets a specific decoded body
- htmlPart - gets a specific decoded body

#### Methods

- isBounce - detects if the message is a bounced message
- isAutoresponder - detects if the message is an autoresponder
- isDeliveryStatusReport - if its a DSR
- hasAttachments - if the message has attachments
- attachments - an array of attachments. Attachments are saved to disk in a tmp directory and the array
- headers - an array of parsed and decoded headers
provides the name and path of the file
- recipients - an array of all email addresses from the to,cc and bcc headers
- contentType - gets the email content type
- multiPart - if the message has multiple parts
- hasHtml 
- hasText

### Callbacks & Hooks

The `Mailbox` object comes with the `intitialize`, `startup` and `shutdown` events as per the rest of the framework. 

You can also register callbacks to be called before or after the processing of the email message. 

```php
$this->beforeProcess('someFunction');
$this->afterProcess('anotherFunction');
$this->onSuccess('doSomethingElse');
```

The `initialize` hook is so you don't have to overwrite the `__construct` method.

The `beforeProcess` callback is called after the `startup` hook and the `afterProcess` is called after the `processs` method has been called but before `onSuccess` and `shutdown`. The `onSuccess` is only called if the message was not bounced.


### Bouncing Emails 

Sometimes you might want to bounce an email back to the sender, for example if the email is not from one of your users.

Call `bounceWith` and pass a name or full class name (with namespace) of the mailer to use, the `Mail` object will be passed to the `Mailer`

```php
$this->bounceWith('UnkownUser'); // automatically resolves
$this->bounceWith(UnkownUserMailer::class); // 
$this->bounceWith('App\Mailer\UnkownUserMailer');
```

When email is bounced back, processing will stop and any other `beforeProcess` or `afterProcess` callbacks will not be run.

Here is how to setup the `Mailer`, as you can see the `Mail` object will be passed to the execute method of the `Mailer`.

```php
namespace App\Mailer;

use App\Mailer\ApplicationMailer;
use Origin\Mailbox\Mail;

class UnkownUserMailer extends ApplicationMailer
{
    protected function execute(Mail $mail) : void
    {
        $this->mail([
            'to' => $mail->to,
            'subject' => 'Email address not recognized'
        ]);
    }
}
```

Here is an example of how to bounce an email if its not from one of your web application users.

```php
namespace App\Mailbox;

use Origin\Mailbox\Mailbox;
use App\Mailer\UnkownUserMailer;

class SupportMailbox extends Mailbox
{
    protected function initialize(): void
    {
        $this->loadModel('User');
        $this->beforeProcess('checkIsUser');
    }

    protected function checkIsUser(): void
    {
        if (!$this->User->findBy(['email'=>$this->mail->from])) {
            $this->bounceWith(UnkownUserMailer::class);
        }
    }
}
```

### Error Handling

Exceptions caught during the processing will be logged to `logs/application.log` and if your `Mailbox`  has a `onError` method this will be called.

You can create a method like this

```php
protected function onError(\Exception $exception) : void
{
    // do something
}
```

### Cleaning Mailboxes

When an email comes in either through piping or downloading, a `MailboxCleanJob` is created and scheduled to delete the email in 30 days, to prevent holding onto lots of personal data but at the same time keeping a trail for debugging or inspection. This is configured in `config/app.php`.

```php
[
    'mailboxKeepEmails' => '+30 days'
]
```

## Mailbox Downloading (IMAP + POP3)

To download messages using IMAP or POP3 setup your accounts, in `config/mailbox.php`

### Configuring Email Accounts

> Gmail won't allow access using traditional imap authentication from 2021, so you will have to use a different email provider for downloading emails.

Sample IMAP configuration for Gmail

```php
return [
    'default' => [
        'host' => 'imap.gmail.com',
        'port' => 993,
        'username' => 'username@gmail.com',
        'password' => 'secret',
        'encryption' => 'ssl',
        'validateCert' => true,
        'protocol' => 'imap',
        'timeout' => 30
    ]
];
```

Sample POP3 configuration for Gmail

```php
return [
    'default' => [
        'host' => 'pop.gmail.com',
        'port' => 995,
        'username' => 'username@gmail.com',
        'password' => 'secret',
        'encryption' => 'ssl',
        'validateCert' => true,
        'protocol' => 'pop3',
        'timeout' => 30
    ]
];
```

### Downloading Messages

You can download email messages using the `mailbox:download` command.

To download email messages for the `default` configured account

```linux
$ bin/console mailbox:download
```

To download mail for a different account

```linux
$ bin/console mailbox:download support
```

You can also download multiple accounts at the same time

```linux
$ bin/console mailbox:download sales support
```

### Configuring Cron Jobs

On Ubuntu to setup cron tab for the www-data user type in the following command:

```linux
$ sudo crontab -u www-data -e
```

Then add the following line to run the CRON job every minute

```linux
* * * * * cd /var/www/app.mydomain.com && bin/console mailbox:download
```

> Remember if you are going to use multiple email accounts then you will need to add these to the cron command

## Email Piping Configuration

To work with email piping you need to configure your mail server to pipe the emails to your application, the following instructions assume your web application is installed in the `/var/www` directory.

You will need to configure your email server to pipe the messages to the following script

```
/var/www/vendor/originphp/framework/src/Mailbox/pipe.php
```

### Postfix Email Piping Configuration

add the following line to `/etc/postfix/virtual`

```linux
example@yourdomain.com youralias
```

execute the `postmap` command after changing the virtual file.

```linux
sudo postmap /etc/postfix/virtual
```

Then add the following to `/etc/aliases`

```linux
youralias: "|/usr/bin/php -q /var/www/vendor/originphp/framework/src/Mailbox/pipe.php"
```

> Remember to check the path to the PHP executable is the same on your system

Now run the following commands to configure Postfix with your new settings

```linux
$ postmap /etc/postfix/virtual
$ /etc/init.d/postfix restart
$ newaliases
```

### Exim Email Piping Configuration

Add the following line to your `/etc/aliases`

```
script: |/var/www/vendor/originphp/framework/src/Mailbox/pipe.php
```

Then in your `/etc/exim.conf` replace the `address_pipe` section with the following

```
address_pipe:
  driver = pipe
  pipe_as_creator
```

### Sendmail Email Piping Configuration

Add the following line to your `/etc/aliases`

```
script: "|/var/www/vendor/originphp/framework/src/Mailbox/pipe.php"
```