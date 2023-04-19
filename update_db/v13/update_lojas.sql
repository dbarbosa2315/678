DROP TABLE IF EXISTS `tec_lojas`;

CREATE TABLE `tec_lojas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(45) NOT NULL,
  `nome` varchar(45) DEFAULT NULL,
  `obs` varchar(100) DEFAULT NULL,
  `token` varchar(50) DEFAULT NULL,
  `tipo` enum('LOJA','DEPOSITO') DEFAULT 'LOJA',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `token_UNIQUE` (`token`),
  UNIQUE KEY `cod_UNIQUE` (`cod`,`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

INSERT INTO `tec_lojas` VALUES
(12,'01','Loja 01','',NULL,'LOJA'),
(13,'69','Loja 69','',NULL,'LOJA'),
(14,'159','Loja 159','',NULL,'LOJA'),
(15,'190','Loja 190','',NULL,'LOJA'),
(16,'213','Loja 213','',NULL,'LOJA'),
(17,'250','Loja 250','',NULL,'LOJA'),
(19,'EST_3','Oriente 3º Andar','',NULL,'DEPOSITO'),
(21,'4017','Estoque 4017','',NULL,'DEPOSITO'),
(22,'4019','Estoque 4019','',NULL,'DEPOSITO'),
(23,'ONLINE','ONLINE','','','LOJA'),
(24,'4001','Estoque 4001','',NULL,'DEPOSITO'),
(25,'4002','Estoque 4002','',NULL,'DEPOSITO'),
(26,'TROCA','TROCA','',NULL,'LOJA');