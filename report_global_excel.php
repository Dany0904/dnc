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
$cellformat = $workbook->add_format();
$cellformat->set_text_wrap();
$cellformat->set_align('top');
$sheet = $workbook->add_worksheet('Reporte DNC');

$sheet->set_column(0, 0, 30); // Nombre
$sheet->set_column(1, 1, 20); // Puesto
$sheet->set_column(2, 2, 20); // Área
$sheet->set_column(3, 100, 35); // Todas las demás

$headers = [
    'Nombre',
    'Puesto',
    'Área'
];

// === TÉCNICA (5) ===
for ($i = 1; $i <= 5; $i++) {
    $headers[] = "Curso Técnica $i";
    $headers[] = "Mes 1 Técnica $i";
    $headers[] = "Mes 2 Técnica $i";
}

// === OTRAS (5) ===
for ($i = 1; $i <= 5; $i++) {
    $headers[] = "Curso Otras $i";
    $headers[] = "Mes 1 Otras $i";
    $headers[] = "Mes 2 Otras $i";
}


$col = 0;
foreach ($headers as $h) {
    $sheet->write(0, $col++, $h, $cellformat);
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

    $col = 0;

    $sheet->write($row, $col++, $r->username, $cellformat);
    $sheet->write($row, $col++, $r->puesto, $cellformat);
    $sheet->write($row, $col++, $r->gerencia, $cellformat);

    $tec = array_values($DB->get_records(
        'dnc_data_cap_tecnica',
        ['dncdataid' => $r->dncdataid],
        'id ASC'
    ));

    for ($i = 0; $i < 5; $i++) {
        if (isset($tec[$i])) {
            $sheet->write($row, $col++, $tec[$i]->descripcion, $cellformat);
            $sheet->write($row, $col++, $tec[$i]->mes1, $cellformat);
            $sheet->write($row, $col++, $tec[$i]->mes2, $cellformat);
        } else {
            // columnas vacías
            $sheet->write($row, $col++, '', $cellformat);
            $sheet->write($row, $col++, '', $cellformat);
            $sheet->write($row, $col++, '', $cellformat);
        }
    }

    $otras = array_values($DB->get_records(
        'dnc_data_cap_otras',
        ['dncdataid' => $r->dncdataid],
        'id ASC'
    ));

    for ($i = 0; $i < 5; $i++) {
        if (isset($otras[$i])) {
            $sheet->write($row, $col++, $otras[$i]->curso, $cellformat);
            $sheet->write($row, $col++, $otras[$i]->mes1, $cellformat);
            $sheet->write($row, $col++, $otras[$i]->mes2, $cellformat);
        } else {
            $sheet->write($row, $col++, '', $cellformat);
            $sheet->write($row, $col++, '', $cellformat);
            $sheet->write($row, $col++, '', $cellformat);
        }
    }

    $row++;
}

$workbook->close();
exit;
