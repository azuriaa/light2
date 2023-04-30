# Light2
Project buat programmer yang males kalo ketemu bloatware.

## Penjelasan
Ini adalah sebuah project sederhana dan memang dibuat sesederhana mungkin untuk meminimalisir latensi response.

Saat environtment di set ke production, Kint & Whoops tidak di load, jangan sampai masih ada kayak gini

```php
// ini helper kint, fungsinya mirip var_dump()
// bedanya kint lebih enak dibaca
d($entahApaItu); // harus dihapus ketika production
```

yang lupa dihapus, nantinya bikin error.

## Setup
Seperti project umumnya, front controller ada di public_html/index.php.

Ubah dan arahkan ROOTPATH ke root folder project yang ditentukan.

```php
// misalnya
define('ROOTPATH', realpath(__DIR__ . '/../project_gabut_v2/'));
```

Setelah itu set env.json pada root project nya, misalnya

```json
{
    "host": "project-gabut.com",
    "forceGlobalSecure": true,
    "environtment": "production",
    "pdo": {
        "driver": "sqlite",
        "database": "database.db",
        "username": null,
        "password": null
    }
}
```

## Routing
Route dapat diatur pada app/Config/Routes.php.

Cara kerja routing pada project ini menggubanan penokenan,
route akan ditoken berdasarkan karakter slash, setelah route ditoken slash berikutnya
akan menjadi params suatu callback route.

Jadi tidak diijinkan register route menggunakan slash lebih dari satu.

Lalu kenapa milih ditoken daripada pakai regex, ya karena speed nya kencengan pake token ketimbang regex.

### Contoh Benar
```php
Router::add('/page', function () {
    // ...
});

Router::add('/page-to-something', function () {
    // ...
});
```
### Contoh Salah
```php
Router::add('/page/to/something', function () {
    // ini gak jalan :v
});
```

## Load Class
Kalau load suatu class atau model, usahain pakai helper biar jadi singleton/shared instance.

```php
// mending
$class = service(\App\DiFolderMana\ClassApapunItu::class);
$model = model('ApapunItuModel');

// ketimbang
$class = new \App\DiFolderMana\ClassApapunItu;
$model = new \App\Models\ApapunItuModel;
```
Karena udah jadi project enteng, jangan sampai malah jadi berat kayak framework di pasaran karna kebanyakan instance.

## Connect Database
Project ini menggunakan FluentPDO.

Kalau di dalam model, begini caranya.
```php
// ini pakai builder
$resultA = $this->connect()->insertInto('table_a', ['key' => 'value1'])->execute();

// pake query manual juga bisa
$resultB = $this->connect()->getPdo()->query('SELECT * FROM table_b')->fetchAll();
```

Kalau di luar model, begini caranya.

```php
$fluent = db_connect('mysql:dbname=fluentdb', 'user', 'password');

// jika sesuai setingan env.json params cukup dikosongi
$fluent = db_connect(); 
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

### Using the Smart Join Builder

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

### CRUD Query Examples

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

## External Library
- Kint 5.0.1
- Whoops 2.15.2
- FluentPDO 2.2.4