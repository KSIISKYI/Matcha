# Matcha

### This project is about creating a dating website.

A user will then be able to register, connect, fill his/her profile, search and look into
the profile of other users, like them, chat with those that “liked” back.

**Please find subject with full description :point_right: [here](matcha.en.pdf) :point_left:.**

## Used technologies:

:point_right: Slim framework

:point_right: Sockets (Realtime chat, notifications)

:point_right: Custom search

:point_right: User management (create user, send confirmation email, edit profile, upload files, authorization)

:point_right: Chat management (create message, edit message, delete message)

<img src="/screenshots/screenshot1.png" alt="project screenshot" title="project screenshot 1" width="700">
<img src="/screenshots/screenshot2.png" alt="project screenshot" title="project screenshot 1" width="700">
<img src="/screenshots/screenshot3.png" alt="project screenshot" title="project screenshot 1" width="700">
<img src="/screenshots/screenshot4.png" alt="project screenshot" title="project screenshot 1" width="700">
<img src="/screenshots/screenshot5.png" alt="project screenshot" title="project screenshot 1" width="700">
<img src="/screenshots/screenshot6.png" alt="project screenshot" title="project screenshot 1" width="700">
<img src="/screenshots/screenshot7.png" alt="project screenshot" title="project screenshot 1" width="700">
<img src="/screenshots/screenshot8.png" alt="project screenshot" title="project screenshot 1" width="700">
<img src="/screenshots/matcha_presentation.gif" alt="project screenshot" title="project screenshot 1" width="700">

## Installation:
1. Clone the repository:
```
git clone https://github.com/KSIISKYI/Matcha.git
```
2. Move to root application directory:
```
cd Matcha/src/
```
3. Install composer:
```
composer install
```
4. Rename .env.example to .env:
```
mv .env.example to .env
```
5. Set credentials for db and google auth
6. Make migrations:
```
php database/migrations/mysql.php
```
7. Run socket server for chat and notifications:
```
php app/Service/Chat/server.php
```
8. Run http server on 8000 port:
```
php -S localhost:8000 -t public
```
#####Credentials for logging into the test account
```
username: my_user
password: Zxcvbnm2
```


#### Enjoy :joy:
