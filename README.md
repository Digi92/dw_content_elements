# dw content elements

This TYPO3 extension is about defining new content elements by just implementing two
files in your own extension.

## Configuration

There is an configuration array with the extension key in `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']`
where you can define your own providers of content elements. Here is an example:

```php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dw_content_elements']['providers']['my_content_elements'] = [
    'pluginName' => 'MyContentElements',
    'pluginCategory' => 'My own content elements',
    'controllerActions' => ['Elements' => 'render'],
    'namespace' => 'Denkwerk.MyContentElements',
    'elementsPath' => '/Configuration/Elements',
    'addElementsToWizard' => TRUE,
    'elementWizardTabTitle' => 'My content elements'
];

```

### Options

There are some mandatory options:

- `pluginName`: The name of the plugin
- `pluginCategory`: The plugins category (optgroup)
- `controllerActions`: The controller actions
- `namespace`: The plugins namespace, vendor and plugin namespace separated by dots
- `addElementsToWizard`: If true, add a tab with the elements of this provider into the new elements wizard
- `elementWizardTabTitle`: The label of the new elements wizard tab

It is recommended that you set you own `elementWizardTabTitle` for better overview.
