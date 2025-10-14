
exports.up = function(knex) {
    return knex.schema.createTable('tab_recibo', function(table) {
        table.bigIncrements('id_recibo').unsigned().notNullable();
        table.bigInteger('id_entidade').unsigned().notNullable();
        table.datetime('data').notNullable();
        table.decimal('valor', 8, 2).notNullable();
        table.string('motivo', 255).notNullable();

        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_recibo');
};