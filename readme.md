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
Route dapat diatur pada app/Config/Routes.php
Cara kerja routing pada framework ini menggubakan penokenan daripada regex karena memang berfokus pada performa,
route akan ditoken berdasarkan karakter slash, setelah route ditoken slash berikutnya
akan menjadi params callback route.
Jadi tidak diijinkan register route menggunakan slash lebih dari satu.
### Benar
```php
Router::add('/page', function () {
    // ...
});
```
### Benar
```php
Router::add('/page-to-something', function () {
    // ...
});
```
### Salah
```php
Router::add('/page/to/something', function () {
    // ...
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