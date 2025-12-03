<?php
namespace mod_dnc\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class student_form extends \moodleform {

    public function definition() {
        $mform = $this->_form;

        // ==========================
        //    DATOS GENERALES
        // ==========================
        // Campo: Puesto (select)
        $mform->addElement('select', 'puesto', get_string('puesto', 'mod_dnc'), [
            'direccion'     => get_string('puesto_direccion', 'mod_dnc'),
            'gerencia'      => get_string('puesto_gerencia', 'mod_dnc'),
            'jefatura'      => get_string('puesto_jefatura', 'mod_dnc'),
            'coordinacion'  => get_string('puesto_coordinacion', 'mod_dnc'),
            'supervision'   => get_string('puesto_supervision', 'mod_dnc'),
            'analista'      => get_string('puesto_analista', 'mod_dnc'),
            'becario'       => get_string('puesto_becario', 'mod_dnc'),
        ]);
        $mform->setType('puesto', PARAM_TEXT);
        $mform->addRule('puesto', get_string('required'), 'required');

       // Campo: Área (select)
        $mform->addElement('select', 'gerencia', get_string('gerencia', 'mod_dnc'), [
            'finanzas'                      => get_string('area_finanzas', 'mod_dnc'),
            'contabilidad'                  => get_string('area_contabilidad', 'mod_dnc'),
            'administracion'                => get_string('area_administracion', 'mod_dnc'),
            'tesoreria'                     => get_string('area_tesoreria', 'mod_dnc'),
            'capitalhumano'                 => get_string('area_capitalhumano', 'mod_dnc'),
            'desarrollohumano'              => get_string('area_desarrollohumano', 'mod_dnc'),
            'planeaciontransformacion'      => get_string('area_planeaciontransformacion', 'mod_dnc'),
            'planeacion'                    => get_string('area_planeacion', 'mod_dnc'),
            'inteligenciatecnologia'        => get_string('area_inteligenciatecnologia', 'mod_dnc'),
            'mejoracontinua'                => get_string('area_mejoracontinua', 'mod_dnc'),
            'produccionstaff'               => get_string('area_produccionstaff', 'mod_dnc'),
            'produccionoperativo'           => get_string('area_produccionoperativo', 'mod_dnc'),
            'compras'                       => get_string('area_compras', 'mod_dnc'),
            'plantastaff'                   => get_string('area_plantastaff', 'mod_dnc'),
            'logistica'                     => get_string('area_logistica', 'mod_dnc'),
            'comercialstaff'                => get_string('area_comercialstaff', 'mod_dnc'),
            'puntosventa'                   => get_string('area_puntosventa', 'mod_dnc'),
            'callcenter'                    => get_string('area_callcenter', 'mod_dnc'),
            'servicio_domicilio'            => get_string('area_servicio_domicilio', 'mod_dnc'),
            'mercadotecnia'                 => get_string('area_mercadotecnia', 'mod_dnc'),
            'direcciongeneral'              => get_string('area_direcciongeneral', 'mod_dnc'),
        ]);
        $mform->setType('gerencia', PARAM_TEXT);
        $mform->addRule('gerencia', get_string('required'), 'required');

        // Campo: Dirección
        $mform->addElement('text', 'direccion', get_string('direccion', 'mod_dnc'));
        $mform->setType('direccion', PARAM_TEXT);
        $mform->addRule('direccion', get_string('required'), 'required');

        // Campo: Antigüedad
        $mform->addElement('text', 'antiguedad', get_string('antiguedad', 'mod_dnc'));
        $mform->setType('antiguedad', PARAM_TEXT);
        $mform->addRule('antiguedad', get_string('required'), 'required');

        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);

        // Campo: Grado de estudios (select)
        $options = [
            'secundaria' => get_string('secundaria', 'mod_dnc'),
            'bachillerato' => get_string('bachillerato', 'mod_dnc'),
            'tecnico' => get_string('tecnico', 'mod_dnc'),
            'licenciatura' => get_string('licenciatura', 'mod_dnc'),
            'posgrado' => get_string('posgrado', 'mod_dnc'),
        ];

        $mform->addElement('select', 'grado_estudios', get_string('grado_estudios', 'mod_dnc'), $options);
        $mform->setType('grado_estudios', PARAM_TEXT);
        $mform->addRule('grado_estudios', get_string('required'), 'required');

