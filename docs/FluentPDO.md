## Getting Started

Create a new PDO instance, and pass the instance to FluentPDO:

```php
$pdo = new PDO('mysql:dbname=fluentdb', 'user', 'password');
$fluent = new \Envms\FluentPDO\Query($pdo);
```

Then, creating queries is quick and easy:

```php
$query = $fluent->from('comment')
             ->where('article.published_at > ?', $date)
             ->orderBy('published_at DESC')
             ->limit(5);
```

which would build the query below:

```mysql
SELECT comment.*
FROM comment
LEFT JOIN article ON article.id = comment.article_id
WHERE article.published_at > ?
ORDER BY article.published_at DESC
LIMIT 5
```

To get data from the select, all we do is loop through the returned array:

```php
foreach ($query as $row) {
    echo "$row['title']\n";
}
```

## Using the Smart Join Builder

Let's start with a traditional join, below:

```php
$query = $fluent->from('article')
             ->leftJoin('user ON user.id = article.user_id')
             ->select('user.name');
```

That's pretty verbose, and not very smart. If your tables use proper primary and foreign key names, you can shorten the above to:

```php
$query = $fluent->from('article')
             ->leftJoin('user')
             ->select('user.name');
```

That's better, but not ideal. However, it would be even easier to **not write any joins**:

```php
$query = $fluent->from('article')
             ->select('user.name');
```

Awesome, right? FluentPDO is able to build the join for you, by you prepending the foreign table name to the requested column.

All three snippets above will create the exact same query:

```mysql
SELECT article.*, user.name 
FROM article 
LEFT JOIN user ON user.id = article.user_id
```

##### Close your connection

Finally, it's always a good idea to free resources as soon as they are done with their duties:
 
 ```php
$fluent->close();
```

## CRUD Query Examples

##### SELECT

```php
$query = $fluent->from('article')->where('id', 1)->fetch();
$query = $fluent->from('user', 1)->fetch(); // shorter version if selecting one row by primary key
```

##### INSERT

```php
$values = array('title' => 'article 1', 'content' => 'content 1');

$query = $fluent->insertInto('article')->values($values)->execute();
$query = $fluent->insertInto('article', $values)->execute(); // shorter version
```

##### UPDATE

```php
$set = array('published_at' => new FluentLiteral('NOW()'));

$query = $fluent->update('article')->set($set)->where('id', 1)->execute();
$query = $fluent->update('article', $set, 1)->execute(); // shorter version if updating one row by primary key
```

##### DELETE

```php
$query = $fluent->deleteFrom('article')->where('id', 1)->execute();
$query = $fluent->deleteFrom('article', 1)->execute(); // shorter version if deleting one row by primary key
```

***Note**: INSERT, UPDATE and DELETE queries will only run after you call `->execute()`*

