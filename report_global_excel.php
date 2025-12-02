<?php
require_once('../../config.php');

require_login();
$context = context_system::instance();
require_capability('mod/dnc:globalreport', $context);

global $DB, $CFG;

// params
$courseid = optional_param('courseid', 0, PARAM_INT);
$userid   = optional_param('userid', 0, PARAM_INT);

require_once($CFG->libdir.'/excellib.class.php');

$filename = "dnc_export_" . date('Ymd_His') . ".xlsx";

$workbook = new MoodleExcelWorkbook("-");
$workbook->send($filename);

$sheet = $workbook->add_worksheet('Reporte DNC');

$headers = [
    'Curso',
    'Usuario',
    'Puesto',
    'Gerencia',
    'Dirección',
    'Antigüedad',
    'Grado de estudios',
    'Escuela',
    'Fecha'
];

$col=0;
foreach ($headers as $h) {
    $sheet->write(0, $col++, $h);
}

// === query base ===
$params = [];
$where = [];

$sql = "
SELECT
    c.fullname AS course,
    CONCAT(u.firstname,' ',u.lastname) AS username,
    dd.puesto,
    dd.gerencia,
    dd.direccion,
    dd.antiguedad,
    dd.grado_estudios,
    dd.escuela,
    dd.timemodified
FROM {dnc_data} dd
JOIN {user} u ON u.id = dd.userid
JOIN {dnc} d ON d.id = dd.dncid
JOIN {course} c ON c.id = d.course
";

if ($courseid > 0) {
    $where[] = "c.id = :courseid";
    $params['courseid'] = $courseid;
}
if ($userid > 0) {
    $where[] = "u.id = :userid";
    $params['userid'] = $userid;
}

if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " ORDER BY dd.timemodified DESC";

$records = $DB->get_records_sql($sql, $params);

$row = 1;
foreach ($records as $r) {
    $sheet->write($row, 0, $r->course);
    $sheet->write($row, 1, $r->username);
    $sheet->write($row, 2, $r->puesto);
    $sheet->write($row, 3, $r->gerencia);
    $sheet->write($row, 4, $r->direccion);
    $sheet->write($row, 5, $r->antiguedad);
    $sheet->write($row, 6, $r->grado_estudios);
    $sheet->write($row, 7, $r->escuela);
    $sheet->write($row, 8, userdate($r->timemodified));
    $row++;
}

$workbook->close();
exit;
