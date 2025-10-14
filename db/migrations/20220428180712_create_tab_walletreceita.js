
exports.up = function(knex) {
    return knex.schema.createTable('tab_walletreceita', function(table) {
        table.bigIncrements('id_walletreceita').unsigned().notNullable().primary();
        table.bigInteger('id_wallet').unsigned().notNullable();
        table.bigInteger('id_entidade').unsigned().notNullable();
        table.datetime('data').notNullable();
        table.decimal('valor', 8, 2).notNullable();
        table.string('walletreceita', 255).notNullable();

        table.foreign('id_wallet').references('id_wallet').inTable('tab_wallet');//.onDelete('CASCADE');
        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_walletreceita');
};