;----------------------------------------------------------------------------------------------
; OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
; all the essential functionalities required for any enterprise.
; Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
;
; OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
; the GNU General Public License as published by the Free Software Foundation; either
; version 2 of the License, or (at your option) any later version.
;
; OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
; See the GNU General Public License for more details.
;
; You should have received a copy of the GNU General Public License along with this program;
; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
; Boston, MA  02110-1301, USA
;----------------------------------------------------------------------------------------------

;---------------------------------------------------------
; OrangeHRM Appliance for Windows NSIS installer script
; Uses NSIS Modern User Interface
;
; Modified by Mohanjith
;
; See readme in this folder for details on how to use this
; script to compile the installer.
;----------------------------------------------------------

;--------------------------------
; Product Details

  !define ProductName "OrangeHRM"
  !define ProductVersion "2.6.2"

  !define Organization "OrangeHRM Inc."

  ; Register details

  BrandingText "${Organization}"
  Name "${ProductName} ${ProductVersion}"

;--------------------------------
; Directory structure

  !define SourceLocation "../SOURCE"
  !define OrangeHRMPath "orangehrm-${ProductVersion}"
  !define XamppPath "xampp"

;--------------------------------
; Output

  OutFile "../../orangehrm-${ProductVersion}.exe"

;--------------------------------
; Includes

  ; Modern UI

  !include "MUI.nsh"

  ; Macros

  !include "Include\WordFunc.nsh"
  !include "Include\StrRep.nsh"
  !include "Include\ReplaceInFile.nsh"
  !include "Include\CheckUserEmailAddress.nsh"

  ; InstallOptions

  ReserveFile "AdminUserDetails.ini"
  ReserveFile "ContactDetails.ini"

;--------------------------------
; Register macros

  !insertmacro WordReplace
  !insertmacro MUI_RESERVEFILE_INSTALLOPTIONS

;--------------------------------
; Global variables

  Var /GLOBAL UserName
  Var /GLOBAL PasswordHash

  Var /GLOBAL ContactName
  Var /GLOBAL ContactEmail
  Var /GLOBAL Coments
  Var /GLOBAL Updates
  Var /GLOBAL PostStr

;--------------------------------
;General

  ; Default installation folder
  InstallDir "$PROGRAMFILES\OrangeHRM\${ProductVersion}"

  ; Get installation folder from registry if available
  InstallDirRegKey HKCU "Software\OrangeHRM\${ProductVersion}" ""

;--------------------------------
; Interface Settings

  ; Icons
  !define MUI_ICON "${NSISDIR}\Contrib\Graphics\Icons\orange-install.ico"
  !define MUI_UNICON "${NSISDIR}\Contrib\Graphics\Icons\orange-uninstall.ico"

  ; Header
  !define MUI_HEADERIMAGE
  !define MUI_HEADERIMAGE_RIGHT
  !define MUI_HEADERIMAGE_BITMAP "${NSISDIR}\Contrib\Graphics\Header\orange-r.bmp"
  !define MUI_HEADERIMAGE_UNBITMAP "${NSISDIR}\Contrib\Graphics\Header\orange-uninstall-r.bmp"

  ; Wizard
  !define MUI_WELCOMEFINISHPAGE_BITMAP "${NSISDIR}\Contrib\Graphics\Wizard\orange.bmp"
  !define MUI_UNWELCOMEFINISHPAGE_BITMAP "${NSISDIR}\Contrib\Graphics\Wizard\orange-uninstall.bmp"

  !define MUI_ABORTWARNING

;--------------------------------
; Pages

  !insertmacro MUI_PAGE_WELCOME
  !insertmacro MUI_PAGE_LICENSE "${SourceLocation}\content\license.txt"
  !insertmacro MUI_PAGE_COMPONENTS
  Page custom AdminUserDetailsEnter AdminUserDetailsEnterValidate
  !insertmacro MUI_PAGE_DIRECTORY
  !insertmacro MUI_PAGE_INSTFILES
  Page custom ContactDetailsEnter ContactDetailsEnterValidate
  !insertmacro MUI_PAGE_FINISH

  !insertmacro MUI_UNPAGE_WELCOME
  !insertmacro MUI_UNPAGE_CONFIRM
  !insertmacro MUI_UNPAGE_INSTFILES
  !insertmacro MUI_UNPAGE_FINISH

;--------------------------------
; Languages

  !insertmacro MUI_LANGUAGE "English"

;--------------------------------
; Utility functions
  Function buildUnixPath

    Var /GLOBAL UNIXINSTDIR
    ${WordReplace} "$INSTDIR" "\" "/" "E+" $UNIXINSTDIR

  FunctionEnd

;--------------------------------
; Installer Sections

!include "installer.nsi"

;--------------------------------
; Uninstaller Sections

!include "uninstaller.nsi"
