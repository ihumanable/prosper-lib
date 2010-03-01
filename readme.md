# prosper #

Version 0.8 - 2010.03.01

by Matt Nowack

<ihumanable@gmail.com>

<http://prosper-lib.com>

## intro ##

Prosper is a database abstraction layer for PHP. This means that you can now write your database code once, and have it work on any backend database. If you have an existing php library you can swap out your database access layer with prosper and voil&agrave;, your library now supports every backend that prosper supports.  Prosper is completely free software, it is released under an Unlicense as described in the UNLICENSE file, please refer to <http://unlicense.org>

## requirements ##

PHP 5.3+

## backends ##

- DB2
- DBase
- Firebird / Interbase
- FrontBase
- Informix
- Ingres
- MaxDB
- MSql
- Microsoft SQL Server (through the built-in VSDX)
- Microsoft SQL Server (through Microsoft's Native Extension)
- MySQL (through the use of mysql)
- MySQL (through the use of mysqli)
- Ovrimos File Database
- Paradox File Database
- Postgre SQL
- Sqlite3
- Sybase 

## install ##

Pull down the source code and put it in your PHP include path.  Then you can use it by requiring the proper adapter, the Query class and configuring Query to work with your backend.  There are two convenience files in the adapters folder, \_all\_.php and \_common\_.php.  \_all\_.php includes every adapter allowing for ultimate flexibility with the trade off being some extra overhead.  \_common\_.php includes only the most common backend adapters mysql, postgre, microsoft sql server, and sqlite.  You can also simply choose the exact adapter you want, for example, for mysql you would include MySqlAdapter.

    define('PROSPER_PATH', "/path/to/prosper/lib/");
    require_once PROSPER_PATH . "/adapters/_all_.php";  //For simplicity include all adapters
    require_once PROSPER_PATH . "/Query.php";
    
    Prosper\Query::configure(Prosper\Query::MYSQL_MODE, "username", "password", "hostname", "schema");

Now you have a cross platform database abstraction layer that will protect you from sql injection attacks, automatically handle resources for you, and provide named and unnamed parameterization.  There is an extra parameter you can pass to force an immediate connection, simply pass `Prosper\Query::EAGER_LOADING` as the last parameter to do so.  The default behaviour is to connect only if a query is actually issued, this allows you to include this configuration with limited overhead on pages that do not talk to the database.

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

### prepared statements ###

With version 0.7 Prosper uses prepared statements for all adapters that support prepared statements.  Prepared statements are transparent to use, prosper will use prepared statements where available falling back to properly escaped string interpolation for adapters that don't support prepared statements.

### explicit transactions ###

With version 0.7 Prosper now supports explicit transaction management.  With the introduction of `begin()` `commit()` and `rollback()` prosper provides a finer tuned control over transaction management.  Some backends do not support transactions, the `begin()` `commit()` and `rollback()` functions are valid but do nothing on these platforms.  To check if a platform supports transactions you can use the `has_transactions()` function like so

    if(Prosper\Query::has_transactions()) {
      //Do transactional operation
    } else {
      //Do non-transactional operation
    }

### iterable ###

With version 0.8 Prosper now supports iterating over a result set, coupled with lazy evaluation, this makes a query more like a first-class object.  Before 0.8 it was necessary to call either the `execute()` or the `verbose()` function to retreive the result set of a query.  This is no longer, the case, the Query object can now be iterated over in a straight forward manner.

	$query = Prosper\Query::select()->from('example');
	foreach($query as $row) {
		//do something with the row
	}

This can be useful for building up different queries for different scenarios.

	$query = Prosper\Query::select()->from('example');
	if($_GET['something'] == "some_case") {
		$query->where('param = ?', $param);
	} else {
		$query->order('sort_order');
	}
	
	foreach($query as $row) {
		//do something with the row
	}

### rebinding ###

Just like with a parameterized query, as of version 0.8 Prosper supports the concept of rebinding prepared statements.  This is accomplished with the `rebind()` function.

	$query = Prosper\Query::select->from('example')->where('something = ?', 1);
	
	//Effectively executes SELECT * FROM \`example\` where \`example\`.\`something\` = '1'
	$query->execute();
	
	$query->rebind(5);
	
	//Effectively executes SELECT * FROM \`example\` where \`example\`.\`something\` = '5'
	$query->execute();
	
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

For all examples we will assume that there is a table named `user` with columns `id`, `name`, and `age`

### select ###

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

This functionality is useful for pulling values out of larger arrays, like $\_GET or $\_POST, so if posting a form with a name and age field you can easily write

    Prosper\Query::select()->from('user')->where('name = :name and age = :age', $_POST);

You are allowed to mix and match named and unnamed parameters, although this is probably not a great idea.  The named parameter associative array should always be the last argument.

    Prosper\Query::select()->from('user')->where('name = :name and age = ?', 23, $_POST);

#### join clauses ####

Joining tables allows you to pull data from multiple sources and place return them in a single record.  There are various ways to join tables in prosper, the most common are built in.

 * Left joins are invoked by using the `left()` function
 * Inner joins are invoked by using the `inner()` function
 * Outer joins are invoked by using the `outer()` function
 * Standard (Cartesian) joins are invoked by using the `join()` function

The join syntax is fairly simple, let's look at an example

    Prosper\Query::select()->from('user')->join('permission')->on('user.id = permission.user_id');
  
The `on()` function allows you to provide an arbitrarily complex join condition, it is parsed by the same tokenizing parser that processes the where clause.

Left, inner, outer, and standard joins cover the majority of joins you will encounter in the wild, but in keeping with the spirit of the best way for prosper to get 100% coverage is to allow you to opt out when needed, you can define your own joins.  The magic here is the `specified_join()` function, `left()`, `inner()`, `outer()`, and `join()` are all defined in terms of `specified_join()`.

    function specified_join($table, $alias = "", $type= "join")
  
Let's say you wanted to have a `RIGHT OUTER JOIN` for some unholy reason.  Prosper has no built in support for this, but you can use the `specified_join()` like so

    Prosper\Query::select()->from('user', 'u')->specified_join('permission', 'p', 'RIGHT OUTER JOIN')->on('u.id = p.user_id');

This allows you to write arbitrary joins.  

#### limit clause ####

Does your table have half a million records, probably don't want them all back.  You can limit the amount of results by using the `limit()` function, here is how you would return the first 10 users

    Prosper\Query::select()->from('user')->limit(10);

This function also allows for easy pagination with an optional offset, lets show how to get a few pages of data

    Prosper\Query::select()->from('user')->limit(10);     //Get the first 10 users (page 1)
    Prosper\Query::select()->from('user')->limit(10, 10); //Get the first 10 starting at 10 (page 2)
    Prosper\Query::select()->from('user')->limit(10, 20); //Get the first 10 starting at 20 (page 3)
  
#### order clause ####

Ordering data is done through the `order()` clause.  The simplest form of the order function is the column ordering.  Here is how to return all the users ordered by name (ascending)

    Prosper\Query::select()->from('user')->order('name');

The default is to order ascending, you can override this behavior by passing an ordering

    Prosper\Query::select()->from('user')->order('name', 'desc');

You can perform multi-column orderings by passing an associative array, let's order by age descending and then by name ascending

    Prosper\Query::select()->from('user')->order(array('age' => 'desc', 'name' => 'asc'));

This should give you all the control needed to order your results.

#### group clause ####

Grouping of data is done through the `group()` clause.  Grouping can be done on multiple columns, the `group()` function takes a vararg.

	Prosper\Query::select()->from('user')->group('age');
	
#### having clause ####

Having clauses can now be done through the `having()` clause.  This is used in conjunction with the `group()` function to create complex queries.  Here is how we would return all the states that have at any users older than 50.

	Prosper\Query::select()->from('user')->group('state')->having('MAX(age) > 50');

### insert ###

Inserting information into a table is simple, let's add a user to our sample user table

    Prosper\Query::insert()->into('user')->values(array('name' => 'Matt', 'age' => '23'));

Inserting is fairly straightforward, the `into()` function takes the table name to insert into and the `values()` function can be called in one of two ways.

#### values with associative array ####

In the first example we used an associative array to insert values into a table.  This can be useful for when you wish to perform some preprocessing and checking before inserting information

    $name = $_POST['first_name'] . " " . $_POST['middle_initial'] . " " . $_POST['last_name'];
    $age  = ticks_to_years(mktime() - strtotime($_POST['birthdate']));
    Prosper\Query::insert()->into('user')->values(array('name' => $name, 'age' => $age));

But wait, didn't we just open ourselves up to a SQL injection attack?  No worries the values will automatically be sanitized by prosper.

#### values from an associative array ####

Sounds very similar doesn't it, but it is a bit different.  This calling method is useful for inserting some of the values in an array but not all.  Let's look at an example

    Prosper\Query::insert()->into('user')->values('name', 'age', $_POST);
    
This is commonly read as "insert into user values name and age from post."  This is a shortcut to writing the following

    Prosper\Query::insert()->into('user')->values(array('name' => $_POST['name'], 'age' => $_POST['age']))

Again, since prosper takes care of sanitizing these values for us we can take input straight from the user and pass it off to prosper.
    
### update ###

Updating information in a database table uses a lot of what we already know, let's change the new user's age, because I just had a birthday ;)

    Prosper\Query::update('user')->set(array('age' => '24'))->where("name = 'Matt'");
    
