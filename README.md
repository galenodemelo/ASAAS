# ASAAS - Charge Management System
System made for a technical test for ASAAS full-stack developer position. The application should create, update, list and delete (CRUD) charges, as well as save customer information. The app should be connected with a MySQL database.

## Languages and technologies
- HTML5
- JavaScript (ES6)
- CSS3
- PHP (Object Oriented, using MVC architecture)
- MySQL (using MariaDB Engine and MySQL workbench for data modelling)
- Vendor bundles
  - [Google Material Design for UI](https://getmdl.io)
  - [Vanilla Masker](https://github.com/vanilla-masker/vanilla-masker) (for mask data input, by Fernando Fleury)
  - [CSS Reset](http://meyerweb.com/eric/tools/css/reset/)
  
## Basic working mean
The base structure is:
- `public_html` folder holds all the front-end assets, which can be accessed by HTTP request;
- `index.php` calls the `autoload.php` from `app`, a folder that can't be accessed by HTTP request;
- The `autoload.php` acts is responsible to load all the classes used;
- `index.php` also works as a controller, defining which controller (class) call and which action (method) to execute;
- Database is connected using PDO, preparing the statements before executing queries, preventing some security breaches, such as SQL Injection;
- `Helpers.php`contains some useful functions, such as sanitize input data to prevent HTML Injection.

## Todo list and known issues
- Forms don't prevent CSRF attacks. Need to create a token stored in session and validate it every time a form is sent;
- Data is validated only in JavaScript. The app must validate it on PHP too;
- There's no CPF or e-mail duplicate checker;
- There's no 404 or 500 error handling;
- Create a search based on relevante (FULLTEXT index with MATCH AGAINST) for name/email filter on charge list view;
- Applications is not responsive.
