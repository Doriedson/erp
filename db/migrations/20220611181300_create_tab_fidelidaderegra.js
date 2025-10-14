
exports.up = function(knex) {
    return knex.schema.createTable('tab_fidelidaderegra', function(table) {
        table.bigIncrements('id_fidelidaderegra').unsigned().notNullable().primary();
        table.integer('prioridade').notNullable();
        table.integer('condicao').notNullable().defaultTo(0);
        table.decimal('valor', 8, 2).notNullable().defaultTo(0);
        table.decimal('desconto', 8, 2).notNullable().defaultTo(0);
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_fidelidaderegra');
};