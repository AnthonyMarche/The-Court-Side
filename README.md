# Project 3 Symfony - Origins Digital - The Court Side

## Presentation

This is our third and final project at the Wild Code School. The team is composed of Alexis Boucherie, Anthony Marché,
Yazid Hamzi and Naomie Atil. We are part of the PHP course.
Our client desired a user-friendly website with an intuitive design.

The Court Side is a direct-to-consumer video content free platform specialized in sports where the users need to
register in order to access the premium content.

## Getting Started for Users

### Prerequisites

1. Check if composer is installed.
2. Check if yarn & node are installed.

### Install

1. Clone this project.
2. Run `composer install`.
3. Run `yarn install`.

#### Modify the .env file

1. Create an .env.local file.
2. Add your credentials
   on `DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7&charset=utf8mb4"`.

### Create and Get the database

1. php bin/console doctrine:database:create.
2. php bin/console doctrine:migrations:migrate.

#### Generate the fixtures and videos

1. php bin/console doctrine:fixtures:load.
2. Our fixtures, operates with the videos
   uploaded [here](https://drive.google.com/drive/folders/1nupHgasIGT-MW0Z0LsW0dQ4ZdwnUYoyd?usp=share_link). <br /> You have to download the videos and move them to the directory `assets/fixtures_videos`.

### Working

1. Run `symfony server:start` to launch your local php web server.
2. Run `yarn dev-server` to launch your local server for assets and `yarn build` for the deployment.

### Mail Verification

1. In your .env.local file modify the `# MAILER_DSN=null://null` with the email delivery platform of your choice.
   After register, you will be sent a confirmation email. When forgetting a password, you will be notified to create a
   new one. And also, if you decide to subscribe to our newsletter, you will be alerted.


### Windows Users

If you develop on Windows, you should edit you git configuration to change your end of line rules with this command:

`git config --global core.autocrlf true`

The `.editorconfig` file in root directory do this for you. You probably need `EditorConfig` extension if your IDE is
VSCode.


### Run locally with Docker

1. Fill DATABASE_URL variable in .env.local file with
   `DATABASE_URL="mysql://root:password@database:3306/<choose_a_db_name>"`
2. Install Docker Desktop an run the command:

```bash
docker-compose up -d
```

3. Wait a moment and visit http://localhost:8000

## Built With

* [Symfony](https://github.com/symfony/symfony)
* [GrumPHP](https://github.com/phpro/grumphp)
* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
* [PHPStan](https://github.com/phpstan/phpstan)
* [PHPMD](http://phpmd.org)
* [ESLint](https://eslint.org/)
* [Sass-Lint](https://github.com/sasstools/sass-lint)

## Application

You will first access to the homepage. From there, you are able to log in using the predefined `SuperAdmin` account whom
credentials are `superadmin@me.fr` and `admin` as password from there you will be able to manage the users profiles and
also you can upgrade a user to the admin role along with downgrading an admin to a user. This is exclusive to the super
admin. To access the `Admin` account, your credentials will be `admin@me.fr` with `admin` as password.
Once connected, you will have access to the premium content and from the admin page you will be able to create, upload
videos and teasers or even delete content for example.

## License

MIT License

Copyright (c) 2019 aurelien@wildcodeschool.fr

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
