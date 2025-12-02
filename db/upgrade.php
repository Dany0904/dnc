<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the dnc module
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_dnc_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager(); // Manager de la base de datos.

    // --------------------------
    // Upgrade a versión 2025120100: crear nuevas tablas
    // --------------------------
    if ($oldversion < 2025120100) {

        // --------------------------
        // Tabla dnc_data_cap_funciones
        // --------------------------
        $table = new xmldb_table('dnc_data_cap_funciones');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('dncdataid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('tipo', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_index('idx_dncdataid', XMLDB_INDEX_NOTUNIQUE, ['dncdataid']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // --------------------------
        // Tabla dnc_data_cap_tecnica
        // --------------------------
        $table = new xmldb_table('dnc_data_cap_tecnica');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('dncdataid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('justificacion', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('mes1', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('mes2', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_index('idx_dncdataid', XMLDB_INDEX_NOTUNIQUE, ['dncdataid']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // --------------------------
        // Tabla dnc_data_cap_des_humano
        // --------------------------
        $table = new xmldb_table('dnc_data_cap_des_humano');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('dncdataid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('tipo', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_index('idx_dncdataid', XMLDB_INDEX_NOTUNIQUE, ['dncdataid']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // --------------------------
        // Tabla dnc_data_cap_otras
        // --------------------------
        $table = new xmldb_table('dnc_data_cap_otras');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('dncdataid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('curso', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('mes1', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('mes2', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_index('idx_dncdataid', XMLDB_INDEX_NOTUNIQUE, ['dncdataid']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // --------------------------
        // Guardar punto de actualización
        // --------------------------
        upgrade_mod_savepoint(true, 2025120100, 'dnc');
    }

    return true;
}
