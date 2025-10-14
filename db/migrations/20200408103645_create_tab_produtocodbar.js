
exports.up = function(knex) {
    return knex.schema.createTable('tab_produtocodbar', function(table) {

        table.string('codbar', 13).notNullable().primary().index();
        table.bigInteger('id_produto').notNullable().unsigned();

        table.foreign('id_produto').references('id_produto').inTable('tab_produto');//.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_produtocodbar');
};