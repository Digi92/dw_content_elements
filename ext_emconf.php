<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "dw_content_elements".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Content Elements',
	'description' => 'Custom content elements',
	'category' => 'misc',
	'shy' => 0,
	'version' => '1.0.0',
	'dependencies' => 'cms,extbase',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Marcel Wieser',
	'author_email' => 'typo3dev@marcel-wieser.de',
	'author_company' => 'denkwerk GmbH',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.00-6.2.99',
			'cms' => '',
			'extbase' => '',
			'vhs' => '2.2.0-2.9.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:0:{}',
	'suggests' => array(
	),
);
