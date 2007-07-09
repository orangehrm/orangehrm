;Uninstaller Section

Section "Uninstall"

      SetOutPath "$INSTDIR\apache"
      ; UnRegister the web server as a service
      ExecWait '"$INSTDIR\apache\apache_uninstallservice.bat" -path "$INSTDIR\apache"'

      SetOutPath "$INSTDIR\mysql"
      ; UnRegister the db server as a service
      ExecWait '"$INSTDIR\mysql\mysql_uninstallservice.bat" -path "$INSTDIR\mysql"'

      Delete "$INSTDIR\Uninstall.exe"

      RMDir /REBOOTOK /r "$INSTDIR"
      RMDir /REBOOTOK /r "$SMPROGRAMS\${ProductName}"

      DeleteRegKey HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}"
      DeleteRegKey /ifempty HKCU "Software\${ProductName}\${ProductVersion}"

SectionEnd
