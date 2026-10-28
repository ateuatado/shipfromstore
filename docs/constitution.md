# Constituição do ShipFromStore

**Versão:** 1.0.0  
**Status:** Proposta inicial  
**Data:** 2026-10-28

## Princípios fundamentais

### I. Prazo de duas horas é a regra de negócio central

Toda decisão de viabilidade deve considerar a entrega concluída em até 120 minutos após a coleta física. O tempo deve incluir deslocamento da unidade até a coleta, coleta, deslocamento até a entrega e entrega.

### II. Privacidade por desenho

O recorte inicial usa somente dados sintéticos da região entre Moema e Jaguaré e unidades fictícias `CenEntEnc_01` a `CenEntEnc_10`. Não devem ser publicados nomes, códigos, endereços ou estrutura operacional real.

### III. Separação clara de responsabilidades

Regras de negócio, integração de mapas, persistência, autenticação e apresentação devem permanecer separadas. Credenciais e configurações sensíveis nunca entram no código ou no repositório.

### IV. Testabilidade e rastreabilidade

Cada funcionalidade deve possuir especificação, critérios de aceite, tarefas e testes verificáveis. Decisões relevantes devem ser registradas nos artefatos da funcionalidade.

### V. Evolução incremental

O sistema deve começar com um MVP demonstrável e ampliar suas integrações somente após validar as regras de negócio, o cálculo de prazo e os fluxos de aceite.

## Restrições técnicas

- PHP 8.2+, CodeIgniter 4, CodeIgniter Shield e MariaDB/MySQL no ambiente atual.
- Bootstrap e assets locais em `public/assets`.
- Aplicação acessível por HTTPS em `https://shipfromstore.test`.
- Leaflet para visualização cartográfica.
- O provedor de roteamento deve ser configurável; o endpoint público usado no protótipo não é compromisso de produção.

## Fluxo de desenvolvimento

Toda funcionalidade relevante deve seguir: especificação → esclarecimento → plano → tarefas → implementação → validação. Implementações existentes devem ser reconciliadas com a especificação, não simplesmente ignoradas.

## Governança

Esta constituição orienta as especificações e prevalece sobre decisões locais conflitantes. Alterações devem registrar motivo, impacto e versão. Uma especificação só pode ser considerada pronta quando seus critérios de aceite forem claros e testáveis.
