-- ============================================================
-- Database: blog_uts
-- ============================================================
CREATE DATABASE IF NOT EXISTS blog_uts CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_uts;

-- Tabel Kategori
CREATE TABLE IF NOT EXISTS kategori (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- Tabel Penulis
CREATE TABLE IF NOT EXISTS penulis (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nama     VARCHAR(100) NOT NULL,
    email    VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    foto     VARCHAR(255) NOT NULL DEFAULT 'default.png'
) ENGINE=InnoDB;

-- Tabel Artikel
CREATE TABLE IF NOT EXISTS artikel (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    judul        VARCHAR(255) NOT NULL,
    isi          TEXT NOT NULL,
    gambar       VARCHAR(255) NOT NULL,
    hari_tanggal VARCHAR(100) NOT NULL,
    id_penulis   INT NOT NULL,
    id_kategori  INT NOT NULL,
    FOREIGN KEY (id_penulis)  REFERENCES penulis(id),
    FOREIGN KEY (id_kategori) REFERENCES kategori(id)
) ENGINE=InnoDB;

-- Data contoh kategori
INSERT INTO kategori (nama_kategori) VALUES ('Teknologi'), ('Pendidikan'), ('Kesehatan'), ('Olahraga'), ('Hiburan');
