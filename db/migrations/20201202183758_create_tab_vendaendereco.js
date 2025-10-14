exports.up = function(knex) {
    return knex.schema.createTable('tab_vendaendereco', function(table) {
        table.bigInteger('id_venda').unsigned().notNullable().primary();
        table.string('nickname', 50).notNullable().defaultTo('');
        table.string('logradouro', 100).notNullable().defaultTo('');
        table.integer('numero');
        table.string('complemento', 50).notNullable().defaultTo('');
        table.string('bairro', 100).notNullable().defaultTo('');
        table.string('cidade', 100).notNullable().defaultTo('');
        table.string('uf', 2).notNullable().defaultTo('SP');
        table.string('cep', 8).notNullable().defaultTo('');
        table.string('obs', 255).notNullable().defaultTo('');

        table.foreign('id_venda').references('id_venda').inTable('tab_venda');//.onDelete('CASCADE');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_vendaendereco');
};