
exports.up = function(knex) {
    return knex.schema.createTable('tab_caixareforco', function(table) {
        table.bigIncrements('id_caixareforco').unsigned().notNullable();
        table.bigInteger('id_caixa').unsigned().notNullable();
        table.bigInteger('id_entidade').unsigned().notNullable();
        table.bigInteger('id_especie').unsigned().notNullable();
        table.datetime('data').notNullable().defaultTo(knex.fn.now());
        table.decimal('valor', 8, 2).notNullable();
        table.boolean('conferido').notNullable().defaultTo(false);

        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade'); //.onDelete('CASCADE');
        table.foreign('id_caixa').references('id_caixa').inTable('tab_caixa'); //.onDelete('CASCADE');
        table.foreign('id_especie').references('id_especie').inTable('tab_especie'); //.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_caixareforco');
};