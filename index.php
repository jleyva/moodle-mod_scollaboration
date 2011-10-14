<?php // $Id: index.php,v 1.7.2.3 2009/08/31 22:00:00 mudrd8mz Exp $

/**
 * This page lists all the instances of scollaboration in a particular course
 *
 * @author  Juan Leyva <juanleyvadelgado@gmail.com>
 * @version $Id: index.php,v 1.7.2.3 2009/08/31 22:00:00 mudrd8mz Exp $
 * @package mod/scollaboration
 */

/// Replace scollaboration with the name of your module and remove this line

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // course

$PAGE->set_url('/mod/scollaboration/index.php', array('id'=>$id));

if (! $course = $DB->get_record('course',array( 'id' =>  $id))) {
    print_error('invalidcourseid', 'error');
}

require_login($course);
$PAGE->set_pagelayout('incourse');


add_to_log($course->id, 'scollaboration', 'view all', "index.php?id=$course->id", '');

/// Get all required stringsscollaboration

$strscollaborations = get_string('modulenameplural', 'scollaboration');
$strscollaboration  = get_string('modulename', 'scollaboration');

/// Print the header
$PAGE->navbar->add($strscollaborations);
$PAGE->set_title("$course->shortname: $strscollaborations");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();


/// Get all the appropriate data

if (! $scollaborations = get_all_instances_in_course('scollaboration', $course)) {
    notice('There are no instances of scollaboration', "../../course/view.php?id=$course->id");
    die;
}

/// Print the list of instances (your module will probably extend this)

$timenow  = time();
$strname  = get_string('name');
$strweek  = get_string('week');
$strtopic = get_string('topic');

$table = new html_table();

if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname);
    $table->align = array ('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array ($strtopic, $strname);
    $table->align = array ('center', 'left', 'left', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');
}

foreach ($scollaborations as $scollaboration) {
    if (!$scollaboration->visible) {
        //Show dimmed if the mod is hidden
        $link = '<a class="dimmed" href="view.php?id='.$scollaboration->coursemodule.'">'.format_string($scollaboration->name).'</a>';
    } else {
        //Show normal if the mod is visible
        $link = '<a href="view.php?id='.$scollaboration->coursemodule.'">'.format_string($scollaboration->name).'</a>';
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array ($scollaboration->section, $link);
    } else {
        $table->data[] = array ($link);
    }
}

echo $OUTPUT->header();echo html_writer::table($table);
echo $OUTPUT->footer();

