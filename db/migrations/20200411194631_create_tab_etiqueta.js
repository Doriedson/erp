
exports.up = function(knex) {
    return knex.schema.createTable('tab_etiqueta', function(table) {
        table.bigIncrements('id_etiqueta').unsigned().notNullable();
        table.bigInteger('id_produto').unsigned().notNullable();

        table.foreign('id_produto').references('id_produto').inTable('tab_produto'); //.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_etiqueta');
};