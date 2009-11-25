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

Prosper was guided by several important design goals:

### fluid interface ###

If you've ever used jQuery then you are familiar with the idea of a fluid interface, its what allows you to write cool code like this.

    $('p').addClass('neat').show('slow');

Every function returns an object that you can call more functions on.  Prosper is written the same way, where in jQuery the main object is the jQuery object, in prosper it is the Query object.

    $sql = Prosper\Query::select()->from("user")->where("name = 'Matt'");
    
This let's you write your php code similarly to how you would write your sql code.

### transparency ###

Unlike ORM layers, prosper is supposed to be as transparent as possible.  When you write some code in prosper you should be able to know the sql it will produce, let's take a look at an example.

    Prosper\Query::configure(MYSQL_MODE, "username", "password", "localhost", "schema");
    $name = "Robert'); DROP TABLE Students;--"; //Little Bobby Tables <http://xkcd.com/327/>
    $sql = Prosper\Query::select()->from('students')->where('name = ?', $name);

This produces the following sql for mysql

    select * from `schema`.`students` where `name` = 'Robert\'); DROP TABLE Students;--'

Sane output, no magic, and as a bonus, you didn't just drop all your tables.

### cross platform ###

Prosper was designed to allow the backend to be quickly and easily changed.  Let's take a look at the output for the above Bobby Tables query in other sql dialects

 * MySQL - select * from \`schema\`.\`students\` where \`name\` = 'Robert\'); DROP TABLE Students;--'
 * Microsoft SQL Server - select * from [schema].[students] where [name] = 'Robert\'); DROP TABLE Students;--'
 * Postgre - select * from "schema"."students" where "name" = 'Robert\'); DROP TABLE Students;--'

This follows along the path of least surprise.  There are some surprises within prosper that can trace their roots to inconsistencies between rdbms' but the main goal has been to keep these to a minimum.

### opt out ###

Prosper is designed to be __the__ way for your application or library to talk to the database.  Part of acheiving this lofty goal is realizing that there will be times that despite its best efforts, prosper will be incapable of doing something you need it to do.  Prosper is prepared for this eventuality with the native function and the is_* family of functions.  It allows you to write code like this:

    if(Prosper\Query::is_oracle()) {
      Prosper\Query::native('some crazy oracle stuff')->execute();
    } else {
      Prosper\Query::some()->prosper()->code()->execute();
    }

Use prosper even when you can't use prosper, that's how you guarentee 100% coverage!

## usage ##

Check out the todo example application in the todo folder to see these used in practice, there is also in code documentation.

### select ###



#### basics ####

The select statement is the most basic statement used to pull data from the database.  This will return you a select statement ready to pull all the columns out

    Prosper\Query::select();

You can specify particular columns in the arguments if you wish like so

    Prosper\Query::select('name', 'age');

You can also provide aliased columns using associative arrays and mix and match this to your heart's content

    Prosper\Query::select(array('my_really_long_column_name' => 'col'), 'name', 'age');

#### from clause ####

After you've figured out what you want to pull, you just need to tell prosper what table to pull from.  This code tells it to look in the 'user' table.

    Prosper\Query::select()->from('user');

From accepts an optional second argument to use as a table alias.

    Prosper\Query::select()->from('stupid_naming_convention_user', 'user');

#### where clause ####

Now you can use a where clause to limit your results, where clauses are automatically parsed, there are a few rules to keep in mind.

 * Any literal should be surrounded by single quotes (')
 * Any non-literal value should be parameterized
 * Where clauses can be arbitrarily complex

Literal values are surrounded by single quotes, if the database backend uses a different convention it will be automatically converted for you.

    Prosper\Query::select()->from('user')->where("name = 'Matt'");

Literal values are not escaped, to prevent sql injection attacks, use parameterized values.  Prosper supports named and unnamed parameters.  For unnamed parameters simply use a question mark (?), you can provide any number of unnamed parameters.

    Prosper\Query::select()->from('user')->where('name = ? and age = ?', $name, $age);

Named parameters are any symbol that begins with a colon, they are pulled from an associative array like so:

    Prosper\Query::select()->from('user')->where('name = :name and age = :age', array('name' => $name, 'age' => $age));

This functionality is useful for pulling values out of larger arrays, like $_GET or $_POST, so if posting a form with a name and age field you can easily write

    Prosper\Query::select()->from('user')->where('name = :name and age = :age', $_POST);

You are allowed to mix and match named and unnamed parameters, although this is probably not a great idea.  The named parameter associative array should always be the last argument.

    Prosper\Query::select()->from('user')->where('name = :name and age = ?', 23, $_POST);

### insert ###

### update ###

### delete ###

## changelog ##

 * 2009.11.16 - v0.5 - Initial Release