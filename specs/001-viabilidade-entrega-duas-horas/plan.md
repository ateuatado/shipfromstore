# Plano de implementação: Viabilidade de entrega em até duas horas

**Branch:** `001-viabilidade-entrega-duas-horas`  
**Data:** 2026-10-28  
**Especificação:** [spec.md](spec.md)

## Resumo

Formalizar e evoluir o protótipo de mapa e estimativa de rota já existente para que ele seja a base do motor de viabilidade do ShipFromStore. A primeira versão usa dez unidades sintéticas, Leaflet no navegador, um serviço de rotas configurável e fallback por distância.

## Contexto técnico

- **Linguagem:** PHP 8.2+
- **Framework:** CodeIgniter 4
- **Autenticação:** CodeIgniter Shield
- **Banco:** MariaDB/MySQL no ambiente atual
- **Frontend:** Bootstrap 5.3.8, Leaflet 1.9.4 e JavaScript separado
- **Roteamento:** OSRM compatível, configurado por `maps.osrmBaseUrl`
- **Fallback:** distância haversine com fator rodoviário e velocidade média explícitos
- **Teste:** PHPUnit/CodeIgniter e validação HTTP local
- **Plataforma:** Apache HTTPS em `shipfromstore.test`

## Verificação da constituição

- [x] O prazo de 120 minutos está explicitamente representado.
- [x] O território e as unidades são sintéticos.
- [x] O provedor de rotas é isolado em serviço configurável.
- [x] A interface usa assets separados.
- [ ] Tempos operacionais e margem de segurança ainda precisam de decisão.

## Componentes

- `app/Config/Map.php`: território e unidades sintéticas.
- `app/Services/Maps/RouteEstimator.php`: cálculo externo e fallback.
- `app/Controllers/Map.php`: apresentação e endpoint de cálculo.
- `app/Views/pages/map.php`: tela do mapa.
- `public/assets/js/map.js`: interação Leaflet e chamada do endpoint.
- `public/assets/leaflet/`: arquivos estáticos do Leaflet.
- `app/Config/Routes.php`: rotas `/map` e `/api/maps/route`.

## Contrato inicial

`POST /api/maps/route`

Entrada JSON:

```json
{
  "coordinates": [[-46.63, -23.60], [-46.67, -23.58], [-46.72, -23.55]]
}
```

Saída de sucesso:

```json
{
  "provider": "osrm",
  "distanceMeters": 12345,
  "durationSeconds": 1800,
  "feasible": true,
  "slaSeconds": 7200,
  "withinSla": true
}
```

## Riscos e decisões pendentes

- O endpoint público de roteamento serve apenas ao protótipo e pode ter limites de uso.
- A estimativa de fallback não substitui uma matriz de tempos confiável.
- O SLA ainda precisa definir tempos operacionais e margem de segurança.
- A seleção de unidades disponíveis será especificada em uma feature posterior.
