
exports.up = function(knex) {
    return knex.schema.createTable('tab_fretevalor', function(table) {

		table.bigIncrements('id_fretevalor').unsigned().notNullable().index();
		table.string('descricao', 255).notNullable();
        table.decimal('valor', 8, 2).notNullable();

    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_fretevalor');
};