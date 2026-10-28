# Especificação: Identidade e hierarquia operacional

**Feature Branch:** `002-identidade-hierarquia-operacional`  
**Criada em:** 2026-10-28  
**Status:** Draft

## Contexto

O piloto precisa representar empregados fictícios que acessam o sistema instalado no computador do gestor da unidade. A autenticação será local, usando CodeIgniter Shield, para simular a futura integração corporativa por LDAP sem expor dados reais.

O piloto utiliza somente dados sintéticos e tem finalidade demonstrativa. Senhas simples podem ser usadas nesse ambiente controlado, mas nunca devem ser reutilizadas em produção.

## Regras de identidade

- Todo usuário do piloto é um **empregado**.
- Cada empregado possui exatamente uma função no sistema.
- Um empregado não pode acumular dois perfis, nem mesmo no piloto.
- Cada empregado possui nome, matrícula, senha, situação e vínculo organizacional.
- A matrícula deve ser única.
- A senha de demonstração pode ser simples, como `123`, exclusivamente no ambiente sintético local.
- O usuário coringa não será criado como exceção de perfil.
- A autenticação atual será feita pelo Shield.
- A autenticação LDAP será uma futura implementação do mecanismo de login, preservando os mesmos perfis e permissões.

## Perfis e escopo

### Gestor de unidade

É vinculado a uma única unidade fictícia e pode visualizar e operar pedidos destinados àquela unidade.

Permissões do piloto:

- visualizar pedidos da própria unidade;
- aceitar ou recusar uma solicitação de coleta;
- registrar o despacho do entregador;
- acompanhar coleta, trânsito e entrega da própria unidade.

### Gestor de área

É vinculado a uma única área e pode visualizar pedidos de todas as unidades pertencentes à área.

Permissões do piloto:

- visualizar pedidos das unidades da própria área;
- acompanhar aceite, coleta, trânsito e entrega;
- consultar recusas, expirações e atrasos.

O gestor de área não aceita nem despacha pedidos em nome de uma unidade nesta primeira versão.

### Gerente

É vinculado ao território sintético e pode visualizar pedidos de todas as áreas e unidades.

Permissões do piloto:

- visualizar todos os pedidos do território;
- acompanhar indicadores de aceite, coleta e entrega;
- consultar atrasos, recusas e expirações.

O gerente não altera decisões operacionais das unidades nesta primeira versão.

## Exemplos sintéticos

| Nome | Matrícula | Função | Vínculo |
|---|---:|---|---|
| Gestor CenEntEnc_01 | 0001 | Gestor de unidade | CenEntEnc_01 |
| Gestor CenEntEnc_02 | 0002 | Gestor de unidade | CenEntEnc_02 |
| Gestor Área Oeste | 0101 | Gestor de área | Área Oeste |
| Gerente Território | 1001 | Gerente | Território sintético |

Os exemplos acima são dados de demonstração. A senha de teste não deve ser publicada em ambientes reais.

## Critérios de aceite

1. Um empregado deve possuir exatamente uma função.
2. O sistema deve impedir a atribuição de uma segunda função ao mesmo empregado.
3. Um gestor de unidade não pode visualizar pedidos de outra unidade.
4. Um gestor de área pode visualizar pedidos de todas as unidades da própria área.
5. Um gerente pode visualizar pedidos de todo o território sintético.
6. A matrícula deve impedir duplicidade de empregado.
7. O login local deve funcionar com o Shield.
8. A futura substituição do login local por LDAP não deve exigir alteração nas regras de escopo.

## Fora do escopo

- Integração real com LDAP.
- Uso de matrículas ou credenciais reais.
- Usuários com múltiplos perfis.
- Administração de empregados por interface pública.
- Delegação temporária de permissões.
- Login do entregador.

## Decisões pendentes

- Definir a quantidade de áreas sintéticas e quais unidades pertencem a cada uma.
- Definir se haverá um usuário administrador técnico separado dos empregados operacionais.
- Definir se o gestor de unidade poderá cancelar uma aceitação antes da coleta.
