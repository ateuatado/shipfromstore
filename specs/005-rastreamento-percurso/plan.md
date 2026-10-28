# Plano de implementação

1. Criar tabela `delivery_tracking_points` vinculada à atribuição, com coordenadas, precisão, sequência e horário do servidor.
2. Expor endpoint autenticado para registrar pontos somente em atribuições ativas.
3. Ativar `watchPosition` na tela do entregador após a coleta confirmada.
4. Aplicar limitação de frequência e distância no navegador.
5. Interromper o rastreamento ao confirmar a entrega.
6. Documentar o fluxo e validar migração, sintaxe e testes automatizados.
