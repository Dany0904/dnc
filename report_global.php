<?php
require_once('../../config.php');

require_login();
$context = context_system::instance();
require_capability('mod/dnc:globalreport', $context);

global $DB, $PAGE, $OUTPUT;

$PAGE->set_url('/mod/dnc/report_global.php');
$PAGE->set_title('Reporte Global DNC');
$PAGE->set_heading('Reporte Global DNC');

echo $OUTPUT->header();

// === PARAMETRO usuario ===
$userid = optional_param('userid', 0, PARAM_INT);

// === SELECT de usuarios con registros ===
$users = $DB->get_records_sql_menu("
    SELECT DISTINCT u.id, CONCAT(u.firstname,' ',u.lastname)
    FROM {dnc_data} dd
    JOIN {user} u ON u.id = dd.userid
    ORDER BY u.lastname ASC
");

// === FORMULARIO ===
echo html_writer::start_div('card p-3 mb-4');
echo html_writer::tag('h3', 'Filtro por usuario');
echo '<form method="GET" action="report_global.php">';
echo '<label>Usuario:</label><br>';
echo html_writer::select($users, 'userid', $userid, ['0'=>'Todos']);
echo '<br><br>';
echo '<button class="btn btn-primary" type="submit">Filtrar</button>';
echo '</form>';
echo html_writer::end_div();

// === QUERY BASE ===
$params = [];
$where = [];

$sql = "
SELECT
    dd.id AS dncdataid,
    dd.dncid,
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

// === TABLA ===
$table = new html_table();
$table->head = [
    "Nombre",
    "Puesto",
    "Área",
    "Curso (Técnica)",
    "Mes 1 Técnica",
    "Mes 2 Técnica",
    "Curso (Otras)",
    "Mes 1 Otras",
    "Mes 2 Otras",
  //  "PDF"
];

foreach ($records as $r) {
     // Obtener el cmid a partir del dncid
    $cm = get_coursemodule_from_instance('dnc', $r->dncid, 0, false, MUST_EXIST);
    $cmid = $cm->id;
    
     $pdfurl = new moodle_url('/mod/dnc/pdf_2.php', [
        'id' => $cmid,        // el id del módulo dnc
        'userid' => $r->userid
    ]);

    $pdfbtn = html_writer::link(
        $pdfurl,
        'Descargar',
        ['class' => 'btn btn-danger btn-sm', 'target' => '_blank']
    );

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

    $table->data[] = [
        $r->username,
        $r->puesto,
        $r->gerencia,
        implode(" | ", $tec_curso),
        implode(" | ", $tec_mes1),
        implode(" | ", $tec_mes2),
        implode(" | ", $otras_curso),
        implode(" | ", $otras_mes1),
        implode(" | ", $otras_mes2),
        //$pdfbtn
    ];

}

echo html_writer::table($table);

// BOTÓN EXPORTAR
$urlparams = [];
if ($userid > 0) $urlparams['userid'] = $userid;

echo html_writer::tag('p',
    html_writer::link(
        new moodle_url('/mod/dnc/report_global_excel.php', $urlparams),
        "Descargar Excel",
        ['class'=>'btn btn-success']
    )
);

echo html_writer::tag('p',
    html_writer::link(
        new moodle_url('/mod/dnc/report_global_zip.php', $urlparams),
        "Descargar ZIP",
        ['class'=>'btn btn-warning']
    )
);

echo $OUTPUT->footer();
