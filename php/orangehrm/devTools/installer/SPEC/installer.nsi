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

;----------------------------------------------------------------------------------------------
; Installer Functions

; Admin user details
Function AdminUserDetailsEnter

    !insertmacro MUI_HEADER_TEXT "Admin User Creation" "After OrangeHRM is configured you will need an Administrator Account to Login into OrangeHRM."
    !insertmacro MUI_INSTALLOPTIONS_DISPLAY "AdminUserDetails.ini"

FunctionEnd

Function AdminUserDetailsEnterValidate

  !insertmacro MUI_INSTALLOPTIONS_READ $0 "AdminUserDetails.ini" "Field 2" "State"
  !insertmacro MUI_INSTALLOPTIONS_READ $1 "AdminUserDetails.ini" "Field 4" "State"
  !insertmacro MUI_INSTALLOPTIONS_READ $2 "AdminUserDetails.ini" "Field 6" "State"
  StrCmpS $1 $2 done error

  error:
        MessageBox MB_OK|MB_ICONEXCLAMATION "Password and Confirm Password don't match."
        Abort

  done:
        StrCpy $UserName "$0"
        md5dll::GetMD5String "$1"
        pop $PasswordHash

  Return

FunctionEnd

; Registration functions
Function ContactDetailsEnter

	!insertmacro MUI_HEADER_TEXT "Registration" "Please take a moment to register"
    !insertmacro MUI_INSTALLOPTIONS_DISPLAY "ContactDetails.ini"

FunctionEnd

Function ContactDetailsEnterValidate

  !insertmacro MUI_INSTALLOPTIONS_READ $0 "ContactDetails.ini" "Field 2" "State"
  !insertmacro MUI_INSTALLOPTIONS_READ $1 "ContactDetails.ini" "Field 4" "State"
  !insertmacro MUI_INSTALLOPTIONS_READ $2 "ContactDetails.ini" "Field 6" "State"
  !insertmacro MUI_INSTALLOPTIONS_READ $3 "ContactDetails.ini" "Field 8" "State"

  ${CheckUserEmailAddress} "$1" "$R1"

  StrCmpS $R1 "1" error done

  error:
  		MessageBox MB_OK|MB_ICONEXCLAMATION "E-mail address provided is invalid"
  		Abort

  done:
  		StrCpy $ContactName "$0"
  		StrCpy $ContactEmail "$1"
  		StrCpy $Coments "$2"
  		StrCpy $Updates "$3"

  		StrCpy $PostStr "userName=$ContactName&userEmail=$ContactEmail&userComments=$Coments&updates=$Updates"

  		;inetc::post "$PostStr" "http://www.orangehrm.com/registration/registerAcceptor.php" \

  		nsExec::ExecToLog '"$INSTDIR\php\php" "$INSTDIR\install\register.php" "$PostStr"'
  		Pop $0

  		StrCmpS $0 "0" success failedToSubmit

  failedToSubmit:
  		MessageBox MB_OK|MB_ICONEXCLAMATION "There was an error submitting the registration information"
  		Return

  success:
  		MessageBox MB_OK|MB_ICONINFORMATION "Your information was successfully received by OrangeHRM"

FunctionEnd

;--------------------------------
; Installer Sections

