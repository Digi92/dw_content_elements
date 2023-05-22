<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "dw_content_elements".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['dw_content_elements'] = array(
    'title' => 'Content Elements',
    'description' => 'Custom content elements',
    'category' => 'misc',
    'shy' => 0,
    'version' => '3.0.0',
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
    'priority' => 'top',
    'constraints' => array(
        'depends' => array(
            'typo3' => '12.0-12.4.99',
            'extbase' => '',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
    '_md5_values_when_last_written' => 'a:0:{}',
    'suggests' => array(),
);
