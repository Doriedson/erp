
exports.up = function(knex) {
    return knex.schema.createTable('tab_compra', function(table) {
        table.bigIncrements('id_compra').unsigned().notNullable();
        table.bigInteger('id_entidade').unsigned().notNullable();
        table.bigInteger('id_comprastatus').unsigned().notNullable();
        table.bigInteger('id_fornecedor').unsigned().notNullable();
        table.datetime('data').notNullable().defaultTo(knex.fn.now());
        table.string('obs', 255).notNullable().defaultTo('');

        table.foreign('id_comprastatus').references('id_comprastatus').inTable('tab_comprastatus');//.onDelete('CASCADE');
        table.foreign('id_fornecedor').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_compra');
};