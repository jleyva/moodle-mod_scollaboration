<?php

/**
 * This page show the full layout of the collaboration tool
 *
 * @author  Juan Leyva <juanleyvadelgado@gmail.com>
 * @version $Id: session.php
 * @package mod/scollaboration
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT); // session ID

$session = $DB->get_record('scollaboration_sessions',array('id' =>  $id), '*', MUST_EXIST);

if($session->completed){
    print_error('sessioncompleted','mod_scollaboration');
}

$scollaboration = $DB->get_record('scollaboration',array('id' =>  $session->scid), '*', MUST_EXIST);
$course = $DB->get_record('course',array('id' => $scollaboration->course),'*', MUST_EXIST);
$cm = get_coursemodule_from_instance('scollaboration', $scollaboration->id, $course->id);

require_course_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/scollaboration:collaborate',$context);

$groupid = $session->groupid;

// groupid = 0 means no groups, the sessions is shared between all users
if(! $groupmode = groups_get_activity_groupmode($cm))
    $groupid = 0;

if($groupid && ! groups_is_member($groupid)){
    print_error('usertnotgroupmember','mod_scollaboration');
}

$moderator = has_capability('mod/scollaboration:moderate',$context);

add_to_log($course->id, "scollaboration", "view", "session.php?id=$cm->id", "$scollaboration->id");

// We need to detect the IE version for the whiteboard
$obsoleteie = false;

if(preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT']) && ! preg_match('/MSIE 9/i',$_SERVER['HTTP_USER_AGENT'])){
    $obsoleteie = true;
}    


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title><?php echo format_string($scollaboration->name); ?></title> 
<style type="text/css"> 
/*margin and padding on body element
  can introduce errors in determining
  element position and are not recommended;
  we turn them off as a foundation for YUI
  CSS treatments. */
body {
	margin:0;
	padding:0;
}
#toggle {
    text-align: center;
    padding: 1em;
}
#toggle a {
    padding: 0 5px;
    border-left: 1px solid black;
}

</style> 
<?php

echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/mod/scollaboration/yui.css" />';
    
if($obsoleteie){
    echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/mod/scollaboration/components/whiteboard/interfaces/default/style.css" />';
    echo '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/scollaboration/components/whiteboard/excanvas.js"></script>';
}
?>

<?php
echo '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/scollaboration/yui.js"></script>';
?>

<!--[if IE]><script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/mod/scollaboration/components/whiteboard/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/mod/scollaboration/lib/namespace.js"></script>

<script type="text/javascript"><!--
    // Global object configuration
    var SCMoodle = {
        SESSION_ID : '<?php echo $id;?>',
        SESSKEY : '<?php echo $USER->sesskey;?>',
        AJAX_POLLING_INT : '2000',
        FULLCANVAS_UPDATE: 60000,
        LAYOUT: 'left',
        MODERATOR : <?php echo ($moderator)? 'true': 'false';?>,
        OBSOLETE_IE: <?php echo ($obsoleteie)? 'true': 'false';?>
    };
    
--></script>

<?php
    $plugins = get_list_of_plugins('components','',$CFG->dirroot.'/mod/scollaboration');
    foreach($plugins as $p){
        $jsfile = "/mod/scollaboration/components/$p/functions.js";
        if(file_exists($CFG->dirroot.$jsfile)){            
	    echo html_writer::script('', $CFG->wwwroot.$jsfile);
        }
    }
    $jsfiles = array('js/jquery-1.6.2.min.js','js/jquery-ui-1.8.16.custom.min.js','js/jquery.layout-latest.js','js/jquery.ui.menu.js','js/jquery.ui.menubar.js','components/whiteboard/paintweb.js','js/scollaboration.js','components/session/functions.ajax.js','components/chat/functions.ajax.js','components/whiteboard/functions.ajax.js');
    foreach($jsfiles as $lib){
	echo html_writer::script('', $CFG->wwwroot.'/mod/scollaboration/'.$lib);
    }
?>

<link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot; ?>/mod/scollaboration/session.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot; ?>/mod/scollaboration/theme/lightness/main.css" />
</head> 
 
<body id="mod-scollaboration-session" class=" yui-skin-sam"> 

<div id="scmenubar" class="ui-layout-north">
    
</div>

<div id="blockssection" class="ui-layout-west">
    	
	<div class="ui-layout-north">
	    <div id="userlistlayer">
		<div id="useroptions">		    
		</div>
		<div id="userstable">
		</div>
	    </div>
	</div>
	    
	<div class="ui-layout-center">
	    <div id="chatlayer">
		<div id="chatlist">
		</div>
		<div id="textarealayer">
		    <div id="chattextarea">
		    <input type="text" id="chattextid" name="chattext">
		    </div>
		    <div id="chatsend">
			<input type="button" id="chatsendb" name="chatsendb" value="Send"> 
		    </div>
		</div>
	    </div>
	</div>
	
	<div class="ui-layout-south">
	    <div id="tooltabslayer">
	    
	    </div>
	</div>

</div>

<div class="ui-layout-center">
    <div id="whiteboardlayer"> 
		<!-- Whiteboard area -->
    <?php
	if($obsoleteie){
	    include_once('obsoletewb.html');
	}
	else{
    ?>    
	    <div id="PaintWebTarget">	
	    </div>
    <?php
	}
    ?>            
		<!--<img id="editableImage" src="components/whiteboard/freshalicious.jpg" alt="Freshalicious">-->
		<img id="editableImage" src="components/whiteboard/defaultimage.php" alt="Freshalicious">           
    </div> 
</div>

</body> 
</html>
