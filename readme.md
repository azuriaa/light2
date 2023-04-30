# Light2
Project buat programmer yang males kalo ketemu bloatware.

Light2 ejaan "Light Two" dibaca "Lhaa..itu" adalah sebuah project sederhana dan memang dibuat sesederhana 
mungkin untuk meminimalisir latensi response.

Awalnya si pengembang ini mencari php framework tapi tidak menemukan yang bisa 
dibawah 10ms response. Akhirnya pengembang membuat project sendiri dari scratch. Setelah 2 tahun riset 
akhirnya terselesaikan tujuan project ini.

Light2, Light artinya ringan, angka 2 artinya project kedua dan sebenarnya tidak ada hubungannya 
dengan versioning project ini, Light2 sendiri dibaca "Lhaa..itu" merupakan plesetan dari 
ejaan inggris "LightTwo" karena pengembang ketika berhasil menyelesaikan tujuan utama project ini 
merasa "Lhaa.. itu, ini dia yang bener".

## Setup
Seperti project umumnya, front controller ada di public_html/index.php.

Ubah dan arahkan ROOTPATH ke root folder project yang ditentukan.

```php
// misalnya
define('ROOTPATH', realpath(__DIR__ . '/../project_gabut_v2/'));
```

Setelah itu sesuaikan setingan env.json yang ada pada ROOTPATH, misalnya

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

Saat environtment di set ke production, Kint & Whoops tidak akan di load, jadi jangan sampai masih ada kayak gini

```php
// ini helper kint, fungsinya mirip var_dump()
// bedanya kint lebih enak dibaca
d($entahApaItu); // harus dihapus ketika production
```

yang lupa dihapus, karena nantinya bikin error.

## Routing
Route dapat diatur pada app/Config/Routes.php.

Cara kerja routing pada project ini adalah dengan cara ditoken.

URI akan ditoken berdasarkan karakter slash seperti di bawah ini.

```/```

Hasil penokenan pertama akan menjadi route dan hasil penokenan berikutnya
akan menjadi params suatu callback route.

Jadi jangan register route yang menggunakan slash lebih dari satu.

Alasan ditoken daripada regex, karena performa nya kencengan pake token.

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