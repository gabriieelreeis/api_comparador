<center><video src="https://user-images.githubusercontent.com/6256321/218345029-19625f66-77df-4117-bb62-abcc43833404.mp4" controls="controls" style="max-width: 730px;">
</video></center>

## Como instalar
Para o funcionamento da api você precisará ter o Docker ou um servidor local PHP com composer instalado.
Irei citar como instalar com o Docker, mas você pode facilmente encontrar metodos de instalação utilizando um servidor local no Google.

Primeiro baixe e instale o Docker
- [Instale agora mesmo](https://www.docker.com/)

Após instalado vá a pasta raiz da api.
Renomeie o arquivo `.env.example` para `.env` e altere as variaveis de acordo com suas configurações locais.
Agora execute o comando `docker-composer up -d` para instalar as imagens necessários e iniciar o seu container, após iniciado o seu container digite `docker-compose exec app php artisan key:generate` para gerar a key para seu projeto Laravel e depois digite `docker-compose exec app php artisan config:cache` para carregar suas configurações.

Após isso seu servidor deverá estar em funcionamento na url `http://localhost/api`
