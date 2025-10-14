// Update with your config settings.

/**
 * @type { Object.<string, import("knex").Knex.Config> }
 */
module.exports = {

  development: {
    client: 'mysql2',
    connection: "mysql://hortifruti:hortifruti@localhost:3306/hortifruti_test",
    migrations: {
	directory: __dirname + "/migrations",
    }
  },

  production: {
    client: 'mysql2',
    connection: {
      database: 'hortifruti_test',
      user:     'hortifruti',
      password: 'hortifruti'
    },
    pool: {
      min: 2,
      max: 10
    },
    migrations: {
	    directory: __dirname + "/migrations",
    }
  }

};
