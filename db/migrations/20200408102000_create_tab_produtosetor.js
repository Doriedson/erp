
exports.up = function(knex) {
    return knex.schema.createTable('tab_produtosetor', function(table) {

        table.bigIncrements('id_produtosetor').primary();
        table.string('produtosetor', 50).notNullable();
        table.boolean('garcom').notNullable().defaultTo(false);
        table.boolean('cardapio_setor').notNullable().defaultTo(false);

    }).then(function() {
        return knex('tab_produtosetor').insert([
            {id_produtosetor: 1, produtosetor: 'Outros'},
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_produtosetor');
};