<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Create a new DNC instance
 */
function dnc_add_instance($data) {
    global $DB;

    $record = new stdClass();
    $record->course        = $data->course;
    $record->name          = $data->name;
    $record->intro         = $data->intro;
    $record->introformat   = $data->introformat;
    $record->timecreated   = time();
    $record->timemodified  = time();

    return $DB->insert_record('dnc', $record);
}

/**
 * Update an existing instance
 */
function dnc_update_instance($data) {
    global $DB;

    $record = new stdClass();
    $record->id            = $data->instance;   // MUY IMPORTANTE
    $record->course        = $data->course;
    $record->name          = $data->name;
    $record->intro         = $data->intro;
    $record->introformat   = $data->introformat;
    $record->timemodified  = time();

    return $DB->update_record('dnc', $record);
}

/**
 * Delete an instance
 */
function dnc_delete_instance($id) {
    global $DB;

    if (!$dnc = $DB->get_record('dnc', ['id'=>$id])) {
        return false;
    }

    // 🔥 también elimina respuestas de alumnos
    $DB->delete_records('dnc_data', ['dncid'=>$id]);

    return $DB->delete_records('dnc', ['id'=>$id]);
}

function dnc_user_has_response(int $dncid, int $userid): bool {
    global $DB;

    return $DB->record_exists('dnc_data', [
        'dncid' => $dncid,
        'userid' => $userid,
    ]);
}
