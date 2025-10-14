exports.up = function(knex) {
    return knex.schema.createTable('tab_blackfriday', function(table) {
        table.bigIncrements('id_blackfriday').unsigned().notNullable();
        table.datetime('data').notNullable();
        table.decimal('desconto', 8, 2).notNullable();
        table.boolean('acumulativo').notNullable().defaultTo(false);
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_blackfriday');
};