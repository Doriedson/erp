
exports.up = function(knex) {
    return knex.schema.createTable('tab_backup', function(table) {

		table.string('db_filename', 100).notNullable();
		table.string('ftp_server', 100).notNullable();
		table.string('ftp_username', 100).notNullable();
		table.string('ftp_pass', 100).notNullable();
    });
};

exports.down = function(knex) {
    return knex.schema.dropTable('tab_backup');
};