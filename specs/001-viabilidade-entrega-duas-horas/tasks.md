# Tarefas: Viabilidade de entrega em até duas horas

**Status:** parte do protótipo já implementada; tarefas abaixo orientam a consolidação.

## Fase 1 — Fundação

- [x] T001 Registrar a constituição do projeto em `docs/constitution.md`.
- [x] T002 Registrar a especificação funcional em `specs/001-viabilidade-entrega-duas-horas/spec.md`.
- [x] T003 Registrar plano, pesquisa, modelo e quickstart.

## Fase 2 — Protótipo existente

- [x] T004 [US1] Configurar dez unidades sintéticas em `app/Config/Map.php`.
- [x] T005 [US1] Criar tela Leaflet em `app/Views/pages/map.php`.
- [x] T006 [US1] Organizar assets em `public/assets/leaflet` e `public/assets/js/map.js`.
- [x] T007 [US2] Implementar estimador em `app/Services/Maps/RouteEstimator.php`.
- [x] T008 [US2] Expor `POST /api/maps/route`.
- [x] T009 [US3] Exibir resultado viável/não viável e provedor utilizado.

## Fase 3 — Consolidação necessária

- [ ] T010 [US2] Criar testes automatizados para coordenadas válidas, inválidas, limite e fallback.
- [ ] T011 [US2] Extrair para configuração os tempos de 600 segundos de coleta e 300 segundos de entrega.
- [ ] T012 [US2] Aplicar margem de segurança configurada de 900 segundos ao resultado do SLA.
- [ ] T013 [US3] Padronizar mensagens de erro e contrato HTTP.
- [ ] T014 [US1] Garantir atribuição e política adequada dos tiles no ambiente escolhido.

## Fase 4 — Próxima feature

- [ ] T015 Modelar entrada de ordem e timestamps operacionais.
- [ ] T016 Modelar disponibilidade e aceite das unidades.
- [ ] T017 Implementar seleção ordenada de unidades viáveis.
- [ ] T018 Implementar disparo de convite e expiração em até duas horas.
