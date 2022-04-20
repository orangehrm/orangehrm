# orangehrm

## Translation generating devtool

### Prerequisites

- Create a new folder named translation inside devTools/core/src/Command/Util/
- Add the messages.langCode.xml files inside it.

### How to run

#### Make sure the $langCodes array items in devTools/core/src/Command/Util/TranslationGenerateTool.php are corresponding to the xml files
```
53   $langCodes = ['bg_BG', 'da_DK'];
```
Then both 
* message.bg_BG.xml 
* message.da_DK.xml 

should be inside devTools/core/src/Command/Util/translation directory

#### Then run the console command 

```
php devTools/core/console.php add-translations
```
