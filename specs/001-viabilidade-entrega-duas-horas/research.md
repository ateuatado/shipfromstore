# Pesquisa: Viabilidade de entrega em até duas horas

## Decisões tomadas

### Leaflet para visualização

Leaflet atende ao mapa interativo no navegador, possui distribuição estática simples e mantém a camada visual separada da regra de cálculo.

### OSRM compatível para o protótipo

O OSRM fornece estimativa de rota de condução entre coordenadas. A URL é configurável para permitir substituição por uma instância própria ou outro provedor aprovado.

### Fallback determinístico

Quando o roteador não estiver disponível, o serviço calcula distância em linha reta, aplica fator rodoviário e velocidade média configurados. O retorno informa que a estimativa é fallback para evitar falsa precisão.

## Não decidido

- Provedor de produção.
- Fonte oficial de geocodificação.
- Tempos de serviço de coleta e entrega.
- Margem de segurança operacional.
- Estratégia de atualização de trânsito.
