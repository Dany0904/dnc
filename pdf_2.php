<?php
require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir . '/tcpdf/tcpdf.php');

global $DB;

// === PARAMETROS ===
$id = required_param('id', PARAM_INT);        // course module id
$userid = required_param('userid', PARAM_INT); // usuario del reporte

// === OBTENER CM Y DNC ===
$cm = get_coursemodule_from_id('dnc', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id'=>$cm->course], '*', MUST_EXIST);
$dnc = $DB->get_record('dnc', ['id'=>$cm->instance], '*', MUST_EXIST);

// === LOGIN Y CONTEXTO ===
require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// === OBTENER REGISTRO DEL USUARIO ===
$data = $DB->get_record('dnc_data', [
    'dncid' => $dnc->id,
    'userid' => $userid
]);

if (!$data) {
    print_error('No se encontró información de DNC para este usuario.');
}

$username = $DB->get_field('user', 'CONCAT(firstname, " ", lastname)', ['id' => $userid]);

$fecha_modificado = userdate($data->timemodified, '%d/%m/%Y');

$capdh = $DB->get_records('dnc_data_cap_funciones', [
    'dncdataid' => $data->id
], 'id ASC');

function mark_opt($value, $target) {
    global $CFG;
    return ($value == $target)
        ? 'x'
        //? '<img src="'.$CFG->wwwroot.'/mod/dnc/pix/check-solid-full.png" width="10">'
        : '';
}

$captecnica = $DB->get_records('dnc_data_cap_tecnica', [
    'dncdataid' => $data->id
]);

$capdes = $DB->get_records('dnc_data_cap_des_humano', [
    'dncdataid' => $data->id
], 'id ASC');

$preguntas_si_no = [
    [
        'preg' => get_string('capdh_q1', 'mod_dnc'),
        'extra' => get_string('capdh_q1_extra', 'mod_dnc')
    ],
    [
        'preg' => get_string('capdh_q2', 'mod_dnc'),
        'extra' => get_string('capdh_q2_extra', 'mod_dnc')
    ],
    [
        'preg' => get_string('capdh_q3', 'mod_dnc'),
        'extra' => get_string('capdh_q3_extra', 'mod_dnc')
    ],
];

$preguntas_abiertas = [
    get_string('preg_ab_1', 'mod_dnc'),
    get_string('preg_ab_2', 'mod_dnc'),
];

function print_si_no($value) {
    if($value == 1){
        return 'Si';
    }else{
        return 'No';
    }
}

$otrascap = $DB->get_records('dnc_data_cap_otras', [
    'dncdataid' => $data->id
]);

if (!$data) {
    print_error('No data found');
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');

// Metadatos
$pdf->SetCreator('Moodle');
$pdf->SetAuthor($username); 
$pdf->SetTitle($dnc->name);
$pdf->SetSubject('Reporte DNC');

// Márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 20);

// Nueva página
$pdf->AddPage();


$html = '
        <table cellpadding="4" border="1">
            <tr>
                <td width="10%">
                    <img src="'.$CFG->dirroot.'/mod/dnc/pix/logo.png" width="50" height="22" />
                </td>
                <td width="20%" style="font-size:8px; text-align:center;">
                    Corporativo Empresarial PACMALAZA
                </td>
                <td width="50%" style="font-size:10px; text-align:center;">
                    FORMATO<br>
                    <span>Detección de Necesidades de Capacitación</span>
                </td>
                <td width="20%" style="font-size:7px;">
                    Código: SGC-FR-CH-158<br>
                    Fecha emisión: 08/01/2024<br>
                    Fecha revisión: 08/01/2024<br>
                    No. Revisión: 02
                </td>
            </tr>
        </table>
        <table border="1">
            <tr>
                <td bgcolor="#DBDBDB" style="text-align: center;">
                    <p style="font-size: 11px; font-weight:bold; margin: 0; color: #782F91;">DATOS GENERALES</p>
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="4">
            <tr>
                <td style="font-size: 9px;" width="30%"><strong>Nombre completo:</strong></td>
                <td style="font-size: 9px;" width="70%">' . fullname($USER) . '</td>
            </tr>
            <tr>
                <td style="font-size: 9px;" width="30%"><strong>Puesto:</strong></td>
                <td style="font-size: 9px;" width="70%">' . format_string($data->puesto) . '</td>
            </tr>
            <tr>
                <td style="font-size: 9px;" width="30%"><strong>Gerencia o área:</strong></td>
                <td style="font-size: 9px;" width="70%">' . format_string($data->gerencia) . '</td>
            </tr>
            <tr>
                <td style="font-size: 9px;" width="30%"><strong>Dirección a la que perteneces:</strong></td>
                <td style="font-size: 9px;" width="70%">' . format_string($data->direccion) . '</td>
            </tr>
            <tr>
                <td style="font-size: 9px;" width="30%"><strong>Antigüedad:</strong></td>
                <td style="font-size: 9px;" width="70%">' . format_string($data->antiguedad) . '</td>
            </tr>
            <tr>
                <td style="font-size: 9px;" width="30%"><strong>Último grado de estudios:</strong></td>
                <td style="font-size: 9px;" width="70%">' . format_string($data->grado_estudios) . '</td>
            </tr>
            <tr>
                <td style="font-size: 9px;" width="30%"><strong>Escuela:</strong></td>
                <td style="font-size: 9px;" width="70%">' . format_string($data->escuela) . '</td>
            </tr>
        </table>
        <table border="1">
            <tr>
                <td bgcolor="#DBDBDB" style="text-align: center;">
                    <p style="font-size: 11px; font-weight:bold; margin: 0; color: #782F91;">CAPACITACIÓN ORIENTADA A FUNCIONES</p>
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="4">
            <tr>
                <td style="font-size: 9px;" width="56%"><strong>Describe tus principales funciones o actividades de acuerdo a lo que actualmente desempeñas</strong></td>
                <td style="font-size: 9px;" width="11%">Excelente</td>
                <td style="font-size: 9px;" width="11%">Bueno</td>
                <td style="font-size: 9px;" width="11%">Regular</td>
                <td style="font-size: 9px;" width="11%">Deficiente</td>
            </tr>';
                foreach ($capdh as $row) {

                $html .= '
                    <tr>
                        <td width="56%" style="font-size: 9px;">' . format_text($row->descripcion) . '</td>
                        <td width="11%" style="text-align:center;">' . mark_opt($row->tipo, 4) . '</td>
                        <td width="11%" style="text-align:center;">' . mark_opt($row->tipo, 3) . '</td>
                        <td width="11%" style="text-align:center;">' . mark_opt($row->tipo, 2) . '</td>
                        <td width="11%" style="text-align:center;">' . mark_opt($row->tipo, 1) . '</td>
                    </tr>';
                }

$html .= '</table>
        <table border="1">
            <tr>
                <td bgcolor="#DBDBDB" style="text-align: center;">
                    <p style="font-size: 11px; font-weight:bold; margin: 0; color: #782F91;">CAPACITACIÓN TÉCNICA</p>
                </td>
            </tr>
                <tr>
                    <td style="text-align: center;">
                        <h4 style="font-weight:bold; margin: 0; font-size: 9px;">Temas de capacitación que podrían mejorar tus funciones
                        </h4>
                    </td>
                </tr>
        </table>
        <table border="1" cellpadding="5" style="border-collapse:collapse;">
            <tr>
                <td rowspan="2" width="56%">
                    <h4 style="font-size: 9px;">Escribe la capacitación técnica de tu interes (Curso)</h4>
                </td>
                <td rowspan="2" width="22%" style="font-size: 9px;"><strong>Justificación</strong></td>
                <td colspan="2" width="22%" style="font-size: 9px;"><strong>Mes de aplicación</strong></td>
            </tr>
            <tr>
                <td width="11%" style="font-size: 9px;">1a opción</td>
                <td width="11%" style="font-size: 9px;">2a opción</td>
            </tr>';
            foreach ($captecnica as $row) {

                $curso = format_text($row->descripcion, FORMAT_PLAIN);
                $justificacion = format_text($row->justificacion, FORMAT_PLAIN);
                $m1 = format_text($row->mes1, FORMAT_PLAIN);
                $m2 = format_text($row->mes2, FORMAT_PLAIN);

                $html .= '
                <tr>
                    <td style="font-size: 9px;" width="56%">'.$curso.'</td>
                    <td style="font-size: 9px;" width="22%">'.$justificacion.'</td>
                    <td style="font-size: 9px;" width="11%">'.$m1.'</td>
                    <td style="font-size: 9px;" width="11%">'.$m2.'</td>
                </tr>';
            }
$html .= '</table>
        <table border="1">
            <tr>
                <td bgcolor="#DBDBDB" style="text-align: center;">
                    <p style="font-size: 11px; font-weight:bold; margin: 0; color: #782F91;">CAPACITACIÓN ORIENTADA A DESARROLLO HUMANO</p>
                </td>
            </tr>
             <tr>
                <td style="text-align: center;">
                    <h4 style="font-weight:bold; margin: 0; font-size: 9px;">Contesta segun sea de tu interes
                    </h4>
                </td>
            </tr>';
            $index = 0;

            foreach ($capdes as $row) {

                if ($index >= 3) break;  // solo 3 primeras

                if ($row->tipo == 1 || $row->tipo == 2) {

                    $p = $preguntas_si_no[$index];

                    $html .= '
                    <tr>
                        <td width="55%" style="font-size: 9px;">'.$p['preg'].' '.print_si_no($row->tipo).'</td>
                        <td width="20%" style="text-align:center; font-size: 9px;">'.$p['extra'].'</td>
                        <td width="25%" style="font-size: 9px;">' . format_text($row->descripcion, FORMAT_PLAIN) . '</td>
                    </tr>';

                    $index++;
                }
            }
            $countOpen = 0;

            foreach ($capdes as $row) {
                if ($row->tipo == 3) {

                    $html .= '
                    <tr>
                        <td colspan="4" style="font-size: 9px;">
                            '.$preguntas_abiertas[$countOpen].'<br>
                            '.format_text($row->descripcion, FORMAT_PLAIN).'
                        </td>
                    </tr>';

                    $countOpen++;

                    if ($countOpen >= 2) break;
                }
            }

$html .= '</table>
        <table border="1">
            <tr>
                <td bgcolor="#DBDBDB" style="text-align: center;">
                    <p style="font-size: 11px; font-weight:bold; margin: 0; color: #782F91;">OTRAS CAPACITACIONES</p>
                </td>
            </tr>
             <tr>
                <td style="text-align: center;">
                    <h4 style="font-weight:bold; margin: 0; font-size: 9px;">¿Qué problemas enfrentaste en tu trabajo que podrían resolverse con capacitación?
                    </h4>
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="5" style="border-collapse:collapse;">
            <tr>
                <td rowspan="2" width="56%">
                    <h4 style="text-align: center; font-size: 9px;">Tema</h4>
                </td>
                <td rowspan="2" style="font-size: 9px;" width="22%"><strong>Capacitación (Curso)</strong></td>
                <td colspan="2" style="font-size: 9px;" width="22%"><strong>Mes de aplicación</strong></td>
            </tr>
            <tr>
                <td style="font-size: 9px;" width="11%">1a opción</td>
                <td style="font-size: 9px;" width="11%">2a opción</td>
            </tr>';
             foreach ($otrascap as $row) {

                $descripcion = format_text($row->descripcion, FORMAT_PLAIN);
                $curso = format_text($row->curso, FORMAT_PLAIN);
                $m1 = format_text($row->mes1, FORMAT_PLAIN);
                $m2 = format_text($row->mes2, FORMAT_PLAIN);

                $html .= '
                <tr>
                    <td style="font-size: 9px;" width="56%">'.$descripcion.'</td>
                    <td style="font-size: 9px;" width="22%">'.$curso.'</td>
                    <td style="font-size: 9px;" width="11%">'.$m1.'</td>
                    <td style="font-size: 9px;" width="11%">'.$m2.'</td>
                </tr>';
            }
$html .= '</table>
        <table border="1" cellpadding="5">
            <tr>
                <td style="text-align: right; font-size: 9px;">
                    <strong>Fecha de aplicacion: '.$fecha_modificado.'</strong>
                </td>

            </tr>
            <tr>
                <td style="text-align: right; font-size: 9px;">
                    _________________________________ <br>
                    <strong>Nombre y firma del colaborador</strong>
                </td>
            </tr>
        </table>';


$pdf->writeHTML($html, true, false, true, false, '');

// Descargar
$filename = 'dnc_'.$USER->id.'.pdf';
$pdf->Output($filename, 'D');