SectionGroup /e "OrangeHRM Appliance" SecGrpOrangeHRMAppliance

    Section "PHP" SecPHP

        SetOutPath "$INSTDIR\php"
        File /a /r "${SourceLocation}\${XamppPath}\php\"
        SetOutPath "$INSTDIR\tmp"
        File /a /r "${SourceLocation}\${XamppPath}\tmp\"

        Call buildUnixPath
        !insertmacro ReplaceInFile "$INSTDIR\php\php.ini" "?INSTDIRW" "$INSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\php\php.ini" "?INSTDIR" "$UNIXINSTDIR"

    SectionEnd

    Section "Apache" SecApache

        SetOutPath "$INSTDIR\apache"
        File /a /r "${SourceLocation}\${XamppPath}\apache\"
        SetOutPath "$INSTDIR\cgi-bin"
        File /a /r "${SourceLocation}\${XamppPath}\cgi-bin\"
        SetOutPath "$INSTDIR\htdocs"
        File /a /r "${SourceLocation}\${XamppPath}\htdocs\"

        Call buildUnixPath
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\httpd.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\httpd.conf" "?OrangeHRMPath" "${OrangeHRMPath}"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-ssl.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-mpm.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-multilang-errordoc.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-autoindex.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-languages.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-userdir.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-info.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-vhosts.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-manual.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-dav.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-default.conf" "?INSTDIR" "$UNIXINSTDIR"
        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\extra\httpd-xampp.conf" "?INSTDIR" "$UNIXINSTDIR"

    SectionEnd

    Section "MySQL" SecMySQL

        SetOutPath "$INSTDIR\mysql"
        File /a /r "${SourceLocation}\${XamppPath}\mysql\"

        Call buildUnixPath
        !insertmacro ReplaceInFile "$INSTDIR\mysql\bin\my.cnf" "?INSTDIR" "$UNIXINSTDIR"

    SectionEnd

    Section "OrangeHRM 2.6-beta.10" SecOrangeHRM

        SetOutPath "$INSTDIR\htdocs\${OrangeHRMPath}"
        File /a /r "${SourceLocation}\${OrangeHRMPath}\"
        File /a /r "${SourceLocation}\content\orangehrm2\"

    SectionEnd

SectionGroupEnd

Section "-Create Uninstaller"

      ;Create uninstaller
      WriteUninstaller "$INSTDIR\Uninstall.exe"

SectionEnd

Section "-XAMPP Files"

    SetOutPath "$INSTDIR\install"
    File /a /r "${SourceLocation}\${XamppPath}\install\"
    SetOutPath "$INSTDIR"
    File /a "${SourceLocation}\${XamppPath}\*.*"

SectionEnd

Section "-Licenses"

    SetOutPath "$INSTDIR\licenses"
    File /a /r "${SourceLocation}\${XamppPath}\licenses\"

SectionEnd

Section "-Register the application"

      ;Store installation folder
      WriteRegStr HKCU "Software\${ProductName}\${ProductVersion}" "" $INSTDIR

      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "DisplayName" "OrangeHRM - Opensource HR management"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "UninstallString" "$INSTDIR\uninstall.exe"

      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "InstallLocation" "$INSTDIR"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "VersionMajor" "2.2"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "VersionMinor" "2"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "DisplayVersion" "2.6-beta.10"

      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "Publisher" "${Organization}"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "HelpLink" "http://orangehrm.com/home/index.php?option=com_content&task=blogsection&id=13&Itemid=87"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "URLUpdateInfo" "http://sourceforge.net/project/showfiles.php?group_id=156477"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "URLInfoAbout" "http://orangehrm.com/home"

      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "NoModify" "1"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "NoRepair" "1"

      CreateDirectory "$SMPROGRAMS\${ProductName}\Documentation"
      CreateShortCut "$SMPROGRAMS\${ProductName}\Documentation\Installation Guide.lnk" "$INSTDIR\htdocs\${OrangeHRMPath}\installer\guide\index.html"
      CreateShortCut "$SMPROGRAMS\${ProductName}\Documentation\Upgrade Guide.lnk" "$INSTDIR\htdocs\${OrangeHRMPath}\upgrader\guide\index.html"
      CreateShortCut "$SMPROGRAMS\${ProductName}\Documentation\FAQ.lnk" "$INSTDIR\htdocs\${OrangeHRMPath}\faq.html"
      CreateDirectory "$SMPROGRAMS\${ProductName}"
      CreateShortCut "$SMPROGRAMS\${ProductName}\XAMPP.lnk" "$INSTDIR\xampp-control.exe"

