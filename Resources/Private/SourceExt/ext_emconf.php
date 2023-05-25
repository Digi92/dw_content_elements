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
    'version' => '1.0.15',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author' => 'Sascha Zander',
    'author_email' => 'sascha.zander@denkwerk.com',
    'author_company' => '',
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
);
