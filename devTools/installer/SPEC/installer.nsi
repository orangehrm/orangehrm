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


Function VerifyRegister
		
   !insertmacro MUI_INSTALLOPTIONS_DISPLAY "ContactDetails.ini"
                  
                  
FunctionEnd
; Registration functions

Function ContactDetailsEnterValidate


  !insertmacro MUI_INSTALLOPTIONS_READ $0 "ContactDetails.ini" "Field 2" "State"
  !insertmacro MUI_INSTALLOPTIONS_READ $1 "ContactDetails.ini" "Field 3" "State"
  
  ${CheckUserEmailAddress} "$1" "$R1"

  StrCmpS $R1 "1" error done

  error:
  		MessageBox MB_OK|MB_ICONEXCLAMATION "Please Prvide a valid email address. eg: myid@.com"
  		Abort

  done:
  		;StrCpy $CompanyName "$0"
  		;StrCpy $Consent "$1"
  		
		

  		inetc::post "$PostStr" "http://www.orangehrm.com/registration/registerAcceptor.php" "$INSTDIR\output.txt" /END

                nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -D orangehrm_mysql -e "INSERT INTO `ohrm_organization_gen_info`(`name`) VALUES ('$CompanyName')"'
                nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -D orangehrm_mysql -e "INSERT INTO `ohrm_organization_gen_info`(`name`) VALUES ('$CompanyName')"'
  		;nsExec::ExecToLog '"$INSTDIR\php\php" "$INSTDIR\install\register.php" "$PostStr"'
  		Pop $0
  		StrCmpS $0 "OK" success failedToSubmit
		

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
        File /a /r "${SourceLocation}\content\mysql_installservice.bat"

        Call buildUnixPath
        !insertmacro ReplaceInFile "$INSTDIR\mysql\bin\my.cnf" "?INSTDIR" "$UNIXINSTDIR"

    SectionEnd

    Section "OrangeHRM 4.0" SecOrangeHRM

        SetOutPath "$INSTDIR\htdocs\${OrangeHRMPath}"
        File /a /r "${SourceLocation}\${OrangeHRMPath}\"
        File /a /r "${SourceLocation}\content\orangehrm2\"

        SetOutPath "$INSTDIR"
        File /a "${SourceLocation}\content\logo.ico"
        File /a "${SourceLocation}\content\start.vbs"
        File /a "${SourceLocation}\content\xampp-control.ini"

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
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "VersionMajor" "3.3"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "VersionMinor" "2"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "DisplayVersion" "3.3"

      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "Publisher" "${Organization}"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "HelpLink" "http://orangehrm.com/home/index.php?option=com_content&task=blogsection&id=13&Itemid=87"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "URLUpdateInfo" "http://sourceforge.net/project/showfiles.php?group_id=156477"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "URLInfoAbout" "http://orangehrm.com/home"

      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "NoModify" "1"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "NoRepair" "1"

      CreateDirectory "$SMPROGRAMS\${ProductName}\Documentation"
      CreateShortCut "$SMPROGRAMS\${ProductName}\Documentation\Installation Guide.lnk" "$INSTDIR\htdocs\${OrangeHRMPath}\installer\guide\index.html"
      CreateShortCut "$SMPROGRAMS\${ProductName}\Documentation\FAQ.lnk" "$INSTDIR\htdocs\${OrangeHRMPath}\faq.html"
      CreateDirectory "$SMPROGRAMS\${ProductName}"
      CreateShortCut "$SMPROGRAMS\${ProductName}\XAMPP.lnk" "$INSTDIR\xampp-control.exe"
      CreateShortCut "$SMPROGRAMS\${ProductName}\OrangeHRM.lnk" "$INSTDIR\start.vbs" ""  "${SHORTCUT_ICON}"
      CreateShortCut "$SMPROGRAMS\${ProductName}\Uninstall.lnk" "$INSTDIR\Uninstall.exe"
        CreateShortCut "$DESKTOP\${ProductName}.lnk" "$INSTDIR\start.vbs" ""  "${SHORTCUT_ICON}"


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
      
      ; Create encryption key
      ; Based on installUtil.php. Concat 4 MD5 Sums
      pwgen::GeneratePassword 60
      pop $0
      md5dll::GetMD5String "$0"
      pop $1

      pwgen::GeneratePassword 60
      pop $0
      md5dll::GetMD5String "$0"
      pop $2

      pwgen::GeneratePassword 60
      pop $0
      md5dll::GetMD5String "$0"
      pop $3

      pwgen::GeneratePassword 60
      pop $0
      md5dll::GetMD5String "$0"
      pop $4

      StrCpy $5 "$1$2$3$4"

      ${WriteToFile} "$INSTDIR\htdocs\orangehrm-${ProductVersion}\lib\confs\cryptokeys\key.ohrm" "$5"

      DetailPrint "Creating OrangeHRM database"
      nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -e "CREATE DATABASE orangehrm_mysql;"'

      DetailPrint "Creating OrangeHRM tables"
      nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -D orangehrm_mysql -e "source $INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-1.sql"'

      DetailPrint "Filling required data"
      nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -D orangehrm_mysql -e "source $INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-2.sql"'

      !insertmacro ReplaceInFile "$INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-user.sql" "?UserName" "$UserName"
      !insertmacro ReplaceInFile "$INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-user.sql" "?PasswordHash" "$PasswordHash"

      DetailPrint "Creating the admin user"
      nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -D orangehrm_mysql -e "source $INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-user.sql"'

      Delete /REBOOTOK "$INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-user.sql"
	  
	  DetailPrint "Installing Functions"
	  nsExec::ExecToLog '"$INSTDIR\mysql\bin\mysql" -u root -D orangehrm_mysql -e "source $INSTDIR\htdocs\orangehrm-${ProductVersion}\dbscript\dbscript-functions.sql"'
	  
      DetailPrint "Registering Product"
      inetc::post "register" "http://127.0.0.1/orangehrm-3.3/installer/registrationMessage.php" "$INSTDIR\output.txt" 

