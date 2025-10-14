
exports.up = function(knex) {
    return knex.schema.createTable('tab_entidadeendereco', function(table) {
        table.bigIncrements('id_endereco').unsigned().notNullable();
        table.bigInteger('id_entidade').unsigned().notNullable().index();
        table.string('nickname', 50).notNullable().defaultTo('');
        table.string('logradouro', 100).notNullable().defaultTo('');
        table.integer('numero');
        table.string('complemento', 50).notNullable().defaultTo('');
        table.string('bairro', 100).notNullable().defaultTo('');
        table.string('cidade', 100).notNullable().defaultTo('');
        table.string('uf', 2).notNullable().defaultTo('SP');
        table.string('cep', 8).notNullable().defaultTo('');
        table.string('obs', 255).notNullable().defaultTo('');

        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_entidadeendereco');
};