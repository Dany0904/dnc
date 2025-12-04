<?php
require_once('../../config.php');

require_login();
$context = context_system::instance();
require_capability('mod/dnc:globalreport', $context);

global $DB, $CFG;

$userid = optional_param('userid', 0, PARAM_INT);

require_once($CFG->libdir.'/excellib.class.php');

$filename = "dnc_export_" . date('Ymd_His') . ".xlsx";

$workbook = new MoodleExcelWorkbook("-");
$workbook->send($filename);
$sheet = $workbook->add_worksheet('Reporte DNC');

$headers = [
    'Nombre',
    'Puesto',
    'Área',
    'Curso (Técnica)',
    'Mes1 Técnica',
    'Mes2 Técnica',
    'Curso (Otras)',
    'Mes1 Otras',
    'Mes2 Otras'
];

$col = 0;
foreach ($headers as $h) {
    $sheet->write(0, $col++, $h);
}

// === QUERY BASE ===
$params = [];
$where = [];

$sql = "
SELECT
    dd.id AS dncdataid,
    u.id AS userid,
    CONCAT(u.firstname,' ',u.lastname) AS username,
    dd.puesto,
    dd.gerencia
FROM {dnc_data} dd
JOIN {user} u ON u.id = dd.userid
";

if ($userid > 0) {
    $where[] = "u.id = :userid";
    $params['userid'] = $userid;
}

if ($where) {
    $sql .= " WHERE ".implode(" AND ", $where);
}

$sql .= " ORDER BY u.lastname ASC";

$records = $DB->get_records_sql($sql, $params);

$row = 1;
foreach ($records as $r) {

    // === CAPACITACIÓN TÉCNICA ===
    $tec = $DB->get_records('dnc_data_cap_tecnica', ['dncdataid' => $r->dncdataid], 'id ASC');
    $tec_curso = [];
    $tec_mes1 = [];
    $tec_mes2 = [];

    foreach ($tec as $t) {
        $tec_curso[] = $t->descripcion;
        $tec_mes1[] = $t->mes1;
        $tec_mes2[] = $t->mes2;
    }

    // === OTRAS CAPACITACIONES ===
    $otras = $DB->get_records('dnc_data_cap_otras', ['dncdataid' => $r->dncdataid], 'id ASC');
    $otras_curso = [];
    $otras_mes1 = [];
    $otras_mes2 = [];

    foreach ($otras as $o) {
        $otras_curso[] = $o->curso;
        $otras_mes1[] = $o->mes1;
        $otras_mes2[] = $o->mes2;
    }

    $sheet->write($row, 0, $r->username);
    $sheet->write($row, 1, $r->puesto);
    $sheet->write($row, 2, $r->gerencia);
    $sheet->write($row, 3, implode(" | ", $tec_curso));
    $sheet->write($row, 4, implode(" | ", $tec_mes1));
    $sheet->write($row, 5, implode(" | ", $tec_mes2));
    $sheet->write($row, 6, implode(" | ", $otras_curso));
    $sheet->write($row, 7, implode(" | ", $otras_mes1));
    $sheet->write($row, 8, implode(" | ", $otras_mes2));

    $row++;
}

$workbook->close();
exit;
