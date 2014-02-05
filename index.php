<?php

require('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
#require_once($CFG->dirroot.'/course/lib.php');
#require_once($CFG->libdir . '/filelib.php');
require_once('courselist_form.php');

@set_time_limit(60*60); // 1 hour should be enough
raise_memory_limit(MEMORY_HUGE);

require_login();
admin_externalpage_setup('tooladmincourselist');
$context = get_system_context();
require_capability('moodle/category:manage', $context);



$bulkcompat        = optional_param('bulkcompat', '', PARAM_INT);
$exportyCsv        = isset($_POST['exportcsv'])         ? true : false;
$exportCategoryCsv = isset($_POST['exportcategorycsv']) ? true : false;
if ($exportyCsv === true) {
    // Export all csv columns as CSV
    $sql = <<<___SQL___
SELECT C.* FROM {course} C
  WHERE C.id != 1
  ORDER BY C.id ASC
___SQL___;

    $rows = $DB->get_records_sql($sql);

    $fields = array('id'               => 'id',
                    'shortname'        => 'shortname',
                    'fullname'         => 'fullname',
                    'idnumber'         => 'idnumber',
                    'category_path'    => 'category_path',
                    'visible'          => 'visible',
                    'startdate'        => 'startdate',
                    'summary'          => 'summary',
                    'format'           => 'format',
                    'theme'            => 'theme',
                    'lang'             => 'lang',
                    'newsitems'        => 'newsitems',
                    'showgrades'       => 'showgrades',
                    'showreports'      => 'showreports',
                    'legacyfiles'      => 'legacyfiles',
                    'maxbytes'         => 'maxbytes',
                    'groupmode'        => 'groupmode',
                    'groupmodeforce'   => 'groupmodeforce',
                    'enablecompletion' => 'enablecompletion',
    );
    if ($bulkcompat) {
        unset($fields['id']);
    }

    $csvexport = new csv_export_writer();
    $csvexport->set_filename('course'); // FIXME?
    $csvexport->add_data($fields);
    foreach ($rows as $key => $row) {
        foreach ($fields as $field) {
            if (!strcmp('category_path', $field)) {
                // fetch recursive category list
                $categories = array();
                $categoryId = $row->category;
                do {
                    $record = $DB->get_record('course_categories', array('id' =>  $categoryId));
                    $categoryId = $record->parent;
                    $categories[] = $record->name;
                } while ($categoryId);

                if ($categories && is_array($categories)) {
                    $categories = array_reverse($categories);
                    $value = implode(' / ', $categories);
                }
                $data[$field] = $value; unset($value);
                continue;
            }
            $data[$field] = $row->$field;
        }
        $csvexport->add_data($data);
    }
    $csvexport->download_file();
    die;
}

if ($exportCategoryCsv === true) {
    // Export all categories as CSV
#    $rows = $DB->get_records('course_categories');

    $sql = <<<___SQL___
SELECT CC.* FROM {course_categories} CC
  ORDER BY CC.id ASC
___SQL___;

    $rows = $DB->get_records_sql($sql);

    $fields = array('id'            => 'id',
                    'category'      => 'category',
                    'category_path' => 'category_path',
                    'description'   => 'description',
                    'idnumber'      => 'idnumber',
                    'theme'         => 'theme',
                    'visible'       => 'visible',
                    'coursecount'   => 'coursecount',
    );
    if ($bulkcompat) {
        unset($fields['id']);
        unset($fields['category']);
        unset($fields['coursecount']);
    }

    $csvexport = new csv_export_writer();
    $csvexport->set_filename('course_categories'); // FIXME?
    $csvexport->add_data($fields);
    foreach ($rows as $key => $row) {
        foreach ($fields as $key => $field) {
            // FIXME...
            if (!strcmp('category', $key)) {
                $data['category'] = $row->name;
                continue;
            }
            if (!strcmp('category_path', $key)) {
                $categories = array();
                $categoryId = $row->id;
                // fetch recursive category list
                do {
                    $record = $DB->get_record('course_categories', array('id' =>  $categoryId));
                    $categoryId = $record->parent;
                    $categories[] = $record->name;
                } while ($categoryId);

                if ($categories && is_array($categories)) {
                    $categories = array_reverse($categories);
                    $value = implode(' / ', $categories);
                }
                $data[$key] = $value; unset($value);
                continue;
            }


            $data[$key] = $row->$field;
        }
        $csvexport->add_data($data);
    }
    $csvexport->download_file();
    die;
}


$frontpagecontext = context_course::instance(SITEID);

$sql = <<<___SQL___
SELECT C.* FROM {course} C
  WHERE C.id != 1
  ORDER BY C.id ASC
___SQL___;

$rows = $DB->get_records_sql($sql);


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('show_course_list', 'tool_courselists'));

$table = new html_table();
$table->id = "cclist";
$table->attributes['class'] = 'generaltable';
$table->tablealign = 'center';
$table->description = '';


$possibleColumns = array(
    'id'           => 'tool_courselists',
    'category'     => null,
    'fullname'     => null,
    'shortname'    => null,
    'idnumber'     => null,
    'startdate'    => null,
    'maxbytes'     => 'admin',
    'timecreated'  => 'tool_courselists',
    'timemodified' => 'tool_courselists',
);

$table->head = array();
foreach ($possibleColumns as $key => $value) {
    $table->head[] = get_string($key, $value);
}

$data = array();
$timeformat = get_string('strftimedatefullshort');
foreach ($rows as $row) {
    $cols = array();
    foreach ($possibleColumns as $key => $null) {
        $value = $row->$key;
        if (in_array($key, array('startdate', 'timecreated', 'timemodified'))) {
            $value = userdate($value, $timeformat);
        }
        if (in_array($key, array('fullname', 'shortname'))) {
            $value = html_writer::link(
                new moodle_url('/course/view.php', array('id' => $row->id)), $value);
        }
        if (!strcmp('category', $key)) {
            // fetch recursive category list
            $categories = array();
            do {
                $record = $DB->get_record('course_categories', array('id' =>  $value));
                $value = $record->parent;
                $categories[] = html_writer::link(
                        new moodle_url('/course/index.php', array('categoryid' => $record->id)), $record->name);
            } while ($value);

            if ($categories && is_array($categories)) {
                $categories = array_reverse($categories);
                $value = implode(' / ', $categories);
            }
        }
        $cols[$key] = $value;
    }
    $data[] = $cols;
}
$table->data = $data;


echo html_writer::tag('div', html_writer::table($table), array('class'=>'flexible-wrap'));


$mform = new admin_courselist_form();
$mform->display();
echo $OUTPUT->footer();
die;