SectionEnd

Section "-Install Services"

      SetOutPath "$INSTDIR"
      ; Setup XAMPP
      DetailPrint "Setting up XAMPP"
      nsExec::ExecToLog '"$INSTDIR\setup_xampp.bat" -path "$INSTDIR"'

      SetOutPath "$INSTDIR\mysql"
      ; Register the db server as a service
      DetailPrint "Installing MySQL database server as a service"
      nsExec::ExecToLog '"$INSTDIR\mysql\mysql_installservice.bat" -path "$INSTDIR\mysql"'

      SetOutPath "$INSTDIR\apache"
      ; Register the web server as a service
      DetailPrint "Installing Apache web server as a service"
      CopyFiles "$INSTDIR\php\php.ini" "$INSTDIR\apache\bin"
      nsExec::ExecToLog '"$INSTDIR\apache\apache_installservice.bat" -path "$INSTDIR\apache"'

SectionEnd

SectionGroup /e "Extras" SecGrpExtraComponents

    Section "Sendmail" SecSendmail

        SetOutPath "$INSTDIR\sendmail"
        File /a /r "${SourceLocation}\${XamppPath}\sendmail\"

    SectionEnd

    Section "phpMyAdmin" SecPhpMyAdmin

        SetOutPath "$INSTDIR\phpMyAdmin"
        File /a /r "${SourceLocation}\${XamppPath}\phpMyAdmin\"

    SectionEnd

SectionGroupEnd

Section "-Complete"

      SetOutPath "$INSTDIR\htdocs\orangehrm-${ProductVersion}"

      DetailPrint "Creating OrangeHRM database"
      nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -e "CREATE DATABASE hr_mysql;"'

      DetailPrint "Creating OrangeHRM tables"
      nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -D hr_mysql -e "source $INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-1.sql"'

      DetailPrint "Filling required data"
      nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -D hr_mysql -e "source $INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-2.sql"'

      !insertmacro ReplaceInFile "$INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-user.sql" "?UserName" "$UserName"
      !insertmacro ReplaceInFile "$INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-user.sql" "?PasswordHash" "$PasswordHash"

      DetailPrint "Creating the admin user"
      nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -D hr_mysql -e "source $INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-user.sql"'

      Delete /REBOOTOK "$INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-user.sql"

SectionEnd

SectionGroup /e "XAMPP Components" SecGrpXamppComponents

    Section "Webalizer" SecWebalizer

        SetOutPath "$INSTDIR\webalizer"
        File /a /r "${SourceLocation}\${XamppPath}\webalizer\"

    SectionEnd

    Section "FileZillaFTP" SecFileZillaFTP

        SetOutPath "$INSTDIR\FileZillaFTP"
        File /a /r "${SourceLocation}\${XamppPath}\FileZillaFTP\"
        SetOutPath "$INSTDIR\anonymous"
        File /a /r "${SourceLocation}\${XamppPath}\anonymous\"

    SectionEnd

    Section "MercuryMail" SecMercuryMail

        SetOutPath "$INSTDIR\MercuryMail"
        File /a /r "${SourceLocation}\${XamppPath}\MercuryMail\"

    SectionEnd

    Section "perl" SecPerl

        SetOutPath "$INSTDIR\perl"
        File /a /r "${SourceLocation}\${XamppPath}\perl\"

    SectionEnd

    Section "webdav" SecWebdav

        SetOutPath "$INSTDIR\webdav"
        File /a /r "${SourceLocation}\${XamppPath}\webdav\"

    SectionEnd

SectionGroupEnd

Section "Demo data" SecDemoData

    SetOutPath "$INSTDIR\mysql\data\hr_mysql"

SectionEnd

