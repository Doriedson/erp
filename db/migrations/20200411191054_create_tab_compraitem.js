
exports.up = function(knex) {
    return knex.schema.createTable('tab_compraitem', function(table) {
        table.bigIncrements('id_compraitem').unsigned().notNullable();
        table.bigInteger('id_compra').unsigned().notNullable().index();
        table.bigInteger('id_produto').unsigned().notNullable();
        table.decimal('qtdvol', 9, 3).notNullable();
        table.decimal('vol', 9, 3).notNullable();
        table.decimal('custo', 8, 2).notNullable();
        table.string('obs', 100).notNullable().defaultTo('');

        table.foreign('id_compra').references('id_compra').inTable('tab_compra').onDelete('CASCADE');
        table.foreign('id_produto').references('id_produto').inTable('tab_produto');//.onDelete('CASCADE');
        // table.primary('id_compraitem', 'id_compra');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_compraitem');
};