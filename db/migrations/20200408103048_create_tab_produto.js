
exports.up = function(knex) {
    return knex.schema.createTable('tab_produto', function(table) {

        table.bigIncrements('id_produto').unsigned().notNullable().primary();
        table.bigInteger('id_produtosetor').unsigned().notNullable(); //antigo id_setor
        table.bigInteger('id_produtounidade').unsigned().notNullable(); //antigo tipo
        table.bigInteger('id_produtotipo').unsigned().notNullable().defaultTo(1); //antigo tipo
        table.bigInteger('id_impressora').unsigned();
        table.string('produto', 50).notNullable();
        table.decimal('preco', 8, 2).notNullable();
        table.decimal('margem_lucro', 8, 2).notNullable().defaultTo(60);
        table.decimal('margem_perda', 8, 2).notNullable().defaultTo(60);
        table.string('imagem', 100).notNullable();
        table.boolean('ativo').notNullable().defaultTo(false);
        table.boolean('cardapio_produto').notNullable().defaultTo(false);
        table.decimal('estoque', 9, 3).notNullable().defaultTo(0);
        table.decimal('estoque_secundario', 9, 3).notNullable().defaultTo(0);
        table.decimal('preco_promo', 8, 2).notNullable().defaultTo(0);
        table.string('obs', 255).notNullable().defaultTo('');

        table.foreign('id_produtosetor').references('id_produtosetor').inTable('tab_produtosetor'); //.onDelete('CASCADE');
        table.foreign('id_produtounidade').references('id_produtounidade').inTable('tab_produtounidade'); //.onDelete('CASCADE');
        table.foreign('id_produtotipo').references('id_produtotipo').inTable('tab_produtotipo'); //.onDelete('CASCADE');
        table.foreign('id_impressora').references('id_impressora').inTable('tab_impressora'); //.onDelete('CASCADE');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_produto');
};
