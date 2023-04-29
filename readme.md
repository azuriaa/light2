# Light
Single digit server response time PHP project.

## Penjelasan
Ini adalah sebuah project sederhana dan memang dibuat
sesederhana mungkin untuk meminimalisir latensi response.

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

## External Library
- Kint
- Whoops
- FluentPDO