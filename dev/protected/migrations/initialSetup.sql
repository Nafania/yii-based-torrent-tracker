-- phpMyAdmin SQL Dump
-- version 4.0.3
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 29 2013 г., 23:21
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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
(18, 'Mp3', 0),
(18, 'Flac', 1),
(19, 'VBR', 0),
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
(30, 'См. инструкцию по установке', 3);

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
  `measure` varchar(255) NOT NULL,
  `common` tinyint(1) NOT NULL,
  `cId` int(10) NOT NULL,
  `separate` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Дамп данных таблицы `attributes`
--

INSERT INTO `attributes` (`id`, `title`, `type`, `validator`, `required`, `measure`, `common`, `cId`, `separate`) VALUES
(1, 'Название', 0, 0, 1, '', 1, 1, 0),
(2, 'Оригинальное название', 0, 0, 1, '', 1, 1, 0),
(3, 'Год выхода', 0, 0, 1, '', 1, 1, 0),
(4, 'Жанр', 0, 0, 1, '', 1, 1, 0),
(5, 'Режиссер', 0, 0, 1, '', 1, 1, 0),
(6, 'В ролях', 0, 0, 1, '', 1, 1, 0),
(7, 'Описание', 5, 0, 1, '', 1, 1, 0),
(8, 'Выпущено', 0, 0, 1, '', 1, 1, 0),
(9, 'Продолжительность', 0, 0, 1, '', 0, 1, 0),
(10, 'Перевод', 1, 0, 1, '', 0, 1, 0),
(11, 'Формат', 1, 0, 1, '', 0, 1, 0),
(12, 'Видео', 0, 0, 1, 'Характеристики видео', 0, 1, 0),
(13, 'Звук', 0, 0, 1, 'Характеристики звуковой дорожки', 0, 1, 0),
(14, 'Семпл', 0, 0, 0, 'Ссылка на семпл', 0, 1, 0),
(15, 'Исполнитель', 0, 0, 1, '', 1, 2, 0),
(16, 'Альбом', 0, 0, 1, '', 1, 2, 0),
(17, 'Источник', 0, 0, 1, '', 0, 2, 0),
(18, 'Формат', 1, 0, 1, '', 0, 2, 1),
(19, 'Битрейт', 1, 0, 1, '', 0, 2, 0),
(20, 'Треклист', 5, 0, 1, '', 1, 2, 0),
(21, 'Год выхода', 0, 0, 1, '', 1, 2, 0),
(22, 'Качество', 0, 0, 1, 'Укажите здесь качество и / или тип видео', 0, 1, 1),
(23, 'Разработчик', 0, 0, 1, '', 1, 3, 0),
(24, 'Издатель', 0, 0, 1, '', 1, 3, 0),
(25, 'Издатель в России', 0, 0, 0, '', 1, 3, 0),
(26, 'Год выхода', 0, 0, 1, '', 1, 3, 0),
(27, 'Жанр', 0, 0, 1, '', 1, 3, 0),
(28, 'Язык интерфейса', 1, 0, 1, '', 0, 3, 0),
(29, 'Язык озвучки', 1, 0, 1, '', 0, 3, 0),
(30, 'Таблетка', 1, 0, 1, '', 0, 3, 0),
(31, 'Описание', 5, 0, 1, '', 1, 3, 0),
(32, 'Особенности игры', 5, 0, 0, '', 1, 3, 0),
(33, 'Особенности RePack''a', 5, 0, 0, 'Впишите сюдя особенности если вы загружаете RePack', 0, 3, 0),
(34, 'Инструкция по установке', 5, 0, 0, 'Впишите сюда инструкцию по установке игры', 0, 3, 0),
(35, 'Системные требования', 5, 0, 1, '', 1, 3, 0),
(36, 'Тип', 0, 0, 1, '', 0, 3, 1),
(37, 'Название', 0, 0, 1, '', 1, 3, 0),
(38, 'Оригинальное название', 0, 0, 1, '', 1, 3, 0);

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
('registered', '3', NULL, 'N;');

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
('createTorrentTask', 1, 'Создание торрента (задача)', NULL, 'N;'),
('guest', 2, 'Guest', 'return Yii::app()->getUser()->getIsGuest();', 'N;'),
('registered', 2, 'Зарегистрированный пользователь', NULL, 'N;'),
('torrents.default.create', 0, 'Создание торрента - первый шаг', NULL, 'N;'),
('torrents.default.createGroup', 0, 'Создание группы торрентов', NULL, 'N;'),
('torrents.default.createTorrent', 0, 'Создание торрента', NULL, 'N;'),
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
('registered', 'createTorrentTask'),
('createTorrentTask', 'torrents.default.create'),
('createTorrentTask', 'torrents.default.createGroup'),
('createTorrentTask', 'torrents.default.createTorrent'),
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `root`, `lft`, `rgt`, `level`, `name`, `image`, `description`) VALUES
(1, 1, 1, 2, 1, 'Видео', '', ''),
(2, 2, 1, 2, 1, 'Аудио', '', ''),
(3, 3, 1, 2, 1, 'Игры', '', ''),
(4, 4, 1, 2, 1, 'Софт', '', ''),
(5, 5, 1, 2, 1, 'Литература', '', ''),
(6, 6, 1, 2, 1, 'Разное', '', '');

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
(2, 9),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 21),
(2, 20),
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 22),
(1, 12),
(1, 13),
(1, 14),
(3, 37),
(3, 38),
(3, 36),
(3, 23),
(3, 24),
(3, 25),
(3, 26),
(3, 27),
(3, 28),
(3, 29),
(3, 30),
(3, 31),
(3, 32),
(3, 33),
(3, 34),
(3, 35);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

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
(7, 'base.fromEmail', 'noreply@yii-torrent.com', '', '', '');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
(1, 3, 'Torrent'),
(1, 4, 'Torrent'),
(1, 15, 'Torrent'),
(2, 3, 'Torrent'),
(2, 4, 'Torrent'),
(2, 5, 'Torrent'),
(2, 6, 'Torrent'),
(2, 7, 'Torrent'),
(3, 8, 'Torrent'),
(3, 9, 'Torrent'),
(3, 10, 'Torrent'),
(3, 11, 'Torrent'),
(4, 8, 'Torrent'),
(4, 12, 'Torrent'),
(4, 13, 'Torrent'),
(4, 14, 'Torrent'),
(5, 8, 'Torrent'),
(5, 12, 'Torrent'),
(5, 13, 'Torrent'),
(5, 14, 'Torrent'),
(6, 1, 'Torrent'),
(6, 2, 'Torrent'),
(7, 1, 'Torrent'),
(7, 2, 'Torrent'),
(8, 16, 'Torrent'),
(8, 17, 'Torrent'),
(8, 18, 'Torrent'),
(8, 19, 'Torrent'),
(9, 16, 'Torrent'),
(9, 17, 'Torrent'),
(9, 18, 'Torrent'),
(9, 19, 'Torrent'),
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Дамп данных таблицы `tags`
--

