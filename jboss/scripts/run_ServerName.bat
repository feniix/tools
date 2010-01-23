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
REM if exist "%JBOSS_HOME%\bin\native" set PATH=%JBOSS_HOME%\bin\native;%PATH%
REM if exist "%JBOSS_HOME%\bin\native" set JAVA_OPTS=%JAVA_OPTS% -Djava.library.path="%PATH%"
REM
REM set RUNJAR=%JBOSS_HOME%\bin\run.jar
REM if exist "%RUNJAR%" goto FOUND_RUN_JAR
REM echo Could not locate %RUNJAR%. Please check that you are in the
REM echo bin directory when running this script.
REM goto END
REM
REM :FOUND_RUN_JAR
REM
REM if not "%JAVA_HOME%" == "" goto ADD_TOOLS
REM
REM set JAVA=java
REM
REM echo JAVA_HOME is not set.  Unexpected results may occur.
REM echo Set JAVA_HOME to the directory of your local JDK to avoid this message.
REM goto SKIP_TOOLS
REM
REM :ADD_TOOLS
REM
REM set JAVA=%JAVA_HOME%\bin\java
REM
REM if not exist "%JAVA_HOME%\lib\tools.jar" goto SKIP_TOOLS
REM
REM set JAVAC_JAR=%JAVA_HOME%\lib\tools.jar
REM
REM :SKIP_TOOLS
REM
REM if not "%JAVAC_JAR%" == "" set RUNJAR=%JAVAC_JAR%;%RUNJAR%
REM if "%JBOSS_CLASSPATH%" == "" set RUN_CLASSPATH=%RUNJAR%
REM if "%RUN_CLASSPATH%" == "" set RUN_CLASSPATH=%JBOSS_CLASSPATH%;%RUNJAR%
REM
REM set JBOSS_CLASSPATH=%RUN_CLASSPATH%
REM
REM set JAVA_OPTS=%JAVA_OPTS% -Dprogram.name=%PROGNAME%
REM
REM "%JAVA%" -version 2>&1 | findstr /I hotspot > nul
REM if not errorlevel == 1 (set JAVA_OPTS=%JAVA_OPTS% -server)
REM
REM set JAVA_OPTS=%JAVA_OPTS% -Xms128m -Xmx200m -XX:MaxPermSize=128m
REM
REM set JAVA_OPTS=%JAVA_OPTS% -Dsun.rmi.dgc.client.gcInterval=3600000 -Dsun.rmi.dgc.server.gcInterval=3600000
REM
REM set JAVA_OPTS=-Djava.awt.headless=true %JAVA_OPTS% 
REM
REM set JAVA_OPTS=-Dcas.env=us-uat %JAVA_OPTS%
REM
REM set JAVA_OPTS=%JAVA_OPTS% -Xrs
REM
REM set JBOSS_ENDORSED_DIRS=%JBOSS_HOME%\lib\endorsed
REM
REM echo ===============================================================================
REM echo.
REM echo   JBoss Bootstrap Environment
REM echo.
REM echo   JBOSS_HOME: %JBOSS_HOME%
REM echo.
REM echo   JAVA: %JAVA%
REM echo.
REM echo   JAVA_OPTS: %JAVA_OPTS%
REM echo.
REM echo   CLASSPATH: %JBOSS_CLASSPATH%
REM echo.
REM echo ===============================================================================
REM echo.
REM
REM :RESTART
REM "%JAVA%" %JAVA_OPTS% "-Djava.endorsed.dirs=%JBOSS_ENDORSED_DIRS%" -classpath "%JBOSS_CLASSPATH%" org.jboss.Main %*
REM if ERRORLEVEL 10 goto RESTART
REM
REM :END
REM if "%NOPAUSE%" == "" pause
REM
REM :END_NO_PAUSE
REM