SectionEnd

;SectionGroup /e "XAMPP Components" SecGrpXamppComponents
;
;    Section "Webalizer" SecWebalizer
;
;        SetOutPath "$INSTDIR\webalizer"
;        File /a /r "${SourceLocation}\${XamppPath}\webalizer\"
;
;    SectionEnd
;
;    Section "FileZillaFTP" SecFileZillaFTP
;
;        SetOutPath "$INSTDIR\FileZillaFTP"
;        File /a /r "${SourceLocation}\${XamppPath}\FileZillaFTP\"
;        SetOutPath "$INSTDIR\anonymous"
;        File /a /r "${SourceLocation}\${XamppPath}\anonymous\"
;
;    SectionEnd
;
;    Section "MercuryMail" SecMercuryMail
;
;        SetOutPath "$INSTDIR\MercuryMail"
;        File /a /r "${SourceLocation}\${XamppPath}\MercuryMail\"
;
;    SectionEnd
;
;    Section "perl" SecPerl
;
;        SetOutPath "$INSTDIR\perl"
;        File /a /r "${SourceLocation}\${XamppPath}\perl\"
;
;    SectionEnd
;
;    Section "webdav" SecWebdav
;
;        SetOutPath "$INSTDIR\webdav"
;        File /a /r "${SourceLocation}\${XamppPath}\webdav\"
;
;    SectionEnd
;
;SectionGroupEnd

;Section "Demo data" SecDemoData
;
;    SetOutPath "$INSTDIR\mysql\data\orangehrm_mysql"
;
;SectionEnd

Function .onInit
		MessageBox MB_OK "If you encounter issues in running OrangeHRM, try disabling your virus guard temporarily. Visit www.orangehrm.com/exe-faq.shtml for more details."
         #MessageBox MB_OK "httpd running"
         
         Push "Status"
         Push "Apache2.2"
         Push ""
         Call Service
         Pop $0 ;response

${If} $0 == "stopped"
         MessageBox MB_OK|MB_ICONSTOP "Apache web server is already installed. Please consider using OrangeHRM web installer with ZIP version. Visit www.orangehrm.com/exe-faq.shtml for more details."
         Abort
${EndIf}
${If} $0 == "running" 


         MessageBox MB_OK|MB_ICONSTOP "Apache web server is already installed. Please consider using OrangeHRM web installer with ZIP version. Visit www.orangehrm.com/exe-faq.shtml for more details."
         Abort
         ${Else}
         Push "Status"
         Push "mysql"
         Push ""
         Call Service
         Pop $1 ;response
         ${If} $1 == "running"
         MessageBox MB_OK|MB_ICONSTOP "MySQL is already installed. Please consider using OrangeHRM web installer with ZIP version. Visit www.orangehrm.com/exe-faq.shtml for more details."
         Abort
         ${EndIf}
${EndIf}
${If} ${TCPPortOpen} 80
	 GetTempFileName $0
     File /oname=$0 `TestPort80.vbs` 
     nsExec::ExecToStack `"$SYSDIR\CScript.exe" $0 //e:vbscript //B //NOLOGO`
	 Pop $0
	 Pop $1
	 MessageBox MB_OK|MB_ICONSTOP '$1'
	 Abort
