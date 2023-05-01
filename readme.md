# Light2
Project buat programmer yang males kalo ketemu bloatware.

Latar belakang project ini adalah bermula ketika mencari framework PHP, namun tidak menemukan framework dengan response time kurang dari 10ms meskipun hanya menampilkan halaman kosong, environtment diset ke production, serta sudah mengaktifkan OPcache & JIT Compilation. Dengan begitu, untuk mencapai tujuan tersebut terpaksa membuat sendiri dari scratch, hingga akhirnya jadilah project ini.

Light2, Light artinya ringan, sesuai namanya project ini harus bisa mencapai response time dibawah 10ms ketika tidak ada beban. Angka 2 artinya project kedua dan sebenarnya tidak ada hubungannya dengan versioning project ini, project ini berbasis pada project pertama ```AzurinCore``` yang secara tidak sengaja project tersebut hilang ketika akan dibongkar, akhirnya terpaksa membuat project baru lagi dari scratch. Light2 sendiri dibaca "Lhaa..itu" merupakan plesetan dari ejaan inggris "LightTwo" karena sewaktu berhasil menyelesaikan tujuan utama project ini merasa "Lhaa.. itu, itu baru bener!".

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
***Note**: host harus diakhiri tanpa tanda slash*

***Note**: jika menggunakan database SQLite maka database akan diarahkan ke folder store*

Saat environtment di set ke production, Kint & Whoops tidak akan di load, jadi jangan sampai masih ada kayak gini

```php
// ini helper kint, mirip var_dump(), bedanya lebih enak dibaca
d($entahApaItu);
```

yang lupa dihapus, karena nantinya bikin error.

## Routing
Route dapat diatur pada ```app/Config/Routes.php```. Cara kerja routing pada project ini adalah dengan cara ditoken.

URI akan ditoken berdasarkan karakter ```/```.
Hasil penokenan pertama akan menjadi route dan hasil penokenan berikutnya
akan menjadi params suatu callback route.
Jadi jangan meregister route yang menggunakan slash lebih dari satu.

Alasan ditoken daripada regex, karena performa nya kencengan pake token.

#### Contoh

```php
Router::add('/page', function () {
    // benar
});

Router::add('/page-to-something', function () {
    // benar
});

Router::add('/page/to/something', function () {
    // ini gak jalan :v
});
```

#### Controller Loader Factory

Meskipun bisa diisi apapun pada callback route nya, namun akan lebih baik jika diarahkan ke controller.

Misalnya pada pada URL ```http://project-gabut.com/user/azuria```

```php
Router::add('/user', function ($id = null) {
    Router::controller(User::class, $id); // param pertama diisi controller class, param kedua diisi parameter yang akan dikirim ke param controller method
});
```

Hasilnya, route nya adalah ```/user```, dan ```$id``` akan berisi ```azuria```.

Router akan mencoba memanggil controller sesuai dengan request method, dan ```Router::runNotFoundHandler()``` akan dijalankan jika method tidak ditemukan.

- ```GET``` dengan ```$id``` null akan memanggil method ```index()```
- ```GET``` dengan ```$id``` terisi akan memanggil method ```show($id)```
- ```POST``` akan memanggil method ```create()```
- ```PUT``` atau ```PATCH``` akan memanggil method ```update($id)```
- ```DELETE``` akan memanggil method ```delete($id)```

## View
View adalah template halaman HTML yang akan dirender dan dikirim ke client. Misalnya

```php
$data = [
    'date' => date('d-m-Y')
];

view('dashboard', $data);
```

data ```date``` akan dikirim ke view dashboard dan untuk menampilkannya adalah sebagai berikut.

```app/Views/dashboard.php```

```php
<h2> <?= $date ?> <h2>
```

```app/Views/dashboard.html```

```html
<h2> {{ date }} </h2>
```

Meskipun view mendukung file PHP dan HTML, direkomendasikan selalu menggunakan PHP karena performa jauh lebih baik.
Selain itu HTML hanya mendukung syntax ```{{ }}``` saja.

## Middleware
Normalnya middleware sebagai jembatan penghubung, tapi di PHP kalau dibuat begitu kesan nya
memaksakan, karena request/response bisa diakses langsung seperti menggunakan $_REQUEST atau header().

Jadi middleware di sini hanya digunakan untuk membuat suatu proses sebelum dan sesudah mengakses controller atau apapun di dalam callback route.

Misalnya untuk membatasi dashboard dengan login session, maka perlu membuat file 
```app/Middlewares/DashboardMiddleware.php```
kurang lebih seperti di bawah ini.

```php
<?php

namespace App\Middlewares;

class DashboardMiddleware
{
    public static function before(): void
    {
        // jika belum login akan di redirect ke halaman login
        session_start();
        if (!$_SESSION['isLoggedIn']) {
            header('Location: http://project-gabut.com/login');
            exit(0);
        }
    }

    public static function after(): void
    {
        // ...
    }
}
```

Lalu dapat diimplementasikan pada ```app/Config/Routes.php```.

```php
// menggunakan controller loader
Router::add('/dashboard', function ($id = null) {
    Router::controller(DashboardController::class, $id, DashboardMiddleware::class);
});

// atau bisa juga manual
Router::add('/dashboard', function ($id = null) {
    DashboardMiddleware::before();
    Router::controller(DashboardController::class, $id);
});
```

## Singleton
Membuat instance suatu class menjadi singleton/shared instance.

```php
// mending
$class = service(\App\DiFolderMana\ClassApapunItu::class);
$model = model('ApapunItuModel');

// ketimbang
$class = new \App\DiFolderMana\ClassApapunItu;
$model = new \App\Models\ApapunItuModel;
```

Kan project uenteng, jangan sampai malah jadi berat kayak framework di pasaran gara-gara kebanyakan instance.

## Model

Model di sini adalah layer yang berkomunikasi ke suatu tabel di database. Meskipun database
bisa connect darimana saja, alangkah baiknya kalau terstruktur rapi di folder ```app/Models/```

Misalnya membuat model untuk tabel user, maka buat file ```app/Models/UserModel.php``` dan isi minimal seperti di bawah ini.

```php
<?php

namespace App\Models;

use Light2\Model;

class UserModel extends Model
{
    public string $table = 'user'; // nama tabel di database
    public string $primaryKey = 'user_id'; // primary key tabel ini
}
```

Cara menggunakanya dapat sebagai berikut, misalnya model dipanggil dari controller.

```php
// memanggil UserModel
$user = model('UserModel');

// SELECT * FROM user
$result = $user->findAll();

// SELECT * FROM user WHERE user_id = 19
$result = $user->find(19);

// INSERT INTO user (user_id, user_name) VALUES (01, 'user_01')
$data = [
    'user_id' => 01,
    'user_name' => 'user_01',
];
$result = $user->insert($data);

// UPDATE user SET user_name = 'user_terabaikan' WHERE user_id = 4
$data = [
    'user_name' => 'user_terabaikan',
];
$result = $user->update($data, 4);

// DELETE FROM user WHERE user_id = 14
$result = $user->delete(14);
```

Method di atas merupakan method bawaan dari hasil extends Model,
selebihnya silahkan buat method tersendiri sesuai dengan kebutuhan.
Intinya, dengan menggunakan model, maka alur ke database menjadi lebih terstruktur dan mudah di maintenance.

## Koneksi Database

Cara menghubungkan ke database:

```php
$fluent = db_connect('mysql:dbname=fluentdb', 'user', 'password');

$fluent = db_connect(); // versi ringkas, mengambil parameter env.json
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
