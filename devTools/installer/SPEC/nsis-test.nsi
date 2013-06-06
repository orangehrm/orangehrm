Name "nsis script test"

OutFile "nsis-test.exe"

Function .onInit
	pwgen::GeneratePassword 10
	Pop $0
	MessageBox MB_OK "Random password: $0"

    md5dll::GetMD5String "$0"
    pop $1
	MessageBox MB_OK "MD5: $1"

FunctionEnd

Section
SectionEnd
