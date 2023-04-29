# Light
Project buat programmer yang males kalo ketemu bloatware.

## Penjelasan
Ini adalah sebuah project sederhana dan memang dibuat
sesederhana mungkin untuk meminimalisir latensi response.
Saat environtment di set ke production, Kint & Whoops tidak di load, jangan sampai masih ada 
```php
d($entahApaItu);
```
yang lupa dihapus/dikomen, nantinya bikin error.

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
Kalau load suatu class atau model, usahain pakai helper service
```php
// mending
$class = service(\App\DiFolderMana\ClassApapunItu::class);
$model = model('ApapunItuModel');

// ketimbang
$class = new \App\DiFolderMana\ClassApapunItu;
$model = new \App\Models\ApapunItuModel;
```
karena udah jadi project enteng, jangan sampai malah jadi berat kayak framework di pasaran karna kebanyakan instance.

## External Library
- Kint
- Whoops
- FluentPDO