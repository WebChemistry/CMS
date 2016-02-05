-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Počítač: 31.31.79.20
-- Vytvořeno: Pát 05. úno 2016, 15:34
-- Verze serveru: 5.5.46-0ubuntu0.14.04.2
-- Verze PHP: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `test`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(30) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `parameter`
--

CREATE TABLE IF NOT EXISTS `parameter` (
  `id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `is_serialized` tinyint(1) NOT NULL DEFAULT '0' COMMENT '(DC2Type:boolean)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vypisuji data pro tabulku `parameter`
--

INSERT INTO `parameter` (`id`, `content`, `is_serialized`) VALUES
('facebook', 'a:2:{s:2:"id";s:0:"";s:3:"url";s:0:"";}', 1),
('google', 'a:3:{s:4:"name";s:0:"";s:2:"id";s:0:"";s:3:"url";s:0:"";}', 1),
('instagram', '', 0),
('twitter', 'a:1:{s:3:"url";s:0:"";}', 1),
('websiteDesc', 'Popis stránky', 0),
('websiteEmail', 'email@example.com', 0),
('websiteFavicon', '', 0),
('websiteKeys', 'klíčové,tagy', 0),
('websiteName', 'Website', 0),
('websiteOldBrowsers', '1', 0),
('websiteTitle', 'Website: %title', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `privileges`
--

CREATE TABLE IF NOT EXISTS `privileges` (
  `id` int(11) NOT NULL,
  `allow` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:json_array)'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vypisuji data pro tabulku `privileges`
--

INSERT INTO `privileges` (`id`, `allow`) VALUES
(1, '[]');

-- --------------------------------------------------------

--
-- Struktura tabulky `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) NOT NULL,
  `privileges_id` int(11) DEFAULT NULL,
  `extends_id` int(11) DEFAULT NULL,
  `name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `allow_all` tinyint(1) NOT NULL COMMENT '(DC2Type:boolean)',
  `is_admin` tinyint(1) NOT NULL COMMENT '(DC2Type:boolean)',
  `monitor` tinyint(1) NOT NULL COMMENT '(DC2Type:boolean)'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vypisuji data pro tabulku `role`
--

INSERT INTO `role` (`id`, `privileges_id`, `extends_id`, `name`, `allow_all`, `is_admin`, `monitor`) VALUES
(1, 1, NULL, 'Owner', 1, 1, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registration` datetime NOT NULL,
  `forget_hash` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `forget_time` datetime DEFAULT NULL,
  `last_visit` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `role_id`, `name`, `email`, `password`, `avatar`, `registration`, `forget_hash`, `forget_time`, `last_visit`) VALUES
(1, 1, 'Administrator', 'admin@example.com', '$2y$10$5sl/WxtlRJFvuGMLVmi0Xu5cDvEgy9BiqEshd9TLKF3xk5vl/rwhO', NULL, '2016-02-05 15:33:23', NULL, NULL, '2016-02-05 15:33:23');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `parameter`
--
ALTER TABLE `parameter`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `privileges`
--
ALTER TABLE `privileges`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`), ADD KEY `IDX_57698A6AA79C4DC9` (`privileges_id`), ADD KEY `IDX_57698A6AD06D9650` (`extends_id`);

--
-- Klíče pro tabulku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`), ADD KEY `IDX_8D93D649D60322AC` (`role_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `privileges`
--
ALTER TABLE `privileges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `role`
--
ALTER TABLE `role`
ADD CONSTRAINT `FK_57698A6AD06D9650` FOREIGN KEY (`extends_id`) REFERENCES `role` (`id`),
ADD CONSTRAINT `FK_57698A6AA79C4DC9` FOREIGN KEY (`privileges_id`) REFERENCES `privileges` (`id`);

--
-- Omezení pro tabulku `user`
--
ALTER TABLE `user`
ADD CONSTRAINT `FK_8D93D649D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
