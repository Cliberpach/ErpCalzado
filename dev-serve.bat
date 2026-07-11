@echo off
REM Sirve ErpCalzado en local con PHP 7.4 (D:\xampp), puerto 8000.
REM Requiere: MySQL corriendo en 127.0.0.1:3306 con la base erpmerris creada.
cd /d "%~dp0"
echo Migrando ErpCalzado...
D:\xampp\php\php.exe artisan migrate
echo.
echo Sirviendo ErpCalzado en http://127.0.0.1:8000 ...
D:\xampp\php\php.exe artisan serve --port=8000
