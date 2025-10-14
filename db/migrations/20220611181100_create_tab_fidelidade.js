
exports.up = function(knex) {
    return knex.schema.createTable('tab_fidelidade', function(table) {
        table.integer('dias_compra').notNullable();
    }).then(function() {
        return knex('tab_fidelidade').insert([
            {dias_compra: 30},
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_fidelidade');
};