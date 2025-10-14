
exports.up = function(knex) {
    return knex.schema.createTable('tab_produtocomplementogrupo', function(table) {

		table.bigInteger('id_produto').unsigned().notNullable();
		table.bigInteger('id_complementogrupo').unsigned().notNullable();

		table.primary(['id_produto', 'id_complementogrupo']);

		table.foreign('id_complementogrupo').references('id_complementogrupo').inTable('tab_complementogrupo').onDelete('RESTRICT');
        table.foreign('id_produto').references('id_produto').inTable('tab_produto').onDelete('RESTRICT');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_produtocomplementogrupo');
};