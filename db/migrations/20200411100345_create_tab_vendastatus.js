
exports.up = function(knex) {
    return knex.schema.createTable('tab_vendastatus', function(table) {
        table.bigIncrements('id_vendastatus').unsigned().notNullable();
        table.string('vendastatus', 50).notNullable();
    }).then(function() {
        return knex('tab_vendastatus').insert([
            {id_vendastatus: 1, vendastatus: 'Venda Paga'},
            {id_vendastatus: 2, vendastatus: 'Venda Cancelada'},
            {id_vendastatus: 3, vendastatus: 'Pedido em Andamento'},
            {id_vendastatus: 4, vendastatus: 'Pedido Efetuado'},
            {id_vendastatus: 5, vendastatus: 'Pedido Pago'},
            {id_vendastatus: 6, vendastatus: 'Pedido Cancelado'},
            {id_vendastatus: 7, vendastatus: 'Venda a Prazo'},
            {id_vendastatus: 8, vendastatus: 'Mesa em Atendimento'},
            {id_vendastatus: 9, vendastatus: 'Mesa em Pagamento'},
            {id_vendastatus: 10, vendastatus: 'Mesa Paga'},
            {id_vendastatus: 11, vendastatus: 'Mesa Cancelada'},
            {id_vendastatus: 12, vendastatus: 'Venda em Andamento'},
            {id_vendastatus: 13, vendastatus: 'Venda a Prazo Paga'},
            {id_vendastatus: 14, vendastatus: 'Pedido Impresso'},
            {id_vendastatus: 15, vendastatus: 'Pedido em Produção'},
            {id_vendastatus: 16, vendastatus: 'Pedido em Entrega'},
            {id_vendastatus: 17, vendastatus: 'Mesa Transferida'},
        ])
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_vendastatus');
};