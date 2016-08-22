
<?php

// so we know config fuile was pulled in
$ConfigFound = TRUE;

/**
 * Returns the Database Hostname
 *
 * @return String Database host name
 */
function getServerDBHost() {
	return 'localhost';
}

/**
 * Returns the username for database access
 *
 * @return String authorised database username
 */
function getServerDBUsername() {
	return 'root';
}

/**
 * Returns the password for database access
 *
 * @return String authorised database password
 */
function getServerDBUserPassword() {
	return 'moonmash';
}

/**
 * Returns the database name to use
 *
 * @return String database name
 */
function getServerDBName() {
	return 'NV_ADMIN_CONTROL';
}


?>
