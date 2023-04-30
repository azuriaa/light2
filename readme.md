# Light2
Project buat programmer yang males kalo ketemu bloatware.

## Penjelasan
Ini adalah sebuah project sederhana dan memang dibuat sesederhana mungkin untuk meminimalisir latensi response.
Saat environtment di set ke production, Kint & Whoops tidak di load, jangan sampai masih ada
```php
d($entahApaItu);
```
yang lupa dihapus/dikomen, nantinya bikin error.

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
Kalau load suatu class atau model, usahain pakai helper biar jadi singleton/shared instance kalo dipanggil berkali kali.
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
Gunakan helper db_connect().
```php
// ngambil parameter dari env.json
$db = db_connect();

// bisa juga manual kalau mau beda setingan dari env.json
$db = db_connect('mysql:host=localhost;dbname=light', 'username', 'password');

// langsung bisa dipakai
$db->insertInto('table_a', ['key' => 'value1'])->execute();
```
Kalau di dalam model, caranya begini.
```php
// ini pakai builder
$resultInsertA = $this->connect()->insertInto('table_a', ['key' => 'value1'])->execute();

// pake query manual juga bisa
$resultSelectB = $this->connect()->getPdo()->query('SELECT * FROM table_b')->fetchAll();
```

## External Library
- Kint
- Whoops
- FluentPDO