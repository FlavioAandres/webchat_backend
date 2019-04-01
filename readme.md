# Web Chat Application 

Chat Web is a realtime web chat application where you can: 
* login and register
* List/Search users
* List/Search Chats
* Start a conversation with a user
* Create a Group 
* Chat with group
* Change name to Group 
* Upload Profile Photo 

Previous requirements
* php7.2
* curl-php

## Quick Start 
For using the demo application you can go to [DEMO](http://flavioaandres.com/webchat)

### Back-end Installation 

First, clone this repository 

```
git clone https://github.com/codeFlavioA/webchat_backend
```


Move to project folder and run command: 
```
composer install 
```

Now, config your .env file with your database connection settings 

Too, for the realtime notifications is necesary create a project on Pusher (http://linktopusher.com)
When u have  a Pusher account, type to .env file: 

PUSHER_APP_ID | PUSHER_APP_KEY | PUSHER_APP_SECRET

now, run the migrations for create the tables on database with
```
php artisan migrate
```

## ¡Ready!

if you aren't running this project on server's folder, use the php server with: 
```
php artisan serve
```

## Front-end Installation

First, clone the repositiory
```
git clone https://github.com/codeFlavioA/webchat
```

When the process has been end, change in apis/host.js  file the API HOST where the frontend application will request the data.

Now, run this command for start development server and run frontend 
    npm start

## Ussage
### Create a new Chat:  
for start a conversation click to + button located to right-side and choose a user for create a new conversation with him 
### Create a new Group: 
for start a new group click to + button for desplagate users list, Click to  Gorup Button and, choose all user you want. 
For finish to choose users and start group click to OK Button 
 
