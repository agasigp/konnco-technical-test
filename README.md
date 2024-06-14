# Konnco Simple Payment
Aplikasi ini adalah api payment sederhana yang dibuat untuk memenuhi tes teknis/technical test di [Konnco](https://github.com/konnco). Dibuat menggunakan [Laravel 11](https://laravel.com/docs/11.x).

## Cara Install
Aplikasi ini dapat diinstal pada server lokal maupun online dengan spesifikasi berikut:

### Kebutuhan Server
1. Minimal PHP 8.2 (dan sesuai dengan [persyaratan server Laravel 11.x](https://laravel.com/docs/11.x/deployment#server-requirements)).
2. Database MySQL atau MariaDB.
3. Redis untuk fitur cache.

### Langkah Instalasi
1. Clone repositori ini dengan perintah: `git clone https://github.com/agasigp/konnco-technical-test.git`
2. Masuk ke direktori konnco-technical-test: `$ cd konnco-technical-test`
3. Instal dependensi menggunakan: `$ composer install`
4. Salin berkas `.env.example` ke `.env`: `$ cp .env.example .env`
5. Generate kunci aplikasi: `$ php artisan key:generate`
6. Buat database MySQL untuk aplikasi ini.
7. Konfigurasi database dan pengaturan lainnya yang dibutuhkan di berkas `.env`.
    ```
    # DB_CONNECTION=sqlite
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=
    DB_PASSWORD=

    QUEUE_CONNECTION=redis
    CACHE_STORE=redis

    REDIS_CLIENT=phpredis
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    ```
8. Jalankan migrasi database: `$ php artisan migrate --seed`.
9. Buat personal acces client untuk passport: `$ php artisan passport:client --personal`. Sesuaikan konfigurasi terkait passport di berkas `.emv` dengan hasil pembuatan personval access client tadi.
    ```
    PASSPORT_PERSONAL_ACCESS_CLIENT_ID=client-id
    PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=client-secret
    ```
10. Generate the encryption key for passport : `$ php artisan passport:keys`.
11. Mulai server: `$ php artisan serve`
12. Untuk akses dokumentasi api, bisa diakses di [https://dark-star-572981.postman.co/workspace/My-Workspace~01171ba4-cc62-4394-8a03-f59cd594a3b0/collection/398070-8c9abd9a-fdee-434f-92ff-26261d673617?action=share&creator=398070](https://dark-star-572981.postman.co/workspace/My-Workspace~01171ba4-cc62-4394-8a03-f59cd594a3b0/collection/398070-8c9abd9a-fdee-434f-92ff-26261d673617?action=share&creator=398070). Alternatif lain bisa import postman collection yang tersedia di repo ini. User yang bisa dipakai adalah `user@konnco.com` / `password`.
13. Untuk seeder 1000 user dengan 100 transaksi untuk masing-masing user, silahkan jalankan perintah berikut : `$ php artisan db:seed --class=UserSeeder`.
14. Untuk menjalankan queue:worker supaya fungsi queue bisa berjalan, silahkan jalankan perintah berikut : `$ php artisan queue:work`.
15. Untuk testing, salin berkas `.env` ke `.env.testing`: `$ cp .env .env.testing`. Sesuaikan isi berkas .env.testing dengan konfigurasi yang sesuai. Konfigurasi di `.env.testing` akan dipakai untuk testing. Berikut ini adalah beberapa hal yang perlu diganti untuk kebutuhan testing :
    ```
    APP_ENV=testing
    CACHE_STORE=array
    QUEUE_CONNECTION=sync
    ```
    Setelah itu, jalankan perintah `$ vendor/bin/pest` untuk menjalankan testing secara otomatis.
