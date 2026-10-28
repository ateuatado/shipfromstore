# Quickstart

1. Acesse `https://shipfromstore.test/map`.
2. Confirme que o mapa exibe coleta, entrega e `CenEntEnc_01` a `CenEntEnc_10`.
3. Selecione uma unidade no mapa.
4. Observe distância, duração, provedor e resultado do SLA.
5. Para testar o contrato, envie JSON com três pares `[longitude, latitude]` para `POST /api/maps/route`.
6. Confirme que uma resposta válida contém `durationSeconds`, `slaSeconds` e `withinSla`.

## Validação mínima

- HTTP da página: `200`.
- Assets Leaflet carregados localmente.
- PHP sem erros de sintaxe.
- Rotas registradas no CodeIgniter.
- Rota externa ou fallback identificado no resultado.
