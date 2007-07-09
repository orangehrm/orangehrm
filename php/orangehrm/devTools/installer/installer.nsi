;--------------------------------
;Installer Sections

SectionGroup /e "OrangeHRM Appliance" SecGrpOrangeHRMAppliance

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

    Section "PHP" SecPHP

        SetOutPath "$INSTDIR\php"
        File /a /r "${SourceLocation}\${XamppPath}\php\"
        SetOutPath "$INSTDIR\tmp"
        File /a /r "${SourceLocation}\${XamppPath}\tmp\"

    SectionEnd

    Section "Apache" SecApache

        SetOutPath "$INSTDIR\apache"
        File /a /r "${SourceLocation}\${XamppPath}\apache\"
        SetOutPath "$INSTDIR\cgi-bin"
        File /a /r "${SourceLocation}\${XamppPath}\cgi-bin\"
        SetOutPath "$INSTDIR\htdocs"
        File /a /r "${SourceLocation}\${XamppPath}\htdocs\"

        !insertmacro ReplaceInFile "$INSTDIR\apache\conf\httpd.conf" "?INSTDIR" "$UNIXINSTDIR"

    SectionEnd

    Section "MySQL" SecMySQL

        SetOutPath "$INSTDIR\mysql"
        File /a /r "${SourceLocation}\${XamppPath}\mysql\"

        !insertmacro ReplaceInFile "$INSTDIR\mysql\bin\my.cnf" "?INSTDIR" "$UNIXINSTDIR"

    SectionEnd

    Section "OrangeHRM 2.2" SecOrangeHRM

        SetOutPath "$INSTDIR\htdocs\${OrangeHRMPath}"
        File /a /r "${SourceLocation}\${OrangeHRMPath}\"

    SectionEnd

    Section "-Create Uninstaller"

      ;Create uninstaller
      WriteUninstaller "$INSTDIR\Uninstall.exe"

    SectionEnd

    Section "-Register the application"

      ;Store installation folder
      WriteRegStr HKCU "Software\${ProductName}\${ProductVersion}" "" $INSTDIR

      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "DisplayName" "OrangeHRM - Opensource HR management"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "UninstallString" "$INSTDIR\uninstall.exe"

      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "InstallLocation" "$INSTDIR"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "VersionMajor" "2.2"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "VersionMinor" "0"
      WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}" "DisplayVersion" "2.2"

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

    Section "-Complete"

      SetOutPath "$INSTDIR"
      ; Setup XAMPP
      ExecWait '"$INSTDIR\setup_xampp.bat" -path "$INSTDIR"'

      SetOutPath "$INSTDIR\apache"
      ; Register the web server as a service
      ExecWait '"$INSTDIR\apache\apache_installservice.bat" -path "$INSTDIR\apache"'

      SetOutPath "$INSTDIR\mysql"
      ; Register the db server as a service
      ExecWait '"$INSTDIR\mysql\mysql_installservice.bat" -path "$INSTDIR\mysql"'

    SectionEnd

SectionGroupEnd

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
  Call buildUnixPath

  ; Mandatory sections
  SectionSetFlags ${SecApache} 17
  SectionSetFlags ${SecMySQL} 17
  SectionSetFlags ${SecPHP} 17
  SectionSetFlags ${SecOrangeHRM} 17

  SectionSetFlags ${SecWebalizer} 0
  SectionSetFlags ${SecFileZillaFTP} 0
  SectionSetFlags ${SecMercuryMail} 0
  SectionSetFlags ${SecPerl} 0
  SectionSetFlags ${SecWebdav} 0

  SectionSetFlags ${SecDemoData} 16

FunctionEnd


;--------------------------------
;Descriptions

  ;Language strings
  LangString DESC_SecGrpOrangeHRMAppliance ${LANG_ENGLISH} "OrangeHRM and all pre-requisities"

  LangString DESC_SecApache ${LANG_ENGLISH} "Apache web server"
  LangString DESC_SecMySQL ${LANG_ENGLISH} "MySQL database server"
  LangString DESC_SecPHP ${LANG_ENGLISH} "PHP Hypertext Preprocessor"
  LangString DESC_SecOrangeHRM ${LANG_ENGLISH} "OrangeHRM 2.2"

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


  ;Assign language strings to sections
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

;--------------------------------