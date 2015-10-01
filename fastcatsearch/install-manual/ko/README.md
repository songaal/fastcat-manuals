검색엔진 설치매뉴얼
==============

목차
---

1. 검색엔진 설치
2. 검색엔진 구동 (Linux)
3. 검색엔진 구동 (Windows)
4. 관리도구 설치
5. 관리도구 구동 (Linux)
6. 관리도구 구동 (Windows)

<span></span>

1. 검색엔진 설치
--------------

### 설치

제공받은 검색엔진 패키지를 최종 설치할 디렉토리로 복사하여 설치완료한다.
디렉토리구조는 아래와 같다. `fastcatsearch-버전` 디렉토리 이하참조.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/install-manual/ko/img/233.jpg)

 
### 서비스 포트준비

검색엔진은 기본적으로 8090 포트를 사용하며 conf/id.properties 중 포트번호를 수정한다.

```
me=node1
master=node1
servicePort=8090
```
 
2. 검색엔진 구동 (Linux)
----------------------

### 디렉토리구조

fastcatsearch 의 디렉토리 구조는 다음과 같다.

```bash
$ ls -l
total 0
drwxr-xr-x   9 swsong  staff   306  3  7 10:37 LICENSE
drwxr-xr-x  18 swsong  staff   612  3  7 10:37 bin
drwxr-xr-x   4 swsong  staff   136  3  7 10:37 collections
drwxr-xr-x   6 swsong  staff   204  3  7 10:37 conf
drwxr-xr-x  38 swsong  staff  1292  3  7 10:37 lib
drwxr-xr-x   3 swsong  staff   102  3  7 10:37 plugin
drwxr-xr-x   5 swsong  staff   170  3  7 10:37 service
```

### 시작스크립트 실행

`bin/fastcatsearch start` 스크립트를 실행한다.

```bash
$ cd bin
$ ./fastcatsearch start
++++++++++ Fastcatsearch Environment ++++++++++
server_home = /home/jhjeon/fastcat/fastcatsearch-2.22.1
################################
Start server PID = 30901
java -Dserver.home=/home/jhjeon/fastcat/fastcatsearch-2.22.1 -Xms4096m -Xmx4096m -XX:+HeapDumpOnOutOfMemoryError -server -Dfile.encoding=UTF-8 -Dlogback.configurationFile=/home/jhjeon/fastcat/fastcatsearch-2.22.1/conf/logback.xml -Dderby.stream.error.file=/home/jhjeon/fastcat/fastcatsearch-2.22.1/logs/db.log  -classpath /home/jhjeon/fastcat/fastcatsearch-2.22.1/lib/fastcatsearch-server-bootstrap.jar org.fastcatsearch.server.Bootstrap start > /home/jhjeon/fastcat/fastcatsearch-2.22.1/logs/output.log 2>&1 &
################################

... 중략 ...

[2014-03-07 11:01:13,335 INFO] (CatServer.java:231) CatServer started!
```

위의 예에서는 검색엔진이 PID 30901 로 실행되었으며, 마지막 라인에서 `CatServer started!` 라는 검색엔진이 시작되었음을 알리는 로그를 볼 수 있다.

로그는 tail을 사용하여 보여주므로 `CTRL-C` 를 입력하여 tail을 빠져나올 수 있다.
언제라도 로그를 다시 보고 싶으면 `tail -f logs/system.log` 를 사용하여 확인가능하다.

### 관리도구 접속

관리도구는 다음 장에서 설치 및 접속하는 방법을 적어놓았다.
현재 설치한 검색엔진의 IP와 PORT를 기억한뒤, 차후 관리도구에서 접속시 사용하도록 한다.

### 검색엔진 종료

`bin/fastcatsearch stop` 스크립트를 실행한다.

```bash
$ cd bin
$ ./fastcatsearch stop
++++++++++ Fastcatsearch Environment ++++++++++
server_home = /home/jhjeon/fastcat/fastcatsearch-2.22.1
################################
Stop Daemon PID = 30901
  PID TTY          TIME CMD
30901 pts/6    00:00:07 java
kill 30901
################################

.. 중략 ..

[2014-03-07 11:09:42,240 INFO] (CatServer.java:353) Server Shutdown Complete!
```

