const bcrypt = require('bcrypt');
const saltRounds = 10;

exports.up = function(knex) {
    return knex.schema.createTable('tab_colaborador', function(table) {
        table.bigInteger('id_entidade').unsigned().notNullable().primary();
        table.string('hash', 60).notNullable().defaultTo('');
        table.string('sessao', 60).notNullable().defaultTo('');
        table.string('acesso', 1024).notNullable().defaultTo('');

        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');

    }).then(function() {
        const hash = bcrypt.hashSync('1234', saltRounds);

        return knex('tab_colaborador').insert([
            {
                id_entidade: 1,
                hash: hash,
                acesso: '[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]'
            },
        ])
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_colaborador');
};
