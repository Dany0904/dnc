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

// Parámetros opcionales
$courseid = optional_param('courseid', 0, PARAM_INT);
$userid   = optional_param('userid', 0, PARAM_INT);


// === 1) SELECT de cursos donde hay DNCs ===
$courses = $DB->get_records_sql_menu("
    SELECT DISTINCT c.id, c.fullname
    FROM {dnc} d
    JOIN {course} c ON c.id = d.course
    ORDER BY c.fullname ASC
");

// === 2) SELECT de usuarios con registros ===
$users = $DB->get_records_sql_menu("
    SELECT DISTINCT u.id, CONCAT(u.firstname,' ',u.lastname)
    FROM {dnc_data} dd
    JOIN {user} u ON u.id = dd.userid
    ORDER BY u.lastname ASC
");


// === FORMULARIO DE FILTRO ===
echo html_writer::start_div('card p-3 mb-4');
echo html_writer::tag('h3', 'Filtros');

echo '<form method="GET" action="report_global.php">';

// curso
echo '<label>Curso:</label><br>';
echo html_writer::select($courses, 'courseid', $courseid, ['0'=>'Todos']);
echo '<br><br>';

// usuario
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
    $sql .= " WHERE ".implode(" AND ", $where);
}

$sql .= " ORDER BY dd.timemodified DESC";

$records = $DB->get_records_sql($sql, $params);

// === TABLA ===
$table = new html_table();
$table->head = [
    "Curso",
    "Usuario",
    "Puesto",
    "Gerencia",
    "Dirección",
    "Antigüedad",
    "Grado",
    "Escuela",
    "Fecha"
];

foreach ($records as $r) {
    $table->data[] = [
        $r->course,
        $r->username,
        $r->puesto,
        $r->gerencia,
        $r->direccion,
        $r->antiguedad,
        $r->grado_estudios,
        $r->escuela,
        userdate($r->timemodified),
    ];
}

echo html_writer::table($table);

// BOTÓN EXPORTAR
$urlparams = [];
if ($courseid > 0) $urlparams['courseid'] = $courseid;
if ($userid > 0) $urlparams['userid'] = $userid;

echo html_writer::tag('p',
    html_writer::link(
        new moodle_url('/mod/dnc/report_global_excel.php', $urlparams),
        "Descargar Excel filtrado",
        ['class'=>'btn btn-success']
    )
);

echo $OUTPUT->footer();
