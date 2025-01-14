rem	================================================
rem	fakemail.sh
rem	Command Line Usage:
rem		echo … | fakemail.bat
rem		echo … | fakemail.bat \path\to\file.txt
rem	php.ini Usage:
rem		sendmail_path \path\to\fakemail.bat
rem		sendmail_path \path\to\fakemail.bat \path\to\file.txt
rem	================================================


@echo off
if "%~1"=="" (
	set file="%~dp0\mail.txt"
) else (
	set file="%~1"
)

echo ================================================>>"%file%"
echo %date% %time%>>"%file%"
echo ------------------------------------------------>>"%file%"
echo.>>"%file%"
more | find /v "" >> "%file%"
