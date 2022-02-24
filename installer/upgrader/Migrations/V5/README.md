# orangehrm

## Translation handling devtool

### Prerequisites

- A yml file with the language strings of the plugin
  - groupNameLangString.yaml => eg:- adminLangString.yaml
- export the messages.langCode.xml files to V5 folder

#### How to run

#### Set the filename and language code in TranslationTool.php
```
41  $filename = 'filepath of .xml language translation file';
42  $langCode = 'bg_BG';
```

#### Call TranslationTool->up() in runTranslate.php
```
$translate->up(string $groupName);
```
