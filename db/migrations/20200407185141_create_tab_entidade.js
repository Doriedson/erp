
exports.up = function(knex) {
    return knex.schema.createTable('tab_entidade', function(table) {
        table.bigIncrements('id_entidade').unsigned().notNullable().primary();
        table.string('cpfcnpj', 14).defaultTo(null);
        table.string('nome', 50).notNullable();
        table.string('email', 40).notNullable().defaultTo('');
        table.string('telcelular', 20).notNullable().defaultTo('');
        table.string('telresidencial', 20).notNullable().defaultTo('');
        table.string('telcomercial', 20).notNullable().defaultTo('');
        table.string('obs', 255).notNullable().defaultTo('');
        table.decimal('limite', 8, 2).notNullable().defaultTo(0);
        table.decimal('credito', 8, 2).notNullable().defaultTo(0);
        table.datetime('datacad').notNullable().defaultTo(knex.fn.now());
        table.boolean('ativo').notNullable().defaultTo(true);

        table.unique('cpfcnpj');
    }).then(function() {
        return knex('tab_entidade').insert([
            {
                id_entidade: 1,
                nome: 'Admin',
            }, 
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_entidade');
};