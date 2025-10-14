
exports.up = function(knex) {
    return knex.schema.createTable('tab_walletdespesa', function(table) {
        table.bigIncrements('id_walletdespesa').unsigned().notNullable().primary();
        table.bigInteger('id_wallet').unsigned().notNullable();
        table.datetime('data').notNullable().index();
        table.decimal('valor', 8, 2).notNullable();
        table.string('walletdespesa', 255).notNullable();
        table.bigInteger('id_walletcashtype').unsigned().notNullable();
        table.bigInteger('id_entidade').unsigned().notNullable().index();
        table.bigInteger('id_walletsector').unsigned().notNullable();
        table.datetime('datapago').nullable().defaultTo(null);
        table.decimal('valorpago', 8, 2).notNullable().defaultTo(0);
        table.string('obs', 50).notNullable().defaultTo('');

        table.foreign('id_walletcashtype').references('id_walletcashtype').inTable('tab_walletcashtype');//.onDelete('CASCADE');
        table.foreign('id_wallet').references('id_wallet').inTable('tab_wallet');//.onDelete('CASCADE');
        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
        table.foreign('id_walletsector').references('id_walletsector').inTable('tab_walletsector');//.onDelete('CASCADE');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_walletdespesa');
};