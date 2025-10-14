
exports.up = function(knex) {
    return knex.schema.createTable('tab_vendastatuschange', function(table) {
        table.datetime('data').notNullable().defaultTo(knex.fn.now()).index();
        table.bigInteger('id_venda').unsigned().index().notNullable();
        table.bigInteger('id_vendastatus').unsigned().notNullable();
        table.bigInteger('id_entidade').unsigned().notNullable();

        table.foreign('id_venda').references('id_venda').inTable('tab_venda');//.onDelete('CASCADE');
        table.foreign('id_vendastatus').references('id_vendastatus').inTable('tab_vendastatus');//.onDelete('CASCADE');
        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_vendastatuschange');
};