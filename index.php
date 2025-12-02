<?php
require('../../config.php');

$id = required_param('id', PARAM_INT); // Course id
$course = get_course($id);

require_login($course);
$PAGE->set_url("/mod/dnc/index.php", ['id' => $id]);
$PAGE->set_title('DNC');
$PAGE->set_heading('DNC');

echo $OUTPUT->header();
echo html_writer::tag('h3', 'Instancias DNC del curso...');
echo $OUTPUT->footer();
