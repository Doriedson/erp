
exports.up = function(knex) {
    return knex.schema.createTable('tab_venda', function(table) {
        table.bigIncrements('id_venda').unsigned().notNullable().primary();
        table.bigInteger('id_vendastatus').unsigned().notNullable();
        table.bigInteger('id_entidade').unsigned().defaultTo(null);
        table.bigInteger('id_colaborador').unsigned().notNullable();
        table.bigInteger('id_caixa').unsigned(); // Caixa responsável para relarório de vendas a prazo e vendas canceladas
        table.datetime('data').defaultTo(knex.fn.now()).index();
        table.decimal('frete', 8, 2).notNullable().defaultTo(0);
        table.decimal('valor_servico', 8, 2).notNullable().defaultTo(0);
        table.string('obs', 255).notNullable().defaultTo('');
        table.string('mesa', 50).notNullable().defaultTo('');
        table.bigInteger('versao').notNullable().defaultTo(0);

        table.foreign('id_vendastatus').references('id_vendastatus').inTable('tab_vendastatus');//.onDelete('CASCADE');
        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
        table.foreign('id_colaborador').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
        table.foreign('id_caixa').references('id_caixa').inTable('tab_caixa');//.onDelete('CASCADE');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_venda');
};