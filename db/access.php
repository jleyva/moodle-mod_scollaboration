<?php
//
// Capability definitions for the scollaboration module.
//


$capabilities = array(

    'mod/scollaboration:collaborate' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/scollaboration:moderate' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'student' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
);

?>
