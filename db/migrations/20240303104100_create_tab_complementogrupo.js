
exports.up = function(knex) {
    return knex.schema.createTable('tab_complementogrupo', function(table) {

		table.bigIncrements('id_complementogrupo').unsigned().notNullable().primary();
		table.string('descricao', 40).notNullable();
        table.integer('qtd_min').notNullable().defaultTo(0);
        table.integer('qtd_max').notNullable().defaultTo(1);
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_complementogrupo');
};