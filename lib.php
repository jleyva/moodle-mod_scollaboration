<?php  // $Id: lib.php,v 1.7.2.5 2009/04/22 21:30:57 skodak Exp $

/**
 * Library of functions and constants for module scollaboration
 * This file should have two well differenced parts:
 *   - All the core Moodle functions, neeeded to allow
 *     the module to work integrated in Moodle.
 *   - All the scollaboration specific functions, needed
 *     to implement all the module logic. Please, note
 *     that, if the module become complex and this lib
 *     grows a lot, it's HIGHLY recommended to move all
 *     these module specific functions to a new php file,
 *     called "locallib.php" (see forum, quiz...). This will
 *     help to save some memory when Moodle is performing
 *     actions across all modules.
 */


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $scollaboration An object from the form in mod_form.php
 * @return int The id of the newly inserted scollaboration record
 */
function scollaboration_add_instance($scollaboration) {

    $scollaboration->timemodified = time();

    # You may have to add extra stuff in here #

    if($returnid = insert_record('scollaboration', $scollaboration)){
    
        $event = NULL;
        $event->name        = $scollaboration->name;
        $event->description = $scollaboration->intro;
        $event->courseid    = $scollaboration->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'scollaboration';
        $event->instance    = $returnid;
        $event->eventtype   = $scollaboration->schedule;
        $event->timestart   = $scollaboration->scollaborationtime;
        $event->timeduration = 0;

        add_event($event);        
    }
    return $returnid;
}


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $scollaboration An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function scollaboration_update_instance($scollaboration) {

    $scollaboration->timemodified = time();
    $scollaboration->id = $scollaboration->instance;

    if ($returnid = update_record("scollaboration", $scollaboration)) {

        $event = new object();

        if ($event->id = get_field('event', 'id', 'modulename', 'scollaboration', 'instance', $scollaboration->id)) {

            $event->name        = $scollaboration->name;
            $event->description = $scollaboration->intro;
            $event->timestart   = $scollaboration->scollaborationtime;

            update_event($event);
        }
    }

    return $returnid;
}


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function scollaboration_delete_instance($id) {

    if (! $scollaboration = get_record('scollaboration', 'id', $id)) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records('scollaboration', 'id', $scollaboration->id)) {
        $result = false;
    }

    return $result;
}


/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function scollaboration_user_outline($course, $user, $mod, $scollaboration) {
    return $return;
}


/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function scollaboration_user_complete($course, $user, $mod, $scollaboration) {
    return true;
}


/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in scollaboration activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function scollaboration_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}


/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function scollaboration_cron () {
    return true;
}


/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of scollaboration. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $scollaborationid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function scollaboration_get_participants($scollaborationid) {
    return false;
}


/**
 * This function returns if a scale is being used by one scollaboration
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $scollaborationid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function scollaboration_scale_used($scollaborationid, $scaleid) {
    $return = false;

    //$rec = get_record("scollaboration","id","$scollaborationid","scale","-$scaleid");
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}


/**
 * Checks if scale is being used by any instance of scollaboration.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any scollaboration
 */
function scollaboration_scale_used_anywhere($scaleid) {
    if ($scaleid and record_exists('scollaboration', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}


/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function scollaboration_install() {
    return true;
}


/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function scollaboration_uninstall() {
    return true;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other scollaboration functions go here.  Each of them must have a name that
/// starts with scollaboration_

// For a single Simple Collaboration instance, multiple sessions are posible
function scollaboration_sessionid($scollaboration, $groupid){
    $timenow = time();
    $session = get_record('scollaboration_sessions','scid', $scollaboration->id, 'timestart', $scollaboration->scollaborationtime,'groupid',$groupid);
    if($session){
        if($session->completed)
            return 0;
        return $session->id;
    }
    else{
        $session = new stdclass;
        $session->scid = $scollaboration->id;
        $session->completed = 0;
        $session->timestart = $scollaboration->scollaborationtime;
        $session->$groupid = $groupid;
        return insert_record('scollaboration_sessions',$session);
    }
}


?>
