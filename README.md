# ShipFromStore

Projeto local em CodeIgniter 4 com autenticação CodeIgniter Shield, Bootstrap e MariaDB.

## Ambiente configurado

- PHP 8.2.12 do XAMPP
- CodeIgniter Framework 4.7.5
- CodeIgniter Shield 1.4.1
- Composer 2.9.8
- Bootstrap 5.3.8 + Popper 2.11.8 em `public/assets/`
- Leaflet 1.9.4 em `public/assets/leaflet/`
- MariaDB 10.4.32 via driver `MySQLi`
- Banco local `shipfromstore`, com migrações do Shield executadas
- URL: `https://shipfromstore.test`

O MariaDB foi escolhido porque já estava ativo, aceita a convenção local existente sem expor credenciais e é compatível com as tabelas InnoDB exigidas pelo Shield. O PostgreSQL 18.4 permanece instalado e preservado.

## Estrutura

- `app/Views/layouts/main.php`: layout principal
- `app/Views/pages/`: páginas da aplicação
- `public/assets/css/`: Bootstrap e estilos próprios
- `public/assets/js/`: Bootstrap, Popper e scripts próprios
- `public/assets/fonts/` e `public/assets/img/`: reservados para assets do projeto

## Acesso

- `/`: página inicial
- `/login` e `/register`: rotas do Shield
- `/dashboard`: painel protegido pelo filtro `auth`

O remetente de e-mail do Shield permanece vazio de propósito; defina um endereço real no `.env` antes de habilitar confirmação de e-mail ou recuperação por e-mail.

## Mapa e rotas

`/map` apresenta um raio sintético de 15 km centrado na Rua Mergenthaler, 595, com as unidades fictícias `CenEntEnc_01` a `CenEntEnc_10`, uma coleta e uma entrega de exemplo. O endpoint `POST /api/maps/route` aceita uma lista de pontos no formato OSRM e retorna distância, duração, provedor e avaliação preliminar do SLA de 120 minutos.

O cálculo considera 10 minutos de coleta, 5 minutos de entrega e 15 minutos de margem de segurança além do deslocamento. As unidades são distribuídas em pontos determinísticos dentro do raio para manter os testes reproduzíveis.

O protótipo usa Leaflet, OpenStreetMap e um endpoint OSRM compatível configurado por `maps.osrmBaseUrl`. Se o serviço de rotas não responder, o sistema usa uma estimativa local de distância geográfica com fator de desvio e velocidade média conservadora. Esse fallback é apenas para desenvolvimento; para produção, deve ser substituído por um serviço de rotas próprio ou homologado.

## TLS/Apache

O certificado foi criado com a CA mkcert já existente:

- `C:\xampp\apache\conf\certs\shipfromstore.test.pem`
- `C:\xampp\apache\conf\certs\shipfromstore.test-key.pem`

O VirtualHost foi adicionado a `C:\xampp\apache\conf\extra\httpd-vhosts.conf`, com redirecionamento HTTP → HTTPS e `DocumentRoot` em `public/`.

O teste `httpd.exe -t` retornou `Syntax OK`. O serviço Apache2.4 estava protegido por privilégios administrativos durante a validação; reinicie-o como administrador para carregar o novo VirtualHost.
