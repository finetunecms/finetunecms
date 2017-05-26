<?php

return [
    'contact' => [
        'validation' => [
            'name' => 'required',
            'email' => 'email|required',
            'message' => 'required',
        ],
        'layout' => 'form',
        'success' => 'Your message has been sent, We will get in touch soon'
    ],
];