        // Campo: Escuela (select)
        $mform->addElement('select', 'escuela', get_string('escuela', 'mod_dnc'), [
            'publica' => get_string('escuela_publica', 'mod_dnc'),
            'privada' => get_string('escuela_privada', 'mod_dnc'),
        ]);
        $mform->setType('escuela', PARAM_TEXT);
        $mform->addRule('escuela', get_string('required'), 'required');

        // ==========================
        //   CAPACITACIÓN ORIENTADA A FUNCIONES
        // ==========================
        // CAPACITACIÓN ORIENTADA A FUNCIONES
        $mform->addElement('html', '<h4>CAPACITACIÓN ORIENTADA A FUNCIONES</h4>');

        // Número máximo de items
        $maxitems = 5;

        for ($i = 0; $i < $maxitems; $i++) {

            $mform->addElement('textarea', "cap_desc_func[$i]", get_string('descripcion', 'mod_dnc'), 'rows="3" cols="60"');
            $mform->setType("cap_desc_func[$i]", PARAM_RAW);

            $options = [
                '' => get_string('choose'), // opción vacía obligatoria
                4 => 'EXCELENTE',
                3 => 'BUENO',
                2 => 'REGULAR',
                1 => 'DEFICIENTE',
            ];

            $mform->addElement('select', "cap_nivel[$i]", get_string('nivel', 'mod_dnc'), $options);
            $mform->setType("cap_nivel[$i]", PARAM_INT);

            $mform->addElement('html', '<hr>');
        }

        // ==========================
        //   CAPACITACIÓN TÉCNICA
        // ==========================
        $mform->addElement('html', '<h4>CAPACITACIÓN TÉCNICA</h4>');

        // Número máximo de items
        $maxitems = 5;

        // Opciones de meses
        $meses = [
            '' => get_string('choose', 'mod_dnc'), // opción vacía por defecto
            'enero'       => get_string('mes_enero', 'mod_dnc'),
            'febrero'     => get_string('mes_febrero', 'mod_dnc'),
            'marzo'       => get_string('mes_marzo', 'mod_dnc'),
            'abril'       => get_string('mes_abril', 'mod_dnc'),
            'mayo'        => get_string('mes_mayo', 'mod_dnc'),
            'junio'       => get_string('mes_junio', 'mod_dnc'),
            'julio'       => get_string('mes_julio', 'mod_dnc'),
            'agosto'      => get_string('mes_agosto', 'mod_dnc'),
            'septiembre'  => get_string('mes_septiembre', 'mod_dnc'),
            'octubre'     => get_string('mes_octubre', 'mod_dnc'),
            'noviembre'   => get_string('mes_noviembre', 'mod_dnc'),
            'diciembre'   => get_string('mes_diciembre', 'mod_dnc'),
        ];

        for ($i = 0; $i < $maxitems; $i++) {

            // Curso / Descripción
            $mform->addElement('textarea', "cap_tec_desc[$i]", get_string('curso', 'mod_dnc'), 'rows="3" cols="60"');
            $mform->setType("cap_tec_desc[$i]", PARAM_RAW);

            // Justificación
            $mform->addElement('text', "cap_tec_just[$i]", get_string('justificacion', 'mod_dnc'));
            $mform->setType("cap_tec_just[$i]", PARAM_TEXT);
            $mform->addRule("cap_tec_just[$i]", get_string('maxlength', '', 255), 'maxlength', 255);

            // Mes de aplicación (2 selects)
            $mform->addElement('select', "cap_tec_mes1[$i]", get_string('mes1', 'mod_dnc'), $meses);
            $mform->setType("cap_tec_mes1[$i]", PARAM_TEXT);

            $mform->addElement('select', "cap_tec_mes2[$i]", get_string('mes2', 'mod_dnc'), $meses);
            $mform->setType("cap_tec_mes2[$i]", PARAM_TEXT);

            $mform->addElement('html', '<hr>');
        }

        // ==========================
        // CAPACITACIÓN ORIENTADA A DESARROLLO HUMANO
        // ==========================
        $mform->addElement('html', '<h4>CAPACITACIÓN ORIENTADA A DESARROLLO HUMANO</h4>');

