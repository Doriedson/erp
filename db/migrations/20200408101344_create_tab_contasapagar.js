
exports.up = function(knex) {
    return knex.schema.createTable('tab_contasapagar', function(table) {

        table.bigIncrements('id_contasapagar').unsigned().notNullable();
        table.bigInteger('id_contasapagarsetor').unsigned().notNullable();
        table.bigInteger('id_entidade').unsigned().notNullable();

        table.datetime('datacad').notNullable().defaultTo(knex.fn.now());
        table.string('descricao', 100).notNullable();
        table.datetime('vencimento').notNullable();
        table.decimal('valor', 8, 2).notNullable();
        table.datetime('datapago').nullable().defaultTo(null);        
        table.decimal('valorpago', 8, 2).notNullable().defaultTo(0);
        table.string('obs').notNullable().defaultTo('');        

        table.foreign('id_contasapagarsetor').references('id_contasapagarsetor').inTable('tab_contasapagarsetor');//.onDelete('CASCADE');
        table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_contasapagar');
};