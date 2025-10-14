
exports.up = function(knex) {
    return knex.schema.createTable('tab_walletshare', function(table) {
        table.bigInteger('id_wallet').unsigned().notNullable();
        table.bigInteger('id_entidade').unsigned().notNullable();

        table.primary(['id_wallet', 'id_entidade']);

        table.foreign('id_wallet').references('id_wallet').inTable('tab_wallet');//.onDelete('CASCADE');
        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_walletshare');
};