<?php //$Id: mod_form.php,v 1.2.2.3 2009/03/19 12:23:11 mudrd8mz Exp $

/**
 * This file defines the main scollaboration configuration form
 * It uses the standard core Moodle (>1.8) formslib. For
 * more info about them, please visit:
 *
 * http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * The form must provide support for, at least these fields:
 *   - name: text element of 64cc max
 *
 * Also, it's usual to use these fields:
 *   - intro: one htmlarea element to describe the activity
 *            (will be showed in the list of activities of
 *             scollaboration type (index.php) and in the header
 *             of the scollaboration main page (view.php).
 *   - introformat: The format used to write the contents
 *             of the intro field. It automatically defaults
 *             to HTML when the htmleditor is used and can be
 *             manually selected if the htmleditor is not used
 *             (standard formats are: MOODLE, HTML, PLAIN, MARKDOWN)
 *             See lib/weblib.php Constants and the format_text()
 *             function for more info
 */

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_scollaboration_mod_form extends moodleform_mod {

    function definition() {

        global $COURSE;
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
        $mform->setHelpButton('intro', array('writing', 'richtext'), false, 'editorhelpbutton');

        $mform->addElement('date_time_selector', 'scollaborationtime', get_string('scollaborationtime', 'scollaboration'));

        $options=array();
        $options[1]  = get_string('repeatnone', 'scollaboration');
        $options[2]  = get_string('repeatdaily', 'scollaboration');
        $options[3]  = get_string('repeatweekly', 'scollaboration');
        $mform->addElement('select', 'schedule', get_string('repeattimes', 'scollaboration'), $options);


        $options=array();
        $options[0]    = get_string('neverdeletemessages', 'scollaboration');
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

?>