The `update()` function takes the tablename to update in, the where clause is the same where clause we saw up in the select section.  Let's take a look at the `set()` function, if you were paying attention to the `values()` function you will feel right at home here

#### set with associative array ####

You can explicitely pass an associative array of key value pairs where keys are columns and values are, well, values to the set funciton

    $id   = $_POST['id'];
    $name = $_POST['first_name'] . " " . $_POST['middle_initial'] . " " . $_POST['last_name']
    $age  = ticks_to_years(mktime() - strtotime($_POST['birthdate']));
    Prosper\Query::update('user')->set(array('name' => $name, 'age' => $age))->where('id = ?', $id);
    
That sure did look familiar.

#### set from an associative array ####

Let's assume you have a nice pretty update form with a hidden id field, a name field, and an age field.

    Prosper\Query::update('user')->set('name', 'age', $_POST)->where('id = :id', $_POST);
    
The magic of the "pull from" form of `set()` and named parameters makes this an easy task indeed.

### delete ###

Deletes are almost identical to selects, except instead of returning the selected records, it deletes them.

    Propser\Query::delete()->from('user')->where("name = 'Matt' and age = '24'");
    
That was simple.

## roadmap ##

- v0.8
    - Rewrite incorrect phpDoc to be phpDoc compliant
    - Write unit tests
    - Perform more real world testing for adapters
    - Improve official documentation
    - Perform load testing
    - Finalize schema reflection api
    - Finalize table management api
    - Possible modularization
    - Clean up Tokenizer
    