        // ==========================
        // Bloque de preguntas con Si/No y campo libre
        $des_humano_preguntas = [
            [
                'label' => get_string('relaciones_mejorar', 'mod_dnc'),
                'campo_libre' => 'relaciones_mejorar_desc',
                'label_libre' => get_string('relaciones_mejorar_detalle', 'mod_dnc'),
            ],
            [
                'label' => get_string('liderazgo', 'mod_dnc'),
                'campo_libre' => 'liderazgo_desc',
                'label_libre' => get_string('liderazgo_detalle', 'mod_dnc'),
            ],
            [
                'label' => get_string('gestion_tiempo', 'mod_dnc'),
                'campo_libre' => 'gestion_tiempo_desc',
                'label_libre' => get_string('gestion_tiempo_detalle', 'mod_dnc'),
            ],
        ];

        // Para las preguntas con Si/No
        foreach ($des_humano_preguntas as $idx => $preg) {
            // Label de la pregunta principal
            $mform->addElement('static', 'label_' . $idx, '', $preg['label']);

            // Select Si/No
            $mform->addElement('select', 'tipo_' . $idx, '', [
                ''  => get_string('choose', 'mod_dnc'),
                1   => get_string('si', 'mod_dnc'),
                2   => get_string('no', 'mod_dnc')
            ]);
            $mform->setType('tipo_' . $idx, PARAM_INT);

            // Campo libre con label específico
            $mform->addElement('text', $preg['campo_libre'], $preg['label_libre'], ['size' => 60]);
            $mform->setType($preg['campo_libre'], PARAM_TEXT);

            $mform->addElement('html', '<hr>');
        }

        // ==========================
        // Preguntas abiertas
        // ==========================
        $preguntas_abiertas = [
            'expectativas' => get_string('expectativas', 'mod_dnc'),
            'comentarios'  => get_string('comentarios', 'mod_dnc')
        ];

        foreach ($preguntas_abiertas as $campo => $label) {
            $mform->addElement('textarea', $campo, $label, ['rows'=>3, 'cols'=>60]);
            $mform->setType($campo, PARAM_TEXT);
            $mform->addElement('html', '<hr>');
        }

        // ==========================
        // OTRAS CAPACITACIONES
        // ==========================
        $mform->addElement('html', '<h4>' . get_string('cap_otras', 'mod_dnc') . '</h4>');

        // Loop 5 campos
        for ($i = 0; $i < 5; $i++) {

            // Tema / descripción
            $mform->addElement(
                'text',
                "cap_otras_desc[$i]",
                get_string('cap_otras_tema', 'mod_dnc'),
                ['size' => 60]
            );
            $mform->setType("cap_otras_desc[$i]", PARAM_TEXT);

            // Curso
            $mform->addElement(
                'text',
                "cap_otras_curso[$i]",
                get_string('cap_otras_curso', 'mod_dnc'),
                ['size' => 60]
            );
            $mform->setType("cap_otras_curso[$i]", PARAM_TEXT);

            // Mes 1
            $mform->addElement(
                'select',
                "cap_otras_mes1[$i]",
                get_string('cap_otras_mes1', 'mod_dnc'),
                $meses
            );
            $mform->setType("cap_otras_mes1[$i]", PARAM_TEXT);

            // Mes 2
            $mform->addElement(
                'select',
                "cap_otras_mes2[$i]",
                get_string('cap_otras_mes2', 'mod_dnc'),
                $meses
            );
            $mform->setType("cap_otras_mes2[$i]", PARAM_TEXT);

            $mform->addElement('html', '<hr>');
        }

