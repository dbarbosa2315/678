CREATE TABLE `tec_bling_pedido_itens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `codigo` varchar(45) NOT NULL,
  `descricao` varchar(128) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valorunidade` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=312 DEFAULT CHARSET=utf8mb4;