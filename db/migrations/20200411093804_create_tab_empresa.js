
exports.up = function(knex) {
    return knex.schema.createTable('tab_empresa', function(table) {
        table.string('empresa', 40).notNullable();
        table.string('cnpj', 14).notNullable().defaultTo('');
        table.string('ie', 9).notNullable().defaultTo('');
        table.string('telefone', 20).notNullable().defaultTo('');
        table.string('celular', 20).notNullable().defaultTo('');
        table.string('cep', 8).notNullable().defaultTo('');
        table.string('rua', 40).notNullable().defaultTo('');
        table.string('bairro', 40).notNullable().defaultTo('');
        table.string('cidade', 40).notNullable().defaultTo('');
        table.string('uf', 2).notNullable().defaultTo('');
        table.string('cupomlinha1', 40).notNullable().defaultTo('');
        table.string('cupomlinha2', 40).notNullable().defaultTo('');
    }).then(function() {
        return knex('tab_empresa').insert([
            {empresa: 'Nome da Empresa'}, 
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_empresa');
};