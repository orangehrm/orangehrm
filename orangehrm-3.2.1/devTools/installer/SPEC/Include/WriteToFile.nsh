Function WriteToFile
Exch $0 ;file to write to
Exch
Exch $1 ;text to write
FileOpen $0 $0 w #open file, overwrite mode
FileSeek $0 0 END #go to end
FileWrite $0 $1 #write to file
FileClose $0
Pop $1
Pop $0
FunctionEnd
!macro WriteToFile NewLine File String
!if `${NewLine}` == true
Push `${String}$\r$\n`
!else
Push `${String}`
!endif
Push `${File}`
Call WriteToFile
!macroend
!define WriteToFile `!insertmacro WriteToFile false`
!define WriteLineToFile `!insertmacro WriteToFile true`