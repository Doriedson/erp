
exports.up = function(knex) {
    return knex.schema.createTable('tab_frete', function(table) {

        table.boolean('fretegratis').notNullable().defaultTo(false);
        table.decimal('fretegratis_valor', 8, 2).notNullable().defaultTo(0);
        table.boolean('deliveryminimo').notNullable().defaultTo(false);
        table.decimal('deliveryminimo_valor', 8, 2).notNullable().defaultTo(0);

    }).then(function() {
        return knex('tab_frete').insert([
            {fretegratis_valor: 0}
        ])
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_frete');
};