Server Shutdown Complete! 메시지가 보이면 검색엔진 종료가 완료된것이다.
tail 로그를 빠져나가기위해 `CTRL-C` 를 입력하여 프롬프트로 빠져나온다.

3. 검색엔진 구동 (Windows)
------------------------

### 디렉토리구조

fastcatsearch 의 디렉토리 구조는 다음과 같다.

```bash
C:\app\fastcatsearch-2.14.3>dir
2014-03-07  오전 11:15    <DIR>          .
2014-03-07  오전 11:15    <DIR>          ..
2014-03-07  오전 11:15    <DIR>          bin
2014-03-07  오전 11:15    <DIR>          collections
2014-03-07  오전 11:15    <DIR>          conf
2014-03-07  오전 11:15    <DIR>          db
2014-03-07  오전 11:15    <DIR>          lib
2014-03-07  오전 11:15    <DIR>          LICENSE
2014-03-07  오전 11:15    <DIR>          logs
2014-03-07  오전 11:15    <DIR>          plugin
2014-03-07  오전 11:15    <DIR>          service
```

시작스크립트 실행

`bin/fastcatsearch.bat` 스크립트를 실행한다.

```bash
> fastcatsearch.bat
fastcatsearch start. see log at logs/system.log
```

cmd 창에는 fastcatsearch 를 시작했으며, `logs/system.log` 파일을 확인하라는 메시지가 보인다. 
텍스트편집기 또는 mtail 과 같은 tail 전용 프로그램을 이용해 로그파일을 열어보면 마지막 라인에서 `CatServer started!` 라는 검색엔진이 시작되었음을 알리는 로그를 볼 수 있다.

### 관리도구 접속

관리도구는 다음 장에서 설치 및 접속하는 방법을 적어놓았다.
현재 설치한 검색엔진의 IP와 PORT를 기억한뒤, 차후 관리도구에서 접속시 사용하도록 한다.

### 검색엔진 종료

단순히 cmd 창에서 CTRL-C 를 입력하여 프로세스를 종료한다.
검색엔진 종료시 로그에서 Server Shutdown Complete! 메시지가 보이면 검색엔진 종료가 완료된것이다.

4. 관리도구 설치
--------------

### 설치

제공받은 검색엔진 패키지를 최종 설치할 디렉토리로 복사하여 설치완료한다.
디렉토리구조는 아래와 같다. `fastcatsearch-console-버전` 디렉토리 이하참조.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/install-manual/ko/img/225.jpg)

### 서비스 포트준비

기본적으로 8080 포트를 사용하며 포트를 변경하고자 한다면 `etc/jetty.xml` 중 Port항목을 변경한다.

```xml
<Call name="addConnector">
  <Arg>
	  <New class="org.eclipse.jetty.server.nio.SelectChannelConnector">
		<Set name="host"><Property name="jetty.host" /></Set>
		<Set name="port"><Property name="jetty.port" default="8080"/></Set>
		<Set name="maxIdleTime">300000</Set>
		<Set name="Acceptors">2</Set>
		<Set name="statsOn">false</Set>
		<Set name="confidentialPort">8443</Set>
		<Set name="lowResourcesConnections">20000</Set>
		<Set name="lowResourcesMaxIdleTime">5000</Set>
	  </New>
  </Arg>
</Call>
```

5. 관리도구 구동 (Linux)
----------------------

### 시작스크립트 실행

디렉토리구조

```bash
$ ls -l
total 16768
drwxr-xr-x   4 swsong  staff      136  3  7 11:34 LICENSE
drwxr-xr-x   3 swsong  staff      102  3  7 11:34 contexts
drwxr-xr-x   8 swsong  staff      272  3  7 11:34 etc
-rw-r--r--   1 swsong  staff  8522819  3  7 11:34 fastcatsearch-console.war
drwxr-xr-x  32 swsong  staff     1088  3  7 11:34 lib
drwxr-xr-x   3 swsong  staff      102  3  7 11:34 logs
drwxr-xr-x   3 swsong  staff      102  3  7 11:34 resources
-rw-r--r--   1 swsong  staff      218  3  7 11:34 start-console.cmd
-rw-r--r--   1 swsong  staff      144  3  7 11:34 start-console.sh
-rw-r--r--   1 swsong  staff     2253  3  7 11:34 start.ini
-rw-r--r--   1 swsong  staff    46310  3  7 11:34 start.jar
```

