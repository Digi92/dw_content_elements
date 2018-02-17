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
    'elementWizardTabTitle' => 'My content elements',
    'addElementsToWizard' => TRUE
];

```

### Options

There are some mandatory options:

- `pluginName`: The name of the plugin
- `pluginCategory`: The plugins category (optgroup)
- `controllerActions`: The controller actions
- `namespace`: The plugins namespace, vendor and plugin namespace separated by dots

It is recommended that you set you own `elementWizardTabTitle` for better overview.
