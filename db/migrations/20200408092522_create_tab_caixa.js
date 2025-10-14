
exports.up = function(knex) {
    return knex.schema.createTable('tab_caixa', function(table) {
        table.bigIncrements('id_caixa');
        table.bigInteger('id_entidade').unsigned().notNullable();
        table.bigInteger('id_pdv').unsigned().notNullable();
        table.datetime('dataini').defaultTo(knex.fn.now());
        table.decimal('trocoini', 8, 2).notNullable().defaultTo(0);
        table.datetime('datafim').nullable().defaultTo(null);
        table.decimal('trocofim', 8, 2).notNullable().defaultTo(0);
        table.string('obs', 255).notNullable().defaultTo('');

        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade'); //.onDelete('CASCADE');
        table.foreign('id_pdv').references('id_pdv').inTable('tab_pdv'); //.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_caixa');
};