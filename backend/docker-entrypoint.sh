#!/bin/sh
set -e

# Buat/ubah tabel (aman dijalankan berulang)
php artisan migrate --force

# Data awal (akun admin + contoh barang); seeder bersifat idempotent
if [ "$RUN_SEEDER" = "true" ]; then
  php artisan db:seed --force
fi

exec "$@"
