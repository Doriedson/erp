
exports.up = function(knex) {
    return knex.schema.createTable('tab_config', function(table) {

        table.decimal('moeda_1', 8, 2).notNullable().defaultTo(0);
        table.decimal('moeda_5', 8, 2).notNullable().defaultTo(0);
        table.decimal('moeda_10', 8, 2).notNullable().defaultTo(0);
        table.decimal('moeda_25', 8, 2).notNullable().defaultTo(0);
        table.decimal('moeda_50', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_1', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_2', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_5', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_10', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_20', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_50', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_100', 8, 2).notNullable().defaultTo(0);
        table.decimal('cedula_200', 8, 2).notNullable().defaultTo(0);
        table.decimal('min_sangria', 8, 2).notNullable().defaultTo(0);
        table.decimal('taxa_servico', 8, 2).notNullable().defaultTo(0);
        table.boolean('fc_waitertip_print').notNullable().defaultTo(false);
        table.boolean('fc_reverseitem_print').notNullable().defaultTo(false);
        table.boolean('fc_reversesale_print').notNullable().defaultTo(false);
        table.boolean('fc_productssold_print').notNullable().defaultTo(false);
        table.boolean('fc_productssoldoption_print').notNullable().defaultTo(false);
        table.boolean('fc_forwardsale_print').notNullable().defaultTo(false);
        table.boolean('fc_forwardsalepaid_print').notNullable().defaultTo(false);
        table.boolean('fc_orderpaid_print').notNullable().defaultTo(false);
        table.boolean('fc_reprint_print').notNullable().defaultTo(false);
        table.boolean('fc_table').notNullable().defaultTo(false);
        table.integer('product_expirate_days').unsigned().notNullable().defaultTo(0);
        table.boolean('scalesbarcode').notNullable().defaultTo(false);
        table.integer('scalesbarcode_startnumber').unsigned().notNullable().defaultTo(2);
        table.integer('scalesbarcode_sizecode').unsigned().notNullable().defaultTo(13);
        table.integer('scalesbarcode_productstartposition').unsigned().notNullable().defaultTo(2);
        table.integer('scalesbarcode_productendposition').unsigned().notNullable().defaultTo(7);
        table.integer('scalesbarcode_weightstartposition').unsigned().notNullable().defaultTo(8);
        table.integer('scalesbarcode_weightendposition').unsigned().notNullable().defaultTo(12);
        table.integer('scalesbarcode_weightdecimals').unsigned().notNullable().defaultTo(3);
        table.boolean('scalesbarcode_weightorprice').notNullable().defaultTo(true);
        table.boolean('estoque_secundario').notNullable().defaultTo(false);

    }).then(function() {
        return knex('tab_config').insert([
            {
                moeda_1: 0,
                moeda_5: 0,
                moeda_10: 0,
                moeda_25: 0,
                moeda_50: 0,
                cedula_1: 0,
                cedula_2: 0,
                cedula_5: 0,
                cedula_10: 0,
                cedula_20: 0,
                cedula_50: 0,
                cedula_100: 0,
                cedula_200: 0,
                min_sangria: 300
            },
        ])
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_config');
};