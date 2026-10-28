# Especificação: rastreamento do percurso do entregador

**Feature Branch:** `005-rastreamento-percurso`  
**Criada em:** 2026-10-28  
**Status:** Implementada

## Objetivo

Registrar o percurso do entregador entre a confirmação da coleta e a confirmação da entrega, para auditoria operacional, análise futura de tempos e evolução do planejamento de rotas.

## Regras

- O rastreamento começa somente depois da confirmação da coleta com assinatura.
- O rastreamento termina quando a entrega é confirmada.
- Cada ponto pertence a uma atribuição e não armazena dados cadastrais do cliente.
- O servidor registra o horário oficial do ponto; o dispositivo não pode escolher esse horário.
- A posição é enviada no máximo a cada 15 segundos ou após deslocamento mínimo de 50 metros.
- A precisão informada pelo dispositivo é armazenada para qualificar a análise.
- A permissão de localização é solicitada pelo navegador; sua recusa não impede a confirmação da entrega.
- O entregador só pode registrar pontos das próprias atribuições e da própria unidade.

## Critérios de aceite

1. Uma atribuição `assigned` não aceita pontos de rastreamento.
2. Uma atribuição `collected` ou `in_transit` aceita pontos válidos.
3. Latitude e longitude fora dos limites geográficos são rejeitadas.
4. Cada ponto recebe sequência, horário UTC e vínculo com a atribuição.
5. Ao concluir a entrega, o navegador interrompe a captura.
6. O painel informa quando o rastreamento está ativo, sem conexão ou sem permissão.

## Privacidade

Os pontos são dados operacionais vinculados à atribuição. A futura visualização deverá respeitar a mesma hierarquia de unidade, área e território definida para os pedidos. Retenção, exportação e anonimização serão definidas antes de qualquer uso fora do piloto.

## Fora do escopo

- Rastreamento antes da coleta.
- Rastreamento contínuo quando o navegador estiver totalmente encerrado.
- Navegação própria ou cálculo de rota em tempo real.
- Aplicativo nativo.
