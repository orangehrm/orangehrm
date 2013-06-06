
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
all the essential functionalities required for any enterprise. 
Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com/

OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
the GNU General Public License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

Installing
----------
1. Install OrangeHRM using the web installer

Deprecated Notes
----------------

The source that you have downloaded(ZIP archive) in which this was enclosed, is of the 
directory structure as follows; ( since release of OrangeHRM version 0.1 we have altered 
the directory structure, for further details please refer to the changelog.txt


|
-dbscript
|	|
|	-- (database script for MySQL)
|
-installer (contains the Web-Installer)
|
-language (contains the Language Packs)
|
-lib
|	|
|	-- common (commonly used objects and methods)
|	-- confs (Configuration files)
|	-- controllers (Primary & Secondary Controllers)
|			CentralController.php (Main Controller)
|			EmpViewController.php (Secondary Controller for PIM Module)
|			MTViewController.php (Secondary Controller for Maintenance Module)
|			RepViewController.php (Secondary Controller for Reports Module)
|			ViewController.php (Secondary Controller for Admin Module)
|
|	-- dao (Data Access Objects)			
|	-- exception (Exception Handlers for Database Exceptions)
|	-- extractor (Data Extractors)
|				|
|				-- eimadmin (Data Extractors for Admin Module)
|				-- hrfunct (Data Extractors for PIM Module)
|				-- maintenance (Data Extractors for Maintenance Module)
|				-- report (Data Extractors for Report Module)
|				
|	-- logs (Log & LogFileWriter)
|	-- Models (Models)
|			|
|			-- eimadmin (Admin Module Objects)
|			-- hrfunct (PIM Module Objects)
|			-- maintenance (Application Maintenance Objects)
|			-- report (Report Module Objects)
|	
-license
|	|
|	-- (The GPL license)
|
-scripts
|	|
|	-- (Javascript files, style sheets required by UI)
|
-templates
|	|
|	-- eimadmin (Admin Module UI templates)
|	-- hrfunct (PIM Module UI templates)
|	-- maintenance (Maintenance Module UI templates)
|	-- report (Report Module UI templates)
|
-themes
	|
	-- (pictures + style sheets)


For further information on how to use the product please refer the Free User Guide 
available on http://orangehrm.com/

In case Installation difficulties there is a seperate Installation Guides also 
available from the same source. 

For Apache/PHP/MySQL Installation issues please refer to the Environment Setup Guide
For OrangeHRM Installation issues please refer to the OrangeHRM Installation Guide
