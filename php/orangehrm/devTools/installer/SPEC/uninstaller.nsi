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

;--------------------------------
; Uninstaller Section

Section "Uninstall"

      SetOutPath "$INSTDIR\apache"
      ; UnRegister the web server as a service
      DetailPrint "Uninstalling Apache web server service"
      nsExec::ExecToLog '"$INSTDIR\apache\apache_uninstallservice.bat" -path "$INSTDIR\apache"'

      SetOutPath "$INSTDIR\mysql"
      ; UnRegister the db server as a service
      DetailPrint "Uninstalling MySQL database server service"
      nsExec::ExecToLog '"$INSTDIR\mysql\mysql_uninstallservice.bat" -path "$INSTDIR\mysql"'

      Delete "$INSTDIR\Uninstall.exe"

      RMDir /REBOOTOK /r "$INSTDIR"
      RMDir /REBOOTOK /r "$SMPROGRAMS\${ProductName}"

      DeleteRegKey HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${ProductName}"
      DeleteRegKey /ifempty HKCU "Software\${ProductName}\${ProductVersion}"

SectionEnd
