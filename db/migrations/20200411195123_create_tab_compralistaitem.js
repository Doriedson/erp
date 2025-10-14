
exports.up = function(knex) {
    return knex.schema.createTable('tab_compralistaitem', function(table) {
        table.bigIncrements('id_compralistaitem').notNullable();
        table.bigInteger('id_compralista').unsigned().notNullable();
        table.bigInteger('id_produto').unsigned().notNullable();

        table.foreign('id_compralista').references('id_compralista').inTable('tab_compralista').onDelete('CASCADE');
        table.foreign('id_produto').references('id_produto').inTable('tab_produto');//.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_compralistaitem');
};
