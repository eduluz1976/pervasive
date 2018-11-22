# Pervasive

## Purpose

The `pervasive` is a library that allows wrap objects and 
inject some behaviours in certain methods, such in events like 
`pre`, `pos` and `guard`. This allows test custom flows 
dynamically, without touch in code already in production.

## Use





### Examples

Considering the class `UserController`, 

```php
<?php

class UserController {
    
    public function addNewUser() {
        // ...
    }
    
} 

```
This method receives the user's data, validate, insert into DB, and return a JSON message to User Interface give the feedback to user.
 
Let's suppose we want check if this user already exists in a 3rd party service provider, replacing the informed email by
another one, given by service provider.

Note: Assuming that have an object of class ``Request`` that manage all requests, and is accessible globally.

In some script between bootstrap and the point that execute the Controller requests:

```php
<?php 

use \eduluz1976\pervasive\Builder;
use \eduluz1976\pervasive\Mock;

// Wrapping UserController into Builder 
Builder::design(UserController::class)
    ->_() // this calls the MockBuilder, that add dynamic behaviour on wrapped classes.
    ->addPreFunction(
        'addNewUser',
        function (Mock $container = null) {
            
            $email = Request::get('email');
            
            $newEmail = ExternalServiceProviderAPI::checkEmail($email);
            
            if ($newEmail) {
                Request::set('email', $newEmail);
            }            
        }
    );

 
 ```


In file that execute the controller:

```php
<?php

// Getting a new instance of the wrapped class 
$controller = Builder::build ( UserController::class ) ;

$controller->addNewUser();        
        
```

