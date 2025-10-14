
exports.up = function(knex) {
    return knex.schema.createTable('tab_especie', function(table) {
        table.bigIncrements('id_especie').unsigned().notNullable();
        table.string('especie', 20).notNullable();
        table.boolean('ativo').notNullable().defaultTo(false);
    }).then(function() {
        return knex('tab_especie').insert([
            {id_especie: 1, especie: 'Dinheiro', ativo: 1}, 
            {id_especie: 2, especie: 'Crédito Cliente', ativo: 1}, 
            {especie: 'Débito', ativo: 1}, 
            {especie: 'Crédito', ativo: 1}, 
            {especie: 'VA/VR', ativo: 1}, 
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_especie');
};