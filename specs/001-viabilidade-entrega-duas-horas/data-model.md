# Modelo de dados conceitual

## Unidade sintética

- `id`: identificador interno.
- `name`: `CenEntEnc_01` a `CenEntEnc_10`.
- `latitude`, `longitude`: posição sintética no território.
- `active`: disponibilidade lógica futura.

## Ponto geográfico

- `latitude`
- `longitude`
- `label`: coleta ou entrega, sem endereço sensível no MVP.

## Avaliação de rota

- `origin`: ponto inicial da unidade.
- `waypoints`: coleta e entrega.
- `distanceMeters`.
- `durationSeconds`.
- `provider`.
- `feasible`.
- `slaSeconds`: 7200 no MVP.
- `withinSla`.
- `pickupServiceSeconds`: 600 no MVP.
- `deliveryServiceSeconds`: 300 no MVP.
- `safetyMarginSeconds`: 900 no MVP.
- `totalPlannedSeconds`: deslocamento mais tempos operacionais e margem.
- `createdAt` para futura auditoria.

## Evolução prevista

Uma futura ordem deve registrar `accepted_at`, `collected_at`, `delivered_at`, origem, destino, unidade selecionada, estimativa no momento da decisão e motivo de impossibilidade. O relógio de SLA começa em `collected_at`.
