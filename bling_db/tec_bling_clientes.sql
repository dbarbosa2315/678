CREATE TABLE `tec_bling_clientes` (
  `id` bigint(20) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `cnpj` varchar(45) NOT NULL,
  `ie` varchar(45) DEFAULT NULL,
  `rg` varchar(45) DEFAULT NULL,
  `endereco` varchar(128) NOT NULL,
  `numero` varchar(16) NOT NULL,
  `complemento` varchar(16) DEFAULT NULL,
  `cidade` varchar(45) NOT NULL,
  `bairro` varchar(45) NOT NULL,
  `cep` varchar(45) NOT NULL,
  `uf` varchar(45) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  `celular` varchar(45) DEFAULT NULL,
  `fone` varchar(45) DEFAULT NULL,
  `dataSync` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