`start-console.sh` 파일을 실행한다.

```bash
$ sh start-console.sh
fastcatsearch-console start. see logs/server.log file.
```

프로세스는 백그라운드로 실행되며, `logs/server.log` 를 확인하라는 메시지가 보인다.​

### 로그 확인

tail 을 통해 `logs/server.log` 파일을 확인한다.
마지막에 8080 포트가 열렸다는 메시지가 보이면 모두 로딩된 것이다.

```bash
$ tail -f logs/server.log
.. 중략 ..
2014-03-07 11:39:47.407:INFO:oejs.AbstractConnector:Started SelectChannelConnector@0.0.0.0:8080
```

### 관리도구 접속

웹브라우저를 이용하여 http://localhost:8080/console 에 접속한다.
고양이캐릭터와 로그인폼이 보이면 올바로 구동된 것이다.
검색엔진의 주소와 포트를 확인하고 ID, Password를 입력하여 로그인한다. 기본 ID, Password 는 admin / 1111 이다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/install-manual/ko/img/235.jpg)

 
### 관리도구 정지

`stop-console.sh` 파일을 실행한다.

```bash
$ sh stop-console.sh
```
 
6. 관리도구 구동 (Windows)
------------------------

### 시작스크립트 실행

`start-console.cmd` 파일을 실행한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/install-manual/ko/img/226.jpg)

시작되면 cmd 창에 시작메시지가 보이고, `logs/server.log` 를 확인하라는 메시지가 보인다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/install-manual/ko/img/227.jpg)

### 로그 확인

`logs/server.log` 파일을 메모장 또는 mtail 과 같은 프로그램을 이용하여 열어서 확인한다.
아직 로딩중이면 로그파일이 올라가는 것이 보이고 마지막에 8080 포트가 열렸다는 메시지가 보이면 모두 로딩된 것이다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/install-manual/ko/img/228.jpg)
 
### 관리도구 접속

웹브라우저를 이용하여 `http://localhost:8080/console` 에 접속한다.
고양이와 로그인폼이 보이면 올바로 구동된 것이다.
검색엔진의 주소와 포트를 확인하고 ID, Password를 입력하여 로그인한다. 기본 ID, Password 는 admin / 1111 이다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/install-manual/ko/img/229.jpg)

만일 아래와 같이 500에러 발생시 log 를 확인해보고 "에러발생시 처리" 항목을 참고한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/install-manual/ko/img/230.jpg)

### 에러발생시 처리

#### 500 에러발생시 Java를 확인한다.

Java의 경로를 별도로 지정하지 않을 경우 시스템 PATH상의 Java를 사용하는데, Java JRE의 경우 아래와 같은 에러가 발생할수 있다.

```bash
2014-03-06 19:20:16.343:WARN:oejs.ServletHandler:/console/
org.apache.jasper.JasperException: PWC6345: There is an error in invoking javac.  A full JDK (not just JRE) is required
	at org.apache.jasper.compiler.DefaultErrorHandler.jspError(DefaultErrorHandler.java:92)
	at org.apache.jasper.compiler.ErrorDispatcher.dispatch(ErrorDispatcher.java:378)
	at org.apache.jasper.compiler.ErrorDispatcher.jspError(ErrorDispatcher.java:119)
	at org.apache.jasper.compiler.Jsr199JavaCompiler.compile(Jsr199JavaCompiler.java:208)
```

JDK가 설치되어 있지 않다면 JDK를 설치하고, 설치되어 있다면 `start-console.cmd` 를 열어서 `java.exe` 의 경로를 명시해준다.

[start-console.cmd 파일]

```bash
@echo off
IF NOT EXIST temp mkdir temp
echo fastcatsearch-console start. see logs/server.log file.
REM SET JAVA_PATH=C:\Program Files\Java\jdk1.6.0_29\bin\
"%JAVA_PATH%java.exe" -jar start.jar>>logs/server.log 2>&1
```

4번째 주석을 해제하고 java/bin 디렉토리의 경로를 명시한다.

```
SET JAVA_PATH=C:\Program Files\Java\jdk1.6.0_29\bin\
```

파일 저장후 `start-console.cmd` 를 다시 실행시킨다.
 
### 관리도구 정지

cmd 창을 닫거나 CTRL-C 를 눌러 서버를 종료한다.
