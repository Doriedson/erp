
exports.up = function(knex) {
    return knex.schema.createTable('tab_walletsector', function(table) {

        table.bigIncrements('id_walletsector').unsigned().notNullable().primary();
        table.bigInteger('id_wallet').unsigned().notNullable();
        table.string('walletsector', 50).notNullable();

        table.foreign('id_wallet').references('id_wallet').inTable('tab_wallet'); //.onDelete('RESTRICT');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_walletsector');
};