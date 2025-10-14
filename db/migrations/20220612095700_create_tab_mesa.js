
exports.up = function(knex) {
    return knex.schema.createTable('tab_mesa', function(table) {
        table.bigIncrements('id_mesa').unsigned().notNullable().primary();
        table.bigInteger('id_venda').unsigned();
        table.bigInteger('id_entidade').unsigned();
        table.string('mesa', 50).notNullable();

        table.foreign('id_venda').references('id_venda').inTable('tab_venda');//.onDelete('CASCADE');
        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_mesa');
};