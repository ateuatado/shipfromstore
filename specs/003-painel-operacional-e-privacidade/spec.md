# Especificação: Painel operacional e privacidade

**Feature Branch:** `003-painel-operacional-e-privacidade`  
**Criada em:** 2026-10-28  
**Status:** Draft

## Objetivo

Oferecer uma operação simples para acompanhar pedidos, coletas e entregas, respeitando o escopo hierárquico do usuário e minimizando a exposição de dados pessoais.

## Vocabulário obrigatório

O sistema não usa o conceito de estoque. A operação é formada por pedidos, coletas, entregas, unidades, áreas e empregados.

## Dados permitidos no painel operacional

- nome de exibição do cliente;
- número do contrato;
- endereço de coleta;
- unidade responsável;
- área responsável;
- status e timestamps operacionais.

## Dados proibidos no painel da unidade

Não exibir CPF, CNPJ, RG, telefone, e-mail, endereço do cliente, dados de pagamento ou qualquer outro dado pessoal que não seja necessário para a coleta. O endereço de entrega também não aparece no painel do gestor da unidade.

## Escopo de visualização

- Gestor de unidade: somente pedidos da unidade vinculada.
- Gestor de área: pedidos das unidades da área vinculada.
- Gerente: pedidos de todo o território sintético.

O histórico de endereços de coleta deve ser formado apenas por pedidos que a unidade aceitou e coletou. Uma unidade não deve visualizar o histórico de coleta ou entrega de outra unidade.

## Navegação

- **Pedidos:** visão principal, com pendentes de aceite no topo, aceitos em seguida, pedidos em entrega depois e entregues na parte inferior.
- **Coletas:** lista de endereços de coleta aceitos ou já coletados dentro do escopo permitido.
- **Pedidos entregues:** histórico de entregas dentro do escopo permitido.

## Critérios de aceite

1. Nenhuma tela do painel deve conter a palavra ou o conceito de estoque.
2. O gestor de unidade deve enxergar somente seus pedidos.
3. O gestor de área deve enxergar somente os pedidos das cinco unidades da sua área.
4. O gerente deve enxergar todos os pedidos do território sintético.
5. A tela de pedidos deve ordenar pendentes, aceitos, em entrega e entregues nessa sequência.
6. A tela de coletas deve exibir somente nome, contrato, endereço de coleta, unidade e status.
7. A tela de entregas deve exibir somente nome, contrato, unidade e data de conclusão.
8. Nenhuma tela deve exibir CPF, CNPJ, RG, endereço de entrega ou credenciais.
9. A interface deve priorizar leitura rápida e poucas ações por tela.

## Fora do escopo inicial

- Persistência definitiva de pedidos recebidos por API.
- Dados reais de clientes.
- Relatórios gráficos avançados.
- Tela para o entregador.
- Alteração de permissões pelo próprio usuário.
