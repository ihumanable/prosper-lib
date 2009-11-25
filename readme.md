# prosper #

Version 0.5 - 2009.11.16

by Matt Nowack

<ihumanable@gmail.com>

<http://ihumanable.com/blog/prosper/>

## intro ##

Prosper is a database abstraction layer for PHP. This means that you can now write your database code once, and have it work on any backend database. If you have an existing php library you can swap out your database access layer with prosper and voil&agrave;, your library now supports every backend that prosper supports.

## requirements ##

PHP 5.3+

## backends ##

- DB2
- Firebird / Interbase
- FrontBase
- Informix
- Ingres
- MaxDB
- MSql
- Microsoft SQL Server
- MySQL (through the use of mysql)
- MySQL (through the use of mysqli)
- Ovrimos File Database
- Paradox File Database
- Postgre SQL
- Sqlite3
- Sybase 

## install ##

Pull down the source code and put it in your PHP include path.  Then you can use it by requiring the proper adapter, the Query class and configuring Query to work with your backend.  There are two convenience files in the adapters folder, _all_.php and _common_.php.  _all_.php includes every adapter allowing for ultimate flexibility with trade off being some extra overhead.  _common_.php includes only the most common backend adapters mysql, postgre, microsoft sql server, and sqlite.  You can also simply choose the exact adapter you want, for example, for mysql you would include MySqlAdapter.

    define('PROSPER_PATH', "/path/to/prosper/lib/");
    require_once PROSPER_PATH . "/adapters/_all_.php";  //For simplicity include all adapters
    require_once PROSPER_PATH . "/Query.php";
    
    Prosper\Query::configure(Prosper\Query::MYSQL_MODE, "username", "password", "hostname", "schema");

Now you have a cross platform database abstraction layer that will protect you from sql injection attacks, automatically handle resources for you, and provide named and unnamed parameterization.  There is an extra parameter you can past to force an immediate connection, simply pass Prosper\Query::EAGER_LOADING as the last parameter to do so.  The default behaviour is to connect only if a query is actually issued, this allows you to include this configuration with limited overhead on pages that do not talk to the database.

## features ##


## usage ##

### select ###

### insert ###

### update ###

### delete ###
