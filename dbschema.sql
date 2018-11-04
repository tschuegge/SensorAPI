CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sensors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `userid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  CONSTRAINT `sensors_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sensordata` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `data` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sensorid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sensorid` (`sensorid`),
  CONSTRAINT `sensordata_ibfk_1` FOREIGN KEY (`sensorid`) REFERENCES `sensors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;