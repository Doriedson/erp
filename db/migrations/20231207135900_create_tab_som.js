
exports.up = function(knex) {
    return knex.schema.createTable('tab_som', function(table) {
        table.bigIncrements('id_som').unsigned().notNullable().index();
        table.string('descricao', 100).defaultTo("");
        table.string('som', 100).notNullable();
        table.integer("volume").notNullable().defaultTo(75);
	}).then(function(){
		return knex('tab_som').insert([
            {
                id_som: 1,
                descricao: "Campainha",
                som: "bell.mp3"
            },
        ])
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_som');
};