<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "dw_content_elements_source".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['dw_content_elements_source'] = array(
    'title' => 'Content Elements Source',
    'description' => 'This extension included your created content elements.',
    'category' => 'misc',
    'shy' => 0,
    'version' => '1.0.15',
    'dependencies' => 'cms,extbase',
    'conflicts' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearcacheonload' => 1,
    'lockType' => '',
    'author' => 'Sascha Zander',
    'author_email' => 'sascha.zander@denkwerk.com',
    'author_company' => '',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'priority' => 'bottom',
    'constraints' => array(
        'depends' => array(
            'typo3' => '8.7.0-10.4.99',
            'cms' => '',
            'extbase' => '',
            'dw_content_elements' => '',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
    '_md5_values_when_last_written' => 'a:0:{}',
    'suggests' => array(),
);
