
exports.up = function(knex) {
    return knex.schema.createTable('tab_fretecep', function(table) {

		table.bigIncrements('id_fretecep').notNullable().index();
		table.string('descricao', 255).notNullable();
		table.bigInteger('id_fretevalor').unsigned().notNullable();
        table.integer('cep_de').notNullable();
        table.integer('cep_ate').notNullable();
		table.boolean('ativo').notNullable().defaultTo(false);

		table.foreign('id_fretevalor').references('id_fretevalor').inTable('tab_fretevalor'); //.onDelete('CASCADE');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_fretecep');
};