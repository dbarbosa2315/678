# Projeto BELA PLUS

![Configuração](https://alisonjuliano.com/wp-content/uploads/2021/10/docker-nginx-php-laravel-mysql-1024x427.png)

Este projeto utiliza Docker para criar um ambiente de desenvolvimento com Nginx, MySQL e PHPMyAdmin. A aplicação principal é acessível através de `localhost` na porta 8080, enquanto o PHPMyAdmin pode ser acessado na porta 8888.

## Pré-requisitos

Para executar este projeto, você precisará instalar o [Docker](https://www.docker.com/) e o [Docker Compose](https://docs.docker.com/compose/).

## Executando a aplicação

1. Clone este repositório:
https://github.com/uesleisales/ecommerce-pdv-test

2. Navegue até a pasta raiz do projeto.

3. Verifique se a pasta mysql-data está previante criada na raiz do projeto. Essa pasta irá armazenar os dados do banco local configurado no Docker.

4. Verifique se as seguintes pastas estão criadas e configuradas no projeto.

[![Print](https://raw.githubusercontent.com/uesleisales/ecommerce-pdv-test/main/image.png
)](https://www.github.com/)

5. Execute o Docker Compose:
docker-compose up -d

Agora a aplicação principal estará disponível em [http://localhost:8080](http://localhost:8080) e o PHPMyAdmin em [http://localhost:8888](http://localhost:8888).

## Licença

Este projeto está licenciado sob a Licença MIT - consulte o arquivo [LICENSE.md](LICENSE.md) para obter detalhes.

