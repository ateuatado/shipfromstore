# Especificação: dimensões do pacote e identidade SPIV

**Feature Branch:** `011-dimensoes-pacote-e-identidade-spiv`

## Objetivo

Registrar as dimensões e o peso do pacote para apoiar a operação de coleta e apresentar uma identificação institucional única para o sistema.

## Dados do pacote

Cada coleta pode informar:

- altura em centímetros;
- largura em centímetros;
- profundidade em centímetros;
- peso em quilogramas.

Os valores devem ser positivos e o peso deve aceitar casas decimais.

## Visualização

Na tela do entregador, os dados devem aparecer em um componente visual tridimensional simples, com:

- representação em perspectiva do pacote;
- altura, largura e profundidade em cm;
- peso em kg;
- leitura adequada em celular.

O desenho é apenas informativo e não pretende representar escala física exata.

## Identidade

O nome exibido no cabeçalho e nos títulos globais passa a ser:

**SPIV — Sistema de Prospecção e Inteligência em Vendas**

ShipFromStore permanece apenas como nome histórico do repositório e não deve ser exibido como marca principal nas telas.

## Critérios de aceite

- [ ] O simulador permite informar altura, largura, profundidade e peso.
- [ ] O entregador visualiza as quatro medidas no detalhe da coleta.
- [ ] O pacote possui representação tridimensional em perspectiva.
- [ ] O cabeçalho exibe SPIV em todas as telas autenticadas e públicas.
- [ ] Dados antigos continuam sendo exibidos sem erro quando não possuem dimensões.
