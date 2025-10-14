
exports.up = function(knex) {
    return knex.schema.createTable('tab_integracaodeliverydireto', function(table) {

        table.boolean('ativo').notNullable().defaultTo(false);
        table.string('store_id', 255).notNullable();
        table.string('client_id', 255).notNullable();
        table.string('client_secret', 255).notNullable();
        table.string('usuario', 255).notNullable();
        table.string('senha', 255).notNullable();
        table.string('token', 1024);

    }).then(function() {
        return knex('tab_integracaodeliverydireto').insert([
            {
                store_id: '', 
                client_id: '7f1dcd0e-85f8-48b7-b762-5e54a01822f8',
                client_secret: 'OWmZxQ982yZr7VvP0RmBvKelsTBcPPUYA2h6',
                usuario: '',
                senha: ''
            },
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_integracaodeliverydireto');
};