${EndIf}

; Missing MS C++ 2008 runtime library warning here
  ReadRegStr $R2 HKLM 'SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\{FF66E9F6-83E7-3A3E-AF14-8DE9A809A6A4}' DisplayVersion
  ReadRegStr $R3 HKLM 'SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\{350AA351-21FA-3270-8B7A-835434E766AD}' DisplayVersion
  ReadRegStr $R4 HKLM 'SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\{2B547B43-DB50-3139-9EBE-37D419E0F5FA}' DisplayVersion

  ReadRegStr $R5 HKLM 'SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\{9A25302D-30C0-39D9-BD6F-21E6EC160475}' DisplayVersion
  ReadRegStr $R6 HKLM 'SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\{8220EEFE-38CD-377E-8595-13398D740ACE}' DisplayVersion
  ReadRegStr $R7 HKLM 'SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\{5827ECE1-AEB0-328E-B813-6FC68622C1F9}' DisplayVersion

  ReadRegStr $R8 HKLM 'SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\{1F1C2DFC-2D24-3E06-BCB8-725134ADF989}' DisplayVersion
  ReadRegStr $R9 HKLM 'SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\{4B6C7001-C7D6-3710-913E-5BC23FCE91E6}' DisplayVersion
  ReadRegStr $R0 HKLM 'SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\{977AD349-C2A8-39DD-9273-285C08987C7B}' DisplayVersion  
  
  StrCmp $R2 "" vc9_test2
  GOTO init_end
  vc9_test2:
  StrCmp $R3 "" vc9_test3
  GOTO init_end
  vc9_test3:
  StrCmp $R4 "" vc9_test4
  GOTO init_end
  vc9_test4:
  StrCmp $R5 "" vc9_test5
  GOTO init_end
  vc9_test5:
  StrCmp $R6 "" vc9_test6
  GOTO init_end
  vc9_test6:
  StrCmp $R7 "" vc9_test7
  GOTO init_end
  vc9_test7:
  StrCmp $R8 "" vc9_test8
  GOTO init_end
  vc9_test8:
  StrCmp $R9 "" vc9_test9
  GOTO init_end
  vc9_test9:
  StrCmp $R0 "" no_vc9
  GOTO init_end

  no_vc9:
    MessageBox MB_YESNO "Warning: XAMPP (PHP) cannot work without the Microsoft Visual C++ 2008 Redistributable Package. Now open the Microsoft page for this download?" IDNO MsPageOut
    ExecShell "open" "http://www.microsoft.com/en-us/download/details.aspx?id=5582"
    GOTO MsPageOut
    MsPageOut:
    ; StrCmp $LANGUAGE "1031" lang_de2
    ; MessageBox MB_YESNO "Perhaps XAMPP do not work without the MS VC++ 2008 runtime library. Still go on with the XAMPP installation?" IDNO GoOut
    ; GOTO init_end
    ; GoOut:
    ; Abort "Exit by user."
  init_end:

  !insertmacro MUI_INSTALLOPTIONS_EXTRACT "AdminUserDetails.ini"
  !insertmacro MUI_INSTALLOPTIONS_EXTRACT "ContactDetails.ini"

  ; Mandatory sections
  SectionSetFlags ${SecApache} 17
  SectionSetFlags ${SecMySQL} 17
  SectionSetFlags ${SecPHP} 17
  SectionSetFlags ${SecOrangeHRM} 17

  ; Optional sections
  ;SectionSetFlags ${SecWebalizer} 0
  ;SectionSetFlags ${SecFileZillaFTP} 0
  ;SectionSetFlags ${SecMercuryMail} 0
  ;SectionSetFlags ${SecPerl} 0
  ;SectionSetFlags ${SecWebdav} 0

  ;SectionSetFlags ${SecDemoData} 16

FunctionEnd

;--------------------------------
; Descriptions

  ; Language strings
  LangString DESC_SecGrpOrangeHRMAppliance ${LANG_ENGLISH} "OrangeHRM and all pre-requisities"

  LangString DESC_SecApache ${LANG_ENGLISH} "Apache web server"
  LangString DESC_SecMySQL ${LANG_ENGLISH} "MySQL database server"
  LangString DESC_SecPHP ${LANG_ENGLISH} "PHP Hypertext Preprocessor"
  LangString DESC_SecOrangeHRM ${LANG_ENGLISH} "OrangeHRM 3.3"
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
