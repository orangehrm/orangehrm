@ECHO OFF
REM Clean up XAMPP - based on make-usb-xampp.bat from xampp/src/
REM
cd xampp
rmdir /S/Q MercuryMail
rmdir /S/Q tomcat
rmdir /S/Q FileZillaFTP
rmdir /S/Q anonymous
rmdir /S/Q webalizer
rmdir /S/Q src
rmdir /S/Q mysql\mysql-test
rmdir /S/Q mysql\sql-bench
rmdir /S/Q mysql\scripts
rmdir /S/Q mysql\include
rmdir /S/Q mysql\lib
rmdir /S/Q mysql\backup
rmdir /S/Q perl\lib
rmdir /S/Q perl\site
rmdir /S/Q perl\vendor
rmdir /S/Q perl\bin
del /F/Q catalina_start.bat
del /F/Q catalina_stop.bat
del /F/Q catalina_service.bat
del /F/Q filezilla_setup.bat
del /F/Q filezilla_start.bat
del /F/Q filezilla_stop.bat
del /F/Q mercury_start.bat
del /F/Q mercury_stop.bat
cd ..
