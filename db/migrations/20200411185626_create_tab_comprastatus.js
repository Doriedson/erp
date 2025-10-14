
exports.up = function(knex) {
    return knex.schema.createTable('tab_comprastatus', function(table) {
        table.bigIncrements('id_comprastatus').unsigned().notNullable();
        table.string('comprastatus', 50).notNullable();
    }).then(function() {
        return knex('tab_comprastatus').insert([
            {id_comprastatus: 1, comprastatus: 'Aberta'}, 
            {id_comprastatus: 2, comprastatus: 'Finalizada'},
            {id_comprastatus: 3, comprastatus: 'Cancelada'},
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_comprastatus');
};