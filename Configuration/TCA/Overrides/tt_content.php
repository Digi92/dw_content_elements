<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$GLOBALS['TCA']['tt_content']['columns']['colPos']['config']['itemsProcFunc'] = 'Denkwerk\DwContentElements\Backend\ItemsProcFuncs\ColPosList->itemsProcFunc';
