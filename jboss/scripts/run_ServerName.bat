@echo off

set TEMP=D:\temp\%SERVERCFG%
set TMP=D:\temp\%SERVERCFG%

if not exist %TEMP% mkdir %TEMP%
if not exist %TMP% mkdir %TMP%

@if not "%ECHO%" == ""  echo %ECHO%
@if "%OS%" == "Windows_NT"  setlocal

set DIRNAME=.\
if "%OS%" == "Windows_NT" set DIRNAME=%~dp0%
set PROGNAME=run_%SERVERCFG%.bat
if "%OS%" == "Windows_NT" set PROGNAME=%~nx0%

pushd %DIRNAME%..
set JBOSS_HOME=%CD%
popd

REM Add bin/native to the PATH if present
if exist "%JBOSS_HOME%\bin\native" set PATH=%JBOSS_HOME%\bin\native;%PATH%
if exist "%JBOSS_HOME%\bin\native" set JAVA_OPTS=%JAVA_OPTS% -Djava.library.path="%PATH%"

set RUNJAR=%JBOSS_HOME%\bin\run.jar
if exist "%RUNJAR%" goto FOUND_RUN_JAR
echo Could not locate %RUNJAR%. Please check that you are in the
echo bin directory when running this script.
goto END

:FOUND_RUN_JAR

if not "%JAVA_HOME%" == "" goto ADD_TOOLS

set JAVA=java

echo JAVA_HOME is not set.  Unexpected results may occur.
echo Set JAVA_HOME to the directory of your local JDK to avoid this message.
goto SKIP_TOOLS

:ADD_TOOLS

set JAVA=%JAVA_HOME%\bin\java

if not exist "%JAVA_HOME%\lib\tools.jar" goto SKIP_TOOLS

set JAVAC_JAR=%JAVA_HOME%\lib\tools.jar

:SKIP_TOOLS

if not "%JAVAC_JAR%" == "" set RUNJAR=%JAVAC_JAR%;%RUNJAR%
if "%JBOSS_CLASSPATH%" == "" set RUN_CLASSPATH=%RUNJAR%
if "%RUN_CLASSPATH%" == "" set RUN_CLASSPATH=%JBOSS_CLASSPATH%;%RUNJAR%

set JBOSS_CLASSPATH=%RUN_CLASSPATH%

set JAVA_OPTS=%JAVA_OPTS% -Dprogram.name=%PROGNAME%

"%JAVA%" -version 2>&1 | findstr /I hotspot > nul
if not errorlevel == 1 (set JAVA_OPTS=%JAVA_OPTS% -server)

set JAVA_OPTS=%JAVA_OPTS% -Xms128m -Xmx200m -XX:MaxPermSize=128m

set JAVA_OPTS=%JAVA_OPTS% -Dsun.rmi.dgc.client.gcInterval=3600000 -Dsun.rmi.dgc.server.gcInterval=3600000

set JAVA_OPTS=-Djava.awt.headless=true %JAVA_OPTS% 

set JAVA_OPTS=-Dcas.env=us-uat %JAVA_OPTS%

set JAVA_OPTS=%JAVA_OPTS% -Xrs

set JBOSS_ENDORSED_DIRS=%JBOSS_HOME%\lib\endorsed

echo ===============================================================================
echo.
echo   JBoss Bootstrap Environment
echo.
echo   JBOSS_HOME: %JBOSS_HOME%
echo.
echo   JAVA: %JAVA%
echo.
echo   JAVA_OPTS: %JAVA_OPTS%
echo.
echo   CLASSPATH: %JBOSS_CLASSPATH%
echo.
echo ===============================================================================
echo.

:RESTART
"%JAVA%" %JAVA_OPTS% "-Djava.endorsed.dirs=%JBOSS_ENDORSED_DIRS%" -classpath "%JBOSS_CLASSPATH%" org.jboss.Main %*
if ERRORLEVEL 10 goto RESTART

:END
if "%NOPAUSE%" == "" pause

:END_NO_PAUSE