Function .onInit
  !insertmacro MUI_INSTALLOPTIONS_EXTRACT "AdminUserDetails.ini"
  !insertmacro MUI_INSTALLOPTIONS_EXTRACT "ContactDetails.ini"

  ; Mandatory sections
  SectionSetFlags ${SecApache} 17
  SectionSetFlags ${SecMySQL} 17
  SectionSetFlags ${SecPHP} 17
  SectionSetFlags ${SecOrangeHRM} 17

  ; Optional sections
  SectionSetFlags ${SecWebalizer} 0
  SectionSetFlags ${SecFileZillaFTP} 0
  SectionSetFlags ${SecMercuryMail} 0
  SectionSetFlags ${SecPerl} 0
  SectionSetFlags ${SecWebdav} 0

  SectionSetFlags ${SecDemoData} 16

FunctionEnd

;--------------------------------
; Descriptions

  ; Language strings
  LangString DESC_SecGrpOrangeHRMAppliance ${LANG_ENGLISH} "OrangeHRM and all pre-requisities"

  LangString DESC_SecApache ${LANG_ENGLISH} "Apache web server"
  LangString DESC_SecMySQL ${LANG_ENGLISH} "MySQL database server"
  LangString DESC_SecPHP ${LANG_ENGLISH} "PHP Hypertext Preprocessor"
  LangString DESC_SecOrangeHRM ${LANG_ENGLISH} "OrangeHRM 2.6-beta.10"
  LangString DESC_SecGrpExtraComponents ${LANG_ENGLISH} "Extra components to make OrangeHRM better"

  LangString DESC_SecSendmail ${LANG_ENGLISH} "Sendmail mail transfer agent"
  LangString DESC_SecPhpMyAdmin ${LANG_ENGLISH} "PHP based MySQL admin Interface"

  LangString DESC_SecGrpXamppComponents ${LANG_ENGLISH} "Components found in XAMPP, not necessary for OrangeHRM"

  LangString DESC_SecWebalizer ${LANG_ENGLISH} "Webalizer server log analizer"
  LangString DESC_SecFileZillaFTP ${LANG_ENGLISH} "FileZillaFTP  FTP server"
  LangString DESC_SecMercuryMail ${LANG_ENGLISH} "MercuryMail mail server"
  LangString DESC_SecPerl ${LANG_ENGLISH} "Perl is a dynamic programming language"
  LangString DESC_SecWebDav ${LANG_ENGLISH} "Web-based Distributed Authoring and Versioning"

  LangString DESC_SecDemoData ${LANG_ENGLISH} "Data for demonstrations"


  ; Assign language strings to sections
  !insertmacro MUI_FUNCTION_DESCRIPTION_BEGIN

  !insertmacro MUI_DESCRIPTION_TEXT ${SecGrpOrangeHRMAppliance} $(DESC_SecGrpOrangeHRMAppliance)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecApache} $(DESC_SecApache)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecMySQL} $(DESC_SecMySQL)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecPHP} $(DESC_SecPHP)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecOrangeHRM} $(DESC_SecOrangeHRM)

  !insertmacro MUI_DESCRIPTION_TEXT ${SecGrpExtraComponents} $(DESC_SecGrpExtraComponents)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecPhpMyAdmin} $(DESC_SecPhpMyAdmin)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecSendmail} $(DESC_SecSendmail)

  !insertmacro MUI_DESCRIPTION_TEXT ${SecGrpXamppComponents} $(DESC_SecGrpXamppComponents)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecWebalizer} $(DESC_SecWebalizer)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecFileZillaFTP} $(DESC_SecFileZillaFTP)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecMercuryMail} $(DESC_SecMercuryMail)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecPerl} $(DESC_SecPerl)
  !insertmacro MUI_DESCRIPTION_TEXT ${SecWebDav} $(DESC_SecWebDav)

  !insertmacro MUI_DESCRIPTION_TEXT ${SecDemoData} $(DESC_SecDemoData)

  !insertmacro MUI_FUNCTION_DESCRIPTION_END
