# Ovalor Test

## Установка

1. Установка пакетов php
```
composer install
```
2. Ключ приложения
```
php artisan key:generate
```
3. Настройте файл .env
```
   DB_CONNECTION=mysql  
   DB_HOST=localhost  
   DB_PORT=3306  
   DB_DATABASE=your_database
   DB_USERNAME=your_login  
   DB_PASSWORD=your_password
```
4. Загрузка таблиц
```
php artisan migrate
```
5. Подготовка файла random.csv
```
Переместите файл random.csv в папку /storage/app
```
6. Запуск тестовой команды
```
php artisan makeMove
```
7. Отчет с ошибками можно найти по адресу:
```
/storage/app/failed.xlsx
```
