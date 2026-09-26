-- Database: dbpwb
CREATE TABLE IF NOT EXISTS `administrator` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(40) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Login: username = admin, password = admin (MD5)
INSERT INTO `administrator` (`full_name`, `username`, `password`)
VALUES ('Delpiah Wahyuningsih', 'admin', MD5('admin'));
