# Especificação: Viabilidade de entrega em até duas horas

**Feature Branch:** `001-viabilidade-entrega-duas-horas`  
**Criada em:** 2026-10-28  
**Status:** Draft — implementação inicial existente para reconciliação

## Contexto

O ShipFromStore recebe uma solicitação contendo um ponto de coleta e um ponto de entrega. O sistema deve avaliar se uma unidade disponível consegue realizar o percurso completo dentro de um raio sintético de 15 km a partir da Rua Mergenthaler, 595, concluindo a entrega em até duas horas após a coleta.

O MVP não usa nomes, códigos ou estrutura real de organizações. As unidades são fictícias: `CenEntEnc_01` até `CenEntEnc_10`, distribuídas em pontos determinísticos dentro do raio para permitir repetibilidade nos testes.

## Regras de negócio

- O SLA de entrega é de 120 minutos a partir de `collected_at`.
- A viabilidade deve considerar o percurso `unidade → coleta → entrega`.
- A unidade precisa alcançar o ponto de coleta antes de iniciar a coleta.
- O cálculo deve reservar 10 minutos para a coleta, 5 minutos para a entrega e 15 minutos de margem de segurança.
- A fórmula do MVP é: `tempo de deslocamento + 10 minutos de coleta + 5 minutos de entrega + 15 minutos de margem <= 120 minutos`.
- O tempo de deslocamento deve contemplar o percurso completo `unidade → coleta → entrega`.
- O resultado deve indicar viável ou não viável e explicar a estimativa usada.
- Se o serviço de rotas não responder, o sistema pode usar uma estimativa de contingência identificada explicitamente como fallback.
- Coordenadas inválidas, sequência incompleta ou pontos fora da área permitida devem gerar erro de validação, sem tentativa silenciosa de cálculo.

## Histórias de usuário

### US1 — Visualizar a área e as unidades (P1)

Como operador, quero visualizar o ponto de coleta, o ponto de entrega e as dez unidades fictícias no mapa para entender a distribuição espacial da operação.

**Teste independente:** acessar `/map` e visualizar os marcadores, a legenda e a identificação das unidades sem depender de uma ordem externa.

**Critérios de aceite:**

1. Dado que a página de mapa foi acessada, quando o mapa carregar, então os pontos de coleta, entrega e as dez unidades devem aparecer.
2. Nenhum nome ou dado de estrutura real deve aparecer na interface.
3. A página deve funcionar sobre HTTPS e carregar os assets locais necessários.

### US2 — Calcular a viabilidade de uma unidade (P1)

Como operador, quero selecionar uma unidade e calcular a rota até a coleta e depois até a entrega para saber se o pedido cabe no SLA.

**Teste independente:** enviar uma sequência válida de três pontos ao endpoint de rota e receber distância, duração, provedor e decisão de SLA.

**Critérios de aceite:**

1. Dado um percurso válido, quando o endpoint for chamado, então deve retornar distância, duração e `withinSla`.
2. A decisão deve usar 7200 segundos como limite máximo do protótipo.
3. O retorno deve identificar se foi usado roteamento externo ou fallback.
4. Uma solicitação inválida deve retornar erro HTTP apropriado e mensagem compreensível.

### US3 — Comunicar impossibilidade de atendimento (P1)

Como operador, quero receber uma indicação clara quando uma unidade não conseguir cumprir o prazo para não prometer uma entrega inviável.

**Teste independente:** usar uma rota cuja duração estimada exceda 7200 segundos e verificar o estado negativo e a justificativa apresentada.

**Critérios de aceite:**

1. A interface deve distinguir visualmente viável e não viável.
2. A duração estimada deve ser apresentada em unidade compreensível.
3. O motivo deve indicar que o limite de duas horas foi excedido.

## Casos de borda

- O provedor de rotas está indisponível ou retorna erro.
- O provedor retorna rota sem duração ou distância.
- A unidade, coleta ou entrega tem latitude/longitude fora dos limites válidos.
- A rota está exatamente no limite de 7200 segundos.
- A rota excede o limite por poucos segundos.
- O navegador não consegue carregar os tiles do mapa.
- A requisição é repetida para a mesma unidade.

## Fora do escopo desta feature

- Recepção real de ordens por API externa.
- Envio real de mensagens para unidades.
- Cadastro de disponibilidade e aceite por unidade.
- Geocodificação de endereços.
- Persistência definitiva da ordem e auditoria operacional.
- Uso de dados ou nomes de unidades reais.

## Evolução prevista

O modelo deverá permitir que várias encomendas compartilhem o mesmo ponto de coleta e sejam agrupadas em uma rota futura. O agrupamento não elimina a avaliação individual do prazo nem os eventos de custódia de cada contrato.

## Questões para a próxima revisão

- A disponibilidade das unidades será informada manualmente, por agenda ou por evento?
- O sistema deve escolher somente a melhor unidade ou retornar uma lista ordenada?
