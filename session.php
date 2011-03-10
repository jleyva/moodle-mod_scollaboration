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

if (! $session = get_record('scollaboration_sessions', 'id', $id)) {
    error('Session ID was incorrect');
}

if($session->completed){
    error('Session completed');
}

if (! $scollaboration = get_record('scollaboration', 'id', $session->scid)) {
    error('Collaboration ID was incorrect');
}
if (! $course = get_record('course', 'id', $scollaboration->course)) {
    error('Course is misconfigured');
}
if (! $cm = get_coursemodule_from_instance('scollaboration', $scollaboration->id, $course->id)) {
    error('Course Module ID was incorrect');
}

require_course_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/scollaboration:collaborate',$context);

$groupid = $session->groupid;

// groupid = 0 means no groups, the sessions is shared between all users
if(! $groupmode = groups_get_activity_groupmode($cm))
    $groupid = 0;

if($groupid && ! groups_is_member($groupid)){
    error('User is not member of the group selected');
}

$moderator = has_capability('mod/scollaboration:moderate',$context);

add_to_log($course->id, "scollaboration", "view", "session.php?id=$cm->id", "$scollaboration->id");

// We need to detect the IE version for the whiteboard
$obsoleteie = false;

if(preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT']) && ! preg_match('/MSIE 9/i',$_SERVER['HTTP_USER_AGENT'])){
    $obsoleteie = true;
}    

// As the plugin chat does, we dont use print_header or print_header simple (neither print_footer)
// To avoid problems with Moodle's CSS and javascript we only load YUI css and js files

$yuijsfiles = array('yahoo-dom-event/yahoo-dom-event','container/container_core-min','yahoo/yahoo-min','event/event-min','dom/dom-min','element/element-beta-min','dragdrop/dragdrop-min','resize/resize-min','animation/animation-min','layout/layout-min','tabview/tabview-min','button/button-min','utilities/utilities','container/container','menu/menu-min','treeview/treeview-min','treeview/treeview','menu/menu','connection/connection-min','container/container-min','json/json-min','datasource/datasource-min','get/get-min','dragdrop/dragdrop-min','datatable/datatable-min');
// TODO: Hopefully, some day, I will find a way to load custom CSS (overriding yui ones)
$yuicssfiles = array('reset-fonts-grids/reset-fonts-grids','resize/assets/skins/sam/resize','layout/assets/skins/sam/layout','button/assets/skins/sam/button','menu/assets/skins/sam/menu','fonts/fonts-min','tabview/assets/skins/sam/tabview','treeview/assets/skins/sam/treeview','container/assets/skins/sam/container','reset/reset','fonts/fonts','datatable/assets/skins/sam/datatable');


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title>Full Page Layout - Example</title> 
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
foreach($yuicssfiles as $f)
    echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/lib/yui/'.$f.'.css" />';
    
if($obsoleteie){
    echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/mod/scollaboration/components/whiteboard/interfaces/default/style.css" />';
    echo '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/scollaboration/components/whiteboard/excanvas.js"></script>';
}
?>

<?php
foreach($yuijsfiles as $f)
    echo '<script type="text/javascript" src="'.$CFG->wwwroot.'/lib/yui/'.$f.'.js"></script>';
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
            echo '<script type="text/javascript" src="'.$CFG->wwwroot.$jsfile.'"></script>';
        }
    }
?>
<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/mod/scollaboration/components/whiteboard/paintweb.js"></script>
<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/mod/scollaboration/scollaboration.js"></script>

<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/mod/scollaboration/components/session/functions.ajax.js"></script>
<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/mod/scollaboration/components/chat/functions.ajax.js"></script>
<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/mod/scollaboration/components/whiteboard/functions.ajax.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot; ?>/mod/scollaboration/session.css" />

</head> 
 
<body id="mod-scollaboration-session" class=" yui-skin-sam"> 

<div id="tooltabs">
</div>

<div id="userlistlayer">
    <div id="useroptions">
        <div id="useractions">
            <!-- menu buttom -->
            <input type="submit" id="menubuttonua" name="menubuttonua_name" value="Actions">
            <select id="menubuttonuaselect" name="menubuttonuaselect">
                <option value="0">Raise Hand</option>
                <option value="1">Status: Away</option>
                <option value="2">Status: :)</option>
                <option value="3">Status: :(</option>
                <option value="4">Status: :S</option>
            </select>
        
        </div>
        <div id="userpollbuttom">
            <input type="button" id="userpollok" name="pollok" value=""> 
            <input type="button" id="userpollnot" name="pollnot" value=""> 
        </div>
    </div>
    <div id="userstable">
    </div>
</div>

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

<div id="tooltabslayer" class="yui-navset">

</div>

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

</body> 
</html> 
