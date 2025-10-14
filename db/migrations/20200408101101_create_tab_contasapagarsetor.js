
exports.up = function(knex) {
    return knex.schema.createTable('tab_contasapagarsetor', function(table) {

        table.bigIncrements('id_contasapagarsetor');

        table.string('contasapagarsetor', 50).notNullable();
    }).then(function() {
        return knex('tab_contasapagarsetor').insert([
            {contasapagarsetor: 'Contas de Consumo'}, 
            {contasapagarsetor: 'Embalagem'}, 
            {contasapagarsetor: 'Contabilidade'}, 
            {contasapagarsetor: 'Frete'}, 
            {contasapagarsetor: 'Funcionário'}, 
            {contasapagarsetor: 'Manutenção'}, 
        ])
    });  
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_contasapagarsetor');
};