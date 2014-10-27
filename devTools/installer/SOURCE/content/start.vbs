Dim Key
Dim Match
Dim CommandTxt
Dim WshShell

Set WshShell = WScript.CreateObject("WScript.Shell") 

' Find if program using port 80
Key = "0.0:80 "
Match = GrepCmdOutput("netstat -o -n -a", Key)

If Match <> "" Then
    Pos = InStrRev(Match, " ")
    ProcessID = Mid(Match, Pos+1)
  
    CommandTxt = "tasklist /FI " & Chr(34) & "PID eq " & ProcessID & Chr(34)
 
    ' Find Process name of program
    Key = " " & ProcessID & " "
	Match = GrepCmdOutput(CommandTxt, Key)

	If Match <> "" Then
		Pos = InStr(Match, " ")
		ExeName = Left(Match, Pos-1)
		If ExeName = "httpd.exe" Then
			 ReturnVal = WshShell.Run("http://127.0.0.1/orangehrm-3.2.1/", 1, false)
		Else
            Rtn = WshShell.Popup("Quit " & ExeName & " and restart OrangeHRM. Once OrangeHRM is started, you can start using " & ExeName & " again. Visit www.orangehrm.com/exe-faq.shtml for more details.", 0, "OrangeHRM", &H40)
		End If	
	End If
Else
    Rtn = WshShell.Popup("Start Apache to proceed with OrangeHRM. Visit www.orangehrm.com/exe-faq.shtml for more details.", 0, "OrangeHRM", &H40)
End If

Function GrepCmdOutput(cmd, Key)
    Dim WshShell, oExec
	Dim LineStr
	Dim PortInUse

	Set WshShell = CreateObject("WScript.Shell")
	Set oExec = WshShell.Exec(cmd)
	LineStr = ""
	PortInUse = false

	Do Until oExec.Status = 0
	    Wscript.Sleep 250
	Loop 

	Do While Not oExec.StdOut.AtEndOfStream 
		LineStr = oExec.StdOut.ReadLine()
	        If InStr(LineStr, key) <> 0 Then 
			' Found Port 80
			PortInUse = true
			Exit DO
	        End If
	Loop
	
	If PortInUse Then
		GrepCmdOutput = LineStr
	Else
		GrepCmdOutput = ""
	End If
End Function
