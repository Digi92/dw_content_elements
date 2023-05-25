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
    'version' => '12.0.0',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author' => 'Sascha Zander',
    'author_email' => 'sascha.zander@denkwerk.com',
    'author_company' => '',
    'constraints' => array(
        'depends' => array(
            'typo3' => '12.4.1-12.4.99',
            'extbase' => '',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
    '_md5_values_when_last_written' => 'a:0:{}',
);
