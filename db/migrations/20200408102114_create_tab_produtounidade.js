
exports.up = function(knex) {
    return knex.schema.createTable('tab_produtounidade', function(table) {

        table.bigIncrements('id_produtounidade').unsigned().notNullable();

        table.string('produtounidade', 2).notNullable();
        table.boolean('balanca').notNullable().defaultTo(false);

    }).then(function() {
        return knex('tab_produtounidade').insert([
            {id_produtounidade: 1, produtounidade: 'UN', balanca: 0}, 
            {id_produtounidade: 2, produtounidade: 'KG', balanca: 1}, 
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_produtounidade');
};