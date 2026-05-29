-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           8.4.3 - MySQL Community Server - GPL
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para corpware
CREATE DATABASE IF NOT EXISTS `corpware` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `corpware`;

-- Copiando estrutura para tabela corpware.area
CREATE TABLE IF NOT EXISTS `area` (
  `id_area` int NOT NULL AUTO_INCREMENT,
  `nome_area` varchar(30) NOT NULL,
  PRIMARY KEY (`id_area`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela corpware.area: ~0 rows (aproximadamente)
INSERT INTO `area` (`id_area`, `nome_area`) VALUES
	(1, 'alguma');

-- Copiando estrutura para tabela corpware.funcionario
CREATE TABLE IF NOT EXISTS `funcionario` (
  `id_funcionario` int NOT NULL AUTO_INCREMENT,
  `nome_funcionario` varchar(40) NOT NULL,
  `senha` varchar(40) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `pontos` int NOT NULL,
  PRIMARY KEY (`id_funcionario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela corpware.funcionario: ~0 rows (aproximadamente)
INSERT INTO `funcionario` (`id_funcionario`, `nome_funcionario`, `senha`, `admin`, `pontos`) VALUES
	(1, 'john', 'johns', 1, 0);

-- Copiando estrutura para tabela corpware.funcionario_pergunta_lista
CREATE TABLE IF NOT EXISTS `funcionario_pergunta_lista` (
  `idf_funcionario` int NOT NULL,
  `idf_pergunta_lista` int NOT NULL,
  `idf_resposta` int NOT NULL,
  PRIMARY KEY (`idf_funcionario`,`idf_pergunta_lista`),
  KEY `idf_pergunta_lista` (`idf_pergunta_lista`),
  KEY `idf_resposta` (`idf_resposta`),
  CONSTRAINT `funcionario_pergunta_lista_ibfk_1` FOREIGN KEY (`idf_funcionario`) REFERENCES `funcionario` (`id_funcionario`),
  CONSTRAINT `funcionario_pergunta_lista_ibfk_2` FOREIGN KEY (`idf_pergunta_lista`) REFERENCES `pergunta_lista` (`id_pergunta_lista`),
  CONSTRAINT `funcionario_pergunta_lista_ibfk_3` FOREIGN KEY (`idf_resposta`) REFERENCES `resposta` (`id_resposta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela corpware.funcionario_pergunta_lista: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela corpware.lista
CREATE TABLE IF NOT EXISTS `lista` (
  `id_lista` int NOT NULL AUTO_INCREMENT,
  `inicio` date NOT NULL,
  `fim` date NOT NULL,
  PRIMARY KEY (`id_lista`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela corpware.lista: ~0 rows (aproximadamente)
INSERT INTO `lista` (`id_lista`, `inicio`, `fim`) VALUES
	(1, '2026-04-30', '2026-05-05');

-- Copiando estrutura para tabela corpware.pergunta
CREATE TABLE IF NOT EXISTS `pergunta` (
  `id_pergunta` int NOT NULL AUTO_INCREMENT,
  `idf_area` int NOT NULL,
  `pergunta` text NOT NULL,
  `valor` int NOT NULL,
  `image` text,
  PRIMARY KEY (`id_pergunta`),
  KEY `idf_area` (`idf_area`),
  CONSTRAINT `pergunta_ibfk_1` FOREIGN KEY (`idf_area`) REFERENCES `area` (`id_area`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela corpware.pergunta: ~0 rows (aproximadamente)
INSERT INTO `pergunta` (`id_pergunta`, `idf_area`, `pergunta`, `valor`, `image`) VALUES
	(1, 1, 'alguma pergunta', 10, NULL);

-- Copiando estrutura para tabela corpware.pergunta_lista
CREATE TABLE IF NOT EXISTS `pergunta_lista` (
  `id_pergunta_lista` int NOT NULL AUTO_INCREMENT,
  `idf_pergunta` int NOT NULL,
  `idf_lista` int NOT NULL,
  PRIMARY KEY (`id_pergunta_lista`),
  KEY `idf_pergunta` (`idf_pergunta`),
  KEY `idf_lista` (`idf_lista`),
  CONSTRAINT `pergunta_lista_ibfk_1` FOREIGN KEY (`idf_pergunta`) REFERENCES `pergunta` (`id_pergunta`),
  CONSTRAINT `pergunta_lista_ibfk_2` FOREIGN KEY (`idf_lista`) REFERENCES `lista` (`id_lista`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela corpware.pergunta_lista: ~0 rows (aproximadamente)
INSERT INTO `pergunta_lista` (`id_pergunta_lista`, `idf_pergunta`, `idf_lista`) VALUES
	(1, 1, 1);

-- Copiando estrutura para tabela corpware.ranking
CREATE TABLE IF NOT EXISTS `ranking` (
  `id_ranking` int NOT NULL AUTO_INCREMENT,
  `qtd_pessoas` int NOT NULL,
  `titulo` varchar(30) NOT NULL,
  `sobre` text,
  PRIMARY KEY (`id_ranking`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela corpware.ranking: ~0 rows (aproximadamente)
INSERT INTO `ranking` (`id_ranking`, `qtd_pessoas`, `titulo`, `sobre`) VALUES
	(1, 10, 'top da galaxia', 'mtfodau');

-- Copiando estrutura para tabela corpware.resposta
CREATE TABLE IF NOT EXISTS `resposta` (
  `id_resposta` int NOT NULL AUTO_INCREMENT,
  `idf_pergunta` int NOT NULL,
  `resposta` varchar(300) NOT NULL,
  `solucao` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_resposta`),
  KEY `idf_pergunta` (`idf_pergunta`),
  CONSTRAINT `resposta_ibfk_1` FOREIGN KEY (`idf_pergunta`) REFERENCES `pergunta` (`id_pergunta`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela corpware.resposta: ~4 rows (aproximadamente)
INSERT INTO `resposta` (`id_resposta`, `idf_pergunta`, `resposta`, `solucao`) VALUES
	(1, 1, 'resposta muito correta', 1),
	(2, 1, 'resposta mei correta', 0),
	(3, 1, 'resposta tola', 0),
	(4, 1, 'resposta de idoso', 0);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
