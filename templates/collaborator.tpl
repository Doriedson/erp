<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

    <div class="flex gap-10">
        <div class="box-header flex-1 gap-10">
            <i class="icon fa-solid fa-user"></i>
            <span>Colaborador / Cadastro & Acesso</span>
        </div>

        <div class="flex flex-ai-fe flex-jc-fe">
            <button type="button" class="entity_bt_new button-icon button-blue" data-window="collaborator" title="Cadastrar novo colaborador/cliente">
                <i class="fa-solid fa-person-circle-plus"></i>
            </button>
        </div>
    </div>

    <div class="flex-responsive">
        <form method="post" id="frm_collaborator" class="flex gap-10">

            <div class="fill">

                <label class="caption flex flex-ai-center gap-5">
                    Colaborador [Código / Nome / Telefone]
                </label>

                <div class="autocomplete-dropdown flex-1">
                    <input
                        type="text"
                        class="uppercase entity_search smart_search smart-search fill"
                        data-source="popup"
                        data-focus_next="#entity_search_submit"
                        maxlength="40"
                        required
                        placeholder=""
                        autocomplete="off"
                        title="Digite o nome do colaborador ou o código"
                        autofocus>
                    {block_entity_autocomplete_search}
                </div>
            </div>

            <div class="flex flex-ai-fe">
                <button id="entity_search_submit" type="submit" class="button-blue fill">Adicionar</button>
            </div>
        </form>
    </div>
</div>

<div class="setor-2 desktop">
	Colaboradores
</div>

