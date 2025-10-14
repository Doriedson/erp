
exports.up = function(knex) {
    return knex.schema.createTable('tab_log', function(table) {
        table.datetime('data').notNullable().defaultTo(knex.fn.now());
        table.json('log').notNullable();
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_log');
};