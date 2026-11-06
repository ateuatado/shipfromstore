# Especificação: controle de tempo e simulador operacional

**Feature Branch:** `008-tempo-aceite-e-simulador-operacional`

## Objetivo

Controlar o ciclo desde o recebimento do pedido até a entrega, permitindo testar o fluxo por um painel administrativo sintético.

## Regras

1. O relógio de aceite começa em `received_at`, quando a coleta é encaminhada às unidades elegíveis.
2. A unidade tem 15 minutos para aceitar e atribuir o pedido.
3. Após 15 minutos sem aceite, o pedido muda para `partner_forwarded` e recebe o parceiro configurado.
4. A contagem de 120 minutos começa somente em `collected_at`.
5. A data limite de entrega é registrada em `sla_deadline_at`.
6. As telas da unidade e dos gestores exibem alertas coloridos de urgência e reencaminhamento.
7. O simulador não pertence aos usuários operacionais; ele exige o grupo `administrador`.
8. As unidades elegíveis são calculadas pela distância geográfica entre o ponto de coleta e as unidades fictícias; no piloto, a coleta pode ser ofertada às três mais próximas.
9. A coleta é um único pedido, mesmo quando possui ofertas simultâneas para várias unidades.
10. O primeiro aceite válido vence a disputa; as demais ofertas da mesma coleta são encerradas e deixam de aparecer nos alertas.

## Estados

```text
coleta recebida → ofertas às unidades → aguardando aceite → atribuído → coleta confirmada → em entrega → entregue
                                      ↘ primeiro aceite vence
                                      ↘ após 15 min sem aceite: parceiro
```

## Critérios de aceite

- [ ] Administrador consegue criar um pedido sintético.
- [x] Sistema seleciona unidades elegíveis por proximidade.
- [x] Sistema contabiliza uma coleta, e não uma oferta por unidade.
- [x] Primeiro aceite encerra as demais ofertas da coleta.
- [ ] Unidade recebe aviso com contador de 15 minutos.
- [ ] Pedido expirado aparece como reencaminhado ao parceiro.
- [ ] Coleta confirmada cria prazo de 120 minutos.
- [ ] Gestores visualizam alertas e estados no próprio escopo.
