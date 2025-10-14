exports.up = function(knex) {
    return knex.schema.createTable('tab_impressora', function(table) {
        table.bigIncrements('id_impressora').unsigned().notNullable();
        table.string('descricao', 50).notNullable();
        table.string('impressora', 255).notNullable();
        table.boolean('bigfont').notNullable().defaultTo(false);
        table.integer('colunas').notNullable().defaultTo(40);
        table.tinyint('linefeed').notNullable().defaultTo(0);
        table.boolean('guilhotina').notNullable();
        table.integer('copies').notNullable().defaultTo(1);
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_impressora');
};