        // Botones
        $this->add_action_buttons(true, get_string('submit', 'mod_dnc'));
    }

    public function validation($data, $files) {
        $errors = [];

        // ==========================
        // Validación CAPACITACIÓN ORIENTADA A FUNCIONES
        // ==========================
        $validcount_func = 0;
        if (!empty($data['cap_desc_func'])) {
            foreach ($data['cap_desc_func'] as $idx => $desc) {
                $desc = trim($desc);
                $nivel = isset($data['cap_nivel'][$idx]) ? $data['cap_nivel'][$idx] : '';

                if ($desc !== '' && $nivel !== '') {
                    $validcount_func++;
                }

                if ($desc !== '' && $nivel === '') {
                    $errors["cap_nivel[$idx]"] = get_string('required');
                }

                if ($desc === '' && $nivel !== '') {
                    $errors["cap_desc_func[$idx]"] = get_string('required');
                }
            }
        }
        if ($validcount_func < 1) {
            $errors["cap_desc_func[0]"] = get_string('cap_min_one', 'mod_dnc');
        }

        // ==========================
        // Validación CAPACITACIÓN TÉCNICA
        // ==========================
        $validcount_tec = 0;
        if (!empty($data['cap_tec_desc'])) {
            foreach ($data['cap_tec_desc'] as $idx => $desc) {

                $desc = trim($desc);
                $just = isset($data['cap_tec_just'][$idx]) ? trim($data['cap_tec_just'][$idx]) : '';
                $mes1 = isset($data['cap_tec_mes1'][$idx]) ? $data['cap_tec_mes1'][$idx] : '';
                $mes2 = isset($data['cap_tec_mes2'][$idx]) ? $data['cap_tec_mes2'][$idx] : '';

                $iscomplete = ($desc !== '' && $just !== '' && ($mes1 !== '' || $mes2 !== ''));

                if ($iscomplete) {
                    $validcount_tec++;
                }

                if ($desc !== '' && $just === '') {
                    $errors["cap_tec_just[$idx]"] = get_string('required');
                }

                if ($desc !== '' && $mes1 === '' && $mes2 === '') {
                    $errors["cap_tec_mes1[$idx]"] = get_string('required');
                }
            }
        }
        if ($validcount_tec < 1) {
            $errors["cap_tec_desc[0]"] = get_string('cap_min_one', 'mod_dnc');
        }

        // ==========================
        // Validación CAPACITACIÓN ORIENTADA A DESARROLLO HUMANO
        // ==========================
        // Campos con Si/No + texto
        for ($i = 0; $i < 3; $i++) {
            $tipo = isset($data['tipo_'.$i]) ? $data['tipo_'.$i] : '';
            $desc = isset($data['relaciones_mejorar_desc']) && $i === 0 ? trim($data['relaciones_mejorar_desc']) : 
                    (isset($data['liderazgo_desc']) && $i === 1 ? trim($data['liderazgo_desc']) : 
                    (isset($data['gestion_tiempo_desc']) && $i === 2 ? trim($data['gestion_tiempo_desc']) : ''));

            if ($tipo === '') {
                $errors['tipo_'.$i] = get_string('required', 'mod_dnc');
            }

            if ($desc === '') {
                $campo = $i === 0 ? 'relaciones_mejorar_desc' : ($i === 1 ? 'liderazgo_desc' : 'gestion_tiempo_desc');
                $errors[$campo] = get_string('required', 'mod_dnc');
            }
        }

        // Campos abiertos
        $abiertos = ['expectativas', 'comentarios'];
        foreach ($abiertos as $campo) {
            if (empty(trim($data[$campo]))) {
                $errors[$campo] = get_string('required', 'mod_dnc');
            }
        }

        // ==========================
        // Validación OTRAS CAPACITACIONES
        // ==========================
        $validcount_otras = 0;

        if (!empty($data['cap_otras_desc'])) {

            foreach ($data['cap_otras_desc'] as $i => $desc) {

                $desc = trim($desc);
                $curso = isset($data['cap_otras_curso'][$i]) ? trim($data['cap_otras_curso'][$i]) : '';
                $mes1  = isset($data['cap_otras_mes1'][$i]) ? $data['cap_otras_mes1'][$i] : '';
                $mes2  = isset($data['cap_otras_mes2'][$i]) ? $data['cap_otras_mes2'][$i] : '';

                $iscomplete = ($desc !== '' && $curso !== '' && ($mes1 !== '' || $mes2 !== ''));

                if ($iscomplete) {
                    $validcount_otras++;
                }

                if ($desc !== '' && $curso === '') {
                    $errors["cap_otras_curso[$i]"] = get_string('required');
                }

                if ($desc !== '' && $mes1 === '' && $mes2 === '') {
                    $errors["cap_otras_mes1[$i]"] = get_string('required');
                }
            }
        }

        if ($validcount_otras < 1) {
            $errors["cap_otras_desc[0]"] = get_string('cap_otras_min_one', 'mod_dnc');
        }

        return $errors;
    }

}
