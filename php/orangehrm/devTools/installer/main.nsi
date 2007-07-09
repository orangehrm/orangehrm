;NSIS Modern User Interface
;Basic Example Script
;Written by Joost Verburg

;--------------------------------
;Include Modern UI

  !include "MUI.nsh"

;--------------------------------
;General

  ; Macros

  !include "Include\WordFunc.nsh"
  !insertmacro WordReplace

  !include "Include\StrRep.nsh"
  !include "Include\ReplaceInFile.nsh"

  ; Product Details
  !define ProductName "OrangeHRM"
  !define ProductVersion "2.2"

  !define Organization "OrangeHRM Inc."

  !define SourceLocation "D:\source"
  !define OrangeHRMPath "orangehrm2"
  !define XamppPath "xampp"


  ;Name and file
  Name "${ProductName} ${ProductVersion}"
  OutFile "build\OrangeHRM-2.2.exe"

  ;Default installation folder
  InstallDir "$PROGRAMFILES\OrangeHRM\${ProductVersion}"

  ;Get installation folder from registry if available
  InstallDirRegKey HKCU "Software\OrangeHRM\${ProductVersion}" ""

  ; MUI Settings / Icons
  !define MUI_ICON "${NSISDIR}\Contrib\Graphics\Icons\orange-install.ico"
  !define MUI_UNICON "${NSISDIR}\Contrib\Graphics\Icons\orange-uninstall.ico"

  ; MUI Settings / Header
  !define MUI_HEADERIMAGE
  !define MUI_HEADERIMAGE_RIGHT
  !define MUI_HEADERIMAGE_BITMAP "${NSISDIR}\Contrib\Graphics\Header\orange-r.bmp"
  !define MUI_HEADERIMAGE_UNBITMAP "${NSISDIR}\Contrib\Graphics\Header\orange-uninstall-r.bmp"

  ; MUI Settings / Wizard
  !define MUI_WELCOMEFINISHPAGE_BITMAP "${NSISDIR}\Contrib\Graphics\Wizard\orange.bmp"
  !define MUI_UNWELCOMEFINISHPAGE_BITMAP "${NSISDIR}\Contrib\Graphics\Wizard\orange-uninstall.bmp"

;--------------------------------
;Interface Settings

  !define MUI_ABORTWARNING

  BrandingText "${Organization}"

;--------------------------------
;Pages

  !insertmacro MUI_PAGE_WELCOME
  !insertmacro MUI_PAGE_LICENSE ".\content\license.txt"
  !insertmacro MUI_PAGE_COMPONENTS
  !insertmacro MUI_PAGE_DIRECTORY
  !insertmacro MUI_PAGE_INSTFILES
  ;!insertmacro MUI_PAGE_FINISH

  !insertmacro MUI_UNPAGE_WELCOME
  !insertmacro MUI_UNPAGE_CONFIRM
  !insertmacro MUI_UNPAGE_INSTFILES
  ;!insertmacro MUI_UNPAGE_FINISH

;--------------------------------
;Languages

  !insertmacro MUI_LANGUAGE "English"

;--------------------------------
;Installer Sections

Function buildUnixPath

    Var /GLOBAL UNIXINSTDIR
    ${WordReplace} "$INSTDIR" "\" "/" "+" $UNIXINSTDIR

FunctionEnd

!include "installer.nsi"

;Uninstaller Section

!include "uninstaller.nsi"