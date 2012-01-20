@ECHO OFF

if "%1" == "sfx" (
    cd xampp
)
set BASE_PATH="."
if "%1" == "-path" (
    set BASE_PATH=%2
)
if exist %BASE_PATH%\php\php.exe GOTO Normal
if not exist %BASE_PATH%\php\php.exe GOTO Abort

:Abort
echo Sorry ... cannot find php cli!
echo Must abort these process!
pause
GOTO END

:Normal
set PHP_BIN=%BASE_PATH%\php\php.exe
set CONFIG_PHP=%BASE_PATH%\install\install.php
%PHP_BIN% -n -d output_buffering=0 %CONFIG_PHP%
GOTO END

:END

