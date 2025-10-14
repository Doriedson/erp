
exports.up = function(knex) {
    return knex.schema.createTable('tab_vendaitemkit', function(table) {
        table.bigIncrements('id_vendaitemkit').unsigned().notNullable().primary();
        table.bigInteger('id_vendaitem').unsigned().index().notNullable();
        table.bigInteger('id_venda').unsigned().index().notNullable();
        table.bigInteger('id_produto').unsigned().notNullable();
        table.decimal('qtd', 9, 3).notNullable();
        table.decimal('preco', 8, 2).notNullable();

        table.foreign('id_venda').references('id_venda').inTable('tab_venda');//.onDelete('CASCADE');
        table.foreign('id_vendaitem').references('id_vendaitem').inTable('tab_vendaitem');//.onDelete('CASCADE');
        table.foreign('id_produto').references('id_produto').inTable('tab_produto');//.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_vendaitemkit');
};