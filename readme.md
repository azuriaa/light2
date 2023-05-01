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
Seperti project pada umumnya, front controller ada di public_html/index.php.

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
        "dsn": "sqlite:database.db",
        "username": null,
        "password": null
    }
}
```
***Note**: jika menggunakan database SQLite maka database akan diarahkan ke folder store`*

Saat environtment di set ke production, Kint & Whoops tidak akan di load, jadi jangan sampai masih ada kayak gini

```php
// ini helper kint, mirip var_dump(), bedanya lebih enak dibaca
d($entahApaItu);
```

yang lupa dihapus, karena nantinya bikin error.

## Routing
Route dapat diatur pada app/Config/Routes.php.

Cara kerja routing pada project ini adalah dengan cara ditoken.

URI akan ditoken berdasarkan karakter ```/```.

Hasil penokenan pertama akan menjadi route dan hasil penokenan berikutnya
akan menjadi params suatu callback route.

Jadi jangan meregister route yang menggunakan slash lebih dari satu.

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

Kalau di luar model atau di file manapun, begini caranya.

```php
$fluent = db_connect('mysql:dbname=fluentdb', 'user', 'password');

// jika sesuai setingan env.json params cukup dikosongi
$fluent = db_connect(); 
```

Cara membuat kueri:

```php
$query = $fluent->from('comment')
             ->where('article.published_at > ?', $date)
             ->orderBy('published_at DESC')
             ->limit(5);
```

Hasilnya:

```mysql
SELECT comment.*
FROM comment
LEFT JOIN article ON article.id = comment.article_id
WHERE article.published_at > ?
ORDER BY article.published_at DESC
LIMIT 5
```

Untuk mengambil data dari SELECT, 
Cukup melakukan loop dari array hasil kembalian:

```php
foreach ($query as $row) {
    echo "$row['title']\n";
}
```

### Smart Join Builder

Ini cara JOIN secara tradisional:

```php
$query = $fluent->from('article')
             ->leftJoin('user ON user.id = article.user_id')
             ->select('user.name');
```

Ribet, kan?

Jika tabel sudah menggunakan penamaan primary key dan foreign key yang sesuai,
maka kueri dapat dipersingkat menjadi:

```php
$query = $fluent->from('article')
             ->leftJoin('user')
             ->select('user.name');
```

Mendingan, tapi masih kurang. 

Lebih simple lagi kalau bisa **tanpa JOIN**:

```php
$query = $fluent->from('article')
             ->select('user.name');
```

Keren, kan? JOIN otomatis dibuatkan,
cukup dengan cara menambahkan foreign key tabel ke kolom yang dituju.

Dari ketiga cara di atas akan menghasilkan kueri yang sama seperti di bawah ini:

```mysql
SELECT article.*, user.name 
FROM article 
LEFT JOIN user ON user.id = article.user_id
```

##### Menutup Koneksi

 ```php
$fluent->close();
```

### Contoh Kueri CRUD

##### SELECT

```php
$query = $fluent->from('article')->where('id', 1)->fetch();
$query = $fluent->from('user', 1)->fetch(); // versi ringkas
```

##### INSERT

```php
$values = array('title' => 'article 1', 'content' => 'content 1');

$query = $fluent->insertInto('article')->values($values)->execute();
$query = $fluent->insertInto('article', $values)->execute(); // versi ringkas
```

##### UPDATE

```php
$set = array('published_at' => new FluentLiteral('NOW()'));

$query = $fluent->update('article')->set($set)->where('id', 1)->execute();
$query = $fluent->update('article', $set, 1)->execute(); // versi ringkas
```

##### DELETE

```php
$query = $fluent->deleteFrom('article')->where('id', 1)->execute();
$query = $fluent->deleteFrom('article', 1)->execute(); // versi ringkas
```

***Note**: kueri INSERT, UPDATE dan DELETE hanya akan dijalankan setelah memanggil `->execute()`*

## External Library
- Kint 5.0.1
- Whoops 2.15.2
- FluentPDO 2.2.4