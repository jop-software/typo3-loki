<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Grafana Loki integration for TYPO3',
    'description' => 'Transport or prepare TYPO3 log messages for Loki.',
    'category' => 'plugin',
    'author' => 'Johannes Przymusinski',
    'author_email' => 'johannes.przymusinski@jop-software.de',
    'author_company' => 'jop-software Inh. Johannes Przymusinski',
    'state' => 'misc',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.99.99',
            'php' => '7.4.0-8.1.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];