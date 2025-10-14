
exports.up = function(knex) {
    return knex.schema.createTable('tab_caixatroco', function(table) {

        table.bigInteger('id_caixa').unsigned().notNullable().primary();

        table.decimal('moeda_1', 8, 2).notNullable().defaultTo(0);
        table.decimal('moeda_5', 8, 2).notNullable().defaultTo(0);
        table.decimal('moeda_10', 8, 2).notNullable().defaultTo(0);
        table.decimal('moeda_25', 8, 2).notNullable().defaultTo(0);
        table.decimal('moeda_50', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_1', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_2', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_5', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_10', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_20', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_50', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_100', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_200', 8, 2).notNullable().defaultTo(0);

        table.foreign('id_caixa').references('id_caixa').inTable('tab_caixa').onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_caixatroco');
};