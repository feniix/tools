@echo off

set JAVA_HOME=D:\Java\jdk1.6.0_16
set SERVERIP=192.168.10.40
set MCASTIP=229.11.0.4
set SERVERCFG=ThisIsTheTestServer
set ADMUSER=-u admin -p CHANGEME

@if not "%ECHO%" == "" echo %ECHO%
@if "%OS%" == "Windows_NT" setlocal
set DIRNAME=%CD%

set SVCNAME=JB_%SERVERCFG%
set SVCDISP=JB_%SERVERCFG%
set SVCDESC=JBoss_%SERVERCFG%
set NOPAUSE=Y

@if "%1" == "install"   goto cmdInstall
@if "%1" == "uninstall" goto cmdUninstall
@if "%1" == "start"     goto cmdStart
@if "%1" == "stop"      goto cmdStop
@if "%1" == "restart"   goto cmdRestart
@if "%1" == "signal"    goto cmdSignal
echo Usage: service install^|uninstall^|start^|stop^|restart^|signal
goto cmdEnd

:errExplain
@if errorlevel 1 echo Invalid command line parameters
@if errorlevel 2 echo Failed installing %SVCDISP%
@if errorlevel 4 echo Failed removing %SVCDISP%
@if errorlevel 6 echo Unknown service mode for %SVCDISP%
goto cmdEnd

:cmdInstall
jbosssvc.exe -iwdc %SVCNAME% "%DIRNAME%" "%SVCDISP%" "%SVCDESC%" service_%SERVERCFG%.bat
@if not errorlevel 0 goto errExplain
echo Service %SVCDISP% installed
goto cmdEnd

:cmdUninstall
jbosssvc.exe -u %SVCNAME%
@if not errorlevel 0 goto errExplain
echo Service %SVCDISP% removed
goto cmdEnd

:cmdStart
REM Executed on service start
REM run_%SERVERCFG%.bat  -b %SERVERIP% -c %SERVERCFG% -u %MCASTIP% -Djboss.partition.name=%SERVERCFG% >>run_%SERVERCFG%.log
REM goto cmdEnd
REM
REM :cmdStop
REM call shutdown.bat -S -s jnp://%SERVERIP%:1099 %ADMUSER% >shutdown_%SERVERCFG%.log
REM goto cmdEnd
REM
REM :cmdRestart
REM call shutdown.bat -S -s jnp://%SERVERIP%:1099 %ADMUSER% >>shutdown_%SERVERCFG%log
REM call run_%SERVERCFG%.bat  -b %SERVERIP% -c %SERVERCFG% -u %MCASTIP% -Djboss.partition.name=%SERVERCFG% >>run_%SERVERCFG%.log
REM goto cmdEnd
REM
REM :cmdSignal
REM @if not ""%2"" == """" goto execSignal
REM echo Missing signal parameter.
REM echo Usage: service signal [0...9]
REM goto cmdEnd
REM :execSignal
REM jbosssvc.exe -k%2 %SVCNAME%
REM goto cmdEnd
REM
REM :cmdEnd
REM
