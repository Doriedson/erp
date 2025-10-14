
exports.up = function(knex) {
    return knex.schema.createTable('tab_colaboradorfingerprint', function(table) {
      table.bigInteger('id_entidade').unsigned().notNullable().index();
      table.json('fingerprint').notNullable();

      table.foreign('id_entidade').references('id_entidade').inTable('tab_entidade');//.onDelete('CASCADE');
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_colaboradorfingerprint');
};