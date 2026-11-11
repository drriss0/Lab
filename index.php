<?php
// Pengalih otomatis ke folder public/ (pintu masuk aplikasi CodeIgniter).
// Ditambahkan 2026-11-11: demi keamanan root project tidak lagi menampilkan
// daftar isi folder, jadi alamat root diarahkan langsung ke aplikasi.
header('Location: public/');
exit;
