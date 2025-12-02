<?php
require_once('../../config.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('dnc', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id'=>$cm->course], '*', MUST_EXIST);
$dnc = $DB->get_record('dnc', ['id'=>$cm->instance], '*', MUST_EXIST);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/dnc:viewreport', $context);

$filename = "dnc_report_{$dnc->id}.xlsx";

$rows = $DB->get_records_sql("
    SELECT
        u.id AS userid,
        CONCAT(u.firstname,' ',u.lastname) fullname,
        d.puesto,
        d.gerencia,
        d.direccion,
        d.antiguedad,
        d.grado_estudios,
        d.escuela,
        d.timemodified
    FROM {dnc_data} d
    JOIN {user} u ON u.id = d.userid
    WHERE d.dncid = ?
    ORDER BY u.lastname
", [$dnc->id]);

$workbook = new \core_excel\workbook(null);
$worksheet = $workbook->add_worksheet('Respuestas');

$headers = [
    'Usuario',
    'Puesto',
    'Gerencia',
    'Direccion',
    'Antigüedad',
    'Grado estudios',
    'Escuela',
    'Fecha'
];

$rownum = 0;
$col = 0;

foreach ($headers as $header) {
    $worksheet->write_string($rownum, $col++, $header);
}

foreach ($rows as $r) {
    $rownum++;
    $col = 0;

    $worksheet->write_string($rownum, $col++, $r->fullname);
    $worksheet->write_string($rownum, $col++, $r->puesto);
    $worksheet->write_string($rownum, $col++, $r->gerencia);
    $worksheet->write_string($rownum, $col++, $r->direccion);
    $worksheet->write_string($rownum, $col++, $r->antiguedad);
    $worksheet->write_string($rownum, $col++, $r->grado_estudios);
    $worksheet->write_string($rownum, $col++, $r->escuela);
    $worksheet->write_string($rownum, $col++, userdate($r->timemodified));
}

$workbook->send($filename);
$workbook->close();
exit;
