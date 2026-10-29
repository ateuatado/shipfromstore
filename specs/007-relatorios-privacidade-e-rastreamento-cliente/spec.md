# Especificação: relatórios, privacidade e rastreamento do cliente

**Feature Branch:** `007-relatorios-privacidade-e-rastreamento-cliente`

## Objetivo

Permitir que gestores acompanhem o desempenho dos entregadores e oferecer ao cliente um link público para uma entrega especial, sem expor o ponto exato de coleta por padrão.

## Regras

1. O percurso bruto permanece restrito ao armazenamento interno.
2. Relatórios gerenciais exibem o percurso a partir de 500 metros do ponto de coleta.
3. O padrão de rastreamento público é aproximado e também respeita a máscara de 500 metros.
4. O modo exato somente pode ser habilitado de forma explícita para um atendimento.
5. Links públicos usam tokens aleatórios, possuem expiração e podem ser revogados.
6. O link público não expõe CPF, CNPJ, telefone, e-mail, endereço de coleta ou dados de outros atendimentos.
7. O gestor de unidade vê somente sua unidade; o gestor de área vê suas unidades; o gerente vê todo o território.
8. O prazo de duas horas continua contado a partir da confirmação física da coleta.

## Relatório

O percurso apresenta entregador, unidade, status, quantidade de pontos visíveis, distância calculada, tempo após coleta e aviso de privacidade. Percursos sem pontos visíveis não devem inventar uma linha.

## Rastreamento público

O gestor gera um link para um atendimento autorizado. O cliente vê status, horário da coleta, horário da entrega quando concluída, destino operacional e o percurso protegido. O token não contém o número do contrato e não permite enumeração.

## Critérios de aceite

- [ ] Gestores não recebem pontos GPS dentro do raio padrão de 500 metros.
- [ ] Escopos de unidade, área e território continuam aplicados ao relatório.
- [ ] Link expirado ou revogado retorna acesso negado.
- [ ] Link aproximado não exibe dados cadastrais nem o endereço de coleta.
- [ ] Link exato funciona somente quando explicitamente habilitado.
- [ ] A geração de link não altera o status operacional do atendimento.
