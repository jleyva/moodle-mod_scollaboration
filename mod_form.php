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

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_scollaboration_mod_form extends moodleform_mod {

    function definition() {

        global $DB, $COURSE;
        $mform =& $this->_form;

//-------------------------------------------------------------------------------
    /// Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

    /// Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('scollaborationname', 'scollaboration'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

    /// Adding the required "intro" field to hold the description of the instance
        $mform->addElement('htmleditor', 'intro', get_string('scollaborationintro', 'scollaboration'));
        $mform->setType('intro', PARAM_RAW);
        $mform->addRule('intro', get_string('required'), 'required', null, 'client');        

        $mform->addElement('date_time_selector', 'scollaborationtime', get_string('scollaborationtime', 'scollaboration'));

        $options=array();
        $options[1]  = get_string('repeatnone', 'chat');
        $options[2]  = get_string('repeatdaily', 'chat');
        $options[3]  = get_string('repeatweekly', 'chat');
        $mform->addElement('select', 'schedule', get_string('repeattimes', 'chat'), $options);


        $options=array();
        $options[0]    = get_string('neverdeletemessages', 'chat');
        $options[365]  = get_string('numdays', '', 365);
        $options[180]  = get_string('numdays', '', 180);
        $options[150]  = get_string('numdays', '', 150);
        $options[120]  = get_string('numdays', '', 120);
        $options[90]   = get_string('numdays', '', 90);
        $options[60]   = get_string('numdays', '', 60);
        $options[30]   = get_string('numdays', '', 30);
        $options[21]   = get_string('numdays', '', 21);
        $options[14]   = get_string('numdays', '', 14);
        $options[7]    = get_string('numdays', '', 7);
        $options[2]    = get_string('numdays', '', 2);
        $mform->addElement('select', 'keepdays', get_string('savemessages', 'scollaboration'), $options);

        $mform->addElement('selectyesno', 'studentlogs', get_string('studentseereports', 'scollaboration'));

        // TODO - Default values for chat, speak, whiteboard, etc....
        // TODO - Default layout
        // TODO - Username format
        
        $features = new stdClass;
        $features->groups = true;
        $features->groupings = true;
        $features->groupmembersonly = true;
        
//-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $features = new stdClass;
        $features->groups = true;
        $features->groupings = false;
        $features->groupmembersonly = true;
        $this->standard_coursemodule_elements($features);
//-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();

    }
}
