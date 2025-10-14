
exports.up = function(knex) {
    return knex.schema.createTable('tab_vendapay', function(table) {
        table.bigIncrements('id_vendapay').unsigned().notNullable();
        table.bigInteger('id_venda').unsigned().notNullable().index();
        table.bigInteger('id_especie').unsigned().notNullable();
        table.decimal('valor', 8, 2).notNullable();
        table.decimal('valor_recebido', 8, 2).notNullable();

        table.foreign('id_venda').references('id_venda').inTable('tab_venda');//.onDelete('CASCADE');
        table.foreign('id_especie').references('id_especie').inTable('tab_especie');//.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_vendapay');
};