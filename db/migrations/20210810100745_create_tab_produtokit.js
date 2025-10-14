
exports.up = function(knex) {
    return knex.schema.createTable('tab_produtokit', function(table) {

        table.bigInteger('id_kit').unsigned().notNullable();
        table.bigInteger('id_produto').unsigned().notNullable();
        table.decimal('qtd', 9, 3).notNullable();
        table.decimal('preco', 8, 2).notNullable();

        table.foreign('id_kit').references('id_produto').inTable('tab_produto').onDelete('RESTRICT');
        table.foreign('id_produto').references('id_produto').inTable('tab_produto').onDelete('RESTRICT');
        table.primary(['id_kit', 'id_produto']);
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_produtokit');
};