<?php
require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir . '/tcpdf/tcpdf.php');

$id = required_param('id', PARAM_INT); // course module id

$cm = get_coursemodule_from_id('dnc', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id'=>$cm->course], '*', MUST_EXIST);
$dnc = $DB->get_record('dnc', ['id'=>$cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

// Obtener registro del usuario
$data = $DB->get_record('dnc_data', [
    'dncid'=>$dnc->id,
    'userid'=>$USER->id
]);

if (!$data) {
    print_error('No data found');
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');

// Metadatos
$pdf->SetCreator('Moodle');
$pdf->SetAuthor(fullname($USER));
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
                <td width="35%">
                    <img src="'.$CFG->dirroot.'/mod/dnc/pix/logo.png" width="50" height="50" />
                    Corporativo Empresarial PACMALAZA
                </td>
                <td width="45%" style="text-align:center;">
                    FORMATO<br>
                    <span>Detección de Necesidades de Capacitación</span>
                </td>
                <td width="20%" style="font-size:8px;">
                    Código: SGC-FR-CH-158<br>
                    Fecha emisión: 08/01/2024<br>
                    Fecha revisión: 08/01/2024<br>
                    No. Revisión: 02
                </td>
            </tr>
        </table>
        <table border="1">
            <tr>
                <td style="text-align: center;">
                    <h3 style="font-weight:bold;">DATOS GENERALES</h3>
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="4">
            <tr>
                <td width="35%"><strong>Nombre completo:</strong></td>
                <td width="65%"></td>
            </tr>
            <tr>
                <td width="35%"><strong>Puesto:</strong></td>
                <td width="65%"></td>
            </tr>
            <tr>
                <td width="35%"><strong>Gerencia o área:</strong></td>
                <td width="65%"></td>
            </tr>
            <tr>
                <td width="35%"><strong>Dirección a la que perteneces:</strong></td>
                <td width="65%"></td>
            </tr>
            <tr>
                <td width="35%"><strong>Antigüedad:</strong></td>
                <td width="65%"></td>
            </tr>
            <tr>
                <td width="35%"><strong>Último grado de estudios:</strong></td>
                <td width="65%"></td>
            </tr>
            <tr>
                <td width="35%"><strong>Escuela:</strong></td>
                <td width="65%"></td>
            </tr>
        </table>
        <table border="1">
            <tr>
                <td style="text-align: center;">
                    <h3 style="font-weight:bold; margin: 0;">CAPACITACIÓN ORIENTADA A FUNCIONES</h3>
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="4">
            <tr>
                <td width="56%">
                    <strong>Describe tus principales funciones o actividades de acuerdo a lo que actualmente
                        desempeñas</strong>
                </td>
                <td width="11%">Excelente</td>
                <td width="11%">Bueno</td>
                <td width="11%">Regular</td>
                <td width="11%">Deficiente</td>
            </tr>
            <tr>
                <td width="56%"></td>
                <td width="11%"></td>
                <td width="11%"></td>
                <td width="11%"></td>
                <td width="11%"></td>
            </tr>
        </table>
        <table border="1">
            <tr>
                <td style="text-align: center;">
                    <h3 style="font-weight:bold; margin: 0;">CAPACITACIÓN TÉCNICA</h3>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <h4 style="font-weight:bold; margin: 0;">Temas de capacitación que podrían mejorar tus funciones
                    </h4>
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="5" style="border-collapse:collapse;">
            <tr>
                <td rowspan="2" width="56%">
                    <h4>Escribe la capacitación técnica de tu interes (Curso)</h4>
                </td>
                <td rowspan="2" width="22%"><strong>Justificación</strong></td>
                <td colspan="2" width="22%"><strong>Mes de aplicación</strong></td>
            </tr>
            <tr>
                <td width="11%">1a opción</td>
                <td width="11%">2a opción</td>
            </tr>
            <tr>
                <td width="56%"></td>
                <td width="22%"></td>
                <td width="11%"></td>
                <td width="11%"></td>
            </tr>
        </table>
        <table border="1">
            <tr>
                <td style="text-align: center;">
                    <h3 style="font-weight:bold; margin: 0;">CAPACITACIÓN ORIENTADA A DESARROLLO HUMANO</h3>
                </td>
            </tr>
            <tr>
                <td>

                </td>
            </tr>
        </table>
        <table border="1">
            <tr>
                <td style="text-align: center;">
                    <h3 style="font-weight:bold; margin: 0;">OTRAS CAPACITACIONES</h3>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <h4 style="font-weight:bold; margin: 0;">¿Qué problemas enfrentaste en tu trabajo que podrían
                        resolverse con capacitación?</h4>
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="5" style="border-collapse:collapse;">
            <tr>
                <td rowspan="2" width="56%">
                    <h4 style="text-align: center;">Tema</h4>
                </td>
                <td rowspan="2" width="22%"><strong>Capacitación (Curso)</strong></td>
                <td colspan="2" width="22%"><strong>Mes de aplicación</strong></td>
            </tr>
            <tr>
                <td width="11%">1a opción</td>
                <td width="11%">2a opción</td>
            </tr>
            <tr>
                <td width="56%"></td>
                <td width="22%"></td>
                <td width="11%"></td>
                <td width="11%"></td>
            </tr>
        </table>
        <table border="1" cellpadding="5">
            <tr>
                <td style="text-align: right">
                    <strong>Fecha de aplicacion:</strong>
                </td>

            </tr>
            <tr>
                <td style="text-align: right;">
                    _________________________________ <br>
                    <strong>Nombre y firma del colaborador</strong>
                </td>
            </tr>
        </table>';


$pdf->writeHTML($html, true, false, true, false, '');

// Descargar
$filename = 'dnc_'.$USER->id.'.pdf';
$pdf->Output($filename, 'D');
