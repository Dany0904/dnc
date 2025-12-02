<?php
defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_heading(
    'dnc/settingsheader',
    'Configuración global DNC',
    ''
));

$ADMIN->add('modsettings', new admin_externalpage(
    'mod_dnc_globalreport',
    get_string('globalreport', 'mod_dnc'),
    new moodle_url('/mod/dnc/report_global.php')
));