
exports.up = function(knex) {
    return knex.schema.createTable('tab_produtocomplemento', function(table) {

        table.bigIncrements('id_produtocomplemento').unsigned().notNullable();
		table.bigInteger('id_produto').unsigned().notNullable();
		table.bigInteger('id_complementogrupo').unsigned().notNullable();
        table.decimal('preco', 8, 2).notNullable().defaultTo(0);

		table.foreign('id_complementogrupo').references('id_complementogrupo').inTable('tab_complementogrupo').onDelete('RESTRICT');
        table.foreign('id_produto').references('id_produto').inTable('tab_produto').onDelete('RESTRICT');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_produtocomplemento');
};