
exports.up = function(knex) {
    return knex.schema.createTable('tab_compralista', function(table) {
        table.bigIncrements('id_compralista').unsigned().notNullable();
        table.string('descricao', 50).notNullable();
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_compralista');
};