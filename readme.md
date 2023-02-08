# Light
Single digit server response time PHP project

### Penjelasan
Ini adalah sebuah project sederhana dan memang dibuat
sesederhana mungkin untuk meminimalisir latensi response.

### Penyederhanaan
1. Tidak adanya dukungan .env
2. Konfigurasi ditulis di index.php dan main.php
3. Tidak digunakannya namespace pada core library
4. Pembacaan routing dengan cara ditoken

### Routing parameter
Karena mentoken symbol ```/``` pada URI dapat mempengaruhi performa, terinsiprasi dari youtube.com yang menggunakan prefix ```@``` sebagai ID dalam mencari sebuah user ID sehingga pentokenan URI cukup mencari 1 kali saja tanpa mentoken berulang.

##### Contoh
URL

```https://localhost/light/public/hello/@world```
```php
// index.php
Router::$prefix = '/light/public';
```
Route: ```/hello```

Parameter: ```world```