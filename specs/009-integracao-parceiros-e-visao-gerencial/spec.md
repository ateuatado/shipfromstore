# Especificação: integração com parceiros e visão gerencial

**Feature Branch:** `009-integracao-parceiros-e-visao-gerencial`

## Objetivo

Permitir que uma coleta não aceita pelas unidades em até 15 minutos seja encaminhada a um parceiro externo por integração de API, sem exigir que o parceiro utilize uma nova interface operacional. O ShipFromStore mantém a coordenação, o prazo, a auditoria e a visão gerencial consolidada.

## Decisões de produto

1. O parceiro trabalha na própria aplicação e nos próprios processos.
2. O ShipFromStore não terá uma tela operacional específica para cada parceiro.
3. A integração será feita por uma camada de adaptadores, permitindo conectar parceiros com APIs diferentes a um contrato interno comum.
4. O piloto pode usar um parceiro fictício, preservando a mesma transição de estado que será usada na integração real.
5. Um pedido encaminhado ao parceiro permanece visível aos gestores, com o status e a linha do tempo da integração.

## Fluxo integrado

```text
Pedido recebido
  → ofertas às unidades próximas
  → aceite de uma unidade
  → operação interna

Pedido recebido
  → 15 minutos sem aceite
  → encaminhamento ao parceiro
  → parceiro recebe pela API
  → parceiro aceita ou recusa
  → parceiro atualiza eventos por webhook/API
  → ShipFromStore consolida o acompanhamento
```

## Contrato interno de integração

O núcleo do sistema deve trabalhar com uma solicitação de coleta independente do fornecedor. O adaptador traduz esse contrato para a API de cada parceiro.

### Envio ao parceiro

```http
POST /api/partners/requests
```

O envio deve conter apenas os dados necessários para executar a coleta e a entrega, respeitando a política de privacidade do piloto.

### Eventos esperados

```text
received
accepted
rejected
courier_assigned
collected
in_transit
delivered
failed
```

Os eventos devem ser idempotentes, possuir identificador externo do parceiro e ser armazenados em histórico.

## Perfil de parceiro

Cada parceiro deve possuir:

- nome e código interno;
- status ativo/inativo;
- regiões atendidas;
- prioridade de acionamento;
- endereço base da API;
- credenciais armazenadas com segurança;
- eventos suportados;
- política de retentativas;
- prazo esperado de resposta.

Credenciais nunca podem aparecer em telas, logs ou respostas da API.

## Visualização por nível de gestão

### Gestor de unidade

Visualiza apenas sua unidade:

- coletas aguardando aceite;
- contador regressivo de 15 minutos;
- coletas aceitas e atribuídas;
- coletas encaminhadas ao parceiro;
- alerta de proximidade do vencimento.

### Gestor de área

Visualiza as unidades da sua área em comparação:

- pendentes por unidade;
- aceites;
- encaminhamentos ao parceiro;
- percentual dentro do prazo;
- tempo médio de resposta;
- detalhe de uma coleta e suas ofertas concorrentes.

### Gestor geral

Visualiza todo o território:

- mapa operacional;
- volume por estado;
- desempenho por área e unidade;
- parceiros acionados;
- taxa de entregas em até duas horas;
- falhas e pendências de integração.

### Administrador

Visualiza também a saúde técnica das integrações:

- solicitações enviadas;
- respostas recebidas;
- falhas de API;
- retentativas;
- latência do parceiro;
- eventos sem correspondência.

## Linha do tempo

Cada coleta deve apresentar uma linha do tempo única, independentemente de ter passado por unidades ou parceiros:

```text
14:00 Pedido recebido
14:00 Ofertado a unidades próximas
14:08 Unidade aceita
14:12 Entregador atribuído
14:25 Coleta realizada
14:25 Prazo de duas horas iniciado
15:17 Entrega concluída
```

Quando houver parceiro:

```text
14:00 Pedido recebido
14:15 Ofertas internas expiradas
14:15 Solicitação enviada ao parceiro
14:17 Parceiro aceitou
14:25 Entregador do parceiro designado
15:20 Entrega concluída
```

## Critérios de aceite

- [ ] Uma coleta encaminhada ao parceiro aparece como uma única solicitação no painel.
- [ ] O gestor da unidade vê o alerta de encaminhamento.
- [ ] O gestor de área vê comparação entre unidades e coletas encaminhadas.
- [ ] O gestor geral vê o consolidado territorial e o mapa.
- [ ] O administrador vê saúde, falhas e retentativas da integração.
- [ ] O parceiro não precisa operar uma tela criada pelo ShipFromStore.
- [ ] Eventos repetidos não duplicam a linha do tempo.
- [ ] Dados sensíveis não são enviados além do necessário para a execução.
