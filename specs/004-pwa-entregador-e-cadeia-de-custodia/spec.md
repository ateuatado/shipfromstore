# Especificação: PWA do entregador e cadeia de custódia

**Feature Branch:** `004-pwa-entregador-e-cadeia-de-custodia`  
**Criada em:** 2026-10-28  
**Status:** Draft

## Objetivo

Disponibilizar ao entregador uma aplicação web responsiva e instalável como PWA, permitindo visualizar as coletas atribuídas, registrar o recebimento do objeto com assinatura na tela e acompanhar o prazo de entrega.

## Regras de atribuição

- A aceitação do pedido é responsabilidade do gestor da unidade.
- O gestor atribui a coleta a um entregador vinculado à própria unidade.
- O entregador não escolhe nem recusa a atribuição nesta primeira versão.
- O entregador visualiza somente as coletas atribuídas a ele.
- O gestor não pode atribuir uma coleta a entregador de outra unidade.

O piloto terá 50 entregadores fictícios, cinco para cada uma das dez unidades. Os identificadores de login serão `entregador_0001` até `entregador_0050`.

## Agrupamento futuro de encomendas

Um mesmo ponto de coleta poderá entregar várias encomendas destinadas à mesma região. Nesse cenário, o deslocamento até o ponto de coleta será compartilhado e o tempo adicional será concentrado na operação de recebimento e nas entregas individuais.

O piloto simplifica a interface para acompanhar uma encomenda ativa por vez, mas o modelo deve permitir a evolução para uma rota agrupada. Futuramente, uma rota poderá conter vários contratos, com sequência de entrega, tempo operacional por encomenda e eventos de custódia independentes.

Quando várias encomendas forem coletadas juntas, cada uma deverá conservar seu próprio `collected_at`, assinatura, status e `delivered_at`, mesmo que os eventos ocorram no mesmo local e momento.

## História de usuário 1 — Visualizar coletas atribuídas (P1)

Como entregador, quero abrir o aplicativo no celular e visualizar minhas coletas para saber quais atendimentos devo realizar.

**Critérios de aceite:**

1. O entregador autenticado vê somente suas próprias coletas.
2. A lista mostra contrato, nome do contato, endereço de coleta e status.
3. A lista não mostra CPF, CNPJ, RG, telefone, e-mail ou dados de outras coletas.
4. Ao tocar em uma coleta, o entregador acessa seus detalhes.

## História de usuário 2 — Confirmar recebimento com assinatura (P1)

Como entregador, quero registrar a assinatura do contato no celular quando receber o objeto para estabelecer a cadeia de custódia.

**Critérios de aceite:**

1. A tela apresenta uma área de assinatura sensível ao toque.
2. O contato pode assinar usando o dedo.
3. A assinatura é obrigatória para confirmar o recebimento.
4. Ao confirmar, o sistema registra entregador, unidade, contrato e data/hora do evento.
5. A confirmação não permite alterar o horário registrado pelo servidor.

## História de usuário 3 — Acompanhar prazo e rota (P1)

Como entregador, quero ver o tempo restante e a rota de entrega para cumprir o limite operacional.

**Critérios de aceite:**

1. O contador de 120 minutos começa somente após a confirmação da coleta com assinatura.
2. O contador aparece em tamanho grande e permanece visível durante o acompanhamento.
3. O sistema indica visualmente quando o prazo está próximo do limite.
4. O entregador consegue visualizar a rota até o destino.
5. O entregador pode abrir a navegação do celular para continuar o deslocamento.

## História de usuário 4 — Confirmar entrega (P1)

Como entregador, quero registrar a entrega concluída para encerrar a cadeia de custódia.

**Critérios de aceite:**

1. Somente uma coleta confirmada pode ser marcada como entregue.
2. O sistema registra o entregador e a data/hora da entrega.
3. O pedido passa para o histórico de entregas da unidade, área e território conforme o perfil de acesso.
4. O entregador não pode editar eventos anteriores.

## Estados do atendimento

```text
atribuída → em deslocamento para coleta → coleta confirmada → em entrega → entregue
```

Casos de não coleta, recusa ou ocorrência serão definidos em uma funcionalidade posterior.

## Registro de atribuição

Cada atribuição deve registrar contrato, unidade, entregador, gestor responsável, data/hora da atribuição, confirmação da coleta, confirmação da entrega e a assinatura digital ou seu hash de integridade.

## Dados exibidos ao entregador

Permitidos:

- número do contrato;
- nome do contato no ponto de coleta;
- endereço de coleta;
- destino necessário para a entrega;
- status, rota e prazo operacional.

Não permitidos:

- CPF, CNPJ ou RG;
- dados de pagamento;
- credenciais;
- pedidos de outros entregadores;
- dados de unidades diferentes da unidade vinculada.

## Requisitos da PWA

- Interface responsiva para celular.
- Pode ser adicionada à tela inicial do dispositivo.
- Deve funcionar sobre HTTPS.
- Deve manter a operação simples, com poucos botões e ações claras.
- O piloto começa conectado à internet.
- O suporte offline e a sincronização posterior ficam para uma evolução futura.

## Fora do escopo

- Aplicativo nativo distribuído por loja de aplicativos.
- Escolha ou recusa da atribuição pelo entregador.
- Registro de ocorrência e não coleta.
- Rastreamento GPS contínuo em tempo real.
- Integração com navegação externa além do botão de abertura da rota.
