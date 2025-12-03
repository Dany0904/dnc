<?php
require_once(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/classes/form/student_form.php');

$id = required_param('id', PARAM_INT); // id del course module.

$cm = get_coursemodule_from_id('dnc', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id'=>$cm->course], '*', MUST_EXIST);
$dnc = $DB->get_record('dnc', ['id'=>$cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = \context_module::instance($cm->id);

$PAGE->set_url('/mod/dnc/view.php', ['id'=>$id]);
$PAGE->set_title($dnc->name);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

// 1️⃣ Buscar si el usuario ya respondió
$existing = $DB->get_record('dnc_data', [
    'dncid'  => $dnc->id,
    'userid' => $USER->id
]);

// 2️⃣ Si ya respondió → mensaje y salir
if ($existing) {
    $url = new moodle_url('/mod/dnc/pdf.php', ['id' => $id]);
    echo $OUTPUT->notification(get_string('alreadycompleted', 'mod_dnc'), 'info');

    echo html_writer::tag('p',
        html_writer::link($url, get_string('downloadpdf', 'mod_dnc'),
        ['class'=>'btn btn-primary'])
    );

    echo $OUTPUT->footer();
    exit;
}

$form = new \mod_dnc\form\student_form(null, ['id' => $id]);

// 3️⃣ Procesar si el form fue enviado
if ($form->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id'=>$course->id]));
}
else if ($data = $form->get_data()) {

    $record = new stdClass();
    $record->dncid = $dnc->id;
    $record->userid = $USER->id;
    $record->timemodified = time();

    $record->puesto          = $data->puesto;
    $record->gerencia        = $data->gerencia;
    $record->direccion       = $data->direccion;
    $record->antiguedad      = $data->antiguedad;
    $record->grado_estudios  = $data->grado_estudios;
    $record->escuela         = $data->escuela;

     $dncdataid = $DB->insert_record('dnc_data', $record);

    // --- Guardar seccion multivalor ---
    foreach ($data->cap_desc_func as $i => $desc) {
        if (trim($desc) === '') {
            continue;
        }

        $detalle = new stdClass();
        $detalle->dncdataid   = $dncdataid;
        $detalle->tipo        = (int)$data->cap_nivel[$i];  // 4/3/2/1
        $detalle->descripcion = $desc;

        $DB->insert_record('dnc_data_cap_funciones', $detalle);
    }

    // Guardar CAPACITACIÓN TÉCNICA
    if (!empty($data->cap_tec_desc)) {
        foreach ($data->cap_tec_desc as $i => $desc) {

            $desc = trim($desc);
            $just = isset($data->cap_tec_just[$i]) ? trim($data->cap_tec_just[$i]) : '';
            $mes1 = isset($data->cap_tec_mes1[$i]) ? $data->cap_tec_mes1[$i] : '';
            $mes2 = isset($data->cap_tec_mes2[$i]) ? $data->cap_tec_mes2[$i] : '';

            if ($desc === '' && $just === '' && $mes1 === '' && $mes2 === '') {
                continue; // registro vacío → saltar
            }

            $record = new stdClass();
            $record->dncdataid = $dncdataid;
            $record->descripcion = $desc;
            $record->justificacion = $just;
            $record->mes1 = $mes1;
            $record->mes2 = $mes2;

            $DB->insert_record('dnc_data_cap_tecnica', $record);
        }
    }

    // ================================================
    // CAPACITACIÓN ORIENTADA A DESARROLLO HUMANO
    // ================================================

    // Preguntas Si/No con campo libre
    $des_humano_preguntas = [
        ['tipo' => 'tipo_0', 'desc' => 'relaciones_mejorar_desc'],
        ['tipo' => 'tipo_1', 'desc' => 'liderazgo_desc'],
        ['tipo' => 'tipo_2', 'desc' => 'gestion_tiempo_desc']
    ];

    foreach ($des_humano_preguntas as $preg) {
        if (isset($data->{$preg['tipo']}) && isset($data->{$preg['desc']}) && trim($data->{$preg['desc']}) !== '') {
            $record = new stdClass();
            $record->dncdataid = $dncdataid;  
            $record->tipo = (int) $data->{$preg['tipo']}; 
            $record->descripcion = trim($data->{$preg['desc']});
            $DB->insert_record('dnc_data_cap_des_humano', $record);
        }
    }

    // Preguntas abiertas
    $preguntas_abiertas = ['expectativas', 'comentarios'];

    foreach ($preguntas_abiertas as $campo) {
        if (isset($data->{$campo}) && trim($data->{$campo}) !== '') {
            $record = new stdClass();
            $record->dncdataid = $dncdataid;
            $record->tipo = 3; // Tipo 3 para preguntas abiertas
            $record->descripcion = trim($data->{$campo});
            $DB->insert_record('dnc_data_cap_des_humano', $record);
        }
    }

        // --- Guardar OTRAS CAPACITACIONES ---
    foreach ($data->cap_otras_desc as $i => $desc) {

        $desc = trim($desc);
        if ($desc === '') continue;

        $detalle = new stdClass();
        $detalle->dncdataid   = $dncdataid;
        $detalle->descripcion = $desc;
        $detalle->curso       = trim($data->cap_otras_curso[$i]);
        $detalle->mes1        = $data->cap_otras_mes1[$i];
        $detalle->mes2        = $data->cap_otras_mes2[$i];

        $DB->insert_record('dnc_data_cap_otras', $detalle);
    }

    redirect($PAGE->url, get_string('datasaved', 'mod_dnc'));
}

// 4️⃣ Mostrar formulario
$form->display();

echo $OUTPUT->footer();
