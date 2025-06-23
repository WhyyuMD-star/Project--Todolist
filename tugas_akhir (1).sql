-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jun 2025 pada 17.43
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tugas_akhir`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `todo`
--

CREATE TABLE `todo` (
  `id_todo` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `duedate` date NOT NULL,
  `status` enum('Pending','Completed','','') NOT NULL DEFAULT 'Pending',
  `email` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `todo`
--

INSERT INTO `todo` (`id_todo`, `task`, `description`, `duedate`, `status`, `email`) VALUES
(18, 'Tidur', '', '2024-05-19', 'Pending', 'nandaputraperbawa@gmail.com'),
(19, 'Lari Sore', '', '2025-06-19', 'Pending', 'wahyumahesa@gmail.com'),
(21, 'matematika', '', '2025-06-28', 'Pending', 'agus@gmail.com'),
(36, 'excel', 'kerjakan tugasnya ini dengan benar', '2025-06-20', 'Pending', 'aguswi@gmail.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `username` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`username`, `email`, `password`) VALUES
('agus pro', 'agus@gmail.com', '$2y$10$v2uQnrh/Zs9uNwJ8k848Xehqfg6ZvBip4fGW44xpVYsM2wWOEzasC'),
('agus', 'aguswi@gmail.com', '$2y$10$67P1Ub3B4BWKSclWi20FYOR7ZwhZdZLFlgx0yOEOgnmZ2.zjZtq4a'),
('Nanda', 'nandaputraperbawa@gmail.com', '$2y$10$lWk5ns/1gqOmDjNbuS.6quxrupDOhUTJiihLgK7pbSGK6frBnG/I6'),
('Vicky', 'vickytampan@gmail.com', '$2y$10$nn3CCcD1j0w6GeAQVbWEiuXJcqC4OEqLNoQSJNbRtlyhg7IqyFOEW'),
('test', 'wahyumahesa@gmail.com', '$2y$10$ilriEsXe8FyAQOAfCQcKvuY05BJiCAHNEI9ZYlT7kdoysUQYO7/F.');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`id_todo`),
  ADD KEY `email` (`email`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `todo`
--
ALTER TABLE `todo`
  MODIFY `id_todo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `todo`
--
ALTER TABLE `todo`
  ADD CONSTRAINT `todo_ibfk_1` FOREIGN KEY (`email`) REFERENCES `user` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
