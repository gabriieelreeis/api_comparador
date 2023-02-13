## Como instalar

Para o funcionamento da api você precisará ter o Docker ou um servidor local PHP com composer instalado.<br />
Irei citar como instalar com o Docker, mas você pode facilmente encontrar metodos de instalação utilizando um servidor local no Google.<br />

Primeiro baixe e instale o Docker

-   [Instale agora mesmo](https://www.docker.com/)

Após instalado vá a pasta raiz da api.<br />
Renomeie o arquivo `.env.example` para `.env` e altere as variaveis de acordo com suas configurações locais.<br />
Agora execute o comando `docker-composer up -d` para instalar as imagens necessários e iniciar o seu container, após iniciado o seu container digite `docker-compose exec app composer install`, após isso digite `docker-compose exec app php artisan key:generate` para gerar a key para seu projeto Laravel e depois digite `docker-compose exec app php artisan config:cache` para carregar suas configurações.<br />
<br />
Após isso seu servidor deverá estar em funcionamento na url `http://localhost/api`
