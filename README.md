# Projeto BELA PLUS

![Configuração](https://media.licdn.com/dms/image/C4E12AQEX-gb_YViiUw/article-inline_image-shrink_400_744/0/1549652116400?e=1687392000&v=beta&t=8KQfUrA_ufRoeUjKaxvM3Hzk0BC2cItOBtr9WlWxL8w)

Este projeto utiliza Docker para criar um ambiente de desenvolvimento com Nginx, MySQL e PHPMyAdmin. A aplicação principal é acessível através de `localhost` na porta 8080, enquanto o PHPMyAdmin pode ser acessado na porta 8888.

## Replicação de ambiente.
Para simular e rodar o projeto original disponilizado, foram neessárias algumas etapas.
1. Repliquei as configurações de ambiente linux e PHP FPM contidas no arquivo setup.sh e criei uma imagem docker para que o ambiente rodasse corretamente. Essa configuração está disponível no arquivo [Dockerfile](./Dockerfile)

2. Criei uma pasta chamada nginx em infra/etc e dentro dessa pasta criei dois subdiretórios: sites-enabled e snippets. Dentro dessas pastas, fiz as configurações necessárias para que o servidor pudesse funcionar e adicionei a configuração do módulo fpm para que os arquivos PHP pudessem ser excutados ao invés de serem baixados.

3. Fiz as configurações e liberei as portas dos containers no arquivo docker-compose.yml.

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

