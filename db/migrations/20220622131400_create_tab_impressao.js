
exports.up = function(knex) {
    return knex.schema.createTable('tab_impressao', function(table) {
        table.bigIncrements('id_impressao').unsigned().notNullable();
        table.bigInteger('id_impressora').unsigned();
        table.string('descricao', 255).notNullable();

        table.foreign('id_impressora').references('id_impressora').inTable('tab_impressora'); //.onDelete('CASCADE');
    }).then(function() {
        return knex('tab_impressao').insert([
            {id_impressao: 1 , descricao: 'Ordens de Compra'}, 
            {id_impressao: 2 , descricao: 'Pedidos de Venda'}, 
            {id_impressao: 3 , descricao: 'Mesas'}, 
            {id_impressao: 4 , descricao: 'Lista de produtos com vencimento próximo'}, 
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_impressao');
};