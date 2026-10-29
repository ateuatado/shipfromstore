# Especificação: controle de tempo e simulador operacional

**Feature Branch:** `008-tempo-aceite-e-simulador-operacional`

## Objetivo

Controlar o ciclo desde o recebimento do pedido até a entrega, permitindo testar o fluxo por um painel administrativo sintético.

## Regras

1. O relógio de aceite começa em `received_at`, quando o pedido é encaminhado à unidade mais próxima.
2. A unidade tem 15 minutos para aceitar e atribuir o pedido.
3. Após 15 minutos sem aceite, o pedido muda para `partner_forwarded` e recebe o parceiro configurado.
4. A contagem de 120 minutos começa somente em `collected_at`.
5. A data limite de entrega é registrada em `sla_deadline_at`.
6. As telas da unidade e dos gestores exibem alertas coloridos de urgência e reencaminhamento.
7. O simulador não pertence aos usuários operacionais; ele exige o grupo `administrador`.
8. A unidade mais próxima é calculada pela distância geográfica entre o ponto de coleta e as unidades fictícias.

## Estados

```text
pedido recebido → aguardando aceite → atribuído → coleta confirmada → em entrega → entregue
                                  ↘ após 15 min: parceiro
```

## Critérios de aceite

- [ ] Administrador consegue criar um pedido sintético.
- [ ] Sistema seleciona a unidade mais próxima.
- [ ] Unidade recebe aviso com contador de 15 minutos.
- [ ] Pedido expirado aparece como reencaminhado ao parceiro.
- [ ] Coleta confirmada cria prazo de 120 minutos.
- [ ] Gestores visualizam alertas e estados no próprio escopo.
