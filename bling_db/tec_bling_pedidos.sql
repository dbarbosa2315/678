CREATE TABLE `tec_bling_pedidos` (
  `numero` int(11) NOT NULL,
  `data` date NOT NULL,
  `id_cliente` bigint(20) NOT NULL,
  `totalprodutos` decimal(10,2) NOT NULL,
  `totalvenda` decimal(10,2) NOT NULL,
  `situacao` varchar(45) NOT NULL,
  `idSituacao` int(11) NOT NULL,
  `numeroPedidoLoja` varchar(45) NOT NULL,
  `tipoIntegracao` varchar(45) NOT NULL,
  `valorfrete` decimal(10,2) NOT NULL,
  `cod_rastreamento` varchar(64) DEFAULT NULL,
  `dataSync` datetime NOT NULL DEFAULT current_timestamp(),
  `dataBaixa` datetime DEFAULT NULL,
  `usuarioBaixa` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;