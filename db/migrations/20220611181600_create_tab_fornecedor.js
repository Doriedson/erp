
exports.up = function(knex) {
    return knex.schema.createTable('tab_fornecedor', function(table) {
        table.bigIncrements('id_fornecedor').unsigned().notNullable().primary();
        table.datetime('datacad').notNullable().defaultTo(knex.fn.now());
        table.boolean('ativo').notNullable().defaultTo(true);
        table.string('cpfcnpj', 14);
        table.string('ie', 14);
        table.string('razaosocial', 100).notNullable();
        table.string('nomefantasia', 100).notNullable();
        table.string('endereco', 50).notNullable().defaultTo('');
        table.string('bairro', 50).notNullable().defaultTo('');
        table.string('cidade', 50).notNullable().defaultTo('');
        table.string('uf', 2).notNullable().defaultTo('SP');
        table.string('cep', 8).notNullable().defaultTo('');
        table.string('telefone1', 20).notNullable().defaultTo('');
        table.string('contato1', 50).notNullable().defaultTo('');
        table.string('telefone2', 20).notNullable().defaultTo('');
        table.string('contato2', 50).notNullable().defaultTo('');
        table.string('telefone3', 20).notNullable().defaultTo('');
        table.string('contato3', 50).notNullable().defaultTo('');
        table.string('email', 40).notNullable().defaultTo('');
        table.string('obs', 255).notNullable().defaultTo('');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_fornecedor');
};