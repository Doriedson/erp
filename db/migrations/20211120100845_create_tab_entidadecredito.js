
exports.up = function(knex) {
    return knex.schema.createTable('tab_entidadecredito', function(table) {

        table.bigIncrements('id_entidadecredito').unsigned().notNullable().primary();
        table.bigInteger('id_entidade').unsigned().notNullable();
        table.bigInteger('id_colaborador').unsigned().notNullable();
        table.datetime('data').notNullable().defaultTo(knex.fn.now());
        table.decimal('valor', 8, 2).notNullable();
        table.string('obs', 255).notNullable();

        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade').onDelete('RESTRICT');
        table.foreign('id_colaborador').references('id_entidade').inTable('tab_entidade').onDelete('RESTRICT');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_entidadecredito');
};