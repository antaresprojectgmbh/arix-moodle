<?php

$capabilities = array(
    'repository/arix:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'user' => CAP_ALLOW 
        )
    )
);