<div class="collaborator_container flex flex-dc gap-10">

	{extra_block_collaborator}

	<!-- BEGIN EXTRA_BLOCK_COLLABORATOR -->
	<div class="w-collaborator window flex flex-dc gap-10 box-container" data-id_entidade='{id_entidade}'>

        <div class="flex gap-10">
            <div class="flex-1">
                <label class="caption">Colaborador</label>
                <div class="addon">
                    {extra_block_entity_button_status}
                    <span class="entity_{id_entidade}_nome">{nome}</span>
                </div>
            </div>

            <div class="flex flex-ai-fe flex-jc-fe gap-10">
                <button type="button" class="collaborator_bt_del button-icon button-red fa-solid fa-trash-can" data-text="{nome}" title="Remover acesso do usuário"></button>
                <button class="bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
            </div>
        </div>

        <div class="expandable" style="display: none;">
            <!-- BEGIN BLOCK_ACCESS -->
            <div class="w-collaborator-accesslist flex flex-dc gap-10">
                <div class="flex flex-dc gap-10">
                    <div class="section-header gap-10">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR" type="checkbox" class="collaborator_access" {CA_SERVIDOR}>
                        </span>
                        <div class="setor-2">ERP (Retaguarda)</div>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_PRODUTO" type="checkbox" class="collaborator_access" {CA_SERVIDOR_PRODUTO}>
                        </span>
                        <span>Cadastro de produto</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_PRODUTO_PRECO" type="checkbox" class="collaborator_access" {CA_SERVIDOR_PRODUTO_PRECO}>
                        </span>
                        <span>Alteração de preço do produto</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_PRODUTO_ESTOQUE_ADD" type="checkbox" class="collaborator_access" {CA_PRODUTO_ESTOQUE_ADD}>
                        </span>
                        <span>Adicionar produtos no estoque primário</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_PRODUTO_ESTOQUE_DEL" type="checkbox" class="collaborator_access" {CA_PRODUTO_ESTOQUE_DEL}>
                        </span>
                        <span>Remover produtos do estoque primário</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_ESTOQUE_SECUNDARIO_ADD" type="checkbox" class="collaborator_access" {CA_ESTOQUE_SECUNDARIO_ADD}>
                        </span>
                        <span>Adicionar produtos no estoque secundário</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_ESTOQUE_SECUNDARIO_DEL" type="checkbox" class="collaborator_access" {CA_ESTOQUE_SECUNDARIO_DEL}>
                        </span>
                        <span>Remover produtos do estoque secundário</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_PRODUTO_SETOR" type="checkbox" class="collaborator_access" {CA_SERVIDOR_PRODUTO_SETOR}>
                        </span>
                        <span>Cadastro de setor do produto</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_CLIENTE" type="checkbox" class="collaborator_access" {CA_SERVIDOR_CLIENTE}>
                        </span>
                        <span>Cadastro de cliente</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_CLIENTE_CREDITO" type="checkbox" class="collaborator_access" {CA_CLIENTE_CREDITO}>
                        </span>
                        <span>Cliente - incluir / remover crédito</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_CLIENTE_LIMITE" type="checkbox" class="collaborator_access" {CA_CLIENTE_LIMITE}>
                        </span>
                        <span>Venda a Prazo - Permitir ajustar limite de crédito do cliente (fiado)</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_VENDA_PRAZO_SEM_LIMITE" type="checkbox" class="collaborator_access" {CA_VENDA_PRAZO_SEM_LIMITE}>
                        </span>
                        <span>Venda a Prazo - Permitir venda sem uso de limite (fiado)</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_VENDA_PRAZO_EDITAR" type="checkbox" class="collaborator_access" {CA_VENDA_PRAZO_EDITAR}>
                        </span>
                        <span>Venda a Prazo - Permitir reabrir venda para alteração</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_COLABORADOR" type="checkbox" class="collaborator_access" {CA_SERVIDOR_COLABORADOR}>
                        </span>
                        <span>Cadastro de acesso para colaborador</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_EMISSAO_RECIBO" type="checkbox" class="collaborator_access" {CA_SERVIDOR_EMISSAO_RECIBO}>
                        </span>
                        <span>Emissão de recibo</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_FORNECEDOR" type="checkbox" class="collaborator_access" {CA_SERVIDOR_FORNECEDOR}>
                        </span>
                        <span>Cadastro de fornecedor</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_ORDEM_COMPRA" type="checkbox" class="collaborator_access" {CA_SERVIDOR_ORDEM_COMPRA}>
                        </span>
                        <span>Cadastro de ordem de compra</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_ORDEM_COMPRA_LISTA" type="checkbox" class="collaborator_access" {CA_SERVIDOR_ORDEM_COMPRA_LISTA}>
                        </span>
                        <span>Cadastro de lista de ordem de compra</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_ORDEM_VENDA" type="checkbox" class="collaborator_access" {CA_SERVIDOR_ORDEM_VENDA}>
                        </span>
                        <span>Venda / Pedido</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_ORDEM_VENDA_EDITAR" type="checkbox" class="collaborator_access" {CA_ORDEM_VENDA_EDITAR}>
                        </span>
                        <span>Pedido / Permitir reabrir pedido para alteração</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO" type="checkbox" class="collaborator_access" {CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO}>
                        </span>
                        <span>Pedido / Alterar preço do item</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO" type="checkbox" class="collaborator_access" {CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO}>
                        </span>
                        <span>Pedido / Alterar desconto do item</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_ORDEM_VENDA_FRETE" type="checkbox" class="collaborator_access" {CA_SERVIDOR_ORDEM_VENDA_FRETE}>
                        </span>
                        <span>Pedido / Alterar valor do frete</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_CONTAS_A_PAGAR" type="checkbox" class="collaborator_access" {CA_SERVIDOR_CONTAS_A_PAGAR}>
                        </span>
                        <span>Contas a pagar</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_CONTAS_A_RECEBER" type="checkbox" class="collaborator_access" {CA_SERVIDOR_CONTAS_A_RECEBER}>
                        </span>
                        <span>Contas a receber</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_RELATORIO" type="checkbox" class="collaborator_access" {CA_SERVIDOR_RELATORIO}>
                        </span>
                        <span>Acesso aos relatórios</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_SERVIDOR_CONFIG" type="checkbox" class="collaborator_access" {CA_SERVIDOR_CONFIG}>
                        </span>
                        <span>Configurações (PDV, frete, blackfriday, ...)</span>
                    </div>
                </div>

                <div class="flex flex-dc gap-10">
                    <div class="section-header gap-10">
                        <span class="flex">
                            <input data-key="CA_PDV" type="checkbox" class="collaborator_access" {CA_PDV}>
                        </span>
                        <div class="setor-2">PDV (abertura e fechamento de caixa)</div>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_PDV_REFORCO" type="checkbox" class="collaborator_access" {CA_PDV_REFORCO}>
                        </span>
                        <span>Reforço</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_PDV_SANGRIA" type="checkbox" class="collaborator_access" {CA_PDV_SANGRIA}>
                        </span>
                        <span>Sangria</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_PDV_CANCELA_ITEM" type="checkbox" class="collaborator_access" {CA_PDV_CANCELA_ITEM}>
                        </span>
                        <span>Estornar Item</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_PDV_CANCELA_VENDA" type="checkbox" class="collaborator_access" {CA_PDV_CANCELA_VENDA}>
                        </span>
                        <span>Estornar venda PDV ou pedido fechado / reaberto</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_PDV_DESCONTO" type="checkbox" class="collaborator_access" {CA_PDV_DESCONTO}>
                        </span>
                        <span>Desconto na Venda</span>
                    </div>
                </div>

                <div class="flex flex-dc gap-10">
                    <div class="section-header gap-10">
                        <span class="flex">
                            <input data-key="CA_WAITER" type="checkbox" class="collaborator_access" {CA_WAITER}>
                        </span>
                        <div class="setor-2">Garçom (acesso a atendimento de mesas)</div>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_TRANSFERENCIA_MESA" type="checkbox" class="collaborator_access" {CA_TRANSFERENCIA_MESA}>
                        </span>
                        <span>Mesa - Permitir transferência entre mesas</span>
                    </div>

                    <div class="addon">
                        <span class="flex">
                            <input data-key="CA_MESA_ITEM_ESTORNO" type="checkbox" class="collaborator_access" {CA_MESA_ITEM_ESTORNO}>
                        </span>
                        <span>Mesa - Permitir estorno de item da mesa</span>
                    </div>
                </div>
            </div>
            <!-- END BLOCK_ACCESS -->
        </div>
    </div>
    <!-- END EXTRA_BLOCK_COLLABORATOR -->
</div>
<!-- END BLOCK_PAGE -->

<!-- BEGIN BLOCK_COLLABORATOR -->
<div class="w-collaborator" data-id_entidade="{id_entidade}">
  <div class="header">
    <span class="name">{nome}</span>
    <button class="collaborator_bt_del" data-text="{nome}">Remover</button>
  </div>

  <div class="w-collaborator-accesslist">
    {acl_html}
  </div>
</div>
<!-- END BLOCK_COLLABORATOR -->

<!-- BEGIN BLOCK_COLLABORATOR_ACCESSLIST -->
<div class="w-collaborator-accesslist">
  {acl_html}
</div>
<!-- END BLOCK_COLLABORATOR_ACCESSLIST -->

<!-- BEGIN EXTRA_BLOCK_ACL_ITEM -->
<div class="acl-row" data-module="{module}">
  <span class="acl-module">{module}</span>
  <label>
    <input type="checkbox"
           class="collaborator_access"
           data-key="{module}.view"
           {checked_view}>
    Ver
  </label>
  <label>
    <input type="checkbox"
           class="collaborator_access"
           data-key="{module}.edit"
           {checked_edit}>
    Editar
  </label>
</div>
<!-- END EXTRA_BLOCK_ACL_ITEM -->
