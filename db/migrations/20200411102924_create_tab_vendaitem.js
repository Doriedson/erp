
exports.up = function(knex) {
    return knex.schema.createTable('tab_vendaitem', function(table) {
        table.bigInteger('id_vendaitem').unsigned().notNullable();
        table.bigInteger('id_venda').unsigned().index().notNullable();
        table.bigInteger('id_entidade').unsigned().notNullable();
        table.bigInteger('id_produto').unsigned().notNullable();
        table.bigInteger('id_produtotipo').unsigned().notNullable().defaultTo(1);
        table.decimal('qtd', 9, 3).notNullable();
        table.decimal('preco', 8, 2).notNullable();
        table.decimal('desconto', 8, 2).notNullable().defaultTo(0);
        table.boolean('estornado').notNullable().defaultTo(false);
        table.string('obs', 255).notNullable().defaultTo('');
        table.bigInteger('complemento').unsigned();
        table.bigInteger('composicao').unsigned();

        table.foreign('id_venda').references('id_venda').inTable('tab_venda');//.onDelete('CASCADE');
        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
        table.foreign('id_produto').references('id_produto').inTable('tab_produto');//.onDelete('CASCADE');
        table.foreign('id_produtotipo').references('id_produtotipo').inTable('tab_produtotipo'); //.onDelete('CASCADE');
        table.foreign('composicao').references('id_produto').inTable('tab_produto');//.onDelete('CASCADE');
        table.primary(['id_vendaitem', 'id_venda']);
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_vendaitem');
};