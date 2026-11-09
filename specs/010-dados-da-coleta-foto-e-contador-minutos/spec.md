# Especificação: dados da coleta, fotografia do objeto e contador em minutos

**Feature Branch:** `010-dados-da-coleta-foto-e-contador-minutos`

## Objetivo

Dar ao entregador as informações mínimas necessárias para executar a coleta e a entrega, substituir a assinatura por uma evidência fotográfica do objeto e tornar o prazo de duas horas imediatamente compreensível no celular.

## Dados operacionais

O atendimento deve apresentar:

- nome da empresa remetente;
- endereço da coleta;
- nome do contato que despacha o objeto;
- nome do destinatário;
- endereço de entrega;
- contrato e status operacional.

Não devem ser exibidos dados pessoais não necessários, como CPF, CNPJ, RG, telefone ou e-mail.

## Evidência fotográfica

1. A tela de coleta não deve solicitar assinatura.
2. Deve apresentar um ícone de câmera destacado.
3. O entregador pode tirar uma foto usando a câmera do aparelho ou selecionar uma fotografia.
4. A foto é obrigatória antes da confirmação da coleta.
5. O servidor registra a imagem, hash, entregador, unidade, atendimento e horário.
6. A imagem fica protegida em armazenamento não público.

## Contador da entrega

1. A contagem inicia no instante confirmado pelo servidor em `collected_at`.
2. O prazo inicial é de 120 minutos.
3. A exibição principal usa minutos restantes, por exemplo `120 min`, `119 min` e `0 min`.
4. O contador deve aparecer na faixa de destaque e no detalhe da coleta em andamento.
5. O contador não substitui o registro preciso de data/hora usado para auditoria.

## Simulador

O administrador deve informar:

- nome da empresa;
- nome do contato na coleta;
- nome do destinatário;
- endereço e coordenadas de coleta;
- endereço e coordenadas de entrega.

O simulador pode continuar sorteando endereços dentro do território do piloto.

## Critérios de aceite

- [ ] O entregador visualiza empresa, contato, destinatário e os dois endereços.
- [ ] A assinatura não aparece mais na confirmação da coleta.
- [ ] O botão de foto é visualmente destacado e funciona em celular.
- [ ] A coleta não é confirmada sem foto válida.
- [ ] A foto fica vinculada à atribuição e possui hash.
- [ ] O contador exibe minutos restantes a partir de 120.
- [ ] O simulador possui os três campos de nomes.
