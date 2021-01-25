# Generate and Maintain Labels
This tool is used to generate and maintain labels.
The labels are used in 'Help' module to redirect a user to the correct article in Zendesk.

This is designed to work only with Zendesk.

Changes of the labels will be logged in _devTools/labels/diff.json_

__Note:__ This tool is not designed to run on all the platforms.

## Generate Labels for screens

Use the tool to generate labels. Follow the given steps correctly. This tool will guide you to create labels both angular and non-angular screens.

Command to generate diff of labels: __php generate-labels.php --generate-diff true__

Command to generate current labels: __php generate-labels.php --keep-new-files true__

__Note:__ The tool will generate labels for all screens in one attempt.

## How to maintain
1. Run the command with _keep-temp-files_ option

Command: __php generate-labels.php --generate-diff true --keep-new-files true__

This will generate _symfony_labels.json_ with all the currently available routes and the label names.

Before creating the last build of a release generate these files and copy the content in to _original_symfony_labels.json_.
Commit that file to the repository.

__Note:__ Please do not commit temporary created files and diff.json file
