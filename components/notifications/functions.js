// Namespace for Notifications
Namespace('SC.notifications');

SC.notifications.lastId = 0;

// Function to add messages to the notifications Area
// If recording, we must save in the server via AJAX the notification (if it's public, not private)

SC.components.resources.initLayout = function(){
    return false;
}