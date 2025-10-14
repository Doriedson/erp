
exports.up = function(knex) {
    return knex.schema.createTable('tab_pdv', function(table) {
        table.bigIncrements('id_pdv').unsigned().notNullable();
        table.bigInteger('id_impressora').unsigned();
        table.bigInteger('id_gaveteiro').unsigned();
        table.string('descricao', 50).notNullable().defaultTo('');
        table.string('hash', 60);
        table.boolean('trocoini').notNullable().defaultTo(false);
        table.boolean('balanca').notNullable().defaultTo(true);
        table.tinyint('balanca_charwrite').notNullable().defaultTo(80);
        table.tinyint('balanca_charend').notNullable().defaultTo(13);
        table.boolean('impressora').notNullable().defaultTo(true);
        table.boolean('gaveteiro').notNullable().defaultTo(false);

        table.foreign('id_impressora').references('id_impressora').inTable('tab_impressora'); //.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_pdv');
};