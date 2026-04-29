# 🚀 [Artimu]

Aplikasi web berbasis Laravel untuk [jelaskan fungsi singkat, misal: sistem manajemen inventory, dashboard admin, e-commerce, dll]. Dibangun dengan Laravel [11], [Tailwind/React], dan [MySQL].

![Demo Screenshot](./assets/demo.png) <!-- Ganti dengan screenshot project kamu -->

## 📦 Prerequisites
Pastikan environment development kamu sudah terinstall:
- PHP 8.1+ ([Download](https://www.php.net/downloads.php))
- Composer ([Install](https://getcomposer.org/download/))
- Node.js 18+ & npm ([Download](https://nodejs.org/))
- MySQL 
- Git

## 🛠️ Installation & Setup

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/nama-repo.git
   cd nama-repo

2. Install Dependencies
    composer install
    npm install

3. Setup Environment
    cp .env.example .env
    php artisan key:generate
    Buka file .env dan sesuaikan konfigurasi database (DB_DATABASE, DB_USERNAME, DB_PASSWORD, dll).

4. Database Migration & Seeding
    php artisan migrate fresh atau php artisan migrate
    php artisan db:seed (jika ada dummy).

5. Generate Storage Link (wajib jika ada fitur upload file)
    php artisan storage:link

Running Locally
Jalankan server Laravel & kompilasi aset frontend secara paralel:
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Frontend Assets (Vite)
npm run dev

# Akun Operator atau user

akun ke 1
name: Budi bisa di edit di database
username: operator1
password: OP1234

akun ke 2 
name: Andi
username: operator2
password:OP2345

passwordnya di hash pakai php artisan tinker

use Illuminate\Support\Facades\Hash;

Hash::make('123456');
