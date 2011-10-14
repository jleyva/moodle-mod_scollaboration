<?php 

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


defined('MOODLE_INTERNAL') || die();

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
    global $DB;

    $scollaboration->timemodified = time();

    
    if($returnid = $DB->insert_record('scollaboration', $scollaboration)){
    
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
    global $DB;

    $scollaboration->timemodified = time();
    $scollaboration->id = $scollaboration->instance;

    if ($returnid = $DB->update_record("scollaboration", $scollaboration)) {

        $event = new object();
        if ($event->id = $DB->get_field('event', 'id',array('modulename' =>  'scollaboration', 'instance' =>  $scollaboration->id))){
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
    global $DB;

    if (! $scollaboration = $DB->get_record('scollaboration',array('id' =>  $id))) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! $DB->delete_records('scollaboration', array('id' => $scollaboration->id))) {
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
    return null;
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
    return null;
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

    //$rec = $DB->get_record("scollaboration",array("id" => "$scollaborationid","scale" => "-$scaleid"));
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
    global $DB;
    
    $timenow = time();
    $session = $DB->get_record('scollaboration_sessions',array('scid' =>  $scollaboration->id, 'timestart' =>  $scollaboration->scollaborationtime,'groupid' => $groupid));
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
        $session->groupid = $groupid;
        return $DB->insert_record('scollaboration_sessions',$session);
    }
}

function scollaboration_json_error($errormessage, $langfile = true){
    if($langfile)
        $errormessage = get_string($errormessage,'mod_scollaboration');
      
    header('Content-type: application/json');
    echo json_encode(array('status'=>'failure','msg'=>$errormessage));
    die;
}

function scollaboration_user_nick($usernameformat, $user = null){
    global $USER;
    
    if(!$user)
        $user = $USER;

    switch($usernameformat){
        case 'fullname': $username = fullname($user);
                        break;
        case 'firstname': $username = $user->firstname;
                        break;
        case 'lastname': $username = $user->lastname;
                        break;     
        case 'idnumber': $username = $user->idnumber;
                        break;                                     
        default: $username = $user->username;
    }
    return $username;
}
