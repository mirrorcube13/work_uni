-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.7.18-log - MySQL Community Server (GPL)
-- Операционная система:         Win64
-- HeidiSQL Версия:              9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица task_4.dictionary
CREATE TABLE IF NOT EXISTS `dictionary` (
  `d_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `d_name` varchar(250) NOT NULL,
  `d_value` varchar(250) NOT NULL,
  PRIMARY KEY (`d_id`),
  UNIQUE KEY `c_name_UNIQUE` (`d_name`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы task_4.dictionary: ~26 rows (приблизительно)
/*!40000 ALTER TABLE `dictionary` DISABLE KEYS */;
INSERT INTO `dictionary` (`d_id`, `d_name`, `d_value`) VALUES
	(2, 'app_header', 'Галлерея фотографий'),
	(4, 'used_space_header', 'Использованно'),
	(5, 'used_spase_amount', ' <span id="mb_used">{DV="mb_used"}</span> МБ из {DV="mb_limit"} MБ'),
	(6, 'statistics_header', 'Статистика'),
	(7, 'stat_user_amount', 'Пользователей'),
	(8, 'stat_file_amount', 'Файлов'),
	(9, 'stat_whole_amount', 'Объём: <span id="storage_size">{DV="storage_size"}</span> МБ'),
	(10, 'stat_avg_amount', 'Объём / польз.: <span id="avg_storage">{DV="avg_storage"}</span> МБ'),
	(11, 'upload_new_file_header', 'Загрузить новый файл'),
	(12, 'select_file_button', 'Выбрать'),
	(13, 'upload_button', 'Загрузить'),
	(14, 'url_get_header', 'Запросить с удалённого сервера'),
	(15, 'put_in_storage_label', 'Поместить в хранилище'),
	(16, 'url_get_button', 'Запросить'),
	(17, 'th_file_name', 'Файл'),
	(18, 'th_file_size', 'Размер (Kb)'),
	(19, 'th_file_uploaded_at', 'Дата и время'),
	(20, 'th_file_delete', 'Удалить'),
	(23, 'copyright_begin', '2017'),
	(24, 'login_label', 'Логин'),
	(25, 'passLabel', 'Пароль'),
	(26, 'rememberLabel', 'Запомнить меня'),
	(27, 'loginBtn', 'Войти'),
	(28, '404_header', 'Страница не найдена'),
	(29, 'not_logged_in_message', 'Необходимо выполнить вход в систему'),
	(30, 'logged_in_message', 'Вы вошли в систему как {DV="user_name"}. Нажмите, чтобы <a href="logout">Выйти</a>'),
	(31, 'limits_header', 'Можно загружать'),
	(32, 'limit_to', 'до'),
	(33, 'limit_MB', 'МБ');
/*!40000 ALTER TABLE `dictionary` ENABLE KEYS */;

-- Дамп структуры для таблица task_4.extensions
CREATE TABLE IF NOT EXISTS `extensions` (
  `ext_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ext_name` varchar(50) NOT NULL,
  PRIMARY KEY (`ext_id`),
  UNIQUE KEY `UNQ_ext_name` (`ext_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы task_4.extensions: ~8 rows (приблизительно)
/*!40000 ALTER TABLE `extensions` DISABLE KEYS */;
INSERT INTO `extensions` (`ext_id`, `ext_name`) VALUES
	(3, 'bmp'),
	(4, 'gif'),
	(2, 'jpeg'),
	(1, 'jpg'),
	(7, 'php'),
	(8, 'png'),
	(6, 'rar'),
	(5, 'zip');
/*!40000 ALTER TABLE `extensions` ENABLE KEYS */;

-- Дамп структуры для таблица task_4.images
CREATE TABLE IF NOT EXISTS `images` (
  `f_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f_title` varchar(250) DEFAULT NULL,
  `f_alt` varchar(250) DEFAULT 'image',
  `f_real_name` varchar(50) NOT NULL,
  `f_uploaded_at` int(10) unsigned NOT NULL,
  `f_size` bigint(20) unsigned NOT NULL,
  `f_views` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`f_id`),
  UNIQUE KEY `UNQ_file_real_name` (`f_real_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы task_4.images: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
