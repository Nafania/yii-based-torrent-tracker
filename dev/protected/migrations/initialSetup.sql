-- phpMyAdmin SQL Dump
-- version 4.0.3
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Авг 07 2013 г., 16:00
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- База данных: `yii-torrent`
--
CREATE DATABASE IF NOT EXISTS `yii-torrent` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `yii-torrent`;

-- --------------------------------------------------------

--
-- Структура таблицы `attrChars`
--

CREATE TABLE IF NOT EXISTS `attrChars` (
  `attrId` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `order` int(10) NOT NULL,
  KEY `attrId` (`attrId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `attrChars`
--

INSERT INTO `attrChars` (`attrId`, `title`, `order`) VALUES
(10, 'Русский профессиональный дубляж', 0),
(10, 'Одноголосный', 1),
(11, 'Avi', 0),
(11, 'Mkv', 1),
(11, 'Mp4', 2),
(51, 'MP3', 0),
(18, 'Flac', 1),
(54, 'VBR', 0),
(19, 'CBR', 1),
(19, 'Lossless', 2),
(28, 'Английский', 0),
(28, 'Русский', 1),
(28, 'Немецкий', 2),
(28, 'Другой язык', 3),
(29, 'Английский', 0),
(29, 'Русский', 1),
(29, 'Немецкий', 2),
(29, 'Другой язык', 3),
(30, 'Вшита', 0),
(30, 'Остутсвует', 1),
(30, 'Не нужна', 2),
(36, 'Полная игра', 0),
(36, 'Repack', 1),
(36, 'Demo', 2),
(30, 'См. инструкцию по установке', 3),
(40, 'Windows', 0),
(40, 'Linux', 1),
(40, 'Mac', 2),
(40, 'Android', 3),
(40, 'IOS', 4),
(40, 'Windows Phone', 5),
(40, 'Symbian', 6),
(40, 'Java', 7),
(43, 'Вшита', 0),
(43, 'Отсутсвует', 1),
(43, 'Не нужна', 2),
(43, 'См. инструкцию по установке', 3),
(44, 'Английский', 0),
(44, 'Русский', 1),
(44, 'Прочее', 2),
(51, 'MP3', 0),
(51, 'OGG', 1),
(51, 'FLAC', 2),
(51, 'FB2', 3),
(51, 'DJVU', 4),
(51, 'PDF', 5),
(51, 'RTF', 6),
(51, 'DOC', 7),
(54, 'VBR', 0),
(54, 'Lossless', 1),
(54, '320', 2),
(54, '256', 3),
(54, '128', 4),
(54, '96', 5),
(66, 'Русский профессиональный дубляж', 0),
(66, 'Одноголосный', 1),
(67, 'AVI', 0),
(67, 'MKV', 1),
(67, 'MP4', 2),
(68, 'DVDRip', 0),
(68, 'WEB-DL', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `attributes`
--

CREATE TABLE IF NOT EXISTS `attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `validator` tinyint(1) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `description` varchar(255) NOT NULL,
  `common` tinyint(1) NOT NULL,
  `cId` int(10) NOT NULL,
  `separate` tinyint(1) NOT NULL,
  `append` varchar(255) NOT NULL,
  `prepend` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;

--
-- Дамп данных таблицы `attributes`
--

INSERT INTO `attributes` (`id`, `title`, `type`, `validator`, `required`, `description`, `common`, `cId`, `separate`, `append`, `prepend`) VALUES
(1, 'Название', 0, 0, 1, '', 1, 1, 0, '', ''),
(2, 'Оригинальное название', 0, 0, 0, '', 1, 1, 0, '', ''),
(3, 'Год выхода', 0, 0, 1, '', 1, 1, 0, '', ''),
(5, 'Режиссер', 0, 0, 1, '', 1, 1, 0, '', ''),
(6, 'В ролях', 0, 0, 1, '', 1, 1, 0, '', ''),
(7, 'Описание', 5, 0, 1, '', 1, 1, 0, '', ''),
(8, 'Выпущено', 0, 0, 1, '', 1, 1, 0, '', ''),
(9, 'Продолжительность', 0, 0, 1, '', 0, 1, 0, '', ''),
(10, 'Перевод', 1, 0, 1, '', 0, 1, 0, '', ''),
(11, 'Формат', 1, 0, 1, '', 0, 1, 0, '', ''),
(12, 'Видео', 0, 0, 1, 'Характеристики видео', 0, 1, 0, '', ''),
(13, 'Звук', 0, 0, 1, 'Характеристики звуковой дорожки', 0, 1, 0, '', ''),
(14, 'Семпл', 0, 0, 0, 'Ссылка на семпл', 0, 1, 0, '', ''),
(15, 'Исполнитель', 0, 0, 1, '', 1, 2, 0, '', ''),
(16, 'Альбом', 0, 0, 1, '', 1, 2, 0, '', ''),
(17, 'Источник', 0, 0, 1, '', 0, 2, 0, '', ''),
(18, 'Формат', 1, 0, 1, '', 0, 2, 1, '', ''),
(19, 'Битрейт', 1, 0, 1, '', 0, 2, 0, '', ''),
(20, 'Треклист', 5, 0, 1, '', 1, 2, 0, '', ''),
(21, 'Год выхода', 0, 0, 1, '', 1, 2, 0, '', ''),
(22, 'Качество', 0, 0, 1, 'Укажите здесь качество и / или тип видео', 0, 1, 1, '', ''),
(23, 'Разработчик', 0, 0, 1, '', 1, 3, 0, '', ''),
(24, 'Издатель', 0, 0, 1, '', 1, 3, 0, '', ''),
(25, 'Издатель в России', 0, 0, 0, '', 1, 3, 0, '', ''),
(26, 'Год выхода', 0, 0, 1, '', 1, 3, 0, '', ''),
(28, 'Язык интерфейса', 1, 0, 1, '', 0, 3, 0, '', ''),
(29, 'Язык озвучки', 1, 0, 1, '', 0, 3, 0, '', ''),
(30, 'Таблетка', 1, 0, 1, '', 0, 3, 0, '', ''),
(31, 'Описание', 5, 0, 1, '', 1, 3, 0, '', ''),
(32, 'Особенности игры', 5, 0, 0, '', 1, 3, 0, '', ''),
(33, 'Особенности RePack''a', 5, 0, 0, 'Впишите сюдя особенности если вы загружаете RePack', 0, 3, 0, '', ''),
(34, 'Инструкция по установке', 5, 0, 0, 'Впишите сюда инструкцию по установке игры', 0, 3, 0, '', ''),
(35, 'Системные требования', 5, 0, 1, '', 1, 3, 0, '', ''),
(36, 'Тип', 0, 0, 1, '', 0, 3, 1, '', ''),
(37, 'Название', 0, 0, 1, '', 1, 3, 0, '', ''),
(38, 'Оригинальное название', 0, 0, 1, '', 1, 3, 0, '', ''),
(39, 'Название', 0, 0, 1, '', 1, 4, 0, '', ''),
(40, 'Платформа', 1, 0, 1, '', 0, 4, 1, '', ''),
(41, 'Версия', 0, 0, 1, '', 1, 4, 0, '', ''),
(42, 'Год выхода', 0, 0, 1, '', 1, 4, 0, '', ''),
(43, 'Таблетка', 1, 0, 1, '', 0, 4, 0, '', ''),
(44, 'Язык', 1, 0, 1, '', 0, 4, 0, '', ''),
(45, 'Инструкция по установке', 5, 0, 0, '', 0, 4, 0, '', ''),
(46, 'Описание программы', 5, 0, 1, '', 1, 4, 0, '', ''),
(47, 'Автор', 0, 0, 1, '', 1, 5, 0, '', ''),
(48, 'Название', 0, 0, 1, '', 1, 5, 0, '', ''),
(49, 'Год выхода', 0, 0, 1, '', 1, 5, 0, '', ''),
(50, 'Автор перевода', 0, 0, 0, '', 0, 5, 0, '', ''),
(51, 'Формат', 1, 0, 1, '', 0, 5, 1, '', ''),
(52, 'Список литературы', 5, 0, 0, 'Если вы загружаете серию книг / журналов, то впишите сюда список', 0, 5, 0, '', ''),
(53, 'Описание', 5, 0, 1, '', 1, 5, 0, '', ''),
(54, 'Битрейт', 1, 0, 0, 'Если вы загружаете аудио книгу, то укажите битрейт', 0, 5, 0, '', ''),
(55, 'Язык', 0, 0, 0, '', 0, 5, 0, '', ''),
(56, 'Название', 0, 0, 1, '', 1, 7, 0, '', ''),
(57, 'Оригинальное название', 0, 0, 0, '', 1, 7, 0, '', ''),
(58, 'Год выхода', 0, 0, 1, '2013', 1, 7, 0, '', ''),
(59, 'Режиссер', 0, 0, 1, '', 1, 7, 0, '', ''),
(60, 'В ролях', 0, 0, 1, '', 1, 7, 0, '', ''),
(61, 'Описание', 5, 0, 1, '', 1, 7, 0, '', ''),
(62, 'Выпущено', 0, 0, 1, '', 1, 7, 0, '', ''),
(63, 'Продолжительность', 0, 0, 1, '', 0, 7, 0, '', ''),
(64, 'Номер сезона', 0, 0, 1, '', 1, 7, 0, 'сезон', ''),
(65, 'Номер серии', 0, 0, 1, '', 0, 7, 1, 'серия', ''),
(66, 'Перевод', 1, 0, 1, '', 0, 7, 0, '', ''),
(67, 'Формат', 1, 0, 1, '', 0, 7, 0, '', ''),
(68, 'Качество', 1, 0, 1, '', 0, 7, 1, '', ''),
(69, 'Видео', 0, 0, 1, '', 0, 7, 0, '', ''),
(70, 'Аудио', 0, 0, 1, '', 0, 7, 0, '', ''),
(71, 'Описание версии', 5, 0, 0, 'Если в в этой версии есть изменения, то впишите их сюда', 0, 4, 0, '', ''),
(72, 'Дополнительная информация о версии', 0, 0, 0, 'Если это Repack или что-то добавлено к оригинальной версии, то впишите информацию об этом сюда', 0, 4, 0, '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `AuthAssignment`
--

CREATE TABLE IF NOT EXISTS `AuthAssignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `AuthAssignment`
--

INSERT INTO `AuthAssignment` (`itemname`, `userid`, `bizrule`, `data`) VALUES
('admin', '2', NULL, 'N;'),
('registered', '3', NULL, 'N;'),
('registered', '4', NULL, 'N;');

-- --------------------------------------------------------

--
-- Структура таблицы `AuthItem`
--

CREATE TABLE IF NOT EXISTS `AuthItem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `AuthItem`
--

INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES
('admin', 2, 'Admin', NULL, 'N;'),
('comments.default.create', 0, 'Создание комментария', NULL, 'N;'),
('comments.default.loadAnswerBlock', 0, 'Ответ на другой комментария', NULL, 'N;'),
('createTorrentTask', 1, 'Создание торрента (задача)', NULL, 'N;'),
('guest', 2, 'Guest', 'return Yii::app()->getUser()->getIsGuest();', 'N;'),
('ratings.default.create', 0, 'Добавление рейтинга', NULL, 'N;'),
('registered', 2, 'Зарегистрированный пользователь', NULL, 'N;'),
('reports.default.create', 0, 'Создание жалобы', NULL, 'N;'),
('site.index', 0, 'Просмотр главной страницы', NULL, 'N;'),
('torrents.default.create', 0, 'Создание торрента - первый шаг', NULL, 'N;'),
('torrents.default.createGroup', 0, 'Создание группы торрентов', NULL, 'N;'),
('torrents.default.createTorrent', 0, 'Создание торрента', NULL, 'N;'),
('torrents.default.download', 0, 'Скачивание торрента', NULL, 'N;'),
('torrents.default.index', 0, 'Просмотр списка торрентов', NULL, 'N;'),
('torrents.default.view', 0, 'Просмотр одного торрента', NULL, 'N;'),
('user.default.login', 0, 'Вход на сайт', NULL, 'N;'),
('user.default.logout', 0, 'Выход с сайта', NULL, 'N;'),
('user.default.register', 0, 'Регистрация пользователя', NULL, 'N;'),
('user.default.reset', 0, 'Сброс пароля', NULL, 'N;'),
('user.default.restore', 0, 'Восстановление пароля', NULL, 'N;');

-- --------------------------------------------------------

--
-- Структура таблицы `AuthItemChild`
--

CREATE TABLE IF NOT EXISTS `AuthItemChild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `AuthItemChild`
--

INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES
('registered', 'comments.default.create'),
('registered', 'comments.default.loadAnswerBlock'),
('registered', 'createTorrentTask'),
('registered', 'ratings.default.create'),
('registered', 'reports.default.create'),
('guest', 'site.index'),
('registered', 'site.index'),
('createTorrentTask', 'torrents.default.create'),
('createTorrentTask', 'torrents.default.createGroup'),
('createTorrentTask', 'torrents.default.createTorrent'),
('guest', 'torrents.default.download'),
('guest', 'torrents.default.index'),
('registered', 'torrents.default.index'),
('guest', 'torrents.default.view'),
('registered', 'torrents.default.view'),
('guest', 'user.default.login'),
('registered', 'user.default.logout'),
('guest', 'user.default.register'),
('guest', 'user.default.reset'),
('guest', 'user.default.restore');

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `root` int(10) unsigned DEFAULT NULL,
  `lft` int(10) unsigned NOT NULL,
  `rgt` int(10) unsigned NOT NULL,
  `level` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `root` (`root`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `root`, `lft`, `rgt`, `level`, `name`, `image`, `description`) VALUES
(1, 1, 1, 2, 1, 'Видео', '', ''),
(2, 2, 3, 2, 1, 'Аудио', '', ''),
(3, 3, 4, 2, 1, 'Игры', '', ''),
(4, 4, 5, 2, 1, 'Софт', '', ''),
(5, 5, 6, 2, 1, 'Литература', '', ''),
(6, 6, 7, 2, 1, 'Разное', '', ''),
(7, 7, 2, 2, 1, 'Сериалы', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `categoryAttributes`
--

CREATE TABLE IF NOT EXISTS `categoryAttributes` (
  `catId` int(10) unsigned NOT NULL,
  `attrId` int(10) unsigned NOT NULL,
  KEY `catId` (`catId`),
  KEY `attrId` (`attrId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `categoryAttributes`
--

INSERT INTO `categoryAttributes` (`catId`, `attrId`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 22),
(7, 56),
(7, 57),
(7, 58),
(7, 59),
(7, 60),
(7, 61),
(7, 62),
(7, 63),
(7, 64),
(7, 65),
(7, 66),
(7, 67),
(7, 68),
(7, 69),
(7, 70),
(2, 9),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(5, 47),
(5, 48),
(5, 49),
(5, 50),
(5, 51),
(5, 52),
(5, 53),
(5, 54),
(5, 55),
(3, 37),
(3, 38),
(3, 36),
(3, 23),
(3, 24),
(3, 25),
(3, 26),
(3, 28),
(3, 29),
(3, 30),
(3, 31),
(3, 32),
(3, 33),
(3, 34),
(3, 35),
(4, 39),
(4, 40),
(4, 41),
(4, 72),
(4, 42),
(4, 43),
(4, 44),
(4, 45),
(4, 71),
(4, 46);

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `ownerId` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `parentId` int(10) NOT NULL,
  `modelName` varchar(45) NOT NULL,
  `modelId` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=91 ;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `text`, `ownerId`, `ctime`, `mtime`, `status`, `parentId`, `modelName`, `modelId`) VALUES
(1, '<p>zzzzzzzzzzz<br /></p>', 1, 1375345072, 0, 0, 0, 'TorrentGroup', 7),
(2, '<p>zzqaqa<br /></p>', 1, 1375345078, 0, 0, 1, 'TorrentGroup', 7),
(3, '<p>zzzzzzzzzzz<br /></p>', 1, 1375345084, 0, 0, 0, 'TorrentGroup', 7),
(4, '<p>xasa<br /></p>', 1, 1375345093, 0, 0, 2, 'TorrentGroup', 7),
(5, '<p>zzzzzzzzzzz<br /></p>', 1, 1375345096, 0, 0, 0, 'TorrentGroup', 7),
(6, '<p>https://www.youtube.com/watch?v=1WDQ4FhslSk<br /></p>', 1, 1375346353, 0, 0, 0, 'TorrentGroup', 7),
(7, 'http://stackoverflow.com/questions/10436583/php-preg-replace-substituting-youtube-links-over-substituting-urls\r\n', 1, 1375346569, 0, 0, 0, 'TorrentGroup', 7),
(8, 'http://stackoverflow.com/questions/10436583/php-preg-replace-substituting-youtube-links-over-substituting-urls\r\n', 1, 1375346772, 0, 0, 0, 'TorrentGroup', 7),
(9, 'http://stackoverflow.com/questions/10436583/php-preg-replace-substituting-youtube-links-over-substituting-urls\r\n', 1, 1375346781, 0, 0, 0, 'TorrentGroup', 7),
(10, 'http://stackoverflow.com/questions/10436583/php-preg-replace-substituting-youtube-links-over-substituting-urls\r\n', 1, 1375346800, 0, 0, 0, 'TorrentGroup', 7),
(11, '<p>TextHelper<br /></p>', 1, 1375346810, 0, 0, 10, 'TorrentGroup', 7),
(12, 'http://stackoverflow.com/questions/10436583/php-preg-replace-substituting-youtube-links-over-substituting-urls\r\n', 1, 1375346815, 0, 0, 0, 'TorrentGroup', 7),
(13, '<p>Комментарий к 22ой серии<br /></p>', 1, 1375347727, 0, 0, 0, 'TorrentGroup', 13),
(14, '<p>Ответ на комментарий к 22ой серии<br /></p>', 1, 1375347737, 0, 0, 13, 'TorrentGroup', 13),
(15, '<p>Комментарий к 23ей серии<br /></p>', 1, 1375347749, 0, 0, 0, 'TorrentGroup', 13),
(16, '<p>Ответ на комментарий к 23ей серии<br /></p>', 1, 1375347760, 0, 0, 15, 'TorrentGroup', 13),
(17, '<p>Ответ на ответ к комментарию к 23ей серии<br /></p>', 1, 1375347778, 0, 0, 16, 'TorrentGroup', 13),
(18, 'Общий каммент<br />', 1, 1375347786, 0, 0, 0, 'TorrentGroup', 13),
(19, 'Общий каммент<br />', 1, 1375347788, 0, 0, 0, 'TorrentGroup', 13),
(20, 'Общий каммент<br />', 1, 1375347790, 0, 0, 0, 'TorrentGroup', 13),
(21, '<p>`1111''"""<br /></p>', 1, 1375350863, 0, 0, 20, 'TorrentGroup', 13),
(22, '<p>ss<br /></p>', 1, 1375350944, 0, 0, 21, 'TorrentGroup', 13),
(23, '<p>xxxxxxxxxxx<br /></p>', 1, 1375350994, 0, 0, 19, 'TorrentGroup', 13),
(24, '<p>s<br /></p>', 1, 1375351200, 0, 0, 20, 'TorrentGroup', 13),
(25, '<p>ssssssss<br /></p>', 1, 1375351230, 0, 0, 24, 'TorrentGroup', 13),
(26, '<p>xxxxxxxxx<br /></p>', 1, 1375351259, 0, 0, 25, 'TorrentGroup', 13),
(27, '<p>ddddddddd<br /></p>', 1, 1375351307, 0, 0, 26, 'TorrentGroup', 13),
(28, '<p>dedcw<br /></p>', 1, 1375351411, 0, 0, 27, 'TorrentGroup', 13),
(29, '<p>dcfvf<br /></p>', 1, 1375351434, 0, 0, 28, 'TorrentGroup', 13),
(30, '<p>cevevvrev<br /></p>', 1, 1375351443, 0, 0, 28, 'TorrentGroup', 13),
(31, '<p>cfrfvevrev<br /></p>', 1, 1375351450, 0, 0, 29, 'TorrentGroup', 13),
(32, '<p>Я с вами не соглашусь<br /></p>', 3, 1375352172, 0, 0, 13, 'TorrentGroup', 13),
(33, '<p>Я с вами не соглашусь<br /></p>', 3, 1375352340, 0, 0, 13, 'TorrentGroup', 13),
(34, '<p>А я соглашусь.<br /></p>', 3, 1375353088, 0, 0, 14, 'TorrentGroup', 13),
(35, '<blockquote>Tves а это Борна воспринимаешь?</blockquote><img src="http://s017.radikal.ru/i413/1208/cb/cb3779f3ff72.jpg" alt="cb3779f3ff72.jpg" />', 3, 1375354239, 0, 0, 0, 'TorrentGroup', 13),
(36, '<p>\r\n	https://www.youtube.com/watch?v=1WDQ4FhslSk\r\n</p>', 3, 1375354901, 0, 0, 0, 'TorrentGroup', 13),
(37, '<p>\r\n	http://youtu.be/1WDQ4FhslSk\r\n</p>', 3, 1375355752, 0, 0, 0, 'TorrentGroup', 13),
(38, '<p>\r\n	xxxxxxxxxxx\r\n</p>', 3, 1375356370, 0, 0, 0, 'TorrentGroup', 13),
(39, '<p>\r\n	 xxxxxxxxxxx\r\n</p>', 3, 1375356400, 0, 0, 0, 'TorrentGroup', 13),
(40, '<p>\r\n	cxdcdscs\r\n</p>', 3, 1375356406, 0, 0, 0, 'TorrentGroup', 13),
(41, '<p>\r\n	csdvsvsddvsvsd\r\n</p>', 3, 1375356413, 0, 0, 40, 'TorrentGroup', 13),
(42, '<p>\r\n	cdvdvd\r\n</p>', 3, 1375356421, 0, 0, 41, 'TorrentGroup', 13),
(43, '<p>\r\n	,kz,kz,kz\r\n</p>', 3, 1375356766, 0, 0, 30, 'TorrentGroup', 13),
(44, '<p>\r\n	Привет\r\n</p>', 3, 1375356811, 0, 0, 42, 'TorrentGroup', 13),
(45, '<p>\r\n	<img src="http://habr.habrastorage.org/post_images/39d/1ab/26c/39d1ab26c28b2e819a3c8a5811519a63.jpg" alt="39d1ab26c28b2e819a3c8a5811519a63.jpg" /></p>', 3, 1375359916, 0, 0, 0, 'TorrentGroup', 13),
(46, '<p>\r\n	&lt;script&gt;alert(10);&lt;/script&gt;\r\n</p>', 3, 1375361471, 0, 0, 0, 'TorrentGroup', 13),
(47, '<p>\r\n	Что за Марафон?\r\n</p>', 3, 1375362476, 0, 0, 0, 'TorrentGroup', 12),
(48, '<p>\r\n	<strong>привет</strong>\r\n</p>\r\n<p>\r\n	<strong></strong>\r\n</p>', 3, 1375363145, 0, 0, 0, 'TorrentGroup', 12),
(49, '<p>\r\n	Прием\r\n</p>', 1, 1375723922, 0, 0, 0, 'TorrentGroup', 3),
(50, '<p>\r\n	прием\r\n</p>', 1, 1375723970, 0, 0, 0, 'TorrentGroup', 3),
(51, '<p>\r\n	тест\r\n</p>', 1, 1375724144, 0, 0, 0, 'TorrentGroup', 3),
(52, '<p>\r\n	тест\r\n</p>', 1, 1375724341, 0, 0, 0, 'TorrentGroup', 3),
(53, '<p>\r\n	ff\r\n</p>', 1, 1375725549, 0, 0, 0, 'TorrentGroup', 3),
(54, '<p>\r\n	,ll,;\r\n</p>', 1, 1375727665, 0, 0, 0, 'TorrentGroup', 3),
(55, '<p>\r\n	mklkkllk\r\n</p>', 1, 1375730248, 0, 0, 0, 'TorrentGroup', 3),
(56, '<p>\r\n	nknlnl\r\n</p>', 1, 1375731372, 0, 0, 0, 'TorrentGroup', 1),
(57, '<p>\r\n	m,m;m;m\r\n</p>', 1, 1375731378, 0, 0, 0, 'TorrentGroup', 1),
(58, '<p>\r\n	,l,;,l;;,\r\n</p>', 1, 1375731387, 0, 0, 0, 'TorrentGroup', 1),
(59, '<p>\r\n	mlm;mm;\r\n</p>', 1, 1375731395, 0, 0, 0, 'TorrentGroup', 1),
(60, '<p>\r\n	cdc\r\n</p>', 1, 1375731554, 0, 0, 0, 'TorrentGroup', 1),
(61, '<p>\r\n	nkjjll\r\n</p>', 1, 1375731588, 0, 0, 0, 'TorrentGroup', 1),
(62, '<p>\r\n	cccc\r\n</p>', 1, 1375731628, 0, 0, 0, 'TorrentGroup', 1),
(63, '<p>\r\n	cccc\r\n</p>', 1, 1375731704, 0, 0, 0, 'TorrentGroup', 1),
(64, '<p>\r\n	cccc\r\n</p>', 1, 1375731714, 0, 0, 0, 'TorrentGroup', 1),
(65, '<p>\r\n	cccccccccccc\r\n</p>', 1, 1375731726, 0, 0, 0, 'TorrentGroup', 1),
(66, '<p>\r\n	&lt;blockquote&gt;&lt;/blockquote&gt;\r\n</p>', 1, 1375766932, 0, 0, 0, 'TorrentGroup', 2),
(67, '<p>\r\n	xxxxxxxxxxxxxx\r\n</p>', 1, 1375766975, 0, 0, 0, 'TorrentGroup', 2),
(68, '<p>\r\n	zzzzzzzzzzzz\r\n</p>', 1, 1375766999, 0, 0, 0, 'TorrentGroup', 2),
(69, '<p>\r\n	ssssssssssss\r\n</p>', 1, 1375767115, 0, 0, 0, 'TorrentGroup', 2),
(70, '<p>\r\n	rrrrrrrrrrrrr\r\n</p>', 1, 1375767202, 0, 0, 0, 'TorrentGroup', 2),
(71, '<p>\r\n	4r44444444444\r\n</p>', 1, 1375767212, 0, 0, 0, 'TorrentGroup', 2),
(72, '<p>\r\n	rrrrrrrrrrrrrrrrr\r\n</p>', 1, 1375767221, 0, 0, 71, 'TorrentGroup', 2),
(73, '<blockquote>\r\n	 &lt;script&gt;alert(10);&lt;/script&gt;\r\n</blockquote>', 1, 1375804248, 0, 0, 0, 'TorrentGroup', 13),
(74, '<blockquote>\r\n	Привет, как дела?<br /></blockquote>', 1, 1375804273, 0, 0, 0, 'TorrentGroup', 13),
(75, '<p>\r\n	xxxxxxxxxxx\r\n</p>', 1, 1375810061, 0, 0, 31, 'TorrentGroup', 13),
(76, '<p>\r\n	cccccccccc\r\n</p>', 1, 1375810068, 0, 0, 75, 'TorrentGroup', 13),
(77, '<p>\r\n	cccccccccccc\r\n</p>', 1, 1375810075, 0, 0, 76, 'TorrentGroup', 13),
(78, '<p>\r\n	cccccccccccc\r\n</p>', 1, 1375810081, 0, 0, 77, 'TorrentGroup', 13),
(79, '<p>\r\n	xxxxxxxxxxxxxxx\r\n</p>', 1, 1375810087, 0, 0, 78, 'TorrentGroup', 13),
(80, '<p>\r\n	ffffffffff\r\n</p>', 1, 1375810093, 0, 0, 79, 'TorrentGroup', 13),
(81, '<p>\r\n	ffffffffffff\r\n</p>', 1, 1375810099, 0, 0, 80, 'TorrentGroup', 13),
(82, '<p>\r\n	fffffffffff\r\n</p>', 1, 1375810106, 0, 0, 81, 'TorrentGroup', 13),
(83, '<p>\r\n	vvvvvvvvvvvv\r\n</p>', 1, 1375810113, 0, 0, 82, 'TorrentGroup', 13),
(84, '<p>\r\n	vvvvvvvvvvv\r\n</p>', 1, 1375810122, 0, 0, 83, 'TorrentGroup', 13),
(85, '<p>\r\n	vvvvvvvvvvv\r\n</p>', 1, 1375810128, 0, 0, 84, 'TorrentGroup', 13),
(86, '<p>\r\n	vvvvvvvvv\r\n</p>', 1, 1375810133, 0, 0, 85, 'TorrentGroup', 13),
(87, '<p>\r\n	Толик\r\n</p>', 3, 1375812749, 0, 0, 0, 'TorrentGroup', 12),
(88, '<p>\r\n	Толик\r\n</p>', 3, 1375812783, 0, 0, 0, 'TorrentGroup', 12),
(89, '<p>\r\n	Что за дело?\r\n</p>', 1, 1375864689, 0, 0, 0, 'TorrentGroup', 10),
(90, '<p>\r\n	дада\r\n</p>', 1, 1375864753, 0, 0, 0, 'TorrentGroup', 10);

-- --------------------------------------------------------

--
-- Структура таблицы `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `param` varchar(128) NOT NULL,
  `value` text NOT NULL,
  `default` text NOT NULL,
  `label` varchar(255) NOT NULL,
  `type` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `param` (`param`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `config`
--

INSERT INTO `config` (`id`, `param`, `value`, `default`, `label`, `type`) VALUES
(1, 'base.siteName', 'Yii torrent', '', '', ''),
(2, 'base.defaultDescription', '', '', '', ''),
(3, 'base.defaultKeywords', '', '', '', ''),
(4, 'base.logoUrl', '', '', '', ''),
(5, 'torrentsModule.xbt_listen_url', 'http://dev.yii-torrent', '', '', ''),
(6, 'torrentsModule.listen_port', '2720', '', '', ''),
(7, 'base.fromEmail', 'noreply@yii-torrent.com', '', '', ''),
(8, 'torrentsModule.torrentsNameDelimiter', '/', '/', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `ctime` int(11) NOT NULL,
  `pinned` tinyint(1) NOT NULL,
  `owner` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `title`, `text`, `ctime`, `pinned`, `owner`) VALUES
(1, 'Альфа-версия', 'Это альфа-версия. Ничего не работает :)\r\nШучу, работает загрузка торрентов и регистрация.', 1375185963, 0, 1),
(2, 'Еще одна новость', 'Текст еще одной новости', 1375188207, 0, 1),
(3, 'Камменты заработали', 'Флудим в камментах', 1375353563, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `ratingRelations`
--

CREATE TABLE IF NOT EXISTS `ratingRelations` (
  `modelName` varchar(255) NOT NULL,
  `modelId` int(10) NOT NULL,
  `rating` int(10) NOT NULL,
  `uId` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`modelName`,`modelId`,`uId`),
  UNIQUE KEY `modelName` (`modelName`,`modelId`,`uId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ratingRelations`
--

INSERT INTO `ratingRelations` (`modelName`, `modelId`, `rating`, `uId`, `ctime`, `state`) VALUES
('Comment', 13, 1, 1, 1375527707, 1),
('Comment', 13, -1, 3, 1375813510, 0),
('Comment', 14, -1, 1, 1375527712, 0),
('Comment', 14, 1, 3, 1375813427, 1),
('Comment', 15, 1, 1, 1375616588, 1),
('Comment', 15, -1, 3, 1375813513, 0),
('Comment', 16, 1, 1, 1375765763, 1),
('Comment', 16, -1, 3, 1375766059, 0),
('Comment', 17, -1, 1, 1375766095, 0),
('Comment', 17, 1, 3, 1375766075, 1),
('Comment', 18, -1, 3, 1375766107, 0),
('Comment', 19, -1, 1, 1375766111, 0),
('Comment', 23, -1, 1, 1375766143, 0),
('Comment', 23, -1, 3, 1375766134, 0),
('Comment', 25, -1, 1, 1375613447, 0),
('Comment', 26, 1, 1, 1375613445, 1),
('Comment', 32, 1, 1, 1375608175, 1),
('Comment', 32, 1, 3, 1375766033, 1),
('Comment', 33, -1, 1, 1375608179, 0),
('Comment', 33, 1, 3, 1375766040, 1),
('Comment', 34, 1, 1, 1375528689, 1),
('Comment', 34, -1, 3, 1375766052, 0),
('Comment', 35, 1, 1, 1375766720, 1),
('Comment', 36, 1, 1, 1375812513, 1),
('Comment', 37, 1, 1, 1375615627, 1),
('Comment', 38, 1, 1, 1375812501, 1),
('Comment', 39, 1, 1, 1375812443, 1),
('Comment', 40, 1, 1, 1375812425, 1),
('Comment', 41, 1, 1, 1375812428, 1),
('Comment', 42, 1, 1, 1375812328, 1),
('Comment', 43, 1, 1, 1375812589, 1),
('Comment', 44, -1, 1, 1375766253, 0),
('Comment', 44, 1, 3, 1375766257, 1),
('Comment', 45, 1, 1, 1375766248, 1),
('Comment', 45, 1, 3, 1375766245, 1),
('Comment', 46, -1, 1, 1375766233, 0),
('Comment', 46, -1, 3, 1375766240, 0),
('Comment', 47, 1, 1, 1375812649, 1),
('Comment', 48, 1, 1, 1375812625, 1),
('Comment', 50, 1, 1, 1375724265, 1),
('Comment', 87, 1, 1, 1375812764, 1),
('Comment', 88, 1, 1, 1375812792, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `modelName` varchar(255) NOT NULL,
  `modelId` int(10) NOT NULL,
  `rating` int(10) NOT NULL,
  PRIMARY KEY (`modelName`,`modelId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ratings`
--

INSERT INTO `ratings` (`modelName`, `modelId`, `rating`) VALUES
('Comment', 13, 3),
('Comment', 14, -10),
('Comment', 15, 3),
('Comment', 16, 4),
('Comment', 17, 4),
('Comment', 18, 1),
('Comment', 19, 0),
('Comment', 23, -1),
('Comment', 25, -2),
('Comment', 26, 4),
('Comment', 32, 4),
('Comment', 33, -3),
('Comment', 34, 4),
('Comment', 35, 5),
('Comment', 36, 2),
('Comment', 37, 4),
('Comment', 38, 3),
('Comment', 39, 4),
('Comment', 40, 5),
('Comment', 41, 5),
('Comment', 42, 5),
('Comment', 43, 1),
('Comment', 44, 4),
('Comment', 45, 7),
('Comment', 46, 4),
('Comment', 47, 1),
('Comment', 48, 1),
('Comment', 50, 4),
('Comment', 87, 1),
('Comment', 88, 1),
('User', 1, -1),
('User', 3, 10);

-- --------------------------------------------------------

--
-- Структура таблицы `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uId` int(10) NOT NULL,
  `modelName` varchar(255) NOT NULL,
  `modelId` int(10) NOT NULL,
  `text` text NOT NULL,
  `state` tinyint(1) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Дамп данных таблицы `reports`
--

INSERT INTO `reports` (`id`, `uId`, `modelName`, `modelId`, `text`, `state`, `ctime`) VALUES
(1, 1, 'Torrent', 18, 'nlnljnjl', 0, 1375387580),
(2, 1, 'Torrent', 17, 'ччч', 0, 1375391238),
(3, 1, 'Torrent', 18, 'сссссс', 0, 1375391513),
(4, 1, 'Torrent', 18, 'сьбжьжжвс', 0, 1375391532),
(5, 1, 'Torrent', 17, 'ссссс', 0, 1375391692),
(6, 1, 'Torrent', 10, 'свввсвсв', 0, 1375391762),
(7, 1, 'Torrent', 10, 'сввссв', 0, 1375391820),
(8, 1, 'Torrent', 10, 'cdcd', 0, 1375391887),
(9, 1, 'Torrent', 10, 'cddcdcdc', 0, 1375391907),
(10, 1, 'Torrent', 10, 'dddddddddd', 0, 1375391931),
(11, 1, 'Torrent', 10, 'ckmcdc', 0, 1375391951),
(12, 1, 'Torrent', 10, 'ccccccccccc', 0, 1375392031),
(13, 1, 'Torrent', 10, 'cmdmdm;', 0, 1375392063),
(14, 1, 'Torrent', 10, 'cdcdsdc', 0, 1375392079),
(15, 1, 'Torrent', 10, 'cdmd;mm', 0, 1375392126),
(16, 1, 'Torrent', 10, 'cdmd;mm', 0, 1375392171),
(17, 1, 'Torrent', 10, 'cdmd;mm', 0, 1375392189),
(18, 1, 'Torrent', 10, 'c m dcdcm', 0, 1375392261),
(19, 1, 'Torrent', 10, 'cdcdd', 0, 1375392363),
(20, 1, 'Torrent', 10, 'cdcddcccdds', 0, 1375392391),
(21, 1, 'Torrent', 10, 'ccccccccccccc', 0, 1375392405),
(22, 1, 'Torrent', 10, 'dcl,,ldcl,d', 0, 1375392436),
(23, 1, 'Torrent', 10, 'cccccc', 0, 1375392584),
(24, 1, 'Torrent', 10, 'nknlnlnlnlnl', 0, 1375392795),
(25, 1, 'Comment', 13, 'плозо', 0, 1375397074),
(26, 1, 'Comment', 14, 'свысвыы', 0, 1375397182);

-- --------------------------------------------------------

--
-- Структура таблицы `staticPages`
--

CREATE TABLE IF NOT EXISTS `staticPages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `pageTitle` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `staticPages`
--

INSERT INTO `staticPages` (`id`, `title`, `pageTitle`, `content`, `url`, `published`) VALUES
(1, 'Правила', '', '<p>\r\n	<a href="http://streamzone.org/tracker/rules.html#rule1">1</a>.Общие положения.\r\n</p>\r\n<p>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.1"><strong>1.1</strong></a>  Настоящие правила обязательны для исполнения всеми без исключения  пользователями трекера: от Личера до Модератора (Дирекция и  Администраторы - как лица, эти правила устанавливающие, - поступают по  своему усмотрению).<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.2"><strong>1.2</strong></a>  StreamZone - торрент трекер является частным торрент-трекером, и его  внутренняя политика определяется исключительно владельцами данного  ресурса. Но Администрация готова принять к рассмотрению все предложения и  пожелания, которые, возможно, смогут улучшить работу трекера.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.3"><strong>1.3</strong></a>  За нарушение настоящих правил предусмотрены различные меры наказания:  от вынесения предупреждения до блокирования аккаунта, а в исключительных  случаях - блокирование IP-адреса нарушителя. Решение о наказании  нарушителя принимается непосредственно Администрацией, за исключением  нарушений, наказания за которые описаны в настоящих правилах.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.4"><strong>1.4</strong></a>  Запрещается создавать повторные аккаунты (аккаунты-клоны) на трекере.  Регулярные проверки позволяют оперативно находить и блокировать  нарушителей такого рода.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.5"><strong>1.5</strong></a>  Любые предложения и замечания, направленные на улучшение работы  трекера, категорически приветствуются. Администрация также просит вас  сообщать о всех ошибках и недостатках в работе трекера и форума.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.6"><strong>1.6</strong></a>  Общие вопросы по работе трекера обсуждаются в разделе "Общие вопросы и  статьи" форума. Вопросы по работе с различными торрент-клиентами  обсуждаются в тематических разделах. В случае, если для вашего  торрент-клиента нет тематического раздела в форуме, вы можете задать  вопрос по нему в разделе "Общие вопросы и статьи".<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.7"><strong>1.7</strong></a> Все частные проблемы пользователей должны решаться только в частном порядке с помощью системы личных сообщений.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.8"><strong>1.8</strong></a>  При регистрации необходимо указывать только рабочий e-mail. Мы  настоятельно не рекомендуем использовать бесплатные почтовые службы,  такие как: Mail.ru или Hotmail.com, из-за используемых ими "анти-спам"  систем. Если письмо с подтверждением регистрации/перерегистрации идёт  слишком долго, пожалуйста, попробуйте использовать другую почтовую  службу или обратитесь в FAQ.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.9"><strong>1.9</strong></a>  Администрация гарантирует, что никакая ваша персональная информация,  включая личный почтовый адрес, никогда не будет передана третьим лицам и  не будет использована в каком-либо виде без вашего согласия.  Убедительная просьба при регистрации указывать также и дополнительные  способы связи (например, номер ICQ, имя в Skype или MSN) для  использования в экстренных случаях.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.10"><strong>1.10</strong></a>  На трекере, в форуме, а также в подписи аккаунта категорически  запрещается размещать ссылки или текст, похожий на ссылку, на ресурсы  схожей тематики.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.11"><strong>1.11</strong></a> На трекере и в форуме запрещается также размещать любые сообщения рекламного характера.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.12"><strong>1.12</strong></a> Запрещается искусственное накручивание положительной и отрицательной репутации пользователям.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.13"><strong>1.13</strong></a>  Запрещается публично предъявлять претензии и обсуждать действия  модератора или администратора. Участник трекера, не согласный с  действиями модератора, может высказать своё несогласие модератору по  почте или в личном сообщении. Если от модератора нет ответа, или ответ,  по мнению участника, не обоснованный, последний вправе переписку с  модератором отправить администратору. Конечное решение принимает  администратор. Это решение является окончательным и необсуждаемым.<br>\r\n	 <a href="http://streamzone.org/tracker/rules.html#rule1.14"><strong>1.14</strong></a>  Запрещается имитация ников Администрации трекера. В случае нарушения  данного правила Вам будет вынесено предупреждение и ник будет изменен\r\n</p>\r\n<div>\r\n	<a href="http://streamzone.org/tracker/rules.html#rule2">2</a>.Общение на трекере и в форуме.\r\n	<p>\r\n		 <a href="http://streamzone.org/tracker/rules.html#rule2.1"><strong>2.1</strong></a>  Общение на трекере и в форуме должно происходить только с  использованием литературного русского языка. Другие языки, а также  транслит и язык "падонкафф" строго запрещены. Все сообщения, написанные  не на русском языке, будут удалены. Для удобства пользователей не из  России на трекере установлен легкий в использовании мод виртуальной  клавиатуры. Данное правило распространяется также и на имена профилей  (за исключением слов, написанных транслитом), и на информацию, которая в  них содержится.<br>\r\n		 <a href="http://streamzone.org/tracker/rules.html#rule2.2"><strong>2.2</strong></a>  Соблюдайте общепринятые правила поведения. Категорически запрещена  нецензурная брань во всех её проявлениях и на всех известных языках,  оскорбление пользователей трекера, любые действия, противоречащие  действующему законодательству РФ. Если вы считаете, что вас оскорбили -  обратитесь к Модераторам с жалобой. Будьте взаимно вежливыми и  корректными в своих высказываниях.<br>\r\n		 <a href="http://streamzone.org/tracker/rules.html#rule2.3"><strong>2.3</strong></a>  Прежде чем задавать какой-то вопрос на форуме, попытайтесь сначала  найти и ознакомиться со всей имеющейся на трекере и в форуме информацией  по этому вопросу.<br>\r\n		 <a href="http://streamzone.org/tracker/rules.html#rule2.4"><strong>2.4</strong></a>  На трекере и в форуме запрещены бессмысленные, бессодержательные  реплики, поток повторяющейся и/или ненужной информации (т.н. "флуд"), а  также сообщения в форуме, несоответствущие теме общения (т.н. "оффтоп").  Сюда же относятся сообщения, состоящие из одних смайлов. Использование  только заглавных букв в сообщениях также не приветствуется, т.к. они  воспринимаются другими пользователями трекера как крик (например, ЧТО  МНЕ ДЕЛАТЬ?) и очень бросаются в глаза. Захламление раздела в форуме  однотипными темами также считается флудом (подобные темы будут удалены  Модератором без объяснений).<br>\r\n		 <a href="http://streamzone.org/tracker/rules.html#rule2.5"><strong>2.5</strong></a>  Запрещено использовать красный цвет при общении на трекере и форуме, а  также в подписях. Этот цвет является привилегией Администрации.<br>\r\n		 <a href="http://streamzone.org/tracker/rules.html#rule2.6"><strong>2.6</strong></a>  Запрещено просить об изменении статуса раздачи на "Свободное  скачивание". Решение об этом статусе принимает только человек, создавший  раздачу.\r\n	</p>\r\n	<div>\r\n		<a href="http://streamzone.org/tracker/rules.html#rule3">3</a>.Раздачи.\r\n		<p>\r\n			 <a href="http://streamzone.org/tracker/rules.html#rule3.1"><strong>3.1</strong></a> Оставайтесь на раздаче торрента до тех пор, пока ваш ратио на этой раздаче не достигнет 1.<br>\r\n			 <a href="http://streamzone.org/tracker/rules.html#rule3.2"><strong>3.2</strong></a>  Все технические вопросы, связанные с раздачей (как установить, как  просмотреть), можно задавать как через комментарии к раздаче, так и в  соответствующей раздаче теме форума.<br>\r\n			 <a href="http://streamzone.org/tracker/rules.html#rule3.3"><strong>3.3</strong></a>  Обсуждение раздачи ведётся только в соответствующей раздаче теме  форума, если она создана (обычно об этом упоминается в описании  раздачи). В противном случае, обсуждение раздачи ведётся непосредственно  в комментариях к раздаче.<br>\r\n			 <a href="http://streamzone.org/tracker/rules.html#rule3.4"><strong>3.4</strong></a> Выразить благодарность за раздачу можно в соответствующей раздаче теме форума или с помощью кнопки "Спасибо" в самой раздаче.<br>\r\n			 <a href="http://streamzone.org/tracker/rules.html#rule3.5"><strong>3.5</strong></a>  Если вы заметили нарушение правил в раздаче, сообщите, пожалуйста, о  нём Модераторам личным сообщением или с помощью автоматизированной  системы (строка "Жалоба" в описании раздачи).<br>\r\n			 <a href="http://streamzone.org/tracker/rules.html#rule3.6"><strong>3.6</strong></a>  Если скачанная вами раздача неработоспособна или не соответствует  описанию торрента, сообщите об этом немедленно Модераторам с подробным  описанием проблемы и ссылкой на саму раздачу.\r\n		</p>\r\n		<div>\r\n			<a href="http://streamzone.org/tracker/rules.html#rule4">4</a>.Использование аватаров.\r\n			<p>\r\n				 <a href="http://streamzone.org/tracker/rules.html#rule4.1"><strong>4.1</strong></a>  В качестве аватара запрещены к использованию материалы следующего  характера: на религиозные и политические темы, изображающие жестокость,  насилие и порнографию.<br>\r\n				 <a href="http://streamzone.org/tracker/rules.html#rule4.2"><strong>4.2</strong></a> Рекомендуемые размеры изображения: 100 пикселей в ширину на 100 пикселей в высоту, размер файла не должен превышать 150 KБ.<br>\r\n				 <a href="http://streamzone.org/tracker/rules.html#rule4.3"><strong>4.3</strong></a>  Запрещается размещать аватары дублирующие или имитирующие аватары  администрации трекера, содержащие знаки отличия администрации трекера  или информацию о принадлежности к администрации трекера. Аватары,  нарушающие данное правило, будут удаляться администрацией, нарушителям  выноситься предупреждение. За систематические нарушения администрация  оставляет за собой право запретить пользователю размещать аватару в  профиле.\r\n			</p>\r\n			<div>\r\n				<a href="http://streamzone.org/tracker/rules.html#rule5">5</a>.Запросы и предложения.\r\n				<p>\r\n					 <a href="http://streamzone.org/tracker/rules.html#rule5.1"><strong>5.1</strong></a>  Если вы не видите ссылки Сделать запрос в разделе трекера Запросы, это  означает, что ваш рейтинг недостаточно высок для подачи запроса, и не  стоит в этом случае пытаться делать запросы в форуме.<br>\r\n					 <a href="http://streamzone.org/tracker/rules.html#rule5.2"><strong>5.2</strong></a> Запросы и предложения, оформленные не по шаблону или не полностью заполненные, удаляются без предупреждения.<br>\r\n					 <a href="http://streamzone.org/tracker/rules.html#rule5.3"><strong>5.3</strong></a> Повторные попытки создания запросов или предложений, нарушающих пункт выше, караются предупреждением.<br>\r\n					 <a href="http://streamzone.org/tracker/rules.html#rule5.4"><strong>5.4</strong></a>  Запросы публикуются на трекере без всяких обязательств по их  исполнению, поэтому, если на ваш запрос никто не ответил, не стоит  писать в комментариях к нему что-то подобное: "Ну что, ни у кого нет?",  "Ну когда уже выложат?".\r\n				</p>\r\n				<div>\r\n					<a href="http://streamzone.org/tracker/rules.html#rule6">6</a>.Общие правила для раздающих.\r\n					<p>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.1"><strong>6.1</strong></a>  Видеоматериалы низкого качества (CAMRip, TS или TC) могут раздаваться  на трекере только в следующем порядке: сначала - CAMRip, потом - TS и,  наконец, TC. При появлении в продаже лицензионного диска кинофильма или  DVDRip в Сети cледующий (и все последующие) релизы данного кинофильма  должны быть только в DVDRip качестве, даже если этот фильм еще не  раздавался на трекере. Данное правило не распространяется на TVRip и  релизы сериалов.<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.2"><strong>6.2</strong></a>  Обязательно проверьте ваш торрент в клиенте перед тем, как будете его  заливать на трекер. После заливки торрента на трекер скачайте его к себе  на жёсткий диск и используйте для раздачи только его! При оформлении  раздачи необходимо верно указывать качество видеоматериалов (CAMRip, TS,  TC, DVDScr, DVDRip, SATRip, TVRip и т.п.) и язык интерфейса программ и  игр. Мы стараемся поддерживать высокий уровень качества материалов,  размещаемых пользователями на трекере. Поэтому, в связи с тем, что в  последнее время в России появилось огромное число низкокачественных  пиратских DVD, переделанных из CAMRip, TS, TC или MPEG4, которые  некоторые "релиз-группы" и индивидуальные пользователи используют для  своих рипов, мы ужесточаем наши требования к раздающим. Рипы с таких  "DVD" не могут маркироваться на нашем трекере DVDRip (более правильное  обозначение - TC). В нашем представлении DVDRip - это рип с  лицензионного DVD 5-й зоны или рип с лицензионного DVD не 5-й зоны,  "переозвученный" наложением или заменой аудиодорожки. Наши  Администраторы прекрасно разбираются в вопросах качества релизов,  поэтому, если у вас возникли какие-либо сомнения или вопросы, вы всегда  можете обратиться к ним. Если Администратор изменил тип вашего релиза в  названии, не пытайтесь оспаривать это решение и пытаться переименовать  вашу раздачу - она просто будет удалена! Пожалуйста, раздавайте только  качественные материалы!<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.3"><strong>6.3</strong></a>  Все раздачи должны быть правильно названы, оформлены и сопровождаться  подробной информацией. Раздающий должен знать, что он раздаёт, а  пользователь имеет право знать, что он скачивает. Используйте для этой  цели специально разработанные шаблоны.<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.4"><strong>6.4</strong></a> Не защищайте свои раздачи паролями!<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.5"><strong>6.5</strong></a>  Если в этом нет особой необходимости, не раздавайте материалы в  архивированном виде (особенно файлы мультимедиа - .avi или .mp3). Если  все же раздаёте архивы, используйте, пожалуйста, распространенные  форматы файлов: .zip или .rar.<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.6"><strong>6.6</strong></a>  Убедитесь в том, что сможете поддерживать вашу раздачу достаточно  долгое время. Постарайтесь оставаться на связи круглосуточно. Если у вас  возникнут проблемы в процессе раздачи, обязательно сообщите об этом в  комментариях к торренту.<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.7"><strong>6.7</strong></a> Если вы используете режим суперсида, сообщите об этом в комментарии к раздаче.<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.8"><strong>6.8</strong></a>  Вы должны поддерживать раздачу до появления 6-ти скачавших или 3-х  раздающих. Если среди первых 6-ти скачавших не нашлось ни одного  раздающего, обязательно сообщите об этом Модераторам с указанием ссылки  на раздачу.<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.9"><strong>6.9</strong></a> Неверно оформленные торренты будут удаляться с трекера незамедлительно.<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.10"><strong>6.10</strong></a>  Вы должны регулярно выкладывать свежие раздачи. Нарушение данного  правила карается понижением в классе до Обычного пользователя (см. FAQ).<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.11"><strong>6.11</strong></a>  В раздачах категорически запрещается выпрашивание благодарностей или  респектов, повышения рейтинга торрента. Нарушители данного правила  рискуют быть забаненными без предупреждения.<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.12"><strong>6.12</strong></a> Названия торрент-файлов должны быть только на английском языке.<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.13"><strong>6.13</strong></a>  Допускается загрузка материала с русскими субтитрами только в том  случае, если указанный материал не выходил и не будет выходить с  голосовым переводом на русском языке. В случае если информацию о выходе  перевода установить не удается, то допускается загрузка материала с  субтитрами и указанием того, что информация о выпуске материала с  голосовым переводом неизвестна.<br>\r\n						 <a href="http://streamzone.org/tracker/rules.html#rule6.14"><strong>6.14</strong></a>  Запрещена раздача DVD материалов, где основной видеоряд пережимался.  Например, DVD официально выходит в формате DVD9, и если DVD5 можно  получить путем выкидывания дополнительных материалов и/или звуковых  дорожек, но не пережатия основного видеоряда, то такая раздача  допустима. При этом в комметариях вы должны указать, каким образом DVD  модифицировался.\r\n					</p>\r\n					<div>\r\n						<a href="http://streamzone.org/tracker/rules.html#rule7">7</a>.Теги.\r\n						<div>\r\n							 <a href="http://streamzone.org/tracker/rules.html#rule7.1"><strong>7.1</strong></a> Запрещено размещать теги, нарушающие установленные правила трекера.<br>\r\n							 <a href="http://streamzone.org/tracker/rules.html#rule7.2"><strong>7.2</strong></a> Все теги должны быть на корректном русском языке, по возможности без грамматических ошибок.<br>\r\n							 <a href="http://streamzone.org/tracker/rules.html#rule7.3"><strong>7.3</strong></a>  Запрещено использовать теги не по назначению и не относящиеся к  содержанию раздачи, такие теги будут удаляться модераторами с дальнейшим  устным предупреждением пользователям.<br>\r\n							 <a href="http://streamzone.org/tracker/rules.html#rule7.4"><strong>7.4</strong></a>  За систематическое нарушение выше перечисленных правил по использованию  тегов, администрация трекера будет выставлять предупреждения.\r\n						</div>\r\n					</div>\r\n				</div>\r\n			</div>\r\n		</div>\r\n	</div>\r\n</div>', 'rules', 1);
INSERT INTO `staticPages` (`id`, `title`, `pageTitle`, `content`, `url`, `published`) VALUES
(2, 'FAQ', '', '<p>\r\n	Общие вопросы\r\n</p>\r\n<ul>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq0">Что такое торрент (BitTorrent)? Как скачивать файлы?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq1">На что расходуются деньги от пожертвований?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq2">Где я могу взять исходники трекера?</a></li>\r\n</ul>\r\n<p>\r\n	Информация для пользователей\r\n</p>\r\n<ul>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq3">Я зарегистрировал аккаунт на трекере, но не получил письмо с подтверждением</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq4">Я забыл (или потерял) имя аккаунта или пароль, не могли бы вы прислать их мне?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq5">Не могли бы вы переименовать мой аккаунт?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq6">Что такое ратио (рейтинг)?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq7">Как мне повысить свой ратио (рейтинг)?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq8">Почему мой IP-адрес отображается на странице с деталями?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq9">Помогите! Я не могу войти на трекер (залогиниться)?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq10">У меня динамический IP-адрес. Что мне необходимо сделать, чтобы иметь возможность работать с трекером?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq11">Почему торрент-клиент выдаёт сообщение о невозможности подключения к трекеру (значение в строке Порт помечено красным цветом)?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq12">Какие классы пользователей существуют на трекере?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq13">Какие правила применимы к классам пользователей?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq14">Почему мои друзья не могут зарегистрироваться на трекере?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq15">Как мне добавить аватар в свой профиль?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq16">Что такое парковка аккаунта? </a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq17">А что это за значок  около торрента в списке?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq18">А что это за значок  около торрента в списке?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq19">А что это за значок  около торрента в списке?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq20">А что это за значок  рядом с моим именем?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq21">А что это за цифра рядом с моим ником и непонятные цифры рядом со словом Репутация?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq22">Как же повысить свой уровень и количество очков репутации?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq23">Что такое "польза" у сообщений на форуме и в комментариях? Она как-то связана с этими уровнями и репутацией?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq24">Что такое "Мой бонус"?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq25">За что я получаю бонус?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq26">Что такое bbcode (бибикод) и какие есть теги на сайте?</a></li>\r\n</ul>\r\n<p>\r\n	Статистика\r\n</p>\r\n<ul>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq27">Наиболее часто встречающиеся причины необновления статистики:</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq28">Полезные советы:</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq29">Какие торрент-клиенты можно использовать на трекере?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq30">Почему торрент, который я скачиваю/раздаю, отображается несколько раз в моем профиле?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq31">Я закончил или отменил торрент. Почему в моем профиле он все ещё отображается?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq32">Почему иногда в моем профиле присутствуют торренты, которые я никогда не качал!?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq33">Несколько IP-адресов или могу ли я работать с трекером с разных компьютеров?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq34">Как NAT/ICS может испортить малину?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq35">Для интересующихся ("анатомия" торрент-сессии)</a></li>\r\n</ul>\r\n<p>\r\n	Раздача или сидирование (Uploading)\r\n</p>\r\n<ul>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq36">Почему я не могу загружать торрент на трекер?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq37">Что мне необходимо сделать, чтобы стать Аплодером?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq38">Могу ли я передавать ваши торренты-файлы на другие трекеры?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq39">Как помочь с раздачей, как продолжить сидирование уже скачанного материала?</a></li>\r\n</ul>\r\n<p>\r\n	Загрузка или скачивание (Downloading)\r\n</p>\r\n<ul>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq40">Что мне делать с закаченными с трекера файлами?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq41">Вы хотите скачать фильм, но не понимаете значение терминов CAMRip, TS, TC, DVDScr в описании торрента?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq42">Почему торрент, только что бывший активным, вдруг исчез!?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq43">Как можно  продолжить скачивание, если торрент отсутствует в списке закачек  торрент-клиента по причине сбоя системы, смены самого клиента или по  другой причине?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq44">Почему мои закачки иногда останавливаются на 99%?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq45">Что означает сообщение "a piece has failed an hash check"?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq46">Размер торрента - 100 МБ. Как я мог скачать 120 МБ?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq47">Что такое "IOError - [Errno13] Permission denied"?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq48">Что такое "TTL" на страницах?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq49">Почему у меня вообще ничего не качается, хотя я использую нормальный клиент? (забаненные клиенты)</a></li>\r\n</ul>\r\n<p>\r\n	Расшифровка ошибок аннонсера и их решение\r\n</p>\r\n<ul>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq50">Bad client и Etot client zabanen. Chitayte FAQ.</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq51">Pozhaluysta obnovite versiyu svoego klienta ili smenite torrent-client</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq52">invalid port</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq53">Missing key: XXX</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq54">Invalid passkey (NNN - XXX) или Unknown passkey. Please redownload torrent from http://streamzone.org/tracker</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq55">Torrent not registered with this tracker</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq56">Vi sidiruete slishkom mnogo torrentov. Odnovremenno mozhno sidirovat 10 torrentov</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq57">Error, your account is parked! Please read the FAQ!</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq58">Vi ne mozhete kachat torrenti</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq59">You can''t leech or seed one torrent from one IP more than one time</a></li>\r\n</ul>\r\n<p>\r\n	Как можно увеличить скорость скачивания торрента?\r\n</p>\r\n<ul>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq60">Не пытайтесь скачивать новые торренты сразу после их выкладывания, особенно, если у вас низкая скорость.</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq61">Настройте свою оборудование на максимальную производительность.</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq62">Ограничьте свою скорость раздачи.</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq63">Ограничьте количество одновременных соединений.</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq64">Ограничьте количество одновременных раздач.</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq65">Не спешите с подключением к раздаче.</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq66">Почему страницы так медленно открываются, когда я качаю что-то?</a></li>\r\n</ul>\r\n<p>\r\n	Прокси-сервера. Работа с трекером из-под прокси-сервера.\r\n</p>\r\n<ul>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq67">Что такое прокси-сервер (proxy)?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq68">Как узнать, нахожусь я за прокси или нет?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq69">Почему написано, что невозможно подключится, хотя я не использую NAT или файервол?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq70">Можно ли обойти прокси-сервер моего провайдера?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq71">Как сделать, чтобы мой торрент-клиент использовал прокси?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq72">Почему я не могу зарегистрироваться из под прокси?</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq73">Данные правила действительны и на других торрент-трекерах?</a></li>\r\n</ul>\r\n<p>\r\n	Запрет на вход на трекер. Блокировка аккаунта.\r\n</p>\r\n<ul>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq74">Блокировка пользователя с занесением IP-адреса в черный список трекера.</a></li>\r\n	<li><a href="http://streamzone.org/tracker/faq.html#faq75">Блокировка IP-адреса трекера со стороны вашего провайдера.</a></li>\r\n</ul>\r\n<p>\r\n	Общие вопросы\r\n</p>\r\n<p>\r\n	<strong>Что такое торрент (BitTorrent)? Как скачивать файлы?</strong><br>\r\n	 Ответы на эти и другие подобные вопросы вы можете найти <a href="http://streamzone.org/tracker/phpbb2.php?page=viewtopic&amp;t=227">здесь</a>.\r\n</p>\r\n<p>\r\n	<strong>На что расходуются деньги от пожертвований?</strong><br>\r\n	 В будущем планируется приобретение выделенного сервера для быстрой и  стабильной работы трекера. Сейчас эти деньги идут на оплату хостинга.\r\n</p>\r\n<p>\r\n	<strong>Где я могу взять исходники трекера?</strong><br>\r\n	 Исходники вы можете найти на <a href="http://tbdevsz.ru/">tbdevsz</a>.  Хотелось бы обратить ваше внимание на то, что администрация трекера не  занимается технической поддержкой, поэтому используйте их на свой  собственный страх и риск.\r\n</p>\r\n<div>\r\n	Информация для пользователей\r\n	<p>\r\n		<strong>Я зарегистрировал аккаунт на трекере, но не получил письмо с подтверждением</strong><br>\r\n		 Попробуйте перерегистрироваться. Обращаем ваше внимание на то, что,  если в первый раз подтверждение на e-mail не пришло, вероятнее всего, и  повторное приглашение до вас не дойдёт. Попробуйте использовать при  регистрации другой адрес. Возможно, что письмо с подтверждением было  определено как спам, тогда вы сможете найти его в папке с нежелательной  почтой.\r\n	</p>\r\n	<p>\r\n		<strong>Я забыл (или потерял) имя аккаунта или пароль, не могли бы вы прислать их мне?</strong><br>\r\n		 Пожалуйста, воспользуйтесь <a href="http://streamzone.org/tracker/signup.php?type=recover">этой формой</a>, чтобы получить по электронной почте ваши регистрационные данные.\r\n	</p>\r\n	<p>\r\n		<strong>Не могли бы вы переименовать мой аккаунт?</strong><br>\r\n		 Мы не переименовываем аккаунты, однако вы можете сделать это сами, использовав свой бонус\r\n	</p>\r\n	<p>\r\n		<strong>Что такое ратио (рейтинг)?</strong><br>\r\n		 Ратио (от англ. ratio) - это соотношение отданных вами данных (upload) к загруженным (download).       Текущее значение вашего ратио показано вверху слева на панели информации, под вашим именем.<br>\r\n		       Необходимо различать общий ратио и ратио для каждого конкретного  торрента, который вы загружаете или раздаёте.       Общий ратио рассчитывается на основе суммарных показателей  загруженного и отданного вами с момента вашей регистрации на трекере.       Индивидуальный ратио каждого торрента учитывает только  количество загруженного/отданного для конкретного файла (торрента).<br>\r\n		       Возможны еще 2 варианта отображения ратио: "Inf." -       сокращение от слова Infinity (англ., бесконечность) - означает,  что вы ничего не загрузили, при этом отдав не нулевое количество данных;       или "---", которое должно читаться как "недоступно", - означает  что вы ещё ничего не отдали и не загрузили.\r\n	</p>\r\n	<p>\r\n		<strong>Как мне повысить свой ратио (рейтинг)?</strong><br>\r\n		 Это очень просто - отдавайте больше, чем загрузили, и ратио будет автоматически повышаться.\r\n	</p>\r\n	<p>\r\n		<strong>Почему мой IP-адрес отображается на странице с деталями?</strong><br>\r\n		 Не стоит волноваться о том, что другие пользователи смогут увидеть ваш  IP-адрес; эти данные доступны только лично вам и Модераторам трекера.\r\n	</p>\r\n	<p>\r\n		<strong>Помогите! Я не могу войти на трекер (залогиниться)?</strong><br>\r\n		 Иногда эта проблема возникает из-за глюков Internet Explorer. Закройте  все окна Internet Explorer`а и откройте Internet Options в панели  управления. Нажмите на кнопку Delete Cookies - это должно помочь.\r\n	</p>\r\n	<p>\r\n		<strong>У меня динамический IP-адрес. Что мне необходимо сделать, чтобы иметь возможность работать с трекером?</strong><br>\r\n		 Вам не нужно ничего делать дополнительно. Просто войдите на трекер  (залогиньтесь) под текущим IP-адресом при запуске новой торрент-сессии  (выборе нового торрент-файла). После этого, даже если ваш IP-адрес  поменяется в течении сессии, скачивание или раздача продолжатся, а  статистика обновится автоматически.\r\n	</p>\r\n	<p>\r\n		<strong>Почему торрент-клиент выдаёт сообщение о невозможности подключения к трекеру (значение в строке Порт помечено красным цветом)?</strong><br>\r\n		 Трекер обнаружил, что вы находитесь за файерволом или NAT, и ваш  компьютер недоступен для прямого подключения (иными словами порт  закрыт).<br>\r\n		       Это означает, что другие участники обмена не могут подключиться к вам, а возможно лишь подключение вас к ним.<br>\r\n		       Для решения этой проблемы необходимо открыть порт, используемый  для входящих соединений (такой же, как и в установках вашего  торрент-клиента), в файерволе и/или настройте ваш NAT-сервер.       (если это необходимо, обратитесь к документации к вашему роутеру  и/или на форум технической поддержки производителя оборудования;  необходимую информацию вы также сможете найти здесь: <a href="http://streamzone.org/tracker/redir.php?url=http://portforward.com/">PortForward</a>)\r\n	</p>\r\n	<p>\r\n		<strong>Какие классы пользователей существуют на трекере?</strong><br>\r\n		 Личер - до этого  класса понижаются Юзеры с низким ратио. Не могут скачивать новые  торренты, а лишь раздают старые. Могут заливать торренты на трекер.<br>\r\n		      Юзер - класс по умолчанию для новых пользователей. Одновременно  могут скачивать не более 5 торрентов. Могут заливать торренты на трекер.<br>\r\n		      Продвинутый юзер - одновременно могут скачивать не более 10 торрентов. Могут заливать торренты на трекер.<br>\r\n		        - люди, сделавшие пожертвования для трекера. Аккаунт таких  пользователей не удаляется автоматически из-за неактивности. Также,  такие пользователи не понижаются до класса Личер в следствии низкого  ратио, но могут получать предупреждения о низком ратио.<br>\r\n		      VIP - элитные  пользователи трекера (в чём-то аналогичны Продвинутым юзерам). Имеют  иммунитет от автоматического понижения в статусе. Отсутствуют  ограничения на количество одновременно загружаемых торрентов. Не  учитывается количество отданных и загруженных данных. Могут заливать  торренты на трекер.<br>\r\n		      Аплодер -  аналогичен классу Продвинутый юзер, может заливать торренты на трекер.  Понижается в классе, если не загружал торренты в течении 48 дней.<br>\r\n		      Модератор -  имеет право редактировать и удалять любой загруженный торрент,  модерировать пользователей, комментарии и блокировать аккаунты.<br>\r\n		      Администратор - может делать всё, что угодно. :)<br>\r\n		      СисОп - владелец сайта (<a href="http://streamzone.org/tracker/user/Nafania/">Nafania</a>).\r\n	</p>\r\n	<p>\r\n		<strong>Какие правила применимы к классам пользователей?</strong><br>\r\n		 Личер - если вы  зарегистрированы на трекере более 7 дней и у вас низкое ратио, то вы  будете автоматически понижены до этого класса с предварительным  уведомлением. Администрация сайта также может понизить вас без  каких-либо уведомлений и предупреждений.<br>\r\n		       Продвинутый юзер  - если вы зарегистрированы более 4 недель, раздали не менее 25 гигабайт  и имеете рейтинг 1.05 или выше, вы будете автоматически повышены до  этого класса. Также автоматически вы будете понижены в классе, как  только ваш рейтинг упадёт ниже 0.95.<br>\r\n		        - данный класс присваивается пользователям, которые пожертвовали некоторую сумму денег трекеру и сообщили об этом <a href="http://streamzone.org/tracker/message.php?action=send&amp;receiver=1">Nafania</a>.<br>\r\n		       VIP - данный класс присваивается пользователям, которые сделали что-то особенное для трекера (назначаются Модераторами).<br>\r\n		(выпрашивание VIP-статуса карается по закону военного времени! :)))<br>\r\n		       Аплодер - назначаются Администраторами/владельцами трекера (см. секцию "Раздача или сидирование (Uploading)").<br>\r\n		       Модератор - это не вы спрашиваете нас, это мы с вас спрашиваем. :)\r\n	</p>\r\n	<p>\r\n		<strong>Почему мои друзья не могут зарегистрироваться на трекере?</strong><br>\r\n		 Это означает, что в настоящее время лимит пользователей превышен.  Аккаунты, неактивные в течении более 28 дней, автоматически удаляются.  Желающие зарегистрироваться могут попробовать сделать это немного позже.       (на трекере не существует системы резервирования мест или  очереди; даже не спрашивайте нас об этом!)\r\n	</p>\r\n	<p>\r\n		<strong>Как мне добавить аватар в свой профиль?</strong><br>\r\n		 Сначала необходимо подобрать картинку, соответствующую <a href="http://streamzone.org/tracker/rules.php">правилам</a>. После этого необходимо найти сайт, например, <a href="http://photobucket.com/">Photobucket</a> или <a href="http://uploadit.org/">Upload-It!</a>, где вы сможете разместить свою картинку.   Адрес URL, который вам выдадут при размещении файла, вы должны поместить в поле Аватар в <a href="http://streamzone.org/tracker/my.php">своем профиле</a>.<br>\r\n		       Пожалуйста, не делайте посты в форуме только лишь для того,  чтобы протестировать аватар. Если всё сделано правильно, вы увидите  аватар на вашей <a href="http://streamzone.org/tracker/user/Nafania/">личной странице</a>\r\n	</p>\r\n	<p>\r\n		<strong>Что такое парковка аккаунта? <br>\r\n		 </strong>На трекере используется система автоочистки неактивных аккаунтов. Если  ваш аккаунт продолжительно время неактивен, то он автоматически  удаляется.       Парковка аккаунта необходима во избежание его удаления в  процессе автоочистки. Если вы надолго куда-либо уезжаете, то просто  припаркуйте свой аккаунт у себя в профиле и не беспокойтесь       о том, что он может быть удален. Хотя припаркованные аккаунты  тоже удаляются, срок их неактивности перед удалением значительно больше,  чем для простых, и составляет 175 дней.\r\n	</p>\r\n	<p>\r\n		<strong><strong>А что это за значок  около торрента в списке?</strong><br>\r\n		 Такой значок присваивается "бесплатным" торрентам. Это означает, что  при его скачивании у вас будет считаться только количество отданной  информации. Все скаченные на этом торренте данные не будут учтены в  глобальной статистике.</strong>\r\n	</p>\r\n	<p>\r\n		<strong><strong>А что это за значок  около торрента в списке?</strong><br>\r\n		 Такой значок присваивается "бесплатным серебрянным" торрентам. Это  означает, что при его скачивании у вас будет считаться только 50% от  количества скачанной информации.</strong>\r\n	</p>\r\n	<p>\r\n		<strong><strong>А что это за значок  около торрента в списке?</strong><br>\r\n		 Такой значок присваивается "бесплатным бронзовым" торрентам. Это  означает, что при его скачивании у вас будет считаться только 75% от  количества скачанной информации.</strong>\r\n	</p>\r\n	<p>\r\n		<strong><strong>А что это за значок  рядом с моим именем?</strong><br>\r\n		 Этот значок означает, что вы получили предупреждение за нарушение  правил трекера. Причина предупреждения, его длительность, а также имя  пользователя, который поставил вам его, посылаются вам в личном  сообщении. Полученное предупреждение означает для вас некоторые  ограничения на трекере:<br>\r\n		       1) вы не можете скачивать более одного торрента за раз,<br>\r\n		       2) вы не можете ставить людям респекты или антиреспекты.</strong>\r\n	</p>\r\n	<p>\r\n		<strong><strong>А что это за цифра рядом с моим ником и непонятные цифры рядом со словом Репутация?</strong><br>\r\n		 Цифра с вашим ником означает ваш уровень на трекере, а репутация -  количество полученных очков репутации. Чем больше у вас очков репутации,  тем выше ваш уровень.</strong>\r\n	</p>\r\n	<p>\r\n		<strong><strong>Как же повысить свой уровень и количество очков репутации?</strong><br>\r\n		 Очки репутации выдаются за разные полезные действия. Например за  загрузку торрентов, выполнение запросов по торрентам, полезные сообщения  на форуме и в комментариях. Чем больше полезных действий вы будете  выполнять, тем выше будет ваша репутация на трекере.</strong>\r\n	</p>\r\n	<p>\r\n		<strong><strong>Что такое "польза" у сообщений на форуме и в комментариях? Она как-то связана с этими уровнями и репутацией?</strong><br>\r\n		 Да, связана. Польза сообщений изменяется другими пользователями путём  нажатия стрелок. Если вам нравится сообщение или комментарий какого-либо  пользователя или в комментарии написана полезная информация, то не  поленитесь и нажмите на стрелочку вверх. Таким образом вы увеличите  репутацию того пользователя, который оставил сообщение.</strong>\r\n	</p>\r\n	<p>\r\n		<strong><strong>Что такое "Мой бонус"?</strong><br>\r\n		 Это уникальная функция трекера. "Мой бонус" - это премиальные очки,  которые вы получаете в процессе сидирования (раздачи) торрента.</strong>\r\n	</p>\r\n	<p>\r\n		<strong><strong>За что я получаю бонус?</strong><br>\r\n		 За каждый час сидирования вы получаете бонус в размере 1  очков. Бонус  считается только за время и не зависит от количества сидируемых  торрентов.</strong>\r\n	</p>\r\n	<p>\r\n		<strong><strong>Что такое bbcode (бибикод) и какие есть теги на сайте?</strong><br>\r\n		 BBcode это специальный язык разметки для форматирования сообщений.  Список поодерживаемых тегов вы можете узнать, пройдя по этой ссылке - <a href="http://streamzone.org/tracker/faq_tags.html">Полный список тегов</a></strong>\r\n	</p>\r\n	<div>\r\n		<strong>Статистика\r\n		<p>\r\n			<strong>Наиболее часто встречающиеся причины необновления статистики:</strong><br>\r\n			 1) пользователь - читер.<br>\r\n			       2) сервер перегружен и не отвечает. По возможности, не  закрывайте сессию до тех пор, пока сервер не заработает снова  (зафлуживание сервера путём периодического ручного обновления страницы  не рекомендуется).<br>\r\n			       3) используется нестабильный/нерабочий торрент-клиент. Желаете  пользоваться экспериментальной версией торрент-клиент, делайте это на  свой страх и риск!\r\n		</p>\r\n		<p>\r\n			<strong>Полезные советы:</strong><br>\r\n			 1) если торрент, который вы скачиваете/раздаёте, не отображен в списке  ваших закачек, просто подождите некоторое время или сделайте обновление  странички вручную.<br>\r\n			       2) убедитесь, что вы правильно закрыли ваш клиент, и трекер получил сообщение "event=completed".<br>\r\n			       3) если сервер упал и лежит некоторое время, не прекращайте  раздачу. Если его восстановят до того, как вы выйдете из клиента,  статистика обновится автоматически.\r\n		</p>\r\n		<p>\r\n			<strong>Какие торрент-клиенты можно использовать на трекере?</strong><br>\r\n			 На текущий момент трекер обновляет статистику корректно при использовании любого торрент-клиента (кроме забаненных конечно).       Тем не менее, мы настоятельно <strong>не рекомендуем</strong> использование следующих клиентов:<br>\r\n			       • BitTorrent++,<br>\r\n			       • Nova Torrent,<br>\r\n			       • TorrentStorm.<br>\r\n			       Эти клиенты неверно обрабатывают отмену/остановку торрент-сессии. Если вы их используете, возможна ситуация,       когда в деталях торренты будут отображаться даже после завершения загрузки или закрытия клиента.<br>\r\n			       Также не приветствуется использование клиентов тестовых версий (alpha или beta).\r\n		</p>\r\n		<p>\r\n			<strong>Почему торрент, который я скачиваю/раздаю, отображается несколько раз в моем профиле?</strong><br>\r\n			 Если по некоторым причинам (например, экстренная перезагрузка  компьютера или зависание клиента) ваш клиент завершил работу  некорректно,       и вы перезапустили его, вам будет выдан новый "peer_id", таким  образом ваша закачка будет опознана, как новый (другой) торрент.       А по старому торренту сервер так никогда и не получит сообщение  "event=completed" или "event=stopped"       и будет отображать его некоторое время в списке ваших активных  торрентов.       Не обращайте на это внимания, в конечном счёте этот глюк  пропадёт.\r\n		</p>\r\n		<p>\r\n			<strong>Я закончил или отменил торрент. Почему в моем профиле он все ещё отображается?</strong><br>\r\n			 Некоторые клиенты, особенно TorrentStorm и Nova Torrent, не отправляют серверу сообщение о прекращении или отмене торрента.       В таких случаях трекер будет ждать сообщения от вашего клиента и показывать скачивание/раздачу ещё некоторое время.       Не обращайте на это внимание, через некоторое время торрент все-таки пропадёт из списка активных.\r\n		</p>\r\n		<p>\r\n			<strong>Почему иногда в моем профиле присутствуют торренты, которые я никогда не качал!?</strong><br>\r\n			 Когда запускается торрент-сессия трекер использует так называемый пасскей (passkey) для идентификации пользователя.       Возможно кто-то украл или узнал ваш пасскей. Обязательно смените его у себя в <a href="http://streamzone.org/tracker/my.php">профиле</a>, если вдруг обнаружите такое. Учтите, что после смены пасскея вам придется перекачать все активные торренты.\r\n		</p>\r\n		<p>\r\n			<strong>Несколько IP-адресов или могу ли я работать с трекером с разных компьютеров?</strong><br>\r\n			 Да, трекер поддерживает несколько сессий с разных IP-адресов для одного  пользователя. Торрент ассоциируется с пользователем в тот момент, когда  он запускает закачку, и только в этот момент IP-адрес важен.       Таким образом, если вы хотите скачивать/раздавать с компьютера А  и компьютера Б, используя один и тот же аккаунт, вам необходимо войти  на сайт с компьютера А,       запустить торрент, и затем проделать то же самое с компьютера Б  (2 компьютера использовано только для примера,       ограничений на количество нет; главное - выполнять оба шага на  каждом из компьтеров).       Вам не нужно перелогиниваться заново, когда вы закрываете  клиент.\r\n		</p>\r\n		<p>\r\n			<strong>Как NAT/ICS может испортить малину?</strong><br>\r\n			 В случае использования NAT вам необходимо настроить разные диапазоны  для торрент-клиентов на разных компьютерах,       и создать NAT-правила в роутере (подробности настройки роутеров  выходят за рамки данного FAQ`а,       поэтому обратитесь к документации к вашему устройству и/или на  форум техподдержки).       Часто в сетях нет возможности конфигурировать роутеры по своему  усмотрению, и вам придется пользоваться трекером на свой страх и риск.       За ошибки, связанные с работой за NAT, администрация  ответственности не несёт.\r\n		</p>\r\n		<p>\r\n			<strong>Для интересующихся ("анатомия" торрент-сессии)</strong><br>\r\n			 Некоторую информацию об "анатомии" торрент-сессии вы можете найти <a href="http://streamzone.org/tracker/faq_anatomy.html">здесь</a>.\r\n		</p>\r\n		<div>\r\n			Раздача или сидирование (Uploading)\r\n			<p>\r\n				<strong>Почему я не могу загружать торрент на трекер?</strong><br>\r\n				 Вы можете загружать торренты на трекер. Пройдите по <a href="http://streamzone.org/tracker/upload.php">этой ссылке</a> и следуйте инструкциям.\r\n			</p>\r\n			<p>\r\n				<strong>Что мне необходимо сделать, чтобы стать Аплодером?</strong><br>\r\n				 Заполните <a href="http://streamzone.org/tracker/uploadapp.php">эту анкету</a>, после чего дождитесь решения администрации трекера.\r\n			</p>\r\n			<p>\r\n				<strong>Могу ли я передавать ваши торренты-файлы на другие трекеры?</strong><br>\r\n				 Нет. Возможность скачивания торрентов с трекера Streamzone имеют право  только зарегистрированные пользователи,       поэтому простое копирование торрент-файлов не даёт другим  пользователям прав на скачивание файлов с трекера.   Чтобы не повредить репутации трекера Streamzone, мы обращаемся с  убедительной просьбой к зарегистрированным пользователям использовать   торрент-файлы только в пределах данного трекера. При этом сами  скаченные данные вы можете использовать, как вам заблагорассудится.\r\n			</p>\r\n			<p>\r\n				<strong>Как помочь с раздачей, как продолжить сидирование уже скачанного материала?</strong><br>\r\n				 В случае, если вы хотите продолжить сидирование, но удалили  торрент-файл или задание из торрент-клиента и если файл/папка только  перемещён(а) и не был переименован(а),       то вам необходимо только скачать торрент-файл со странички  раздачи и указать в качестве места для хранения файлов торрента  местоположение этого файла/папки на вашем компьютере.       Клиент проведёт хеш-проверку (или сделайте это сами) и запустит  торрент в режиме сидирования.<br>\r\n				       Если же файл или папка были переименована(ы), то сначала вам  необходимо вернуть оригинальное(ые) название(я) файлу (папке), а затем  повторить действия, описанные выше.<br>\r\n				       Если у вас есть желание помочь кому-либо с раздачей, и вы  уверены, что файл(ы), имеющийся(иеся) у вас, аналогичен(ны)  участвующему(им) в раздаче, то сначала проверьте размер вашего(их)  файла(ов).       Для этого зайдите на страничку торрента и сравните размер  файла(ов), указанный(ые) в описании в <strong>байтах</strong>, с размером файла(ов), имеющегося(ихся) у вас. При совпадении размеров необходимо будет повторить все действия, описанные выше.\r\n			</p>\r\n			<div>\r\n				Загрузка или скачивание (Downloading)\r\n				<p>\r\n					<strong>Что мне делать с закаченными с трекера файлами?</strong><br>\r\n					 Скорее всего, ответ вы найдёте <a href="http://streamzone.org/tracker/faq_formats.html">здесь</a>.\r\n				</p>\r\n				<p>\r\n					<strong>Вы хотите скачать фильм, но не понимаете значение терминов CAMRip, TS, TC, DVDScr в описании торрента?</strong><br>\r\n					 Вам <a href="http://streamzone.org/tracker/faq_videoformats.html">сюда</a>.\r\n				</p>\r\n				<p>\r\n					<strong>Почему торрент, только что бывший активным, вдруг исчез!?</strong><br>\r\n					 На это может быть несколько причин:<br>\r\n					       1) торрент не соответствовал <a href="http://streamzone.org/tracker/rules.html">правилам</a>.<br>\r\n					       2) Аплодер удалил его, т.к. релиз оказался некачественным. Возможно, он будет заменён другим релизом немного позже.<br>\r\n					       3) торренты автоматически удаляются по истечении TTL (англ. Time to Live - время жизни).\r\n				</p>\r\n				<p>\r\n					<strong>Как  можно продолжить скачивание, если торрент отсутствует в списке закачек  торрент-клиента по причине сбоя системы, смены самого клиента или по  другой причине?</strong><br>\r\n					 Откройте торрент-файл (расширение .torrent), сохраненный на вашем  компьютере, или загрузите его с трекера.               Укажите торрент-клиенту место на вашем компьютере, где  располагаются недокаченные файлы. Закончив проверку файлов, клиент  продолжит закачку.\r\n				</p>\r\n				<p>\r\n					<strong>Почему мои закачки иногда останавливаются на 99%?</strong><br>\r\n					 У вас уже скачано достаточно большое количество частей, и клиент  пытается найти пользователей,       у которых есть части, которые у вас отсутствуют или скачаны с  ошибками. Поэтому загрузка иногда может останавливаться в тот момент,       когда до завершения осталось всего несколько процентов.  Потерпите немножко, и в скором (ну или не очень :))       времени клиент докачает все недостающие части. Такая проблема  может возникать также при использовании некоторых клиентов -       для проверки вашего клиента используйте другой торрент-клиент,  например, uTorrent или Azureus.\r\n				</p>\r\n				<p>\r\n					<strong>Что означает сообщение "a piece has failed an hash check"?</strong><br>\r\n					 Торрент-клиенты проверяют принятые данные на наличие ошибок.       Если порция данных получена с ошибкой, клиент автоматически  будет закачивать эти данные до тех пор, пока ошибка не исчезнет.       Такая ситуация случается достаточно часто, поэтому вам не стоит  беспокоиться по этому поводу.<br>\r\n					       В некоторых клиентах есть возможность автоматической блокировки пользователей, постоянно присылающих части с ошибками.\r\n				</p>\r\n				<p>\r\n					<strong>Размер торрента - 100 МБ. Как я мог скачать 120 МБ?</strong><br>\r\n					 См. предыдущий пункт. Если ваш торрент-клиент получает порции данных с ошибками, он вынужден закачивать их вновь и вновь.       Таким образом, общее количество закаченного может превышать размер самого торрента.\r\n				</p>\r\n				<p>\r\n					<strong>Что такое "IOError - [Errno13] Permission denied"?</strong><br>\r\n					 Для решения данной проблемы достаточно просто перезагрузить компьютер.       А для любопытных поясняем:<br>\r\n					       IOError означает ошибку ввода-вывода, и это ошибка вашей системы  (компьютера), а не трекера.       Она появляется в тот момент, когда торрент-клиент по разным  причинам не может открыть закачиваемые файлы.       Наиболее вероятная из них - запущено одновременно 2 клиента.  Например, вы попытались закрыть клиента, но он не завершил свою работу,   а просто "подвис" и остался в памяти компьютера. И при попытке  запуска второй копии программы, она выдаст вам подобное сообщение,       т.к. первая копия программы просто-напросто заблокирует рабочие  файлы.<br>\r\n					       Более редкий случай - ошибка в таблице размещения вашей файловой  системы (FAT или FAT32), когда загруженные файлы будут читаться с  ошибкой или не читаться совсем.       (это более свойственно компьютерам с ОС Windows 9x, которая  использует по умолчанию FAT или FAT32, а также с Windows NT/2000/XP,   когда была выбрана FAT вместо NTFS при установке. NTFS - более  надёжная файловая система, и она не должна приводить к ошибкам подобного  рода)\r\n				</p>\r\n				<p>\r\n					<strong>Что такое "TTL" на страницах?</strong><br>\r\n					 Это время жизни конкретного торрента.   Показывает через какое время торрент будет удалён с сервера (даже если он будет оставаться активным).   Помните, что это максимальное значение! Если торрент долгое время неактивен, он может быть удалён и раньше.\r\n				</p>\r\n				<p>\r\n					<strong>Почему у меня вообще ничего не качается, хотя я использую нормальный клиент? (забаненные клиенты)</strong><br>\r\n					 Возможно, вы используете один из забаненых (отключенных) клиентов.<br>\r\n					       На нашем треккере нельзя использовать следующих клиентов или их версии:<br>\r\n					       ABC - версии ниже 3.01,<br>\r\n					       Bitlord - все версии,<br>\r\n					       BitComet - все версии,<br>\r\n					       BitSpirit - все версии,<br>\r\n					       Opera BT client (встроенный в браузер Opera торрент-клиент) - все версии,<br>\r\n					       FlashGet - все версии.<br>\r\n					       Если речь идёт только о конкретных версиях вашего клиента, то вы  сможете работать с нашим трекером, лишь обновив его версию.       В противном случае вам придется сменить программу, иначе вы не  сможете ничего скачать с нашего трекера.\r\n				</p>\r\n				<div>\r\n					Расшифровка ошибок аннонсера и их решение\r\n					<p>\r\n						<strong>Bad client и Etot client zabanen. Chitayte FAQ.</strong><br>\r\n						 Используемый торрент-клиент не работает с нашим трекером, смените его.\r\n					</p>\r\n					<p>\r\n						<strong>Pozhaluysta obnovite versiyu svoego klienta ili smenite torrent-client</strong><br>\r\n						 Версия используемого торрент-клиента не работает с нашем трекером. Обновите свой торрент-клиент или смените его.\r\n					</p>\r\n					<p>\r\n						<strong>invalid port</strong><br>\r\n						 Используется неверный порт в торрент-клиенте, поменяйте его.\r\n					</p>\r\n					<p>\r\n						<strong>Missing key: XXX</strong><br>\r\n						 При передаче данных от вашего клиента трекеру не передаётся ключ XXX.  Если раньше всё было нормально, и вдруг появилась ошибка, то попробуйте  остановить торрент и снова запустить его. Иначе - смените  торрент-клиент.\r\n					</p>\r\n					<p>\r\n						<strong>Invalid passkey (NNN - XXX) или Unknown passkey. Please redownload torrent from http://streamzone.org/tracker</strong><br>\r\n						 У вас неверный пасскей. Смените пасскей в панели управления и перекачайте активные торренты с трекера.\r\n					</p>\r\n					<p>\r\n						<strong>Torrent not registered with this tracker</strong><br>\r\n						 Торрента, за которым обращается ваш клиент, на трекере нет. Скорее всего, он был удалён. Удалите задание из торрент-клиента.\r\n					</p>\r\n					<p>\r\n						<strong>Vi sidiruete slishkom mnogo torrentov. Odnovremenno mozhno sidirovat 10 torrentov</strong><br>\r\n						 Вы сидируете слишком много торрентов. Одновременно можно сидировать не  более 10 торрентов. Приостановите те торренты, где число личеров  невелико или их нет совсем. Старайтесь сидировать только те торренты,  где число личеров велико или вы являетесь единственным сидером.\r\n					</p>\r\n					<p>\r\n						<strong>Error, your account is parked! Please read the FAQ!</strong><br>\r\n						 Ваш аккаунт припаркован, и вы не можете качать торренты. Отключите парковку в панели управления.\r\n					</p>\r\n					<p>\r\n						<strong>Vi ne mozhete kachat torrenti</strong><br>\r\n						 Вы не можете качать торренты. Скорее всего, ваш класс - Личер, и эта  возможность у вас отсутствует; возможна также ситуация, когда  администрацией трекера введён запрет на скачивание с вашего аккаунта.  Сидируйте торренты, чтобы повысить своё ратио, и тогда запрет на  скачивание обязательно будет снят.\r\n					</p>\r\n					<p>\r\n						<strong>You can''t leech or seed one torrent from one IP more than one time</strong><br>\r\n						 Вы не можете сидировать или качать один и тот же торрент с одного  IP-адреса более одного раза. Иными словами, на один торрент с одного  IP-адреса аккаунта пользователя допустимо одно соединение. Остановите  задание в торрент-клиенте.\r\n					</p>\r\n					<div>\r\n						Как можно увеличить скорость скачивания торрента?\r\n						<p>\r\n							<strong>Не пытайтесь скачивать новые торренты сразу после их выкладывания, особенно, если у вас низкая скорость.</strong><br>\r\n							 Позвольте сначала скачать данные пользователям с широкими каналами  доступа, что позволит всем остальным, в том числе и вам, в дальнейшем  качать торрент без задержек и с комфортной скоростью.   Наилучший момент для присоединения к раздаче находится примерно в  середине жизни торрента, однако возможности вашего последущего  сидирования будут ограничены из-за того,       что большая часть пользователей уже скачала данный торрент. Ваша  основная задача - соблюсти баланс между этими двумя условиями. :)\r\n						</p>\r\n						<p>\r\n							<strong>Настройте свою оборудование на максимальную производительность.</strong><br>\r\n							 См. <em>Почему торрент-клиент выдаёт сообщение о невозможности подключения к трекеру? (в строке Порт значение выделено красным цветом)</em>\r\n						</p>\r\n						<p>\r\n							<strong>Ограничьте свою скорость раздачи.</strong><br>\r\n							 Большинство торрент-клиентов настроены на сбалансированный обмен данными между пользователями, когда       скорость отдачи будет примерно равна скорости закачивания. Поэтому возможны случаи, когда скорость   загрузки будет напрямую зависеть от скорости отдачи. Например, если пользователи А и Б скачивают один   и тот же файл, и А отсылает данные Б с высокой скоростью, тогда Б будет стараться отдать данные с той   скоростью и в том же объёме. Таким образом, высокая скорость отдачи ведёт к высокой скорости загрузки.<br>\r\n							       Процесс обмен данными между торрент-клиентами А и Б подтверждается системными сообщениями ACKs (одним   из видов сообщения "получено!"). Если один из клиентов не сможет подтвердить приём данных в   процессе обмена, то другой клиент приостановит раздачу. Соответственно упадет и скорость закачивания   торрента, и скорость его отдачи. Для предотвращения подобных ситуаций необходимо сбалансированно подойти   к процессу скачивания/раздачи торрентов.<br>\r\n							<strong>Самое простое решение - ограничить скорость отдачи в пределах 80% от теоретически возможной.</strong>       Однако, в любом случае, вам потребуется более точная настройка, т.к. для каждой системы эти параметры   подбираются индивидуально. Одни клиенты (например, Azureus) могут ограничивать общую скорость раздачи,   другие (например, Shad0w`s) позволяют ввести ограничения для каждого отдельного торрента. Помните также,   что часть вашего канала потребуется другим программам, которым необходим доступ в Интернет.\r\n						</p>\r\n						<p>\r\n							<strong>Ограничьте количество одновременных соединений.</strong><br>\r\n							 Некоторые операционные системы (такие как Windows 9x) плохо  "переваривают" большое количество одновременных соединений       и даже могут подвиснуть. Также некоторые домашние роутеры  (особенно когда запущен NAT и/или файервол в режиме сканирования)       могут снижать скорость соединения или зависать, когда  задействовано большое число активных соединений. В данном случае не  существует удиверсального решения, вам       необходимо будет под', 'faq', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `tagRelations`
--

CREATE TABLE IF NOT EXISTS `tagRelations` (
  `modelId` int(10) unsigned NOT NULL,
  `tagId` int(10) unsigned NOT NULL,
  `modelName` varchar(45) NOT NULL,
  PRIMARY KEY (`modelId`,`tagId`,`modelName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `tagRelations`
--

INSERT INTO `tagRelations` (`modelId`, `tagId`, `modelName`) VALUES
(1, 8, 'Torrent'),
(1, 9, 'Torrent'),
(1, 10, 'Torrent'),
(1, 11, 'Torrent'),
(2, 3, 'Torrent'),
(2, 4, 'Torrent'),
(2, 5, 'Torrent'),
(2, 6, 'Torrent'),
(2, 7, 'Torrent'),
(3, 8, 'Torrent'),
(3, 9, 'Torrent'),
(3, 10, 'Torrent'),
(3, 11, 'Torrent'),
(4, 16, 'TorrentGroup'),
(4, 17, 'TorrentGroup'),
(4, 18, 'TorrentGroup'),
(4, 19, 'TorrentGroup'),
(5, 8, 'Torrent'),
(5, 12, 'Torrent'),
(5, 13, 'Torrent'),
(5, 14, 'Torrent'),
(6, 17, 'TorrentGroup'),
(6, 20, 'TorrentGroup'),
(6, 21, 'TorrentGroup'),
(7, 22, 'TorrentGroup'),
(7, 23, 'TorrentGroup'),
(7, 24, 'TorrentGroup'),
(8, 13, 'TorrentGroup'),
(8, 25, 'TorrentGroup'),
(9, 9, 'TorrentGroup'),
(10, 9, 'TorrentGroup'),
(11, 22, 'Torrent'),
(11, 23, 'Torrent'),
(11, 24, 'Torrent'),
(12, 4, 'TorrentGroup'),
(13, 4, 'TorrentGroup'),
(14, 13, 'TorrentGroup'),
(14, 14, 'TorrentGroup'),
(14, 27, 'TorrentGroup'),
(14, 28, 'TorrentGroup'),
(14, 29, 'TorrentGroup'),
(15, 11, 'TorrentGroup'),
(15, 30, 'TorrentGroup'),
(16, 4, 'Torrent'),
(17, 4, 'Torrent'),
(21, 11, 'Torrent'),
(21, 30, 'Torrent'),
(25, 1, 'Torrent'),
(25, 2, 'Torrent'),
(26, 1, 'Torrent'),
(26, 2, 'Torrent'),
(27, 1, 'Torrent'),
(27, 2, 'Torrent'),
(28, 3, 'Torrent'),
(28, 4, 'Torrent'),
(29, 3, 'Torrent'),
(29, 4, 'Torrent');

-- --------------------------------------------------------

--
-- Структура таблицы `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `userId` int(10) NOT NULL,
  `count` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Tag_name` (`name`),
  KEY `count` (`count`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Дамп данных таблицы `tags`
--

INSERT INTO `tags` (`id`, `name`, `userId`, `count`) VALUES
(1, 'Rap', 1, 2),
(2, 'Hip-hop', 1, 2),
(3, 'Экшн', 1, 2),
(4, 'Комедия', 1, 6),
(5, 'перевод Гоблина', 1, 0),
(6, 'Гоблин', 1, 0),
(7, 'Goblin', 1, 0),
(8, 'Боевик', 1, 3),
(9, 'Детектив', 1, 4),
(10, 'Криминал', 1, 2),
(11, 'Триллер', 1, 4),
(12, 'фантастика', 1, 1),
(13, 'фэнтези', 1, 3),
(14, 'приключения', 1, 2),
(15, 'Кака', 1, 0),
(16, 'RPG', 1, 1),
(17, '3D', 1, 2),
(18, '1st Person', 1, 1),
(19, '3rd Person', 1, 1),
(20, 'Arcade', 1, 1),
(21, 'Fighting', 1, 1),
(22, 'Навител навигатор', 1, 2),
(23, 'карты', 1, 2),
(24, 'навигация', 1, 2),
(25, 'мистика', 1, 1),
(26, 'мелодрама', 1, 0),
(27, 'мультфильм', 1, 1),
(28, 'семейный', 1, 1),
(29, 'мультипликация', 1, 1),
(30, 'ужасы', 1, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_migration`
--

CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `tbl_migration`
--

INSERT INTO `tbl_migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1375876650);

-- --------------------------------------------------------

--
-- Структура таблицы `torrentCommentsRelations`
--

CREATE TABLE IF NOT EXISTS `torrentCommentsRelations` (
  `commentId` int(10) NOT NULL,
  `torrentId` int(10) NOT NULL,
  PRIMARY KEY (`commentId`,`torrentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `torrentCommentsRelations`
--

INSERT INTO `torrentCommentsRelations` (`commentId`, `torrentId`) VALUES
(1, 11),
(2, 11),
(4, 11),
(8, 11),
(9, 11),
(10, 11),
(11, 11),
(12, 11),
(13, 17),
(14, 17),
(15, 18),
(16, 18),
(17, 18),
(32, 17),
(33, 17),
(34, 17),
(49, 17),
(64, 1),
(65, 1),
(71, 5),
(72, 5),
(90, 14);

-- --------------------------------------------------------

--
-- Структура таблицы `torrentGroups`
--

CREATE TABLE IF NOT EXISTS `torrentGroups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `ctime` int(11) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `mtime` int(10) NOT NULL,
  `cId` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Дамп данных таблицы `torrentGroups`
--

INSERT INTO `torrentGroups` (`id`, `title`, `ctime`, `picture`, `mtime`, `cId`, `uid`) VALUES
(1, 'Эволюция Борна / The Bourne Legacy / 2012', 1375019822, 'uploads/images/TorrentGroup/1/995cd4b8daa050e2ed9a2a0c80a0b6f0.jpg', 1375137004, 1, 0),
(2, 'Росомаха: Бессмертный / The Wolverine / 2013', 1375025671, 'uploads/images/TorrentGroup/2/7c767d30c13801734d228509344210ae.jpg', 1375025869, 1, 0),
(3, 'T1One / По - настоящему / 2013', 1375026137, 'uploads/images/TorrentGroup/3/b7a9564c7f80de5a006b14f19e26d28f.jpg', 1375026267, 2, 0),
(4, 'The Elder Scrolls V: Skyrim / 2013', 1375028777, 'uploads/images/TorrentGroup/4/4f7938dd3459439edcd056bb14d94073.png', 1375200406, 3, 0),
(6, 'Mortal Kombat: Komplete Edition / 2013', 1375195220, 'uploads/images/TorrentGroup/6/9a2f3ddfd9fbd456039d6761e009e83f.png', 1375195398, 3, 1),
(7, 'Навител Навигатор / 7.5.0.2131 / 2013', 1375202106, 'uploads/images/TorrentGroup/7/3f04361f0a0888379b6d0eec79271a91.jpg', 0, 4, 1),
(8, 'Разные / Книжная серия "Сумерки" / 2009-2013', 1375259915, 'uploads/images/TorrentGroup/8/498e3a1aa0ab791182e2d7838c6b2f99.jpeg', 1375259915, 5, 1),
(10, 'Джеймс Хедли Чейз / Дело о наезде / 2013', 1375260566, 'uploads/images/TorrentGroup/10/83ac3b45d8b12fa8ebd174c505ed94b4.jpg', 1375260566, 5, 1),
(11, 'Любовь без пересадок / Amour &amp; turbulences / 2013', 1375275693, 'uploads/images/TorrentGroup/11/2c1f186f356d0900786008d02a1c22d9.jpg', 1375275693, 1, 1),
(12, 'Марафон / 2013', 1375275901, 'uploads/images/TorrentGroup/12/a5a7d26b9608a94024438000825232fc.jpg', 1375275901, 1, 1),
(13, 'СашаТаня / 1 сезон / 2013', 1375277135, 'uploads/images/TorrentGroup/13/8b16946402f5713f88cc3d7f30840c68.png', 1375277392, 7, 1),
(14, 'Эпик / Epic / 2013', 1375868998, 'uploads/images/TorrentGroup/14/57a89e1defe199444181749aaf23cae6.jpg', 1375869197, 1, 1),
(15, 'Омут / Внизу / Beneath / 2013', 1375869643, 'uploads/images/TorrentGroup/15/afd7d446894eb5b6f6468fc4e68fe63d.jpg', 1375869643, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `torrentGroupsEAV`
--

CREATE TABLE IF NOT EXISTS `torrentGroupsEAV` (
  `entity` bigint(20) unsigned NOT NULL,
  `attribute` varchar(250) NOT NULL,
  `value` text NOT NULL,
  KEY `ikEntity` (`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `torrentGroupsEAV`
--

INSERT INTO `torrentGroupsEAV` (`entity`, `attribute`, `value`) VALUES
(2, '1', 'Росомаха: Бессмертный'),
(2, '2', 'The Wolverine'),
(2, '3', '2013'),
(2, '4', 'фантастика, фэнтези, боевик, приключения'),
(2, '5', 'Джеймс Мэнголд'),
(2, '6', 'Хью Джекман, Тао Окамото, Рила Фукусима, Светлана Ходченкова, Уилл Юн Ли, Харукико Яманоути, Хироюки Санада, Брайан Ти Кен Ямамура, Фамке Янссен'),
(2, '7', 'Новая глава приключений Росомахи развернётся в Японии, где Логану предстоит выяснить, что острее — когти Росомахи или меч Серебряного Самурая.'),
(2, '8', 'США'),
(3, '15', 'T1One'),
(3, '16', 'По - настоящему'),
(3, '21', '2013'),
(3, '20', '01. Пой\r\n02. Не ангелы feat. Normal''ный (Normal''ный Prod.)\r\n03. Когда с небес сорвалась звезда feat. Ar-Side\r\n04. Новый день\r\n05. Худею\r\n06. На волю\r\n07. Любимая моя\r\n08. Мало\r\n09. Привычка сильнее любви feat. Александр Леницкий\r\n10. Тонем\r\n11. Единсвенная\r\n12. Перемена мест feat. Natali\r\n13. Попрошу у неба\r\n14. Жемчужные мысли feat. Ahasverus (Ahasverus Prod.)\r\n15. Просто такая жизнь (Ahasverus Prod.)\r\n16. Вместе мы обязательно будем feat. Михаил Бублик\r\n17. Пёс\r\n18. Только ты одна'),
(1, '1', 'Эволюция Борна'),
(1, '2', 'The Bourne Legacy'),
(1, '3', '2012'),
(1, '4', 'боевик, триллер, детектив, приключения'),
(1, '5', 'Тони Гилрой'),
(1, '6', 'Джереми Реннер, Рейчел Вайс, Эдвард Нортон, Джоан Аллен, Альберт Финни, Скотт Гленн, Стейси Кич, Донна Мерфи, Майкл Чернус, Кори Столл'),
(1, '7', 'В игре всегда несколько фигур. Одна из них — Джейсон Борн, другая — совершенный агент Аарон Кросс. Их возможности безграничны. Но даже у идеального оружия бывают сбои…'),
(1, '8', 'США, Universal Pictures'),
(4, '37', 'The Elder Scrolls V: Skyrim'),
(4, '38', 'The Elder Scrolls V: Skyrim'),
(4, '23', 'Bethesda Game Studios'),
(4, '24', 'Bethesda Softworks'),
(4, '25', 'СофтКлаб'),
(4, '26', '2013'),
(4, '27', 'RPG / 3D / 1st Person / 3rd Person'),
(4, '31', 'Знаменитая ролевая сага The Elder Scrolls V: Skyrim собрала более двух сотен различных наград. И вот настало время для выхода единого издания, включающего не только оригинальную игру, но и все дополнения к ней. Издание The Elder Scrolls V: Skyrim. Legendary Edition адресовано самым преданным фанатам серии. Оно предлагает не только игру и три дополнения для нее, но и различные улучшения – в том числе сражения верхом, новые варианты добиваний, дополнительный уровень сложности для самых опытных игроков, а также «легендарные» умения, позволяющие развивать навыки персонажа бесконечно.'),
(4, '32', 'The Elder Scrolls V: Skyrim. Создайте уникального персонажа и делайте в волшебном мире все, что заблагорассудится. Skyrim вобрала все лучшие черты серии The Elder Scrolls: свободу выбора, возможность самому написать историю Тамриэля и пережить незабываемое приключение.\r\nThe Elder Scrolls V: Skyrim – Dawnguard. Вам, Драконорожденному, предстоит встретиться с лордом-вампиром Харконом, который задумал уничтожить солнце с помощью силы Древних свитков. Присоединитесь ли вы к древнему ордену Стражи Рассвета или решите сами стать владыкой вампиров? Какую бы сторону вы ни приняли, вас ждет множество приключений.\r\nThe Elder Scrolls V: Skyrim – Hearthfire. Станьте землевладельцем и постройте дом своей мечты – однокомнатную избушку или огромное поместье со своей оружейной, алхимической лабораторией и прочими необходимыми постройками. Используйте новые инструменты, чтобы создавать из камней, глины и бревен мебель и украшения. А с возможностью усыновления вы сможете превратить свое жилище в настоящий дом.\r\nThe Elder Scrolls V: Skyrim – Dragonborn. Оставьте позади суровый Скайрим. На бескрайних просторах Солстейма вам предстоит повстречать темных эльфов и познакомиться с племенем скаалов. Но самое главное, вы встретитесь лицом к лицу с небывалым противником – первым Драконорожденным…'),
(4, '35', '✔ Операционная система: Windows XP, Windows Vista, Windows 7\r\n✔ Процессор: Двухъядерный процессор с тактовой частотой 2,0 ГГц или аналогичный AMD\r\n✔ Оперативная память: 2 Гб\r\n✔ Видеокарта: 512 Мб памяти, c поддержкой DirectX 9.0\r\n✔ Звуковая карта: Звуковое устройство, совместимое с DirectX® 9.0с\r\n✔ Свободное место на жестком диске: 14 ГБ'),
(6, '37', 'Mortal Kombat: Komplete Edition'),
(6, '38', 'Mortal Kombat: Komplete Edition'),
(6, '23', 'High Voltage Software'),
(6, '24', 'Warner Bros. Interactive Entertainment'),
(6, '26', '2013'),
(6, '27', 'Arcade (Fighting) / 3D'),
(6, '31', 'Спустя годы легендарная серия Mortal Kombat вернулась такой, какой она запомнилась тысячам игроков. Это жестокая, кровавая и беспощадная битва не на жизнь, а на смерть. Битва, в которой может победить только сильнейший.\r\nСтолетия понадобились Шао Кану, чтобы одержать верх над Рейденом и его союзниками. Однако перед лицом смерти Рейден все же смог использовать свой последний шанс спасти мир. Он отправил ментальное сообщение самому себе в прошлое, во времена первого турнира, когда силы добра еще были способны уничтожить Шао Кана…\r\nИгрокам предстоит принять участие в турнире «Смертельной битвы», узнать истинную историю мира Mortal Kombat и изменить прошлое, чтобы спасти будущее.'),
(6, '32', '» Легендарные бойцы. Сражайтесь за или против героев культовой серии Mortal Kombat, среди которых Рейден, Джонни Кейдж и Скорпион.\r\n» Сражайся или умри! Используйте смертоносные и жестокие удары, а также фирменные добивания серии Mortal Kombat – знаменитые Fatality.\r\n» Многообразие режимов. Померьтесь силами или вступите в бой плечом к плечу с друзьями в режимах Tag Team, Co-op Arcade и Team Online.\r\n» Графика нового поколения. Настолько красочным и кровавым Mortal Kombat еще никогда не был.'),
(6, '35', 'Минимальные системные требования:\r\n- ОС: (32-бит) Windows Vista / 7 / 8\r\n- Процессор: Intel Core Duo, 2.4 ГГц / AMD Athlon X2, 2.8 ГГц\r\n- Память: 2 Гб\r\n- Видео: NVIDIA GeForce 8800 GTS / AMD Radeon 3850\r\n- DirectX 10\r\n\r\nРекомендуемые системные требования:\r\n- ОС: (64-бит) Windows Vista/ 7 / 8\r\n- Процессор: Intel Core i5 750, 2.67 ГГц / AMD Phenom II X4 965, 3.4 ГГц\r\n- Память: 4 Гб\r\n- Видео: NVIDIA GeForce GTX 560 / AMD Radeon HD 6950\r\n- DirectX 11'),
(8, '47', 'Разные'),
(8, '48', 'Книжная серия "Сумерки"'),
(8, '49', '2009-2013'),
(8, '53', 'Серия подростковой фантастики, открытая на волне популярности вампирско-девчачьей «Сумеречной саги» американской писательницы Стефани Майер. Своеобразный ответ издательского тандема Эксмо / Домино издательству АСТ, владеющему правами на книги Сумеречной Саги Стефани Майер в России. На момент открытия в серии выходили книги только Скотта Вестерфельда, которые на самом деле ничего общего, кроме некоторых элементов антуража, с романами Майер не имеют.'),
(9, '47', 'Джеймс Хедли Чейз'),
(9, '48', 'Дело о наезде'),
(9, '49', '2013'),
(9, '53', 'Искусник детективной замысла, царь внезапных сюжетных заворотов, потрясающий специалист человечьих душ, эксперт самых хитроумных полицейских ухищрений и даже… тонкий ценитель экзотической кухни. Пожалуй, комплекта этих совершенств с лихвой укусило бы на добросердечный десяток авторов детективных историй. Но самое сногсшибательное состоит в том, что все данные качества характеризуют одного примечательного писателя. Первые же страницы известного романа «Дело о наезде» послужат пробелом в вселенная, полный невиданных авантюр и опасных секретов, – вселенная книжек Джеймса Хедли Чейза, в котором никому еще не было скучно.'),
(10, '47', 'Джеймс Хедли Чейз'),
(10, '48', 'Дело о наезде'),
(10, '49', '2013'),
(10, '53', 'Искусник детективной замысла, царь внезапных сюжетных заворотов, потрясающий специалист человечьих душ, эксперт самых хитроумных полицейских ухищрений и даже… тонкий ценитель экзотической кухни. Пожалуй, комплекта этих совершенств с лихвой укусило бы на добросердечный десяток авторов детективных историй. Но самое сногсшибательное состоит в том, что все данные качества характеризуют одного примечательного писателя. Первые же страницы известного романа «Дело о наезде» послужат пробелом в вселенная, полный невиданных авантюр и опасных секретов, – вселенная книжек Джеймса Хедли Чейза, в котором никому еще не было скучно.'),
(11, '1', 'Любовь без пересадок'),
(11, '2', 'Amour &amp; turbulences'),
(11, '3', '2013'),
(11, '5', 'Александр Кастаньетти'),
(11, '6', 'Людивин Санье, Николя Бедо, Джонатан Коэн, Арно Дюкре, Бриджитт Катийон, Жакки Берруайе, Клементин Селарье, Мишель Вюйермоз, Лила Сале, Ина Кастаньетти'),
(11, '7', 'Преуспевающий адвокат Антуан никогда не знал недостатка в женском внимании. Отправляясь по делам из Нью-Йорка в Париж, в самолете он оказывается в соседнем кресле с бывшей подругой Джули. Обреченные провести вместе семь долгих часов бывшие любовники успевают вспомнить былые обиды, все неприятности, доставленные друг другу, и понять, что их разрыв был ошибкой. Но успеют ли они эту ошибку исправить?'),
(11, '8', 'Франция'),
(13, '56', 'СашаТаня'),
(13, '58', '2013'),
(13, '59', 'Михаил Старчак'),
(13, '60', 'Андрей Гайдулян, Валентина Рубцова, Алексей Климушкин, Елена Бирюкова, Татьяна Орлова, Андрей Лебедев'),
(13, '61', 'В сериале рассказывается история жизни молодой семьи. Молодая пара, Саша и Таня, закончили университет и стали жить взрослой жизнью. Пара сталкивается с проблемами семейной жизни. Саша не хочет принимать финансовую помощь от своего отца олигарха. В результате вся жизнь парня заключается в проживании в многоэтажке на краю города, скучной и суетливой работе в офисе, а также супруге домохозяйки и постоянно кричащего ребенка. Саша живет от зарплаты до зарплаты и изменений в его жизни пока не предвидеться...'),
(13, '62', 'ТНТ, Комеди Клаб Продакшн'),
(13, '64', '1'),
(12, '1', 'Марафон'),
(12, '3', '2013'),
(12, '5', 'Карен Оганесян'),
(12, '6', 'Михаил Пореченков, Екатерина Васильева, Юлия Пересильд, Анатолий Белый, Игорь Савочкин, Ольга Волкова, Сергей Газаров, Анна Михалкова, Мария Аронова, Ирина Пивоварова'),
(12, '7', 'Жизнь Толика не удалась, спортивная карьера не состоялась, жена ушла к другому. Неуспешный, нелюбимый, безнадежный он потерял веру в себя. Все меняет случайная встреча со странной незнакомкой Анной Ильиничной, которая предлагает ему принять участие в необычном марафоне в далекой Америке…'),
(12, '8', 'Россия'),
(7, '39', 'Навител Навигатор'),
(7, '41', '7.5.0.2131'),
(7, '42', '2013'),
(7, '46', 'NAVITEL ® — ведущий поставщик навигационных сервисов и цифровой картографии на рынке автомобильной навигации. Навител Навигатор - это современная мультиплатформенная и мультиязычная навигация для Android, Windows Phone, Symbian, Windows Mobile, iPhone, iPad, Bada, Java, Windows CE, собственные онлайн-сервисы Навител.Пробки, Навител.Друзья, Навител.События, Динамические POI, Навител.SMS, Навител.Погода. Актуальные карты России, Европы и Азии.'),
(14, '1', 'Эпик'),
(14, '2', 'Epic'),
(14, '3', '2013'),
(14, '5', 'Крис Уэдж'),
(14, '6', 'Джейсон Судейкис, Стивен Тайлер, Аманда Сайфред, Питбулл, Бейонсе Ноулз, Джош Хатчерсон, Джуда Фридлендер, Колин Фаррелл, Азиз Ансари, Блейк Андерсон'),
(14, '7', 'После долгой разлуки юная Мэри Кэтрин возвращается в дом, где отшельником живет ее отец, безумный профессор Бомба. Однако тот совсем не обращает внимания на дочь и одержим лишь одной идеей — изучить скрытый от посторонних глаз таинственный лесной мир. Девушка отказывается верить, что под ногами человечества идет вечная борьба между армиями, воюющими на сторонах добра и зла. Чудесным образом Мэри Кэтрин уменьшается в размерах и знакомится с маленькими воинами. Теперь от нее зависит спасение не только волшебного лесного, но и реального человеческого мира…'),
(14, '8', 'США'),
(15, '1', 'Омут / Внизу'),
(15, '2', 'Beneath'),
(15, '3', '2013'),
(15, '5', 'Ларри Фесенден'),
(15, '6', 'Дэниэл Зоватто, Бонни Деннисон, Крис Конрой, Джонатан Орсини, Гриффин Ньюман, МакКензи Росман, Марк Марголис, Грэхэм Резник'),
(15, '7', 'Несколько старшеклассников отправляются на прогулку на лодке и по пути подвергаются нападению рыбы-людоеда. Теперь герои должны решить кем из них придется пожертвовать, чтобы отвлечь монстра и добраться до спасительного берега...'),
(15, '8', 'США / Lock It In Entertainment / Quantum Productions');

-- --------------------------------------------------------

--
-- Структура таблицы `torrents`
--

CREATE TABLE IF NOT EXISTS `torrents` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `info_hash` blob NOT NULL,
  `gId` int(10) NOT NULL,
  `ctime` int(11) DEFAULT NULL,
  `size` bigint(20) NOT NULL,
  `downloads` int(10) NOT NULL,
  `seeders` int(10) NOT NULL,
  `leechers` int(10) NOT NULL,
  `mtime` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Дамп данных таблицы `torrents`
--

INSERT INTO `torrents` (`id`, `info_hash`, `gId`, `ctime`, `size`, `downloads`, `seeders`, `leechers`, `mtime`, `uid`) VALUES
(1, 0x51ea34aaf0ff4996374d3ca34b60f6fdb4469479, 1, 1375019822, 1563533312, 0, 0, 0, 1375870778, 0),
(2, 0x559534742326c2f9fd35a824ca62f347e4cdb7fc, 1, 1375020288, 2335645696, 0, 0, 0, 1375870784, 0),
(3, 0xe11bbd3e985d2418199d86ac710412171a4c13fa, 1, 1375023409, 1465827084, 0, 0, 0, 1375870790, 0),
(4, 0xc18765ccb5180ac4fa605689d6f6d5a04501a4af, 2, 1375025671, 1471240192, 0, 0, 0, 1375025671, 0),
(5, 0xf355cb2cc2839d3f71a8b3a209d8370dd7dcc42f, 2, 1375025869, 1469216768, 0, 0, 0, 1375025869, 0),
(6, 0x4c5428832755b6f9d1c05898e55fccc2dc86542b, 3, 1375026137, 465979017, 0, 0, 0, 1375026137, 0),
(7, 0xf0b6eaa8701aebd2d1fa446a13564c5d3bcfa959, 3, 1375026267, 464366660, 0, 0, 0, 1375026267, 0),
(8, 0xafc7fafbd275ddb8de74efc8dd7fa705b9cace13, 4, 1375028777, 10244931728, 0, 0, 0, 1375200394, 0),
(9, 0x81a2f309e02efbb461ae790d03cf8d2ea39e3f5b, 4, 1375029104, 5479464960, 0, 0, 0, 1375200406, 0),
(10, 0x9b6ec40c05c36e2fc3ac312351dcf2da837b203f, 6, 1375195220, 7006274998, 0, 0, 0, 1375200950, 1),
(11, 0x363347ec8e5e23fd2d853533e1520aeb695cdd58, 7, 1375202106, 12461475962, 0, 0, 0, 1375427170, 1),
(12, 0x44f5e973a73657cdf4f6d96426d9a26ab16bc974, 8, 1375259915, 93504204, 0, 0, 0, 1375259915, 1),
(14, 0x8ddc147dc556b9ac5a13290c66a1d96aff1f8bd1, 10, 1375260566, 383143695, 0, 0, 0, 1375260877, 1),
(15, 0x5516fc2aeb89f00cfadc3fce9444b51fa007e72c, 11, 1375275693, 1465778176, 0, 0, 0, 1375275693, 1),
(16, 0x61e3c6dac0473a8e9329100ed57481cf0bc4ebea, 12, 1375275901, 1467975680, 0, 0, 0, 1375277318, 1),
(17, 0x6ab09753048852717d42d977cd4c86bebc0f8ca4, 13, 1375277135, 278847680, 0, 0, 0, 1375277256, 1),
(18, 0xaf20906d61afb4b7c148f86a4299e6bb9ad78177, 13, 1375277392, 265449002, 0, 0, 0, 1375277392, 1),
(19, 0x19d20663a60f7b2e4902dfaa24ba752d4f7cc564, 14, 1375868998, 1468313600, 0, 0, 0, 1375868998, 1),
(20, 0x22ebb304386a5bb9393ae5b4dcc38e29dffb0014, 14, 1375869197, 1470279680, 0, 0, 0, 1375869197, 1),
(21, 0x82b7a71fe75265b0ba751d55c754ff34e8febb5a, 15, 1375869643, 1564766208, 0, 0, 0, 1375869643, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `torrentsEAV`
--

CREATE TABLE IF NOT EXISTS `torrentsEAV` (
  `entity` bigint(20) unsigned NOT NULL,
  `attribute` varchar(250) NOT NULL,
  `value` text NOT NULL,
  KEY `ikEntity` (`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `torrentsEAV`
--

INSERT INTO `torrentsEAV` (`entity`, `attribute`, `value`) VALUES
(4, '9', '01:58:45'),
(4, '10', 'Русский профессиональный дубляж'),
(4, '11', 'Avi'),
(4, '22', 'TS'),
(4, '12', '640x304 (2.11:1), 29.970 fps, XviD build 50 ~1512 kbps avg, 0.26 bit/pixel'),
(4, '13', '48 kHz, MPEG Layer 3, 2 ch, ~128.00 kbps avg'),
(4, '14', 'http://multi-up.com/888793'),
(5, '9', '01:44:50'),
(5, '10', 'Русский профессиональный дубляж'),
(5, '11', 'Avi'),
(5, '22', 'CAM'),
(5, '12', '640x272 (2.35:1), 25 fps, XviD build 50 ~1730 kbps avg, 0.40 bit/pixel'),
(5, '13', '44.100 kHz, MPEG Layer 3, 2 ch, ~128.00 kbps avg'),
(5, '14', 'http://multi-up.com/888564'),
(6, '9', '01:04:00'),
(6, '17', 'CDDA'),
(6, '18', 'Mp3'),
(6, '19', 'CBR'),
(7, '9', '01:04:00'),
(7, '17', 'CDDA'),
(7, '18', 'Flac'),
(7, '19', 'Lossless'),
(8, '28', 'Русский'),
(8, '29', 'Русский'),
(8, '30', 'Вшита'),
(8, '33', 'Версия игры - 1.9.32.0.8\r\nНичего не вырезано/перекодировано\r\nВшито обновление русской локализации от 12.07.2013\r\nВозможность выбора сочетания текста и озвучки\r\nДополнения:\r\nDawnguard\r\nHearthfire\r\nDragonborn\r\nHigh Resolution Texture Pack(Опционально)'),
(8, '34', '1. Скачать\r\n2. Запустить процесс установки\r\n3. Играть ;)'),
(8, '36', 'Legendary Edition (RePack)'),
(9, '28', 'Английский'),
(9, '29', 'Английский'),
(9, '30', 'См. инструкцию по установке'),
(9, '34', '1.Распаковать с помощью программы Феникс(http://multi-up.com/592943)\r\n2.Скачать таблетку(выше ссылка)\r\n3.Скопировать содержимое в папку с установленной игрой с заменой\r\n4.Что бы игра была на весь экран,зайдите в "Мои документы"/My Games/Skyrim и в файле SkyrimPrefs.ini смените значения bFull Screen=0 на bFull Screen=1 ,а ниже выставьте разрешение какое вам угодно,например iSize H=1080\r\niSize W=1920\r\n5.Запускайте TESV.exe\r\n6.Наслаждайтесь'),
(9, '36', 'Лицензия'),
(10, '36', 'DLC (v1.0) (RePack)'),
(10, '28', 'Английский'),
(10, '29', 'Английский'),
(10, '30', 'Вшита'),
(10, '33', 'За основу взята Пиратка\r\nНичего не вырезано | Ничего не перекодировано\r\nИгровые архивы не тронуты\r\nDLC:\r\n- Персонажи Скарлет, Кенши, Рейн и Фредди Крюгер;\r\n- 15 классических костюмов;\r\n- 3 классических добивания для Скорпиона, Саб-Зиро и Рептилии.\r\nВерсия игры - v.1.0\r\nВремя установки - 10 минут\r\nRePack by SEYTER'),
(10, '34', 'Установить\r\nИграть'),
(12, '50', 'С.Трофимов, О.Степашкина, и др.'),
(12, '51', 'FB2'),
(12, '52', 'Бессмертные\r\nБлэк. Белая Кошка\r\nБлэк. Зачарованная\r\nБлэк. Красная перчатка\r\nБлэк. Отважная\r\nБлэк. Решительная\r\nБлэкли-Картрайт. Красная Шапочка\r\nВестерфельд. Городской охотник\r\nВестерфельд. Инферно. Книга 1. Армия ночи\r\nВестерфельд. Инферно. Книга 2. Последние дни\r\nВестерфельд. Полуночники. Книга 1. Тайный час\r\nВестерфельд. Полуночники. Книга 2. Прикосновение тьмы\r\nВестерфельд. Полуночники. Книга 3. Чёрный полдень\r\nГарсиа, Штоль. Прекрасные создания\r\nГарсиа, Штоль. 2 Прекрасная тьма\r\nДероше. В объятиях демона\r\nКагава. Железные фейри. Книга 1. Железный король\r\nКагава. Железные фейри. Книга 2. Железная принцесса\r\nКагава. Железные фейри. Книга 3. Железная королева\r\nКаст. Богиня весны\r\nКаст. Богиня легенды\r\nКаст. Богиня моря\r\nКаст. Богиня По Зову Сердца\r\nКаст. Богиня по крови\r\nКаст. Богиня По ошибке\r\nКаст. Богиня роз\r\nКаст. Богиня света\r\nКаст. Влюблённая в демона\r\nКаст. Чаша любви\r\nКейн. Бог хаоса\r\nКейн. Пиршество Демонов\r\nКейн. Полночная Аллея\r\nКейн. Стеклянный Дом\r\nКейн. Танец Мёртвых Девушек\r\nКейт. Обреченные\r\nКейт. Падшие\r\nКруз. Голубая Кровь\r\nКруз. Любовь на крови\r\nКруз. Маскарад\r\nКруз. Наследие Ван Аленов\r\nКруз. Обманутый ангел\r\nКруз. Откровения\r\nКруз. Потерянные во времени\r\nКруз. Тайные Архивы Голубой Крови\r\nКруз. Ведьмы с Восточного побережья\r\nМакманн. Отчаяние\r\nМакманн. Пробуждение\r\nМакманн. Прощание\r\nМарр. Коварная Красота\r\nМарр. Роковая Татуировка\r\nМарр. Смертные тени\r\nМарр. Темное предсказание\r\nМагдалена Козак. Ночар\r\nМелисса де ла Круз. Ведьмы с Восточного побережья\r\nМид. Академия вампиров. Книга 1. Охотники и жертвы\r\nМид. Академия вампиров. Книга 2. Ледяной укус\r\nМид. Академия вампиров. Книга 3. Поцелуй тьмы\r\nМид. Академия вампиров. Книга 4. Кровавые обещания\r\nМид. Академия вампиров. Книга 5. Оковы для призрака\r\nМид. Академия вампиров. Книга 6. Последняя жертва\r\nМид. Кровные узы. Книга 1. Принцесса По Крови\r\nМостерт. Хранитель Света и Праха\r\nПайк. Крылья\r\nПайк. Миражи\r\nПайк. Чары\r\nСмит, Тара. Посредники\r\nСмит. Дочери Тьмы\r\nСмит. Избранная\r\nСмит. Колдовской свет\r\nСмит. Колдунья\r\nСмит. Одержимость\r\nСмит. Охотница\r\nСмит. Предначертание\r\nСмит. Предчуствие\r\nСмит. Тайный Вампир\r\nСмит. Тёмный Ангел\r\nСмит. Черный рассвет\r\nСтивотер. Вечность\r\nСтивотер. Дрожь\r\nСтивотер. Превращение\r\nСэйнткроу. Возвращение Мертвеца\r\nСэйнткроу. Грешники Святого Города\r\nСэйнткроу. Дорога в Ад\r\nСэйнткроу. Контракт с Дьяволом\r\nСэйнткроу. Правая Рука Дьявола\r\nФлинн. Зачарованный\r\nФлинн. Поцелуй Во Времени\r\nФлинн. Чудовище\r\nХаббард. Иная\r\nХаббард. Исчезнувшая\r\nХантер. Рожденная в полночь\r\nХарви. Рожденные вампирами. Книга 1. Королевская кровь\r\nХарви. Рожденные вампирами. Книга 2. Кровная месть\r\nХолдер, Виге. Ведьма\r\nХолдер, Виге. Воскрешение\r\nХолдер, Виге. Крестовый поход\r\nХолдер, Виге. Наваждение\r\nХолдер, Виге. Наследие\r\nХолдер, Виге. Отчаяние\r\nШрайбер. Однажды в полнолуние\r\nШрайбер. Поцелуй вампира. Книга 1. Начало\r\nШрайбер. Поцелуй вампира. Книга 2. Темный рыцарь\r\nШрайбер. Поцелуй вампира. Книга 3. Вампирвилль\r\nШрайбер. Поцелуй вампира. Книга 4. Танец смерти\r\nШрайбер. Поцелуй вампира. Книга 5. Клуб бессмертных\r\nШрайбер. Поцелуй вампира. Книга 6. Королевская кровь\r\nШрайбер. Поцелуй вампира. Книга 7. Укус Любви'),
(14, '55', 'Русский'),
(14, '51', 'MP3'),
(14, '54', '96'),
(15, '9', '01:33:10'),
(15, '10', 'Русский профессиональный дубляж'),
(15, '11', 'Avi'),
(15, '12', '720x304 (2.37:1), 25 fps, XviD build 50 ~1831 kbps avg, 0.33 bit/pixel'),
(15, '13', '48 kHz, AC3 Dolby Digital, 3/2 (L,C,R,l,r) + LFE ch, ~256 kbps'),
(15, '14', 'http://multi-up.com/888268'),
(15, '22', 'DVDRip'),
(17, '63', '00:23:00'),
(17, '65', '22'),
(17, '66', 'Русский профессиональный дубляж'),
(17, '67', 'AVI'),
(17, '68', 'DVDRip'),
(17, '69', 'XviD, 720x400, 25 fps, ~ 1500 Кбит/с'),
(17, '70', 'MP3, 48000 Hz, 128 kbit/s, stereo'),
(16, '9', '01:35:21'),
(16, '10', 'Русский профессиональный дубляж'),
(16, '11', 'Avi'),
(16, '12', '23.976 fps, XviD, 1594 Кбит/с, 720x384'),
(16, '13', 'AC3, 6 ch, 448 Кбит/с, 48 kHz'),
(16, '14', 'http://multi-up.com/888374'),
(16, '22', 'DVDRip'),
(18, '63', '00:23:00'),
(18, '65', '23'),
(18, '66', 'Русский профессиональный дубляж'),
(18, '67', 'AVI'),
(18, '68', 'DVDRip'),
(18, '69', 'XviD, 720x400, 25 fps, ~ 1500 Кбит/с'),
(18, '70', 'MP3, 48000 Hz, 128 kbit/s, stereo'),
(11, '40', 'Android'),
(11, '72', 'карты Q1 2013'),
(11, '43', 'См. инструкцию по установке'),
(11, '44', 'Русский'),
(11, '45', '- Забрасываем apk на карту памяти (Чтобы установить любой .apk НЕ из Android Market, нужно в Настройки &gt; Приложения &gt; на пукте "Неизвестные источники" поставить галочку).\r\n- С помощью любого файл менеджера (например ES проводник, в маркете можно скачать бесплатно) запускаем и ждем завершения установки.\r\n- После первого запуска программа создаст на карте памяти папку Navitel/Content.\r\n- В папку Navitel/Content/Maps можно копировать карты'),
(11, '71', 'Что нового версии 7.5.0.2131:\r\n• Автоматическое обновление данных о камерах контроля скорости (SPEEDCAM) из меню программы.\r\n• Сохранение личных настроек, истории, путевых точек при установке обновления.\r\n• Выбор датчиков на экране карты при движении по маршруту.\r\n• Переключение профилей без необходимости перезагрузки приложения.\r\n• Автоматическое переименование экспортируемых файлов (маршруты, путевые точки) во избежание перезаписи уже существующих данных.\r\n• Улучшено отображение названий объектов на карте при её смещении.\r\n• Исправлена ошибка построения короткого маршрута на картах, в которых не предусмотрено наличие информации для его поддержки.\r\n• Исправлены другие возможные ошибки маршрутизации.\r\n• А также внесены прочие исправления, повышающие стабильность и надежность работы программы.'),
(19, '9', '01:42:47'),
(19, '10', 'Русский профессиональный дубляж'),
(19, '11', 'Avi'),
(19, '12', '720x304 (2.37:1), 23.976 fps, XviD build 50 ~1764 kbps avg, 0.34 bit/pixel'),
(19, '13', '48 kHz, MPEG Layer 3, 2 ch, ~128.00 kbps avg'),
(19, '14', 'http://multi-up.com/891449'),
(19, '22', 'WEB-DLRip (звук с TS)'),
(20, '9', '01:34:05'),
(20, '10', 'Русский профессиональный дубляж'),
(20, '11', 'Avi'),
(20, '12', 'XviD, 720x288, 29.97 fps, ~1944 Kbit/s'),
(20, '13', 'MP3, 128 Kb/s (2 ch), 48 kHz'),
(20, '14', 'http://multi-up.com/869848'),
(20, '22', 'TS'),
(21, '9', '01:29:51'),
(21, '10', 'Одноголосный'),
(21, '11', 'Avi'),
(21, '12', '720x400 (1.80:1), 23.976 fps, XviD build 50 ~2120 kbps avg, 0.31 bit/pixel'),
(21, '13', '48 kHz, AC3 Dolby Digital, 2/0 (L,R) ch, ~192 kbps'),
(21, '14', 'http://multi-up.com/891434'),
(21, '22', 'WEB-DLRip'),
(1, '9', '02:15:02'),
(1, '10', 'Русский профессиональный дубляж'),
(1, '11', 'Avi'),
(1, '12', '720x304 (2.37:1), 23.976 fps, XviD build 50 ~1150 kbps avg, 0.22 bit/pixel'),
(1, '13', '48 kHz, AC3 Dolby Digital, 3/2 (L,C,R,l,r) + LFE ch, ~384 kbps'),
(1, '14', 'http://sendfile.su/719839'),
(1, '22', 'HDRip'),
(2, '9', '02:15:02'),
(2, '10', 'Одноголосный'),
(2, '11', 'Avi'),
(2, '12', '720x304 (2.37:1), 23.976 fps, XviD build 50 ~1150 kbps avg, 0.22 bit/pixel'),
(2, '13', '48 kHz, AC3 Dolby Digital, 3/2 (L,C,R,l,r) + LFE ch, ~384 kbps'),
(2, '14', 'http://sendfile.su/719839'),
(2, '22', 'HDRip (перевод Гоблина)'),
(3, '9', '02:15:02'),
(3, '10', 'Русский профессиональный дубляж'),
(3, '11', 'Mkv'),
(3, '12', '1920x800, 5305 Кбит/сек, 23,976 кадр/сек, AVC, x264'),
(3, '13', 'AC-3, 384 Кбит/сек, 6 канала(ов), 48,0 КГц'),
(3, '14', 'http://multi-up.com/799405'),
(3, '22', 'BDRip 1080p');

-- --------------------------------------------------------

--
-- Структура таблицы `torrentsNameRules`
--

CREATE TABLE IF NOT EXISTS `torrentsNameRules` (
  `attrId` int(10) NOT NULL,
  `catId` int(10) NOT NULL,
  `order` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `torrentsNameRules`
--

INSERT INTO `torrentsNameRules` (`attrId`, `catId`, `order`) VALUES
(1, 1, 0),
(2, 1, 1),
(3, 1, 2),
(56, 7, 0),
(57, 7, 1),
(64, 7, 2),
(58, 7, 3),
(15, 2, 0),
(16, 2, 1),
(21, 2, 2),
(47, 5, 0),
(48, 5, 1),
(49, 5, 2),
(37, 3, 0),
(38, 3, 1),
(26, 3, 2),
(39, 4, 0),
(41, 4, 1),
(42, 4, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `userProfiles`
--

CREATE TABLE IF NOT EXISTS `userProfiles` (
  `uid` int(10) NOT NULL,
  `picture` varchar(255) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `userProfiles`
--

INSERT INTO `userProfiles` (`uid`, `picture`) VALUES
(1, ''),
(3, ''),
(4, '');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `resetHash` varchar(32) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `resetHash`, `active`, `ctime`) VALUES
(1, 'admin', 'admin@yii-torrent.com', '$2a$10$2AulI0UYvajzd9qsjX8yEe6DTWVflryLUra6yrBvMo00JZpG2t1j2', '2ae7f092c41e541ae36d41008d8c28a0', 1, 0),
(3, 'user', 'user@yii-torrent.com', '$2a$10$yRODMwj31p1eIJEWcJ1e9OSfTF.7he9anaIFACNxh3IRGVfM6kRuu', '68f3710508d1ccf4f0c3c24968f1874c', 1, 0),
(4, 'user2', 'user2@yii-torrent.com', '$2a$10$QBr6vZylAyTMqoyLqsa5B.f3dbljCsnkvjVfIRRxJv3V.46GleGXq', '', 1, 1375451019);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `AuthAssignment`
--
ALTER TABLE `AuthAssignment`
  ADD CONSTRAINT `authassignment_ibfk_1` FOREIGN KEY (`itemname`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `AuthItemChild`
--
ALTER TABLE `AuthItemChild`
  ADD CONSTRAINT `authitemchild_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `authitemchild_ibfk_2` FOREIGN KEY (`child`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;
