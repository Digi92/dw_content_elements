<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_dwc_related_link_item'] = array(
	'ctrl' => $TCA['tx_dwc_related_link_item']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'l10n_parent, l10n_diffsource, link_text, link',
	),
	'types' => array(
		'1' => array('showitem' => 'l10n_parent, l10n_diffsource, link_text, link,
		--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_dwc_related_link_item',
				'foreign_table_where' => 'AND tx_dwc_related_link_item.pid=###CURRENT_PID### AND tx_dwc_related_link_item.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'link_text' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:dw_content_elements/Resources/Private/Language/locallang_db.xml:tx_dwc_related_link_item.link_text',
			'exclude' => 1,
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
			'defaultExtras' => 'richtext:rte_transform[flag=rte_enabled|mode=ts_css]'
		),
        'link' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:dw_content_elements/Resources/Private/Language/locallang_db.xml:tx_dwc_related_link_item.link',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'softref' => 'typolink[linklist]',
                'wizards' => array(
                    '_PADDING' => 2,
                    'link' => array(
                        'type' => 'popup',
                        'title' => 'Link',
                        'icon' => 'link_popup.gif',
                        'script' => 'browse_links.php?mode=wizard',
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
                    )
                ),
                'eval' => 'trim,required'
            ),
        ),
	),
);

?>
