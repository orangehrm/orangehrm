@ echo off

echo Testing OrangeHRM lib files
echo ***************************
echo Common
echo ******
cd common
call test
cd ..
echo Leave
echo *****
cd models\leave
call test
cd ..\..
