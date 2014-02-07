<?php

defined('MOODLE_INTERNAL') || die;

if (has_capability('moodle/site:uploadusers', context_system::instance())) {
    $ADMIN->add('courses', new admin_externalpage('tooladmincourselist', get_string('pluginname', 'tool_courselists'), "$CFG->wwwroot/$CFG->admin/tool/courselists/index.php", 'moodle/site:uploadusers'));
}
