<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_dnc_mod_form extends moodleform_mod {

    function definition() {
        global $CFG;
        $mform = $this->_form;

        // Nombre de la actividad
        $mform->addElement('text', 'name', get_string('modulename', 'mod_dnc'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // 🟢 Aquí SI se puede llamar al intro estándar
        $this->standard_intro_elements();

        // Configuraciones estándar
        $this->standard_coursemodule_elements();

        // Botones
        $this->add_action_buttons();
    }
}
