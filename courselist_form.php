<?php

defined('MOODLE_INTERNAL') || die();
require_once $CFG->libdir.'/formslib.php';

class admin_courselist_form extends moodleform {

    function definition () {
        $mform   = $this->_form;

        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'exportcsv', get_string('export_as_csv', 'tool_courselists'));
        $buttonarray[] = &$mform->createElement('submit', 'exportcategorycsv', get_string('export_all_categories_as_csv', 'tool_courselists'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
        $mform->addElement('advcheckbox', 'bulkcompat', null, get_string('compatibility_with_bulkupload', 'tool_courselists'));
        $mform->setType('bulkcompat', PARAM_INT);

    }
}
