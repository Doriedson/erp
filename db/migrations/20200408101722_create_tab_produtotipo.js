
exports.up = function(knex) {
    return knex.schema.createTable('tab_produtotipo', function(table) {
        table.bigIncrements('id_produtotipo').unsigned().notNullable();
        table.string('produtotipo', 50).notNullable();
    }).then(function() {
        return knex('tab_produtotipo').insert([
            {id_produtotipo: 1, produtotipo: 'Produto'},
            {id_produtotipo: 2, produtotipo: 'Composição'},
            {id_produtotipo: 3, produtotipo: 'Kit'},
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_produtotipo');
};