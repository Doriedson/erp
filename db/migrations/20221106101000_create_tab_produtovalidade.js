
exports.up = function(knex) {
    return knex.schema.createTable('tab_produtovalidade', function(table) {

        table.bigIncrements('id_produtovalidade').unsigned().notNullable().primary();
        table.bigInteger('id_produto').notNullable().unsigned();
        table.datetime('data').notNullable();

        table.foreign('id_produto').references('id_produto').inTable('tab_produto');//.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_produtovalidade');
};