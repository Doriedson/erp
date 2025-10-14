
exports.up = function(knex) {
    return knex.schema.createTable('tab_wallet', function(table) {
        table.bigIncrements('id_wallet').unsigned().notNullable().primary();
        table.bigInteger('id_entidade').unsigned().notNullable().index();
        table.string('wallet', 50).notNullable().default('');
        table.decimal('saldo', 8, 2).notNullable().defaulTo(0);
        table.boolean('deleted').notNullable().defaultTo(false);

        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_wallet');
};