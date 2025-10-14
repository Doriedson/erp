
exports.up = function(knex) {
    return knex.schema.createTable('tab_vendapaycaixa', function(table) {

        table.bigInteger('id_venda').unsigned().notNullable().primary();
        table.bigInteger('id_caixa').unsigned().notNullable();

        table.foreign('id_venda').references('id_venda').inTable('tab_venda'); //.onDelete('RESTRICT');
        table.foreign('id_caixa').references('id_caixa').inTable('tab_caixa'); //.onDelete('RESTRICT');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_vendapaycaixa');
};