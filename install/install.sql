CREATE TABLE IF NOT EXISTS `adv` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `link` varchar(250) NOT NULL,
  `html` varchar(5000) NOT NULL,
  `image` varchar(250) NOT NULL,
  `type` varchar(10) NOT NULL,
  `redirect` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `albums` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(300) NOT NULL,
  `user_id` int(25) NOT NULL,
  `type` int(1) NOT NULL,
  `password` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `albums_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_name` varchar(1000) NOT NULL,
  `album_id` int(25) NOT NULL,
  `description` varchar(500) NOT NULL,
  `ext` varchar(5) NOT NULL,
  `user_id` int(32) NOT NULL,
  `time` int(32) NOT NULL,
  `dl_times` int(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `black_list` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `user_id` int(16) NOT NULL,
  `block_id` int(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `blogs` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `tags` varchar(1000) NOT NULL,
  `user_id` int(32) NOT NULL,
  `time` int(32) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `chat` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `text` varchar(1000) NOT NULL,
  `user_id` int(32) NOT NULL,
  `time` int(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `time` int(32) NOT NULL,
  `user_id` int(32) NOT NULL,
  `object_name` varchar(32) NOT NULL,
  `object_id` int(32) NOT NULL,
  `text` varchar(5000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `downloads` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `dir_id` int(25) NOT NULL,
  `type` int(1) NOT NULL,
  `server_path` varchar(100) NOT NULL,
  `access` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `downloads_archive` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `file_id` int(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `size` int(100) NOT NULL,
  `server_name` varchar(1000) NOT NULL,
  `ext` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `downloads_files` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `server_name` varchar(1000) NOT NULL,
  `server_dir` varchar(1000) NOT NULL,
  `ext` varchar(5) NOT NULL,
  `user_id` int(25) NOT NULL,
  `time` int(32) NOT NULL,
  `ref_id` int(32) NOT NULL,
  `from_id` int(32) NOT NULL,
  `dl_times` int(32) NOT NULL,
  `size` int(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `downloads_rating` (
  `file_id` int(16) NOT NULL,
  `user_id` int(16) NOT NULL,
  `rated` int(16) NOT NULL,
  `rating` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `forum` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `pos` int(32) NOT NULL,
  `desc` varchar(160) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `forum_c` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `f_id` int(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `pos` int(32) NOT NULL,
  `desc` varchar(160) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `forum_pt` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `topic_id` int(32) NOT NULL,
  `cat_id` int(32) NOT NULL,
  `f_id` int(32) NOT NULL,
  `time` int(32) NOT NULL,
  `user_id` int(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `file` varchar(1000) NOT NULL,
  `file_size` int(100) NOT NULL,
  `text` text NOT NULL,
  `edit_time` int(11) NOT NULL,
  `count_edit` int(11) NOT NULL,
  `edit_user_id` int(11) NOT NULL,
  `pin` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `forum_t` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `cat_id` int(32) NOT NULL,
  `f_id` int(32) NOT NULL,
  `time_last_post` int(11) NOT NULL,
  `user_last_post` int(11) NOT NULL,
  `closed` int(1) NOT NULL,
  `attach` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `forum_vote` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `user_id` int(32) NOT NULL,
  `topic_id` int(32) NOT NULL,
  `time` int(32) NOT NULL,
  `count` int(32) NOT NULL,
  `closed` int(1) NOT NULL,
  `type` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `forum_vote_rez` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `user_id` int(32) NOT NULL,
  `topic_id` int(32) NOT NULL,
  `vote` int(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `friends` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `user_id` int(16) NOT NULL,
  `friend_id` int(16) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `guests` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `browser` text NOT NULL,
  `time` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `likes` (
  `id` int(16) NOT NULL,
  `type` varchar(100) NOT NULL,
  `user_id` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mail` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `sender_id` int(32) NOT NULL,
  `receiver_id` text NOT NULL,
  `time_last_message` int(32) NOT NULL,
  `last_message` text NOT NULL,
  `viewed` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mail_dialogs` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `sender_id` int(32) NOT NULL,
  `receiver_id` text NOT NULL,
  `dialog_id` int(32) NOT NULL,
  `time` int(32) NOT NULL,
  `text` text NOT NULL,
  `viewed` int(1) NOT NULL DEFAULT '0',
  `del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `time` int(32) NOT NULL,
  `user_id` int(32) NOT NULL,
  `name` varchar(256) NOT NULL,
  `picture` varchar(256) NOT NULL,
  `text` text NOT NULL,
  `tags` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notify` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `user_id` int(16) NOT NULL,
  `request_id` varchar(256) NOT NULL,
  `from_id` int(16) NOT NULL,
  `type` varchar(64) NOT NULL,
  `time` int(16) NOT NULL,
  `read` int(1) NOT NULL,
  `request_value` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `lat_name` varchar(100) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `settings` (
  `user_id` int(20) NOT NULL,
  `language` varchar(5) NOT NULL,
  `items` int(2) NOT NULL,
  `theme_mobile` varchar(50) NOT NULL,
  `theme_web` varchar(50) NOT NULL,
  `fast_form` int(1) NOT NULL,
  `show_profile` int(1) NOT NULL,
  `allow_messages` int(1) NOT NULL,
  `timezone` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `smiles` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `cat` int(16) NOT NULL,
  `smile` varchar(32) NOT NULL,
  `ext` varchar(5) NOT NULL,
  `type` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Dumping data for table `smiles`
--
INSERT INTO `smiles` (`id`, `name`, `cat`, `smile`, `ext`, `type`) VALUES
(1, 'smiles_general', 0, '', '', 0),
(2, '', 1, ':)', 'gif', 1),
(3, '', 1, ':(', 'gif', 1),
(4, '', 1, ':D', 'gif', 1),
(5, '', 1, ':lol:', 'gif', 1),
(6, '', 1, ':mat:', 'gif', 1),
(7, '', 1, ':ogo:', 'gif', 1),
(8, '', 1, ':beer:', 'gif', 1),
(9, '', 1, ';(', 'gif', 1),
(10, '', 1, ':-/', 'gif', 1),
(11, '', 1, ':super:', 'gif', 1),
(12, '', 1, 'o_О', 'gif', 1),
(13, '', 1, ':bye:', 'gif', 1),
(14, '', 1, ';)', 'gif', 1),
(15, '', 1, ':???:', 'gif', 1),
(16, '', 1, ':no:', 'gif', 1),
(17, '', 1, ':be:', 'gif', 1),
(18, 'smiles_gestures', 0, '', '', 0),
(19, '', 18, ':nono:', 'gif', 1),
(20, '', 18, ':ok:', 'gif', 1),
(21, '', 18, ':poklon:', 'gif', 1),
(22, '', 18, ':bad:', 'gif', 1),
(23, '', 18, ':tupak:', 'gif', 1),
(24, '', 18, ':cool:', 'gif', 1),
(25, '', 18, ':apl:', 'gif', 1),
(26, '', 18, ':hi:', 'gif', 1),
(27, '', 18, ':nez:', 'gif', 1),
(28, '', 18, ':rock:', 'gif', 1),
(29, '', 18, ':fig:', 'gif', 1),
(30, 'smiles_emotions', 0, '', '', 0),
(31, '', 30, ':aaa:', 'gif', 1),
(32, '', 30, ':wall:', 'gif', 1),
(33, '', 30, ':zbs:', 'gif', 1),
(34, '', 30, ':dum:', 'gif', 1),
(35, '', 30, ':fuu:', 'gif', 1),
(36, '', 30, ':help:', 'gif', 1),
(37, '', 30, ':haha:', 'gif', 1),
(38, '', 30, ':hm:', 'gif', 1),
(39, '', 30, ':idea:', 'gif', 1),
(40, '', 30, ':mda:', 'gif', 1),
(41, '', 1, '8)', 'gif', 1),
(42, '', 30, ':gy:', 'gif', 1),
(43, 'smiles_actions', 0, '', '', 0),
(44, '', 43, ':read:', 'gif', 1),
(45, '', 43, ':dush:', 'gif', 1),
(46, '', 43, ':photo:', 'gif', 1),
(47, '', 43, ':game:', 'gif', 1),
(48, '', 43, ':game2:', 'gif', 1),
(49, '', 43, ':cards:', 'gif', 1),
(50, '', 43, ':mob:', 'gif', 1),
(51, 'smiles_music', 0, '', '', 0),
(52, '', 51, ':bayan:', 'gif', 1),
(53, '', 51, ':baraban:', 'gif', 1),
(54, '', 51, ':disko:', 'gif', 1),
(55, '', 51, ':dj:', 'gif', 1),
(56, '', 51, ':guitar:', 'gif', 1),
(57, '', 51, ':music:', 'gif', 1),
(58, '', 51, ':punk:', 'gif', 1),
(59, '', 51, ':skripka:', 'gif', 1),
(60, '', 51, ':stereo:', 'gif', 1),
(61, '', 51, ':truba:', 'gif', 1),
(62, 'smiles_sport', 0, '', '', 0),
(63, '', 62, ':basket:', 'gif', 1),
(64, '', 62, ':best:', 'gif', 1),
(65, '', 62, ':box:', 'gif', 1),
(66, '', 62, ':fanat:', 'gif', 1),
(67, '', 62, ':ganteli:', 'gif', 1),
(68, '', 62, ':ganteli2:', 'gif', 1),
(69, '', 62, ':nhl:', 'gif', 1),
(70, '', 62, ':serf:', 'gif', 1),
(71, '', 62, ':sharf:', 'gif', 1),
(72, '', 62, ':tenis:', 'gif', 1),
(73, '', 62, ':velo:', 'gif', 1),
(74, '', 62, ':shtanga:', 'gif', 1),
(75, 'smiles_admin', 0, '', '', 0),
(76, '', 75, ':boss:', 'gif', 1),
(77, '', 75, ':ban:', 'gif', 1),
(78, '', 75, ':prison:', 'gif', 1),
(79, '', 75, ':rules:', 'gif', 1),
(80, '', 75, ':moder:', 'gif', 1);

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `nick` varchar(32) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(25) NOT NULL,
  `surname` varchar(40) NOT NULL,
  `skype` varchar(50) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `personal_status` varchar(40) NOT NULL,
  `site` varchar(40) NOT NULL,
  `info` varchar(1000) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `city` varchar(55) NOT NULL,
  `country` varchar(55) NOT NULL,
  `time` varchar(55) NOT NULL,
  `reg_time` varchar(55) NOT NULL,
  `location` varchar(100) NOT NULL,
  `level` int(2) NOT NULL,
  `ban_time` int(32) NOT NULL,
  `ban_text` varchar(1000) NOT NULL,
  `balance` int(64) NOT NULL,
  `nick_color` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users_rating` (
  `user_id` int(16) NOT NULL,
  `plus` int(16) NOT NULL,
  `minus` int(16) NOT NULL,
  `from_id` int(16) NOT NULL,
  `text` varchar(200) NOT NULL,
  `time` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