## changelog ##

- 2010.03.01 - v0.8
	- Proper escaping for Sybase
	- Proper escaping for Microsoft SQL Server
	- New Native Adapter for Microsoft SQL Server
	- Added the `group()` and `having()` functions
	- Architectural changes to allow concurrent query generation
	- Added 142 Unit Tests
	- Queries can have their parameters rebound now
	- Query implements the IteratorAggregate interface
	- Prosper is officially Unlicensed
- 2009.12.22 - v0.7
    - Added prepared statement support
    - Added transaction management facilities
    - Changed how the MS-SQL windowing function worked to support SQL Server 2005
    - Improved escaping for string literal interpolation to use platform specific functions
    - Added the DBase Adapter
    - Cleaned up lots of phpDoc to be compliant
    - Experimental schema reflection functionality in MySqlAdapter
- 2009.12.03 - v0.6 
    - Changed the configuration system to take constants instead of string literals
    - Added support for the older mysql library in addition to the mysqli library
    - Refactored adapters internally for more concise and logical class layout
    - Moved project to GitHub
    - Adapters are lazy loading now, this allows the configuration to be done with minimal overhead.  This functionality also allows for unit testing
    - Added phpDoc Documentation to the project
    - Added this frontpage documentation and project roadmap
- 2009.11.16 - v0.5 - Initial Release