INSERT INTO `tags` (`id`, `name`, `userId`, `count`) VALUES
(1, 'Rap', 1, 5),
(2, 'Hip-hop', 1, 5),
(3, 'Экшн', 1, 4),
(4, 'Комедия', 1, 4),
(5, 'перевод Гоблина', 1, 1),
(6, 'Гоблин', 1, 1),
(7, 'Goblin', 1, 1),
(8, 'Боевик', 1, 3),
(9, 'Детектив', 1, 1),
(10, 'Криминал', 1, 1),
(11, 'Триллер', 1, 1),
(12, 'фантастика', 1, 2),
(13, 'фэнтези', 1, 2),
(14, 'приключения', 1, 2),
(15, 'Кака', 1, 1),
(16, 'RPG', 1, 2),
(17, '3D', 1, 2),
(18, '1st Person', 1, 2),
(19, '3rd Person', 1, 2);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `torrentGroups`
--

INSERT INTO `torrentGroups` (`id`, `title`, `ctime`, `picture`, `mtime`, `cId`, `uid`) VALUES
(1, '', 1375019822, 'uploads/images/TorrentGroup/1/995cd4b8daa050e2ed9a2a0c80a0b6f0.jpg', 1375030528, 1, 0),
(2, '', 1375025671, 'uploads/images/TorrentGroup/2/7c767d30c13801734d228509344210ae.jpg', 1375025869, 1, 0),
(3, '', 1375026137, 'uploads/images/TorrentGroup/3/b7a9564c7f80de5a006b14f19e26d28f.jpg', 1375026267, 2, 0),
(4, '', 1375028777, 'uploads/images/TorrentGroup/4/4f7938dd3459439edcd056bb14d94073.png', 1375029584, 3, 0);

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
(1, '1', 'Эволюция Борна'),
(1, '2', 'The Bourne Legacy'),
(1, '3', '2012'),
(1, '4', 'боевик, триллер, детектив, приключения'),
(1, '5', 'Тони Гилрой'),
(1, '6', 'Джереми Реннер, Рейчел Вайс, Эдвард Нортон, Джоан Аллен, Альберт Финни, Скотт Гленн, Стейси Кич, Донна Мерфи, Майкл Чернус, Кори Столл'),
(1, '7', 'В игре всегда несколько фигур. Одна из них — Джейсон Борн, другая — совершенный агент Аарон Кросс. Их возможности безграничны. Но даже у идеального оружия бывают сбои…'),
(1, '8', 'США, Universal Pictures');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `torrents`
--

