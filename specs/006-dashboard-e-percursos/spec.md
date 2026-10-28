# Especificação: dashboards operacionais e visualização de percursos

**Feature Branch:** `006-dashboard-e-percursos`  
**Criada em:** 2026-10-28  
**Status:** Implementada

## Objetivo

Oferecer aos gestores uma visão visual da operação, dos indicadores de prazo e dos percursos registrados pelos entregadores, respeitando o escopo de unidade, área ou território.

## Escopo de acesso

- Gestor de unidade: vê somente sua unidade.
- Gestor de área: vê somente as unidades de sua área.
- Gerente: vê todas as unidades do território.
- Entregador: não acessa o mapa gerencial nem dados de outros entregadores.

## Dashboard

O painel apresenta contagens por etapa, fluxo operacional, percursos ativos, percentual de entregas dentro de duas horas e tempo médio entre coleta e entrega quando houver dados concluídos.

## Mapa de percursos

O mapa mostra os pontos GPS registrados para atribuições coletadas, em entrega ou entregues. Cada percurso identifica contrato, unidade, entregador e estado operacional, sem expor dados cadastrais do cliente.

## Experiência móvel

- A entrega ativa aparece no topo da tela do entregador.
- O contador permanece destacado e usa tipografia compacta em telas pequenas.
- O detalhamento da coleta continua disponível abaixo do resumo.

## Critérios de aceite

1. Um gestor de unidade não recebe rotas de outra unidade pela tela nem pelo endpoint.
2. Um gestor de área não recebe rotas de outra área.
3. O gerente territorial recebe todas as rotas disponíveis.
4. Percursos sem pontos aparecem como atendimento sem percurso registrado, sem inventar uma linha.
5. O dashboard mantém a regra de prazo de 120 minutos após a coleta.
6. A tela do entregador mostra primeiro o atendimento em andamento.
