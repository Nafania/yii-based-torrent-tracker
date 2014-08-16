SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `yii-torrent-test`
--
CREATE DATABASE IF NOT EXISTS `yii-torrent` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `yii-torrent`;

-- --------------------------------------------------------

--
-- Структура таблицы `advertisements`
--

CREATE TABLE IF NOT EXISTS `advertisements` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `systemName` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `code` text NOT NULL,
  `bizRule` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

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

-- --------------------------------------------------------

--
-- Структура таблицы `attributes`
--

CREATE TABLE IF NOT EXISTS `attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `validator` varchar(255) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `description` varchar(255) NOT NULL,
  `common` tinyint(1) NOT NULL,
  `cId` int(10) NOT NULL,
  `separate` tinyint(1) NOT NULL,
  `append` varchar(255) NOT NULL,
  `prepend` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `AuthAssignment`
--

CREATE TABLE IF NOT EXISTS `AuthAssignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Структура таблицы `blogPosts`
--

CREATE TABLE IF NOT EXISTS `blogPosts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `blogId` int(10) NOT NULL,
  `ownerId` int(11) unsigned DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `pinned` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `blogId` (`blogId`),
  KEY `ownerId` (`ownerId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `blogs`
--

CREATE TABLE IF NOT EXISTS `blogs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `ownerId` int(10) unsigned NOT NULL,
  `ctime` int(11) NOT NULL,
  `description` text NOT NULL,
  `groupId` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ownerId` (`ownerId`),
  KEY `groupId` (`groupId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

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

-- --------------------------------------------------------

--
-- Структура таблицы `commentCounts`
--

CREATE TABLE IF NOT EXISTS `commentCounts` (
  `modelName` varchar(255) NOT NULL,
  `modelId` int(10) NOT NULL,
  `count` int(10) NOT NULL,
  PRIMARY KEY (`modelName`,`modelId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `ownerId` int(10) unsigned DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `parentId` int(10) NOT NULL,
  `modelName` varchar(45) NOT NULL,
  `modelId` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ownerId` (`ownerId`),
  KEY `parentId` (`parentId`),
  KEY `modelId` (`modelId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `delete187F3`
--

CREATE TABLE IF NOT EXISTS `delete187F3` (
  `tId` int(10) NOT NULL,
  `reason` text NOT NULL,
  PRIMARY KEY (`tId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `downloads`
--

CREATE TABLE IF NOT EXISTS `downloads` (
  `tId` int(10) NOT NULL,
  `uId` int(10) unsigned NOT NULL,
  `uploaded` bigint(20) NOT NULL,
  `downloaded` bigint(20) NOT NULL,
  `mtime` int(11) NOT NULL,
  `completeTime` int(11) NOT NULL,
  PRIMARY KEY (`tId`,`uId`),
  KEY `uId` (`uId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `drafts`
--

CREATE TABLE IF NOT EXISTS `drafts` (
  `formId` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `uId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`formId`,`uId`),
  KEY `uId` (`uId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `icon` varchar(32) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ctime` int(11) NOT NULL,
  `uId` int(10) unsigned NOT NULL,
  `unread` tinyint(1) NOT NULL,
  `notified` tinyint(1) NOT NULL,
  `uniqueType` varchar(60) NOT NULL,
  `count` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `unread` (`unread`),
  KEY `uId` (`uId`),
  KEY `notified` (`notified`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE IF NOT EXISTS `favorites` (
  `ctime` int(11) NOT NULL,
  `modelId` int(10) NOT NULL,
  `modelName` varchar(60) NOT NULL,
  `uId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`modelId`,`modelName`,`uId`),
  KEY `uId` (`uId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `originalTitle` varchar(255) NOT NULL,
  `extension` varchar(5) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ownerId` int(10) NOT NULL,
  `modelName` varchar(45) NOT NULL,
  `modelId` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  `ownerId` int(10) NOT NULL,
  `blocked` tinyint(1) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `groupUsers`
--

CREATE TABLE IF NOT EXISTS `groupUsers` (
  `idGroup` int(10) NOT NULL,
  `idUser` int(10) unsigned NOT NULL,
  `ctime` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`idGroup`,`idUser`),
  KEY `idUser` (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `peers`
--

CREATE TABLE IF NOT EXISTS `peers` (
  `fid` int(10) unsigned NOT NULL DEFAULT '0',
  `peer_id` blob NOT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `left` bigint(20) unsigned NOT NULL DEFAULT '0',
  `started` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `useragent` varchar(60) DEFAULT NULL,
  `downspeed` bigint(20) unsigned NOT NULL DEFAULT '0',
  `upspeed` bigint(20) unsigned NOT NULL DEFAULT '0',
  `announced` int(11) NOT NULL DEFAULT '0',
  `completed` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `fid` (`fid`,`uid`),
  KEY `peer_id` (`peer_id`(20)),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `privateMessages`
--

CREATE TABLE IF NOT EXISTS `privateMessages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `senderUid` int(10) unsigned DEFAULT NULL,
  `receiverUid` int(10) unsigned DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `readed` tinyint(1) NOT NULL,
  `branch` int(10) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  `parentId` int(10) DEFAULT NULL,
  `deletedBy` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `senderUid` (`senderUid`),
  KEY `receiverUid` (`receiverUid`),
  KEY `branch` (`branch`),
  KEY `parentId` (`parentId`),
  KEY `deletedBy` (`deletedBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `ratingRelations`
--

CREATE TABLE IF NOT EXISTS `ratingRelations` (
  `modelName` varchar(255) NOT NULL,
  `modelId` int(10) NOT NULL,
  `rating` int(10) NOT NULL,
  `uId` int(10) unsigned NOT NULL,
  `ctime` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`modelName`,`modelId`,`uId`),
  KEY `uId` (`uId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `modelName` varchar(255) NOT NULL,
  `modelId` int(10) NOT NULL,
  `rating` float(10,2) NOT NULL,
  PRIMARY KEY (`modelName`,`modelId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `modelName` varchar(255) NOT NULL,
  `modelId` int(10) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `reportsContent`
--

CREATE TABLE IF NOT EXISTS `reportsContent` (
  `rId` int(10) NOT NULL,
  `uId` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`rId`,`uId`),
  KEY `uId` (`uId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE IF NOT EXISTS `reviews` (
  `modelId` int(10) NOT NULL,
  `modelName` varchar(45) NOT NULL,
  `apiName` varchar(255) NOT NULL,
  `mtime` int(11) NOT NULL,
  `ratingText` text NOT NULL,
  UNIQUE KEY `modelId` (`modelId`,`modelName`,`apiName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `reviewsRelations`
--

CREATE TABLE IF NOT EXISTS `reviewsRelations` (
  `apiName` varchar(255) NOT NULL,
  `cId` int(10) unsigned NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`apiName`,`cId`),
  KEY `cId` (`cId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `savedSearches`
--

CREATE TABLE IF NOT EXISTS `savedSearches` (
  `uId` int(10) unsigned NOT NULL,
  `modelName` varchar(255) NOT NULL,
  `data` text NOT NULL,
  UNIQUE KEY `uId_2` (`uId`,`modelName`),
  KEY `uId` (`uId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(32) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` text,
  `uId` int(10) unsigned DEFAULT NULL,
  `lastVisit` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uId` (`uId`),
  KEY `expire` (`expire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `subscriptions`
--

CREATE TABLE IF NOT EXISTS `subscriptions` (
  `modelId` int(10) NOT NULL,
  `modelName` varchar(45) NOT NULL,
  `uId` int(10) unsigned NOT NULL,
  `ctime` int(11) NOT NULL,
  UNIQUE KEY `unique` (`modelId`,`modelName`,`uId`),
  KEY `uId` (`uId`),
  KEY `modelId` (`modelId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tagRelations`
--

CREATE TABLE IF NOT EXISTS `tagRelations` (
  `modelId` int(10) unsigned NOT NULL,
  `tagId` int(10) unsigned NOT NULL,
  `modelName` varchar(45) NOT NULL,
  `uId` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`modelId`,`tagId`,`modelName`),
  KEY `tagId` (`tagId`),
  KEY `uId` (`uId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `count` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Tag_name` (`name`),
  KEY `count` (`count`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_migration`
--

CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `torrentCommentsRelations`
--

CREATE TABLE IF NOT EXISTS `torrentCommentsRelations` (
  `commentId` int(10) NOT NULL,
  `torrentId` int(10) NOT NULL,
  PRIMARY KEY (`commentId`,`torrentId`),
  KEY `torrentId` (`torrentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `cId` int(10) unsigned DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `cId` (`cId`),
  KEY `uid` (`uid`),
  KEY `mtime` (`mtime`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `torrentGroupsEAV`
--

CREATE TABLE IF NOT EXISTS `torrentGroupsEAV` (
  `entity` int(10) NOT NULL,
  `attribute` int(10) unsigned NOT NULL,
  `value` text NOT NULL,
  KEY `ikEntity` (`entity`),
  KEY `attribute` (`attribute`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `uid` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gId` (`gId`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `torrentsEAV`
--

CREATE TABLE IF NOT EXISTS `torrentsEAV` (
  `entity` int(10) NOT NULL,
  `attribute` int(10) unsigned NOT NULL,
  `value` text NOT NULL,
  KEY `ikEntity` (`entity`),
  KEY `attribute` (`attribute`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `torrentsNameRules`
--

CREATE TABLE IF NOT EXISTS `torrentsNameRules` (
  `attrId` int(10) unsigned NOT NULL,
  `catId` int(10) unsigned NOT NULL,
  `order` int(10) NOT NULL,
  KEY `attrId` (`attrId`),
  KEY `catId` (`catId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `torrentstream_categories`
--

CREATE TABLE IF NOT EXISTS `torrentstream_categories` (
  `id_torrentstream_category` int(10) NOT NULL AUTO_INCREMENT,
  `fk_category` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id_torrentstream_category`),
  KEY `fk_category` (`fk_category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `userConfirmCodes`
--

CREATE TABLE IF NOT EXISTS `userConfirmCodes` (
  `uId` int(10) unsigned NOT NULL,
  `confirmCode` varchar(32) NOT NULL,
  KEY `uId` (`uId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `userProfiles`
--

CREATE TABLE IF NOT EXISTS `userProfiles` (
  `uid` int(10) unsigned NOT NULL,
  `picture` varchar(255) NOT NULL,
  `torrentPass` varchar(32) NOT NULL,
  `disabledNotifies` tinyint(1) NOT NULL DEFAULT '0',
  `theme` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `emailConfirmed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `userSocialAccounts`
--

CREATE TABLE IF NOT EXISTS `userSocialAccounts` (
  `uId` int(10) unsigned NOT NULL,
  `id` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`service`),
  KEY `uId` (`uId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `userWarnings`
--

CREATE TABLE IF NOT EXISTS `userWarnings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uId` int(10) unsigned NOT NULL,
  `fromUid` int(10) unsigned DEFAULT NULL,
  `text` text NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uId` (`uId`,`fromUid`),
  KEY `fromUid` (`fromUid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `xbt_changed_hashes`
--

CREATE TABLE IF NOT EXISTS `xbt_changed_hashes` (
  `tId` int(11) NOT NULL,
  PRIMARY KEY (`tId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `xbt_deleted_hashes`
--

CREATE TABLE IF NOT EXISTS `xbt_deleted_hashes` (
  `fid` int(10) NOT NULL DEFAULT '0',
  `info_hash` blob NOT NULL,
  KEY `fid` (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `attrChars`
--
ALTER TABLE `attrChars`
  ADD CONSTRAINT `attrChars_ibfk_1` FOREIGN KEY (`attrId`) REFERENCES `attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

--
-- Ограничения внешнего ключа таблицы `blogPosts`
--
ALTER TABLE `blogPosts`
  ADD CONSTRAINT `blogPosts_ibfk_1` FOREIGN KEY (`blogId`) REFERENCES `blogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blogPosts_ibfk_2` FOREIGN KEY (`ownerId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`ownerId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blogs_ibfk_2` FOREIGN KEY (`groupId`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`ownerId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `downloads`
--
ALTER TABLE `downloads`
  ADD CONSTRAINT `downloads_ibfk_1` FOREIGN KEY (`tId`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `downloads_ibfk_2` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `drafts`
--
ALTER TABLE `drafts`
  ADD CONSTRAINT `drafts_ibfk_1` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `groupUsers`
--
ALTER TABLE `groupUsers`
  ADD CONSTRAINT `groupUsers_ibfk_1` FOREIGN KEY (`idGroup`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `groupUsers_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `privateMessages`
--
ALTER TABLE `privateMessages`
  ADD CONSTRAINT `privatemessages_ibfk_1` FOREIGN KEY (`senderUid`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `privatemessages_ibfk_2` FOREIGN KEY (`receiverUid`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `privatemessages_ibfk_3` FOREIGN KEY (`branch`) REFERENCES `privateMessages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `privatemessages_ibfk_4` FOREIGN KEY (`parentId`) REFERENCES `privateMessages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `privatemessages_ibfk_5` FOREIGN KEY (`deletedBy`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `ratingRelations`
--
ALTER TABLE `ratingRelations`
  ADD CONSTRAINT `ratingRelations_ibfk_1` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reportsContent`
--
ALTER TABLE `reportsContent`
  ADD CONSTRAINT `reportscontent_ibfk_1` FOREIGN KEY (`rId`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reportscontent_ibfk_2` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reviewsRelations`
--
ALTER TABLE `reviewsRelations`
  ADD CONSTRAINT `reviewsrelations_ibfk_1` FOREIGN KEY (`cId`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `savedSearches`
--
ALTER TABLE `savedSearches`
  ADD CONSTRAINT `savedsearches_ibfk_1` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tagRelations`
--
ALTER TABLE `tagRelations`
  ADD CONSTRAINT `tagrelations_ibfk_1` FOREIGN KEY (`tagId`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tagrelations_ibfk_2` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `torrentCommentsRelations`
--
ALTER TABLE `torrentCommentsRelations`
  ADD CONSTRAINT `torrentCommentsRelations_ibfk_1` FOREIGN KEY (`commentId`) REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `torrentCommentsRelations_ibfk_2` FOREIGN KEY (`torrentId`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `torrentGroups`
--
ALTER TABLE `torrentGroups`
  ADD CONSTRAINT `torrentGroups_ibfk_1` FOREIGN KEY (`cId`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `torrentGroups_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `torrentGroupsEAV`
--
ALTER TABLE `torrentGroupsEAV`
  ADD CONSTRAINT `torrentGroupsEAV_ibfk_1` FOREIGN KEY (`attribute`) REFERENCES `attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `torrentGroupsEAV_ibfk_2` FOREIGN KEY (`entity`) REFERENCES `torrentGroups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `torrents`
--
ALTER TABLE `torrents`
  ADD CONSTRAINT `torrents_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `torrents_ibfk_2` FOREIGN KEY (`gId`) REFERENCES `torrentGroups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `torrentsEAV`
--
ALTER TABLE `torrentsEAV`
  ADD CONSTRAINT `torrentsEAV_ibfk_1` FOREIGN KEY (`entity`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `torrentsEAV_ibfk_2` FOREIGN KEY (`attribute`) REFERENCES `attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `torrentsNameRules`
--
ALTER TABLE `torrentsNameRules`
  ADD CONSTRAINT `torrentsNameRules_ibfk_1` FOREIGN KEY (`attrId`) REFERENCES `attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `torrentsNameRules_ibfk_2` FOREIGN KEY (`catId`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `torrentstream_categories`
--
ALTER TABLE `torrentstream_categories`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`fk_category`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `userConfirmCodes`
--
ALTER TABLE `userConfirmCodes`
  ADD CONSTRAINT `userConfirmCodes_ibfk_1` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `userProfiles`
--
ALTER TABLE `userProfiles`
  ADD CONSTRAINT `userProfiles_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `userSocialAccounts`
--
ALTER TABLE `userSocialAccounts`
  ADD CONSTRAINT `userSocialAccounts_ibfk_1` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `userWarnings`
--
ALTER TABLE `userWarnings`
  ADD CONSTRAINT `userwarnings_ibfk_1` FOREIGN KEY (`uId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userwarnings_ibfk_2` FOREIGN KEY (`fromUid`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `xbt_changed_hashes`
--
ALTER TABLE `xbt_changed_hashes`
  ADD CONSTRAINT `xbt_changed_hashes_ibfk_1` FOREIGN KEY (`tId`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES
('admin', 2, 'Администратор', NULL, 'N;'),
('blogs.blogsBackend.*', 0, 'Управление блогами в администраторском разделе', NULL, 'N;'),
('blogs.default.create', 0, 'Создание блога', '', 'N;'),
('blogs.default.index', 0, 'Просмотр списка блогов', '', 'N;'),
('blogs.default.my', 0, 'Просмотр своих блогов', '', 'N;'),
('blogs.default.view', 0, 'Просмотр одного блога', '', 'N;'),
('blogs.post.create', 0, 'Создание записи', '', 'N;'),
('blogs.post.delete', 0, 'Удаление записи', '', 'N;'),
('blogs.post.tagsSuggest', 0, 'Ajax подсказки тегов для записи в блоге', '', 'N;'),
('blogs.post.update', 0, 'Редактирование записи', '', 'N;'),
('blogs.post.view', 0, 'Просмотр записи из блога', '', 'N;'),
('canViewTorrentOwner', 0, 'Может видеть владельца торрента', '', 'N;'),
('changeOwnStatus', 0, 'Изменение статуса приглашения в группе', 'return $params[''uId''] == Yii::app()->getUser()->getId();', 'N;'),
('changeOwnStatusTask', 1, 'Изменение статуса приглашения в группе (задача)', NULL, 'N;'),
('comments.default.create', 0, 'Создание комментария', 'return Yii::app()->getUser()->getRating() >= 0;', 'N;'),
('comments.default.delete', 0, 'Удаление комментария', NULL, 'N;'),
('comments.default.loadAnswerBlock', 0, 'Ответ на другой комментария', NULL, 'N;'),
('comments.default.update', 0, 'Редактирование комментария', '', 'N;'),
('createPostInBlog', 0, 'Создание записи в любом блоге', '', 'N;'),
('createPostInBlogTask', 1, 'Создание записи в любом блоге (задача)', '', 'N;'),
('createPostInGroupMemberBlog', 0, 'Может оставлять записи в группе, в которой является участником', 'return $params[''isMember''] == true;', 'N;'),
('createPostInOwnBlog', 0, 'Создание записи в своем блоге', 'return $params[''ownerId''] == Yii::app()->getUser()->getId();', 'N;'),
('createPostInOwnBlogTask', 1, 'Создание записи в своем блоге (задача)', '', 'N;'),
('createTorrentTask', 1, 'Создание торрента (задача)', NULL, 'N;'),
('createUserWarning', 0, 'Создание предупреждения для пользователя', '$a = function ( $params ) { $model = $params[''model'']; $roles = Yii::app()->getAuthManager()->getRoles($model->getId()); foreach ( $roles AS $role ) { if ( $role->getName() == ''admin'' || $role->getName() == ''moderator'' ) { return false; } } return true; }; return $a($params);', 'N;'),
('createWarningTask', 1, 'Создание предупреждения (задача)', '', 'N;'),
('deleteComment', 0, 'Удаление любого комментария', NULL, 'N;'),
('deleteCommentTask', 1, 'Удаление любого комментария (задача)', '', 'N;'),
('deleteFile', 0, 'Удаление любого загруженного файла', '', 'N;'),
('deleteOwnComment', 0, 'Удаление своего комментария', 'return $params[''model'']->ownerId == Yii::app()->getUser()->getId() && $params[''model'']->ctime > time() - 5 * 60 && !$params[''model'']->childs;', 'N;'),
('deleteOwnCommentTask	', 1, 'Удаление своего комментария (задача)', '', 'N;'),
('deleteOwnFile', 0, 'Удаление своего загруженного файла', 'return $params[''ownerId''] == Yii::app()->getUser()->getId();', 'N;'),
('deleteOwnFileTask', 1, 'Удаление своего загруженного файла (задача)', '', 'N;'),
('deletePostInBlog', 0, 'Удаление записи в любом блоге', '', 'N;'),
('deletePostInBlogTask', 1, 'Удаление записи в любом блоге (задача)', '', 'N;'),
('deletePostInOwnBlog', 0, 'Удаление записи в своем блоге', 'return $params[''ownerId''] == Yii::app()->getUser()->getId();', 'N;'),
('deletePostInOwnBlogTask', 1, 'Удаление записи в своем блоге (задача)', '', 'N;'),
('deleteTorrent', 0, 'Удаление любого торрента', NULL, 'N;'),
('deleteTorrentGroup', 0, 'Удаление любой группы торрентов', NULL, 'N;'),
('deleteTorrentGroupTask', 1, 'Удаление любой группы торрентов (задача)', NULL, 'N;'),
('deleteTorrentTask', 1, 'Удаление любого торрента (задача)', NULL, 'N;'),
('drafts.default.create', 0, 'Создание черновика', NULL, 'N;'),
('drafts.default.delete', 0, 'Удаление черновика', NULL, 'N;'),
('drafts.default.get', 0, 'Получение черновика', NULL, 'N;'),
('favorites.default.create', 0, 'Добавление в избранное', '', 'N;'),
('favorites.default.delete', 0, 'Удаление из избранного', '', 'N;'),
('favorites.default.index', 0, 'Просмотр своего избранного', '', 'N;'),
('favoritesTask', 1, 'Работа с избранным (задача)', '', 'N;'),
('files.default.delete', 0, 'Удаление файла', '', 'N;'),
('files.default.index', 0, 'Просмотр загруженных файлов', '', 'N;'),
('files.default.upload', 0, 'Загрузка файлов через редактор', NULL, 'N;'),
('groups.default.changeMemberStatus', 0, 'Изменение статуса пользователя в группе', NULL, 'N;'),
('groups.default.create', 0, 'Создание группы', NULL, 'N;'),
('groups.default.index', 0, 'Просмотр списка групп', NULL, 'N;'),
('groups.default.invite', 0, 'Приглашения в группу', NULL, 'N;'),
('groups.default.join', 0, 'Вступление в группу', NULL, 'N;'),
('groups.default.members', 0, 'Просмотр списка членов группы', NULL, 'N;'),
('groups.default.my', 0, 'Просмотр своих групп', NULL, 'N;'),
('groups.default.unJoin', 0, 'Выход из группы', NULL, 'N;'),
('groups.default.view', 0, 'Просмотр одной группы', NULL, 'N;'),
('groups.groupsBackend.*', 0, 'Управление группами в администраторском разделе', NULL, 'N;'),
('guest', 2, 'Guest', 'return Yii::app()->getUser()->getIsGuest();', 'N;'),
('inviteInOwnGroup', 0, 'Приглашения в свою группу', 'return $params[''ownerId''] == Yii::app()->getUser()->getId();', 'N;'),
('joinGroup', 0, 'Вступление в группу', 'return $params[''isMember''] == false && $params[''groupType''] == Group::TYPE_OPENED && $params[''ownerId''] != Yii::app()->getUser()->getId();', 'N;'),
('joinGroupTask', 1, 'Вступление в группу (задача)', NULL, 'N;'),
('moderator', 2, 'Модератор', NULL, 'N;'),
('pms.default.create', 0, 'Создание личного сообщения', NULL, 'N;'),
('pms.default.delete', 0, 'Удаление своих личных сообщений', NULL, 'N;'),
('pms.default.index', 0, 'Просмотр личных сообщений', NULL, 'N;'),
('pms.default.loadAnswerBlock', 0, 'Ответ на сообщение', NULL, 'N;'),
('pms.default.view', 0, 'Просмотр своего личного сообщения', NULL, 'N;'),
('ratings.default.create', 0, 'Добавление рейтинга', NULL, 'N;'),
('registered', 2, 'Зарегистрированный пользователь', NULL, 'N;'),
('reports.default.create', 0, 'Создание жалобы', NULL, 'N;'),
('reports.reportsBackend.*', 0, 'Управление жалобами в администраторском разделе', NULL, 'N;'),
('savedsearches.default.create', 0, 'Создание настроек поиска', NULL, 'N;'),
('site.index', 0, 'Просмотр главной страницы', NULL, 'N;'),
('staticpages.default.index', 0, 'Просмотр статичной страницы', NULL, 'N;'),
('subscriptions.default.create', 0, 'Создание своей подписки', NULL, 'N;'),
('subscriptions.default.delete', 0, 'Удаление своей подписки', NULL, 'N;'),
('subscriptions.event.getList', 0, 'Получение списка своих событий', NULL, 'N;'),
('subscriptions.event.read', 0, 'Отмечание события прочитанным', NULL, 'N;'),
('torrents.default.create', 0, 'Создание торрента - первый шаг', 'return Yii::app()->getUser()->getRating() > -20;', 'N;'),
('torrents.default.createGroup', 0, 'Создание группы торрентов', NULL, 'N;'),
('torrents.default.createTorrent', 0, 'Создание торрента', NULL, 'N;'),
('torrents.default.delete', 0, 'Удаление группы торрентов', NULL, 'N;'),
('torrents.default.deleteTorrent', 0, 'Удаление торрента', NULL, 'N;'),
('torrents.default.download', 0, 'Скачивание торрента', NULL, 'N;'),
('torrents.default.fileList', 0, 'Просмотр списка файлов для торрента', NULL, 'N;'),
('torrents.default.index', 0, 'Просмотр списка торрентов', NULL, 'N;'),
('torrents.default.suggest', 0, 'Ajax подсказки по названиям торрентов', NULL, 'N;'),
('torrents.default.tagsSuggest', 0, 'Ajax подсказки тегов для торрентов', NULL, 'N;'),
('torrents.default.updateGroup', 0, 'Редактирование группы торрентов', NULL, 'N;'),
('torrents.default.updateTorrent', 0, 'Редактирование торрента', NULL, 'N;'),
('torrents.default.view', 0, 'Просмотр одного торрента', NULL, 'N;'),
('torrents.default.watchOnline', 0, 'Онлайн просмотр торрентов', '', 'N;'),
('torrents.torrentsBackend.*', 0, 'Управление торрентами в администраторском разделе', NULL, 'N;'),
('unJoinGroup', 0, 'Выход из группы', 'return $params[''isMember''] == true && $params[''ownerId''] != Yii::app()->getUser()->getId();', 'N;'),
('unJoinGroupTask', 1, 'Выход из группы (задача)', NULL, 'N;'),
('update.post.delete', 0, 'Редактирование записи', '', 'N;'),
('updateComment', 0, 'Редактирование любого комментария', '', 'N;'),
('updateCommentTask', 1, 'Редактирование любого комментария (задача)', '', 'N;'),
('updateMembersStatusInOwnGroup', 0, 'Изменение статуса пользователей в своей группе', 'return $params[''ownerId''] == Yii::app()->getUser()->getId();', 'N;'),
('updateMembersStatusInOwnGroupTask', 1, 'Изменение статуса пользователей в своей группе (задача)', NULL, 'N;'),
('updateOwnComment', 0, 'Редактирование своего комментария', 'return $params[''model'']->ownerId == Yii::app()->getUser()->getId() && $params[''model'']->ctime > time() - 5 * 60 && !$params[''model'']->childs;', 'N;'),
('updateOwnCommentTask', 1, 'Редактирование своего комментария (задача)', '', 'N;'),
('updateOwnTorrent', 0, 'Редактирование своего торрента', 'return $params[''model'']->uid == Yii::app()->getUser()->getId();', 'N;'),
('updateOwnTorrentGroup', 0, 'Редактирование свой группы торрентов', 'return $params[''model'']->uid == Yii::app()->getUser()->getId() && $params[''model'']->ctime > time() - 30 * 60;', 'N;'),
('updateOwnTorrentGroupTask', 1, 'Редактирование своей группы торрентов (задача)', '', 'N;'),
('updateOwnTorrentTask', 1, 'Редактирование своего торрента (задача)', NULL, 'N;'),
('updatePostInBlog', 0, 'Редактирование записи в любом блоге', '', 'N;'),
('updatePostInBlogTask', 1, 'Редактирование записи в любом блоге (задача)', '', 'N;'),
('updatePostInOwnBlog', 0, 'Редактирование записи в своем блоге', 'return $params[''ownerId''] == Yii::app()->getUser()->getId();', 'N;'),
('updatePostInOwnBlogTask', 1, 'Редактирование записи в своем блоге (задача)', '', 'N;'),
('updateTorrent', 0, 'Редактирование любого торрента', NULL, 'N;'),
('updateTorrentGroup', 0, 'Редактирование любой группы торрентов', NULL, 'N;'),
('updateTorrentGroupTask', 1, 'Редактирование любой группы торрентов (задача)', NULL, 'N;'),
('updateTorrentTask', 1, 'Редактирование любого торрента (задача)', NULL, 'N;'),
('user.default.confirmEmail', 0, 'Подтверждение своего email адреса', NULL, 'N;'),
('user.default.delete', 0, 'Удаление своего аккаунта', '', 'N;'),
('user.default.login', 0, 'Вход на сайт', NULL, 'N;'),
('user.default.logout', 0, 'Выход с сайта', NULL, 'N;'),
('user.default.register', 0, 'Регистрация пользователя', NULL, 'N;'),
('user.default.reset', 0, 'Сброс пароля', NULL, 'N;'),
('user.default.restore', 0, 'Восстановление пароля', NULL, 'N;'),
('user.default.settings', 0, 'Настройки аккаунта', NULL, 'N;'),
('user.default.socialAdd', 0, 'Привязка аккаунтов социальных сетей к своему аккаунта', NULL, 'N;'),
('user.default.socialDelete', 0, 'Удаление своего аккаунта социальной сети', NULL, 'N;'),
('user.default.suggest', 0, 'Ajax подсказки имен пользователей', NULL, 'N;'),
('user.default.view', 0, 'Просмотр профиля пользователя', NULL, 'N;'),
('userwarnings.default.create', 0, 'Создание предупреждения', '', 'N;'),
('yiiadmin.default.index', 0, 'Доступ на главную страницу администраторского раздела', NULL, 'N;');

--
-- Дамп данных таблицы `AuthItemChild`
--

INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES
('admin', 'blogs.blogsBackend.*'),
('registered', 'blogs.default.create'),
('guest', 'blogs.default.index'),
('registered', 'blogs.default.index'),
('registered', 'blogs.default.my'),
('guest', 'blogs.default.view'),
('registered', 'blogs.default.view'),
('createPostInBlogTask', 'blogs.post.create'),
('createPostInOwnBlogTask', 'blogs.post.create'),
('deletePostInBlogTask', 'blogs.post.delete'),
('deletePostInOwnBlogTask', 'blogs.post.delete'),
('registered', 'blogs.post.tagsSuggest'),
('updatePostInBlogTask', 'blogs.post.update'),
('updatePostInOwnBlogTask', 'blogs.post.update'),
('guest', 'blogs.post.view'),
('registered', 'blogs.post.view'),
('moderator', 'canViewTorrentOwner'),
('changeOwnStatusTask', 'changeOwnStatus'),
('registered', 'changeOwnStatusTask'),
('registered', 'comments.default.create'),
('deleteCommentTask', 'comments.default.delete'),
('deleteOwnCommentTask	', 'comments.default.delete'),
('registered', 'comments.default.loadAnswerBlock'),
('updateCommentTask', 'comments.default.update'),
('updateOwnCommentTask', 'comments.default.update'),
('createPostInBlogTask', 'createPostInBlog'),
('registered', 'createPostInGroupMemberBlog'),
('createPostInOwnBlogTask', 'createPostInOwnBlog'),
('registered', 'createPostInOwnBlogTask'),
('registered', 'createTorrentTask'),
('createWarningTask', 'createUserWarning'),
('moderator', 'createWarningTask'),
('deleteCommentTask', 'deleteComment'),
('moderator', 'deleteCommentTask'),
('deleteOwnCommentTask	', 'deleteOwnComment'),
('registered', 'deleteOwnCommentTask	'),
('deleteOwnFileTask', 'deleteOwnFile'),
('registered', 'deleteOwnFileTask'),
('deletePostInBlogTask', 'deletePostInBlog'),
('moderator', 'deletePostInBlogTask'),
('deletePostInOwnBlogTask', 'deletePostInOwnBlog'),
('deleteTorrentTask', 'deleteTorrent'),
('deleteTorrentGroupTask', 'deleteTorrentGroup'),
('moderator', 'deleteTorrentGroupTask'),
('moderator', 'deleteTorrentTask'),
('registered', 'drafts.default.create'),
('registered', 'drafts.default.delete'),
('registered', 'drafts.default.get'),
('favoritesTask', 'favorites.default.create'),
('favoritesTask', 'favorites.default.delete'),
('favoritesTask', 'favorites.default.index'),
('registered', 'favoritesTask'),
('deleteOwnFileTask', 'files.default.delete'),
('registered', 'files.default.index'),
('registered', 'files.default.upload'),
('changeOwnStatusTask', 'groups.default.changeMemberStatus'),
('updateMembersStatusInOwnGroupTask', 'groups.default.changeMemberStatus'),
('registered', 'groups.default.create'),
('guest', 'groups.default.index'),
('registered', 'groups.default.index'),
('updateMembersStatusInOwnGroupTask', 'groups.default.invite'),
('joinGroupTask', 'groups.default.join'),
('registered', 'groups.default.join'),
('registered', 'groups.default.members'),
('registered', 'groups.default.my'),
('registered', 'groups.default.unJoin'),
('unJoinGroupTask', 'groups.default.unJoin'),
('guest', 'groups.default.view'),
('registered', 'groups.default.view'),
('admin', 'groups.groupsBackend.*'),
('updateMembersStatusInOwnGroupTask', 'inviteInOwnGroup'),
('joinGroupTask', 'joinGroup'),
('registered', 'joinGroupTask'),
('admin', 'moderator'),
('registered', 'pms.default.create'),
('registered', 'pms.default.delete'),
('registered', 'pms.default.index'),
('registered', 'pms.default.loadAnswerBlock'),
('registered', 'pms.default.view'),
('registered', 'ratings.default.create'),
('moderator', 'registered'),
('registered', 'reports.default.create'),
('admin', 'reports.reportsBackend.*'),
('registered', 'savedsearches.default.create'),
('guest', 'site.index'),
('registered', 'site.index'),
('guest', 'staticpages.default.index'),
('registered', 'staticpages.default.index'),
('registered', 'subscriptions.default.create'),
('registered', 'subscriptions.default.delete'),
('registered', 'subscriptions.event.getList'),
('registered', 'subscriptions.event.read'),
('createTorrentTask', 'torrents.default.create'),
('createTorrentTask', 'torrents.default.createGroup'),
('createTorrentTask', 'torrents.default.createTorrent'),
('deleteTorrentGroupTask', 'torrents.default.delete'),
('deleteTorrentTask', 'torrents.default.deleteTorrent'),
('guest', 'torrents.default.download'),
('registered', 'torrents.default.download'),
('guest', 'torrents.default.fileList'),
('registered', 'torrents.default.fileList'),
('guest', 'torrents.default.index'),
('registered', 'torrents.default.index'),
('registered', 'torrents.default.suggest'),
('registered', 'torrents.default.tagsSuggest'),
('updateOwnTorrentGroupTask', 'torrents.default.updateGroup'),
('updateTorrentGroupTask', 'torrents.default.updateGroup'),
('updateOwnTorrentTask', 'torrents.default.updateTorrent'),
('updateTorrentTask', 'torrents.default.updateTorrent'),
('guest', 'torrents.default.view'),
('registered', 'torrents.default.view'),
('guest', 'torrents.default.watchOnline'),
('registered', 'torrents.default.watchOnline'),
('admin', 'torrents.torrentsBackend.*'),
('unJoinGroupTask', 'unJoinGroup'),
('registered', 'unJoinGroupTask'),
('updateCommentTask', 'updateComment'),
('moderator', 'updateCommentTask'),
('updateMembersStatusInOwnGroupTask', 'updateMembersStatusInOwnGroup'),
('registered', 'updateMembersStatusInOwnGroupTask'),
('updateOwnCommentTask', 'updateOwnComment'),
('registered', 'updateOwnCommentTask'),
('updateOwnTorrentTask', 'updateOwnTorrent'),
('updateOwnTorrentGroupTask', 'updateOwnTorrentGroup'),
('registered', 'updateOwnTorrentGroupTask'),
('registered', 'updateOwnTorrentTask'),
('updatePostInBlogTask', 'updatePostInBlog'),
('moderator', 'updatePostInBlogTask'),
('updatePostInOwnBlogTask', 'updatePostInOwnBlog'),
('registered', 'updatePostInOwnBlogTask'),
('updateTorrentTask', 'updateTorrent'),
('updateTorrentGroupTask', 'updateTorrentGroup'),
('moderator', 'updateTorrentGroupTask'),
('moderator', 'updateTorrentTask'),
('registered', 'user.default.confirmEmail'),
('registered', 'user.default.delete'),
('guest', 'user.default.login'),
('registered', 'user.default.logout'),
('guest', 'user.default.register'),
('guest', 'user.default.reset'),
('guest', 'user.default.restore'),
('registered', 'user.default.settings'),
('registered', 'user.default.socialAdd'),
('registered', 'user.default.socialDelete'),
('registered', 'user.default.suggest'),
('registered', 'user.default.view'),
('createWarningTask', 'userwarnings.default.create'),
('admin', 'yiiadmin.default.index');

--
-- Дамп данных таблицы `config`
--

INSERT INTO `config` (`id`, `param`, `value`, `default`, `label`, `type`) VALUES
(1, 'base.siteName', '', '', '', ''),
(2, 'base.defaultDescription', '', '', '', ''),
(3, 'base.defaultKeywords', '', '', '', ''),
(4, 'base.logoUrl', '/images/logo.gif', '', '', ''),
(5, 'torrentsModule.xbt_listen_url', 'http://yii-torrent', '', '', ''),
(6, 'torrentsModule.listen_port', '2720', '', '', ''),
(7, 'base.fromEmail', 'noreply@yii-torrent', '', '', ''),
(8, 'torrentsModule.torrentsNameDelimiter', '/', '/', '', ''),
(9, 'ratingsModule.ratings', 'a:18:{i:0;s:2:"10";i:1;s:1:"3";i:2;s:3:"0.1";i:3;s:1:"3";i:4;s:4:"0.01";i:5;s:1:"1";i:6;s:3:"0.5";i:7;s:3:"0.5";i:8;s:1:"0";i:9;s:1:"1";i:10;s:1:"1";i:11;s:1:"1";i:12;s:3:"0.1";i:13;s:1:"1";i:14;s:3:"0.1";i:15;s:1:"1";i:16;s:3:"0.1";i:17;s:2:"20";}', '', 'Коэффициенты для рейтингов', ''),
(10, 'subscriptionsModule.socketIOHost', '', '', '', ''),
(11, 'subscriptionsModule.socketIOPort', '', '', '', ''),
(14, 'chatModule.socketIOHost', '', '', '', ''),
(15, 'chatModule.socketIOPort', '', '', '', ''),
(16, 'blogsModule.newsBlogId', '1', '0', 'ID блога, из которого будут публиковать новости', ''),
(17, 'torrentsModule.pageSize', '10', '10', '', ''),
(18, 'announce_interval', '3600', '3600', '', ''),
(19, 'clean_up_interval', '600', '600', '', ''),
(20, 'read_config_interval', '3600', '3600', '', ''),
(21, 'read_db_interval', '180', '180', '', ''),
(22, 'scrape_interval', '3600', '3600', '', ''),
(23, 'write_db_interval', '360', '180', '', ''),
(24, 'analyticsWidget.yaId', '', '', '', ''),
(25, 'analyticsWidget.gaId', '', '', '', ''),
(26, 'analyticsWidget.gaDomain', '', '', '', ''),
(27, 'userModule.socialServices', '', '', '', ''),
(28, '', '', 'a:0:{}', '', ''),
(31, 'reviewsModule.proxies', '["93.115.8.229:8089","119.46.110.17:8080","190.151.10.226:8080"]', '', 'reviewsModule.proxies', 'array');

INSERT INTO `AuthAssignment` (`itemname`, `userid`, `bizrule`, `data`) VALUES
('admin', 1, NULL, NULL);

INSERT INTO `users` (`id`, `name`, `email`, `password`, `resetHash`, `active`, `ctime`, `emailConfirmed`) VALUES
(1, 'admin', 'root@yii-torrent', '$2a$13$0z3MYzTBBFurM3zgn9SJVeg/dBplMw9TCXgaOEB6nJFjcyI/0Qgwy', '', 1, UNIX_TIMESTAMP(NOW()), 0);

INSERT INTO `userProfiles` (`uid`, `picture`, `torrentPass`, `disabledNotifies`, `theme`) VALUES
(1, '', '620c8a3fb9ca410ec5bbb58bfcb3d0b1', 0, 'default');