INSERT INTO `torrents` (`id`, `info_hash`, `gId`, `ctime`, `size`, `downloads`, `seeders`, `leechers`, `mtime`, `uid`) VALUES
(1, 0x51ea34aaf0ff4996374d3ca34b60f6fdb4469479, 1, 1375019822, 1563533312, 0, 0, 0, 1375027556, 0),
(2, 0x559534742326c2f9fd35a824ca62f347e4cdb7fc, 1, 1375020288, 2335645696, 0, 0, 0, 1375027687, 0),
(3, 0xe11bbd3e985d2418199d86ac710412171a4c13fa, 1, 1375023409, 1465827084, 0, 0, 0, 1375023409, 0),
(4, 0xc18765ccb5180ac4fa605689d6f6d5a04501a4af, 2, 1375025671, 1471240192, 0, 0, 0, 1375025671, 0),
(5, 0xf355cb2cc2839d3f71a8b3a209d8370dd7dcc42f, 2, 1375025869, 1469216768, 0, 0, 0, 1375025869, 0),
(6, 0x4c5428832755b6f9d1c05898e55fccc2dc86542b, 3, 1375026137, 465979017, 0, 0, 0, 1375026137, 0),
(7, 0xf0b6eaa8701aebd2d1fa446a13564c5d3bcfa959, 3, 1375026267, 464366660, 0, 0, 0, 1375026267, 0),
(8, 0xafc7fafbd275ddb8de74efc8dd7fa705b9cace13, 4, 1375028777, 10244931728, 0, 0, 0, 1375028777, 0),
(9, 0x81a2f309e02efbb461ae790d03cf8d2ea39e3f5b, 4, 1375029104, 5479464960, 0, 0, 0, 1375029584, 0);

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
(3, '9', '02:15:02'),
(3, '10', 'Русский профессиональный дубляж'),
(3, '11', 'Mkv'),
(3, '22', 'BDRip 1080p'),
(3, '12', '1920x800, 5305 Кбит/сек, 23,976 кадр/сек, AVC, x264'),
(3, '13', 'AC-3, 384 Кбит/сек, 6 канала(ов), 48,0 КГц'),
(3, '14', 'http://multi-up.com/799405'),
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
(1, '9', '02:15:02'),
(1, '10', 'Русский профессиональный дубляж'),
(1, '11', 'Avi'),
(1, '22', 'HDRip'),
(1, '12', '720x304 (2.37:1), 23.976 fps, XviD build 50 ~1150 kbps avg, 0.22 bit/pixel'),
(1, '13', '48 kHz, AC3 Dolby Digital, 3/2 (L,C,R,l,r) + LFE ch, ~384 kbps'),
(1, '14', 'http://sendfile.su/719839'),
(2, '9', '02:15:02'),
(2, '10', 'Одноголосный'),
(2, '11', 'Avi'),
(2, '22', 'HDRip (перевод Гоблина)'),
(2, '12', '720x304 (2.37:1), 23.976 fps, XviD build 50 ~1150 kbps avg, 0.22 bit/pixel'),
(2, '13', '48 kHz, AC3 Dolby Digital, 3/2 (L,C,R,l,r) + LFE ch, ~384 kbps'),
(2, '14', 'http://sendfile.su/719839'),
(8, '36', 'Legendary Edition (RePack)'),
(8, '28', 'Русский'),
(8, '29', 'Русский'),
(8, '30', 'Вшита'),
(8, '33', 'Версия игры - 1.9.32.0.8\r\nНичего не вырезано/перекодировано\r\nВшито обновление русской локализации от 12.07.2013\r\nВозможность выбора сочетания текста и озвучки\r\nДополнения:\r\nDawnguard\r\nHearthfire\r\nDragonborn\r\nHigh Resolution Texture Pack(Опционально)'),
(8, '34', '1. Скачать\r\n2. Запустить процесс установки\r\n3. Играть ;)'),
(9, '36', 'Лицензия'),
(9, '28', 'Английский'),
(9, '29', 'Английский'),
(9, '30', 'См. инструкцию по установке'),
(9, '34', '1.Распаковать с помощью программы Феникс(http://multi-up.com/592943)\r\n2.Скачать таблетку(выше ссылка)\r\n3.Скопировать содержимое в папку с установленной игрой с заменой\r\n4.Что бы игра была на весь экран,зайдите в "Мои документы"/My Games/Skyrim и в файле SkyrimPrefs.ini смените значения bFull Screen=0 на bFull Screen=1 ,а ниже выставьте разрешение какое вам угодно,например iSize H=1080\r\niSize W=1920\r\n5.Запускайте TESV.exe\r\n6.Наслаждайтесь');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `resetHash`, `active`, `ctime`) VALUES
(1, 'admin', 'admin@yii-torrent.com', '$2a$10$2AulI0UYvajzd9qsjX8yEe6DTWVflryLUra6yrBvMo00JZpG2t1j2', '2ae7f092c41e541ae36d41008d8c28a0', 1, 0),
(3, '', 'user@yii-torrent.com', '$2a$10$yRODMwj31p1eIJEWcJ1e9OSfTF.7he9anaIFACNxh3IRGVfM6kRuu', '68f3710508d1ccf4f0c3c24968f1874c', 0, 0);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `AuthAssignment`
--
ALTER TABLE `AuthAssignment`
  ADD CONSTRAINT `authassignment_ibfk_1` FOREIGN KEY (`itemname`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `AuthItemChild`
--
ALTER TABLE `AuthItemChild`
  ADD CONSTRAINT `authitemchild_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `authitemchild_ibfk_2` FOREIGN KEY (`child`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
