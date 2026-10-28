# Validação rápida

1. Execute `php spark migrate`.
2. Entre como um entregador com atendimento em estado `collected` ou `in_transit`.
3. Permita a localização no navegador.
4. Confirme que a tela mostra `Rastreamento ativo`.
5. Consulte a tabela `delivery_tracking_points` e verifique `assignment_id`, sequência, coordenadas e `recorded_at`.
6. Confirme a entrega e verifique que novos pontos não são aceitos para a atribuição concluída.
