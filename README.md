# ShipFromStore

Projeto local em CodeIgniter 4 com autenticação CodeIgniter Shield, Bootstrap e MariaDB.

## Ambiente configurado

- PHP 8.2.12 do XAMPP
- CodeIgniter Framework 4.7.5
- CodeIgniter Shield 1.4.1
- Composer 2.9.8
- Bootstrap 5.3.8 + Popper 2.11.8 em `public/assets/`
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

## TLS/Apache

O certificado foi criado com a CA mkcert já existente:

- `C:\xampp\apache\conf\certs\shipfromstore.test.pem`
- `C:\xampp\apache\conf\certs\shipfromstore.test-key.pem`

O VirtualHost foi adicionado a `C:\xampp\apache\conf\extra\httpd-vhosts.conf`, com redirecionamento HTTP → HTTPS e `DocumentRoot` em `public/`.

O teste `httpd.exe -t` retornou `Syntax OK`. O serviço Apache2.4 estava protegido por privilégios administrativos durante a validação; reinicie-o como administrador para carregar o novo VirtualHost.
