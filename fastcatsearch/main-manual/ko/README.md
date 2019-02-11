검색엔진 통합매뉴얼
===================

1. 시스템 개요
2. 검색엔진설치
3. 검색엔진설정
4. 관리도구 사용법

## 1. 시스템개요

### 1.1. 개요

#### 1.1.1. 분산병렬처리
Fastcatsearch는 대용량문서를 빠른 시간안에 색인 및 검색이 가능하도록 설계된 분산검색엔진입니다. 병렬처리의 장점을 활용하여 검색시 여러 서버에 분산되어 있는 색인문서를 병렬로 동시에 검색하여 브로커서버에서 중간결과들을 합쳐서 최종검색결과를 만들어 냅니다. 또한 색인시에도 하나의 컬렉션을 내부적으로 분할하여 색인함으로써 길어질 수 있는 전체색인시간을 현저히 줄여줍니다.

#### 1.1.2. 한국어분석기
자연어처리모듈이 탑재된 한국어분석기를 제공하며, 키워드 검색의 대상이 되는 명사뿐만이 아니라, 조사, 어미등에 대해서도 단어를 추출함으로써 단순 키워드검색뿐만이 아니라, 문장검색과 인접검색 기능을 제공합니다. 한국어분석기에 사용되는 기초사전 및 확률사전은 국립국어원 세종말뭉치를 기반으로 추출하였으며, 실무에서 자주 사용되는 신조어와 외래어등이 추가 보강되어 95%이상의 분석품질을 제공하고 있습니다. 또한 한국어기반 검색에서 매우 중요한 두단어 이상이 결합된 복합명사에 대해서도 정교하게 분리해내어 운영자의 추가 관리부담없이도 최상의 검색결과를 기대할 수 있습니다.

#### 1.1.3. 사전
기초사전 이외에 신조어추가에 사용되는 사용자사전과, 검색어 확장에 사용되는 유사어사전, 그리고 광고성키워드나 성인키워드등의 제거에 사용되는 불용어사전을 기본적으로 제공합니다. 운영자는 원하는 시간이면 언제든지 사전을  편집하고 적용할수 있으며, 즉시 검색결과에 반영됩니다. 또한 사전통합검색기능도 제공하여, 특정 단어가 어떤 사전에 존재하여 검색결과에 영향을 미치는지 신속히 판단할 수 있습니다. 그리고, 운영자가 커스텀 사전을 무제한으로 생성하여 사용할수 있는 확장성도 제공합니다.

#### 1.1.4. 웹관리도구
검색엔진의 관련된 모든 관리작업을 웹브라우저를 통해서 수행이 가능합니다. 사전관리, 컬렉션관리, 색인관리, 분석기관리, 서버관리, 로그관리, 검색테스트, 전체현황 대시보드등을 웹을 통해 관리할 수 있습니다. 관리도구는 운영자별로 계정을 만들어 사용할 수 있으며, 각각 계정에 서로 다른 권한을 부여할 수 있어, 안전하게 권한을 분담하여 검색엔진을 관리할 수 있습니다.

#### 1.1.5. 검색클라이언트
검색엔진과의 통신은 HTTP기반의 REST방식으로 이루어집니다. 검색쿼리는 문자열로 구성되어 있으며, 검색결과도 JSON이나 XML형태로 제공됩니다. 별도로 제공되는 검색클라이언트는 없으며, 각 언어에서 제공하는 HTTP 클라이언트 라이브러리를 사용하여 검색요청을 하게 됩니다.

### 1.2. 노드구성
표1. 노드타입

|타입		|설명																																																			|
|-----------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
|마스터노드	|관리도구와 통신.<br>컬렉션관리, 스케쥴관리, 분석기관리 등을 담당.																																				|
|색인노드	|문서색인을 담당.<br>색인후 데이터노드에 색인데이터 전파.																																						|
|검색노드	|검색요청을 받아들이며, 로드밸런싱도 함께 담당.<br>실제 색인데이터는 가지고 있지 않으며, 데이터노드로 검색 재요청 (로드밸런싱).<br>분산검색의 경우 여러 데이터노드의 중간검색결과를 머징하는 브로커 역할 담당.	|
|데이터노드	|실제 색인데이터 존재.<br>색인노드에서 생성된 색인데이터가 데이터노드로 전송.<br>하나의 컬렉션이 여러 데이터노드에 설정시, 부하분산 및 Failover가능.															|

#### 1.2.1. 단일서버구성
문서량이 작을 경우에는 색인노드,검색노드,데이터노드를 동일한 단일서버에 구성할 수 있다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/325.jpg)

#### 1.2.2. 분산서버구성

##### 장애대비 이중화 구성
2대 이상의 데이터노드를 구성시 1대 서버장애시 나머지 서버로 검색서비스가 가능하다.
![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/327.jpg)

##### 데이터파티셔닝 구성
문서량이 많아서 2대 이상의 서버에 문서를 나누어 색인후 검색시 병합하여 검색한다.
![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/326.jpg)

##### 복합부하분산구성
장애대비 이중화 + 부하분산 + 데이터파티셔닝을 모두 포함한 구성이다.
예시에서는 검색노드 2대, 데이터노드 3대로 총 5대의 서버로 구성하였다.
![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/328.jpg)


## 2. 검색엔진설치

### 2.1. 설치환경

##### 소스핸들링
시스템에 메이븐이 설치되어 있어야 한다.

##### 지원 운영체제
Java 1.6이상이 설치된 모든 운영체제에 설치가 가능하다.

 
##### 하드웨어사양

표2. 하드웨어사양

|구분	|최소사양	|권장사양				|
|-------|-----------|-----------------------|
|CPU	|Pentium 4	|Intel Xeon 1.8GHz 이상	|
|Memory	|2G			|8G 이상				|
|Disk	|10GB		|100GB 이상				|

##### 검색엔진사양

하드웨어 사양과는 달리, 순수하게 검색엔진에서 필요로 하는 리소스를 설명한다.

표3. 검색엔진사양(예시)

|구분	|최소사양	|일반사양	|대용량	|
|-------|-----------|-----------|-------|
|문서량	|100MB		|1G			|1T		|
|Memory	|512M		|2G			|8G		|
|Disk	|500MB		|5GB		|2T		|
 
### 2.2. 소스빌드

#### 2.2.1. 준비환경

표4. 소스빌드 준비환경

|구분	|내용																							|
|-------|-----------------------------------------------------------------------------------------------|
|Java	|JDK 1.6 이상																					|
|Maven	|Ver 2.0 이상																					|
|기타	|인터넷 접속 가능환경 <br> Maven 빌드시 의존 라이브러리를 받아올 수 있도록 인터넷 환경 필요	|
 
####2.2.2. 다운로드와 빌드
 
##### 소스다운로드

소스는 github에 위치해 있으며, 각 모듈별로 다른 프로젝트로 구성되어 있다. 아래 3개의 프로젝트에서 소스를 동일한 디렉토리 하위로 다운로드 받는다.

```
한글 분석기 플러그인은 유료이며, 오픈소스 프로젝트에 한해 무료로 제공됩니다. 
유료정책에 대해서는 웹스퀘어드로 문의바랍니다. contact@websqrd.com
```

표5. 소스위치

|구분			|위치													|
|---------------|-------------------------------------------------------|
|검색엔진		|https://github.com/fastcatsearch/fastcatsearch			|
|관리도구		|https://github.com/fastcatsearch/fastcatsearch-console	|
|한국어분석기	|https://bitbucket.org/fastcat/analyzers/.../korean		|

다운로드후 모습은 다음과 같다.

```bash
$ ls -al
drwxr-xr-x+  22 swsong  staff    748  6  9 18:47 .
drwxr-xr-x+ 107 swsong  staff   3638  6 28 14:43 ..
drwxr-xr-x+  11 swsong  staff    374  3 10 10:45 analyzer
drwxr-xr-x+  18 swsong  staff    612  6 26 22:08 fastcatsearch
drwxr-xr-x+  15 swsong  staff    510  6 13 09:53 fastcatsearch-console
```
 
##### 빌드

maven을 사용하여 소스를 빌드한다. 빌드 스크립트는 아래와 같으며, 해당위치에서 mvn이 실행가능하도록 환경 Path 에 설정되어 있어야 한다.

```bash
#!/bin/sh
BUILD_TARGET=build/
rm -rf $BUILD_TARGET/*
mkdir $BUILD_TARGET

#
# 1. fastcatsearch
#
mvn -f fastcatsearch/pom.xml clean install
cp -r fastcatsearch/target/fastcatsearch* $BUILD_TARGET/fastcatsearch

#
# 2. analyzer
#
mvn -f analyzer/korean/pom.xml clean install
cp -r analyzer/korean/target/analyzer-korean-*/plugin $BUILD_TARGET/fastcatsearch/

#
# 3. fastcatsearch-console
#
mvn -f fastcatsearch-console/pom.xml clean install
cp -r fastcatsearch-console/target/fastcatsearch-console* $BUILD_TARGET/fastcatsearch-console
```

빌드가 성공했다면 아래와 같은 로그를 볼 수 있다.

```
[INFO] Scanning for projects...
[INFO] ------------------------------------------------------------------------
[INFO] Reactor Build Order:
[INFO]
[INFO] fastcatsearch
[INFO] fastcatsearch-core
[INFO] fastcatsearch-server
[INFO] fastcatsearch-package
[INFO]
[INFO] ------------------------------------------------------------------------
[INFO] Building fastcatsearch 2.14.3
[INFO] ------------------------------------------------------------------------

..중략..

[INFO] ------------------------------------------------------------------------
[INFO] Building fastcatsearch-core 2.14.3
[INFO] ------------------------------------------------------------------------

..중략..

[INFO] ------------------------------------------------------------------------
[INFO] Building fastcatsearch-server 2.14.3
[INFO] ------------------------------------------------------------------------

..중략..

[INFO] ------------------------------------------------------------------------
[INFO] Building fastcatsearch-package 2.14.3
[INFO] ------------------------------------------------------------------------

..중략..

[INFO] ------------------------------------------------------------------------
[INFO] Reactor Summary:
[INFO]
[INFO] fastcatsearch ..................................... SUCCESS [0.570s]
[INFO] fastcatsearch-core ................................ SUCCESS [7.396s]
[INFO] fastcatsearch-server .............................. SUCCESS [9.407s]
[INFO] fastcatsearch-package ............................. SUCCESS [0.911s]
[INFO] ------------------------------------------------------------------------
[INFO] BUILD SUCCESS
[INFO] ------------------------------------------------------------------------
[INFO] Total time: 18.479s
[INFO] Finished at: Sun Jun 29 17:00:03 KST 2014
[INFO] Final Memory: 35M/249M
[INFO] ------------------------------------------------------------------------
[INFO] Scanning for projects...
[INFO]
[INFO] ------------------------------------------------------------------------
[INFO] Building analyzer-korean 2.14.5.30
[INFO] ------------------------------------------------------------------------

..중략..

[INFO] ------------------------------------------------------------------------
[INFO] BUILD SUCCESS
[INFO] ------------------------------------------------------------------------
[INFO] Total time: 5.050s
[INFO] Finished at: Sun Jun 29 17:00:10 KST 2014
[INFO] Final Memory: 23M/253M
[INFO] ------------------------------------------------------------------------
[INFO] Scanning for projects...
[INFO]
[INFO] ------------------------------------------------------------------------
[INFO] Building fastcatsearch-console 2.14.3
[INFO] ------------------------------------------------------------------------

..중략..

[INFO] ------------------------------------------------------------------------
[INFO] BUILD SUCCESS
[INFO] ------------------------------------------------------------------------
[INFO] Total time: 5.631s
[INFO] Finished at: Sun Jun 29 17:00:17 KST 2014
[INFO] Final Memory: 17M/250M
[INFO] ------------------------------------------------------------------------
```
 
##### 패키지

빌드가 성공한 후 `build/` 디렉토리 하위의 모습은 다음과 같다.

fastcatsearch와 fastcatsearch-console 두개의 패키지가 생성되며, `fastcatsearch/plugin/analysis/Korean` 에 한글분석기가 설치된것도 확인할 수 있다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/269.jpg)

### 2.3. 검색엔진설치

##### 파일복사

위에서 빌드된 패키지를 설치위치에 단순히 복사하는 것으로 설치는 완료된다.

```bash
$ mv -r fastcatsearch [설치위치]/
$ mv -r fastcatsearch-console [설치위치]/
```
 
##### 검색 통신포트설정

검색엔진에서는 검색요청을 받을때 디폴트로 8090포트를, 내부노드끼리 통신은 9090포트를 사용한다.

그리고 웹관리도구는 8090포트를 사용한다. 만약 기존에 사용하는 포트와 겹쳐서 수정이 필요할때는 관련된 설정파일을 수정한다.

표6. 포트수정 설정파일

|통신		|PORT	|설정파일								|
|-----------|-------|---------------------------------------|
|검색요청	|8090	|fastcatsearch/conf/id.properties		|
|내부통신	|9090	|fastcatsearch/conf/node-list.xml		|
|웹관리도구	|8080	|fastcatsearch-console/etc/jetty.xml	|
 
#### 노드설정

여러서버 설정시 id.properties 파일을 수정하여 자신의 노드ID와 Master 노드ID를 설정해주어야 분산구성이 가능하다.

단일노드구성시에는 파일을 수정하지 않아도 된다.

표7. 분산서버 2대구성 예시

|구분				|Node1											|Node2											|
|-------------------|-----------------------------------------------|-----------------------------------------------|
|용도				|마스터 노드									|슬레이브 노드									|
|id.properties 내용	|me=node1<br>master=node1<br>servicePort=8090	|me=node2<br>master=node1<br>servicePort=8090	|

- me : 자신의 노드아이디
- master : 마스터 노드아이디
- servicePort : 서비스할 포트아이디

##### 포트오픈시 점검사항

```
노드들사이에 8090포트와 9090포트는 모두 열려있어야 함.
관리도구서버와 노드들사이에는 8090포트가 열려있어야 함.
검색노드의 경우 WAS쪽으로 8090포트가 열려있어야 검색요청을 받을 수 있음.
```
 
##### 웹관리도구포트수정

기존적으로 8080포트를 사용하나, 변경하려면 etc/jetty.xml 중 jetty.port 항목을 수정한다.

```
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


### 2.4. 검색엔진 서비스 등록

##### 메모리설정

- Linux : `bin/environment.sh` 에서는 다음과 같이 jvm 등의 메모리 설정을 할 수 있다.
`HEAP_MEMORY_SIZE=4g`
- Windows :  `bin/environment.bat` 파일에서 수정가능하다.
`java -Xmx4g ... `

##### 서비스등록

서비스에 등록을 해두면 운영체제가 재시작되는 경우에도 검색엔진이 자동으로 시작된다.

|운영체제	|스크립트						|
|-----------|-------------------------------|
|Linux		|service/bin/installDaemon.sh	|
|Windows	|service/bat/installService.bat	|

##### 서비스시작

- Linux : 시작 스크립트는 `bin/fastcatsearch start` 이며,  종료 시에는 `bin/fastcatsearch stop` 으로 종료한다.
실행시 log파일이 자동으로 `tail` 을 사용해서 뿌려진다.
로그를 그만보고자 하면 `ctrl^c`를 누르면 되며, 검색엔진은 background로 계속 실행된다.
- Windows : `bin/fastcatsearch.bat` 파일을 더블 클릭하여 실행한다. 종료는 cmd 창을 닫으면 된다.

##### 서비스종료

- Linux : `bin/daemon.sh stop` 를 호출한다.  또는 `bin/stop.sh` 를 호출하면 process를 찾아서 `kill` 한다.
- Windows : 실행중인 cmd창을 닫는다.

##### 프로세스확인

- Linux : `ps -ef | grep java | grep fastcatsearch | grep Bootstrap`
- Windows : 작업관리자 또는 프로세스가 실행된 cmd 창을 확인한다.

##### 로그확인

- Linux : `tail -f logs/system.log` 를 통해 시스템로그를 확인한다.
- Windows :  `logs/system.log` 파일을 윈도우용 `tail` 프로그램 또는 텍스트편집기로 열어본다.


### 2.5 관리도구 서비스 등록

##### 서비스등록

서비스에 등록을 해두면 운영체제가 재시작되는 경우에도 검색엔진이 자동으로 시작된다.

|운영체제	|스크립트						|
|-----------|-------------------------------|
|Linux		|service/bin/installDaemon.sh	|
|Windows	|service/bat/installService.bat	|

##### 서비스시작

시작스크립트를 호출한다.

- Linux : `start-console.sh`
- Windows : `start-console.cmd`

##### 서비스종료

- Linux : `stop-console.sh` 를 호출한다.
- Windows : 실행중인 cmd창을 닫는다.

##### 프로세스확인

- Linux : `ps -ef | grep java | grep start.jar`
- Windows : 작업관리자 또는 프로세스가 실행된 cmd 창을 확인한다.

##### 로그확인

- Linux : `tail -f logs/server.log`
- Windows : `logs/server.log` 파일을 윈도우용 tail 프로그램 또는 텍스트편집기로 열어본다.

##### 관리도구 접속

웹브라우저를 이용하여 `http://[검색엔진설치IP]:[PORT]/console` 에 접속한다.
로그인화면이 보이면 올바로 접속된 것이다.



## 3.검색엔진설정

여기에서 설명하는 설정내용들은 `conf/system.properties` 파일에서 수정할 수 있으며, 동적으로 적용되지 않기 때문에, 수정후에는 반드시 검색엔진을 재시작해야 한다.

### 3.1 동시검색량 설정

검색작업은 `작업 쓰레드 POOL`에서 수행되며, `POOL`의 기본크기는 `300`으로 설정되어 있다. 색인작업이나 관리도구를 위한 작업등이 함께 수행되나, 그 갯수는 `10%`를 넘지 않는다. 그러므로 검색량이 많은 서버라면 이 `POOL` 사이즈값을 높여서 동시검색량을 늘릴 수 있다. 하지만, `POOL`의 크기는 시스템의 `프로세스당 생성가능한 최대 쓰레드 갯수`를 넘지 못하므로, 시스템을 확인하여 적절한값을 설정해야 한다.

```
http.execute_pool_size=300
```

### 3.2 디스크 사용률 알림

관리도구를 통해 알림설정을 등록할 수 있는데, 이때 디스크 사용률이 높을때 알림을 발생시킬 수 있다.

```
system.disk_usage_warning=90
```

위에서는 디스트 사용률이 `90%`이상일때 알림이 발생한다.

### 3.3 알림설정

검색엔진의 알림을 이메일로 전송할 수 있도록 하는 기능이다. 관리자는 이메일로 경고성 또는 정보성 알림을 받아봄으로써 검색엔진 모니터링에 대한 부담을 줄일 수 있다.

메일발송은 `sendmail` 과 `smtp`방식을 지원하며, `*nix` 시스템에 `sendmail` 이 설치된 환경이라면, `sendmail`이 빠르고 간단하다. 하지만 `sendmail`을 사용할 수 없는 상황이라면, `smtp`를 사용하여 외부 서버에서 메일을 전송하도록 한다.

```
system.mail.sender=sendmail
#system.mail.sender=smtp
```

`sendmail`을 선택했다면 실행파일의 경로를 설정한다. 여기서는 `/usr/sbin/` 하위에 존재한다고 가정하여, 경로는 생략하였다.

```
sendmail.path=sendmail
```

`smtp`를 선택했다면 `smtp-config.id`에 이메일 사용자 아이디를 `smtp-config.password`에 암호를 입력한다. `smtp-config.mail.smtp` 로 시작하는 설정에는 `smtp` 메일서버정보를 입력한다.

```
smtp-config.id=webmaster@mydomain.com
smtp-config.password=my_email_password
smtp-config.mail.smtp.port=
smtp-config.mail.smtp.host=
smtp-config.mail.smtp.starttls.enable=
smtp-config.mail.smtp.auth=
smtp-config.mail.smtp.ssl.trust=
```

다음은 `gmail` 사용시의 설정예제이다.
```
smtp-config.mail.smtp.port=587
smtp-config.mail.smtp.host=smtp.gmail.com
smtp-config.mail.smtp.starttls.enable=true
smtp-config.mail.smtp.auth=true
smtp-config.mail.smtp.ssl.trust=smtp.gmail.com
```

### 3.4. 묶음검색설정

검색결과가 많을때 묶음검색을 사용시 메모리 부족에러가 발생할 가능성이 있는데, 메모리 사용량은 아래 설정값으로 결정된다.
묶음검색시 내부적으로 메모리와 파일을 혼합한 하이브리드기반의 해시맵을 사용하는데, 검색결과내 번들키값이 `bundleMemMaxCount` 갯수 이전까지는 메모리를 사용하고, 그 이후부터는 파일기반으로 전환하여 사용한다. 파일기반으로 동작시 메모리를 적게 사용하므로, 평균적으로 검색되는 문서의 수와 묶음검색결과를 판단하여, 적절한 값을 설정하도록 한다. `bundleHashBucket` 는 파일기반 해시셋 버킷의 크기이다.
아래는 기본설정 값이며, 묶음키가 `10만`개를 넘으면 파일기반으로 전환되며 이때 버킷크기는 `100만`을 사용한다.

```
bundleHashBucket=1000000
bundleMemMaxCount=100000
```

### 3.5. 실시간 동적색인 설정 ```v3.0```

실시간 동적색인은 디스크, 메모리, CPU등 통합적으로 고려할 사항이 많다. 설정에 따라 색인이 늦게 수행되기도 하고, 메모리 부족에러가 발생할 수도 있기 때문이다. 동적색인의 소스가 되는 문서는 메모리에 저장하였다가 일정주기마다 컬렉션 데이터 디렉토리 하위에 `indexlog/`로 저장이 되는데, 이때 숫자이름의 파일이 생성되면서 `JSON`형식으로 `Append` 기록이된다.

`Flush`주기는 메모리 `JSON` 데이터를 파일로 기록하여 색인스케줄러에서 읽을 수 있도록 해준다. 그러므로 이 주기가 느리면 메모리 데이터 사용량이 늘어나고, 색인스케줄러는 최신 데이터를 늦게 확인하게 된다. 해당 파라미터는 `log_flush_period_SEC` 이며, `1`초주기로 셋팅하는 것이 기본값이다.

```
ir.indexing.dynamic.log_flush_period_SEC=1
```

`Flush`된 데이터는 하나의 파일에 계속 쌓이게 되는데, 주기적으로 파일을 변경하지 않으면 하나의 파일이 크기가 너무 커지게 된다. 그러므로 파일을 주기적으로 바꾸어 다 읽은 파일은 삭제하여 전체적으로 디스크를 효율적으로 사용할 수 있도록 한다. 파일을 주기적으로 바꾸는 주기는 `log_rolling_period_SEC` 이며 기본값은 `30`초이다.

```
ir.indexing.dynamic.log_rolling_period_SEC=30
```

색인스케줄러는 `indexlog`에 만들어진 색인할 문서들을 읽어들여 색인작업을 각 노드에 분산하여 전송한다. 이때 한번에 수행할 색인문서집합의 크기가 너무 크면 색인시간이 길어지고, 검색에 노출되는 시간도 느려진다. 그러므로 적정량으로 나누어 연속적으로 색인작업을 만들어내면, 각 색인작업이 끝날때마다 최근 문서의 검색이 가능해진다. 색인문서량의 결정은 두개의 파라미터로 결정되는데, `dynamic.max_log_size_MB`는 문서집합의 크기로 결정하는 방법이고, `dynamic.max_log_count` 문서갯수로 결정하는 방법이다. 색인작업 생성시 이 두가지 값중 먼저 부합되는 파라미터가 사용된다. 예를 들어 아래와 같이 설정후 색인할 `100`개 문서의 크기가 `20MB`에 도달했다면 `10000`개 문서를 모두 채우지 않고, 해당 `100`개 문서만 동적색인이 시작된다.

```
ir.indexing.dynamic.max_log_size_MB=20
ir.indexing.dynamic.max_log_count=10000
```

색인스케줄러는 동적색인스케줄러와 머징색인스케줄러로 나뉜다. 동적색인스케줄러는 신규,업데이트,삭제 요청시 작업을 결정하며, 머징색인스케줄러는 기존에 이미 생성된 문서들의 삭제문서를 제거해주는 일종의 조각모음작업을 결정한다. 동적색인은 `indexing_period_SEC`값을 참조하여 기본 `1`초 마다 한번씩 시도하며, 문서가 1개라도 색인작업이 시작된다. 이는 여러문서를 모아서 색인하는 것보다 효율성은 좋지 않지만, 실시간 색인을 위해서는 감수해야 하는 부분이다. 머징색인은 `merge_period_SEC`값을 참조하여 기본 `5`초마다 한번씩 확인을 하며, 머징할 대상이 없다면, 그 다음주기를 기다린다.

```
ir.indexing.dynamic.merge_period_SEC=5
ir.indexing.dynamic.indexing_period_SEC=1
```

### 3.6. 색인배포시 별도 네트워크 사용

분산검색시스템에서는 전체색인후 각 노드에 색인파일을 배포하는데, 색인파일의 크기가 수십 GB에 달하는 경우에는 전송시간도 많이 걸리며, 네트워크 리소도 과도하게 사용하여, 검색 네트워크에 지장을 줄 가능성이 있다. 그러므로 이러한 문제를 해결하기 위해, 검색에 사용되는 네트워크와 파일전송 네트워크를 별도로 나누어 설정할 수 있는 기능이 존재한다. 물론 물리적으로 추가적인 이더넷카드를 설치후 IP할당까지 완료된 상태여야 한다. 해당 설정은 `node-list.xml` 파일에 `dataAddress` 속성을 추가한다.

아래의 예시에서는 `index node`와 `data node` 간에만 별도 네트워크를 구축하고 있다. `broker node`에는 일반적으로 색인데이터가 전송되지 않으므로 별도네트워크를 연결할 필요가 없다.

```
<node-list>
        <node id="index" name="index node" address="10.0.1.4" port="9090" enabled="true" dataAddress="192.168.20.4"/>
        <node id="broker" name="broker node" address="10.0.1.11" port="9090" enabled="true"/>
        <node id="data" name="data node" address="10.0.1.10" port="9090" enabled="true" dataAddress="192.168.20.10"/>
</node-list>
```
설정을 수정했다면 검색엔진을 재시작한다.
만약 나중에 설정에서 `dataAddress` 를 삭제한다면, 자동적으로 `address` 에 설정된 네트워크만 사용하게 된다.


### 3.7 로그설정

로그설정은 `conf/logback.xml` 파일에 설정을 한다.

로그가 저장되는 위치는 `logs/` 이다.

로그의 종류는 아래와 같다.

|로거 이름	| 로그파일 | 설명	|
|-------  |-----|-----|
|ROOT | system.log | 기본 최상위 로깅|
|SEARCH_LOG | search.log | 검색결과정보 로깅|
|REQUEST_LOG | request.log | 검색요청내용 로깅|
|SEARCH_ERROR_LOG | search_error.log | 검색에러가 발생한 쿼리 로깅|
|INDEXING_LOG | indexing.log | 색인정보 로깅|
|SEGMENT_LOG | segment.log | 세그먼트 생성정보 로깅|

로그레벨은 다음과 같다. 로그가 너무 많이 남거나, 너무 적게 출력되어 확인하기 어렵다면 레벨을 조정한다.

|로그레벨 | 설명|
|-----------|-----|
|INFO | 정보 출력|
|DEBUG | 디버그 출력|
|TRACE | 상세 디버그를 포함한 모든 내용 출력|
|ERROR | 에러만 출력. INFO 및 WARN 내용은 출력되지 않음|
|WARN | 경고만 출력. INFO 내용은 출력되지 않음|

로그레벨을 수정하려면 Root로거는 `<root level="debug">`를 수정하고, 다른 로거들은 일치하는 이름을 찾아서 `<logger name="SEARCH_LOG" level="INFO" additivity="false">` 부분을 찾아서 수정한다.

로그주기 롤링 주기는 `1`일 이며, 파일명에 날짜 확장자가 붙어서 롤링된다. 로그의 저장기간을 늘리려면, 각 로그에 해당하는 `<append>` 항목을 찾아 아래를 수정한다. 기본값은 7일로 되어있다.
```
<MaxHistory>7</MaxHistory>
```


## 4.관리도구사용법

### 4.1. 관리도구 접속 및 로그아웃

관리도구 설치 후 웹 브라우저에서 `http://[관리도구서버 IP]:[관리도구 PORT]/console` 으로 접속하면 다음과 같이 관리도구의 로그인 화면 접속이 가능하다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/270.jpg)

검색엔진에 접속하기위해서는 마스터 서버위치와 계정을 입력해야한다. 

마스터서버와 관리도구가 동일한 서버에 설치되었다면 IP는 localhost 또는 127.0.0.1을 사용해도 무방하다.

기본 아이디 패스워드는 아래와 같다. 패스워드 수정은 [계정관리]항목을 참고한다.

|서버위치	|localhost:8090	|
|-----------|---------------|
|아이디		|admin			|
|패스워드	|1111			|

만일 검색엔진이 시작되지 않았거나, 정지상태라면 "Host is not alive" 라는 메시지가 뜨면서 접속을 할 수 없다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/271.jpg)

성공적으로 로그인을 했다면 관리도구 첫페이지로 이동되고 우측상단에 접속한 서버위치가 나타난다.
 
![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/273.jpg)

로그아웃시는 상단 메뉴중 "사람모양 아이콘"을 클릭후 `LogOut` 을 클릭한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/274.jpg)
 
### 4.2. 대시보드

대시보드는 검색엔진의 전체현황을 보여준다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/276.jpg)

##### 1) Realtime Query Request

컬렉션별 실시간 검색요청건수를 막대 그래프로 보여준다. 컬렉션별로 각각 다른 색깔로 표현하며 평균 QPS와, 현재 Realtime QPS를 보여준다.

- ![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/382.jpg) : QPS 측정을 시작한다.
- ![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/383.jpg) : QPS 측정을 잠시 멈춘다.
- ![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/384.jpg) : 측정값을 지운다.
- Current Time : 현재시각
- Time Elapsed : 모니터링이 시작한뒤로 부터 소요된 시간
- Average QPS : 모니터링이 시작한 뒤로부터 측정된 평균 QPS (초당 요청쿼리건수)
- Realtime QPS : 현재 초의 QPS 값
 
##### 2) Collections

컬렉션별로 색인문서수와 차지하는 디스크 용량와 업데이터된 시간을 보여준다.

- Collections : 컬렉션 이름 (컬렉션 아이디)
- Documents : 총 색인문서수. 삭제문서수도 포함
- Disk Size : 색인용량
- Update Time : 마지막 색인 시각

##### 3) Indexing Result

컬렉션별로 색인결과상태와 문서수 색인소요시간, 그리고 종료시간을 보여준다.

- Collections : 컬렉션 이름 (컬렉션 아이디)
- Status : 색인작업 결과상태
- Documents : 총 색인문서수. 삭제문서수도 포함
- Duration : 색인소요시간
- Time : 마지막 색인 시각
 
##### 4) Server Status

서버별로 주소, 포트, 서버상태, 디스크 사용률, CPU 사용률, 시스템 Load등을 보여준다.

- Server Name : 노드이름
- IP Address : 노드 IP주소
- Port : 노드 내부 통신 PORT번호
- Status : 상태. `Alive` = 정상. `Stop` = 정지
- Disk : 색인파일 디스크용량 비율 (색인디스크용량 / 전체 시스템 디스크용량)
- Java CPU : Java CPU 사용률
- System CPU : System CPU 사용률
- Java Memory : Java 메모리사용률 (Java 현재 Memory 용량 / Java 전체 Memory 용량)
- System Memory : System 메모리리 용량
- Load : System 로드
 
##### 5) Notifications

검색엔진에서 발생하는 중요한 메시지에 대해 발생한 Notification을 보여준다. 색인 시작/종료 등의 메시지를 확인할수 있다.

- Message : Notification 메시지 내용
- Time : Notification 발생시각
 
##### 6) Task Status

검색엔진 내부 작업(Task)들의 실행결과를 보여준다.

- Task : 작업내용
- Status : 작업상태. `SUCCESS` = 정상종료. `FAIL` = 실패
- Elapsed : 소요시간
- Start Time : 시작시각
- End Time : 종료시각

### 4.3. 컬렉션 생성 위자드
 
#### 4.3.1. 시작

컬렉션 생성 위자드는 번거운 컬렉션 생성과정을 Step별로 따라오면서 쉽게 생성할 수 있도록 도움을 주는 도구이다.

상단의 `Manager` 메뉴을 누르면 화면에 `Create Collection Wizard` 항목을 볼 수 있다. 클릭하면 위자드가 시작된다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/277.jpg)
 
#### 4.3.2. 컬렉션 기본정보 입력

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/371.jpg)

- Collection ID : 컬렉션 아이디 (영문 숫자 조합)
- Collection Name : 컬렉션명 (사람이 이해하기 쉬운 문자열)
- Index Node : 색인노드 선택
- Search Node List : 검색노드 아이디. 컴마구분으로 여러개 추가가능. 우측 Drop-Down 리스트에서 노드를 선택하면 자동입력된다.
- Data Node List : 데이터노드 아이디. 컴마구분으로 여러개 추가가능. 우측 Drop-Down 리스트에서 노드를 선택하면 자동입력된다.
- `Next` : 모든 사항이 올바로 입력되었다면 이 버튼으로 다음 단계로 넘어간다.
- `Cancel collection` : 언제든지 이 버튼을 누르면 지금까지 설정한 컬렉션 설정정보가 삭제되면서 컬렉션 생성작업을 취소할 수 있다.

#### 4.3.3. 데이터맵핑

컬렉션 기본정보를 입력하였으면, 데이터소스와 데이터를 맵핑해준다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/372.jpg)

```
데이터소스가 DBMS의 경우 아래의 설정을 이용하며, 데이터소스가 다를 경우 설정값이 달라질 수 있다. 
설정값은 각 데이터소스 Reader를 참고한다.
```

- Source Type : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- JDBC : 접속할 DB에 대한 JDBC 설정. 아직 JDBC설정값이 없는 경우 Create New.. 를 클릭하여 새로 생성한다.
- `Create New..` : DB에 접속할 수 있는 JDBC설정을 추가한다. 사용법은 아래 "JDBC 소스 생성" 항목을 참조한다.
- `Query Test` : 쿼리를 실제로 수행해 볼수 있는 SQL Editor를 실행한다. 사용법은 아래 "수집쿼리 테스트" 항목을 참조한다.
- Bulk Size : Reader가 DB에서 한번씩 데이터를 가져오는 갯수. Bulk Size만큼 메모리에 쌓아두기 때문에, 값이 아주 큰 경우 OutOfMemory 에러가 발생할 수 있으니, 100정도의 디폴트값을 이용하는 것을 추천한다.
- Fetch Size : JDBC의 Statement fetch-size를 가리킨다. 값이 0이면 각 JDBC별 디폴트값이 사용되고, -1이면, read-only cursor로 동작한다. JDBC수집중 OutOfMemory가 발생한다면 -1을 사용한다.
- Data SQL : 색인할 데이터를 가져오는 SQL 쿼리문을 입력한다. 프로시저 실행 또한 가능하다.
- Delete SQL : 기존에 색인된 데이터 중 삭제할 데이터의 아이디를 가져오는 SQL 쿼리문을 입력한다. Select 문을 사용해야 하며 Data SQL의 쿼리 결과와 값이 중복될 경우 Data SQL의 결과가 우선시되기 때문에 전체 색인(Full Indexing)시에는 Delete SQL을 입력한 후 색인을 진행해도 Delete SQL 결과가 적용되지 않으므로 증분 색인(Add Indexing)에 설정해야 기존에 색인된 데이터 중 쿼리 결과로 나온 아이디 값의 삭제가 이루어진다. 아이디로서 Select되는 필드는 컬렉션 스키마의 주키(Primary Key)가 되어야 한다. 

```
Delete SQL의 Select할 필드에는 오직 하나의 주키 필드만 가져오도록 한다.
```

- Before SQL : Data SQL이 데이터를 수집하기 전에 실행되는 SQL 쿼리문을 입력한다.
- After SQL : Data SQL이 데이터를 수집한 뒤 마지막으로 실행되는 SQL 쿼리문을 입력한다.

```
Before SQL과 After SQL 은 오직 Update SQL만 실행할 수 있다. Select 문은 사용할 수 없다.
```

- `Back` : 이전 단계로 이동한다. 현재 단계의 입력내용은 사라진다.
- `Next` : 입력내용을 저장하고, 다음 단계로 이동한다.
 
#### 4.3.4. JDBC 소스 생성

- Id : 생성할 JDBC 설정값의 아이디 (영문 숫자 조합)
- Name : 생성할 JDBC 설정의 이름 (사람이 이해하기 쉬운 문자열)
- DB Vendor : DBMS 제공사

```
DB Vendor 리스트에 원하는 DB가 보이지 않을 경우 "기타 > JDBC DB Vendor 추가하기" 항목을 참고한다.
```

- JDBC Driver : JDBC class 명. DB Vendor 선택시 자동으로 입력된다.
- Host : DB서버 IP주소
- Port : DB서버 서비스 PORT
- DB Name : 데이터가 존재하는 DB의 이름
- User : DB사용자 아이디
- Password : DB사용자 암호
- JDBC Parameter : 추가적인 JDBC 파라미터. 아래에서 자동으로 생성되는 JDBC URL에 추가적으로 이어 붙는다. 비워놓을 경우 사용되지 않는다.
- JDBC URL : 자동으로 생성되는 JDBC URL. 
- Create : 입력한 내용으로 JDBC를 생성한다.
- Test Connection : 실제로 접속이 되는지 여부를 테스트해본다. 

```
Test Connection 실행시 접속클라이언트의 위치는 검색엔진 Master 노드이다.
```

```
JDBC드라이버 ClassNotFoundException 발생!
드라이버를 사용하려면 JDBC드라이버가 검색엔진 Classpath상에 존재해야 한다.
드라이버를 Classpath에 추가하려면 [검색엔진설치위치]/lib/ 하위로 드라이버 JAR파일을 복사하고 검색엔진을 재시작한다.
```
 
#### 4.3.5. 수집쿼리테스트

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/374.jpg)

##### 1) SQL 쿼리문 입력란

SQL쿼리를 입력한다.

결과는 최대 100개까지만 출력된다.

"--" 사용시 주석처리되어 실행되지 않는다.

`Run Query` : 쿼리를 실행한다. 앞쪽 입력란에는 제한할 결과갯수를 정수로 입력한다. 

```
제한 갯수가 100을 넘을 경우에도 결과는 최대 100개까지만 출력된다.
```
 
##### 2) SQL 쿼리결과 출력란

쿼리의 Select 컬럼별로 데이터를 출력한다.

#### 4.3.6. 스키마 설정

이전 단계에서 입력한 Data SQL문을 기반으로 컬렉션 스키마를 자동구성하여 추천해준다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/375.jpg)

- ID : 필드아이디 (영문 숫자 조합)
- Name : 필드명 (사람이 이해하기 쉬운 문자열)
- Type : 필드타입

|타입명		|설명							|
|-----------|-------------------------------|
|ASTRING	|영문자 데이터					|
|STRING		|영문포함 2바이트문자 데이터	|
|DATETIME	|날짜형							|
|INT		|INT형 정수						|
|LONG		|LONG형 정수					|
|FLOAT		|FLOAT형 실수					|
|DOUBLE		|DOUBLE형 실수					|

- Length : 문자형 필드의 경우 문자열길이. 입력시 고정필드로 설정된다.
- Remove Tags : HTML태그를 자동제거 여부
- Multi Value : 다중값 필드여부
- Multi Value Delimiter : 다중값으로 변환시 사용할 구분자
 
#### 4.3.7. 필드매핑 테스트

SQL쿼리로 데이터를 가져와서 스키마 설정을 이용해 객체로 변환해보면 매핑이 올바로 진행되는지 테스트해 볼 수 있다.
각 필드별 데이터를 출력한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/283.jpg)

```
수집쿼리테스트에서는 SQL문으로 실행된 데이터를 그대로 보여주는 반면 필드매핑 테스트는 스키마에 설정된 
필드타입으로 변환후 보여주기 때문에, 필드매핑 테스트에서 필드값이 올바로 출력되지 않을 경우 스키마를 수정해야 한다.
```

#### 4.3.8. 설정확인

이전단계에서 설정한 값들을 전체적으로 최종확인한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/284.jpg)

##### 1) Collection Information & Data Mapping

컬렉션 정보와 데이터맵핑 정보를 확인한다.
 
##### 2) Fields

스키마 필드정보를 확인한다.
 
##### 3) Everything is OK, Create Collection

모든 사항이 올바르다면, 클릭하여 컬렉션을 생성한다.
 
#### 4.3.9. 컬렉션 생성완료

컬렉션이 생성되었고, 색인작업과 데이터조회는 가능한 상태가 되었다.

하지만 검색에 필요한 색인필드설정등은 아직 수행하지 않았으므로, `Continue to setting index field `를 클릭하여 추가적인 설정으로 넘어가도록 한다.

추가적인 설정은 컬렉션 항목에서 자세하게 설명하므로 참고하도록 한다. 
 
![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/285.jpg)
 
### 4.4. 사전

분석기에서 사전사용을 허용하는 경우 DICTIONARY 메뉴에 해당 분석기명이 나타난다. 

분석기명을 클릭하면, 사전의 Overview정보를 확인할 수 있다.

#### 4.4.1. Overview

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/376.jpg) 

`Apply Dictionary` : 사전을 편집한 경우 적용버튼을 눌러야 검색엔진에 적용된다. 적용할 사전은 Combo-Box를 클릭하여 다중선택한다.

- Name : 사전이름
- Type : 사전종류
- Working Entry Size : 사전적용되어 메모리에 로딩되어 있는 단어 갯수
- Modified Time : 편집한 마지막 시각
- Applied Entry Size : 사전적용된 단어갯수
- Applied Time : 사전적용시각
- Token Type : 사전로딩시 해당사전의 단어들을 기초사전에 추가할지 여부. NONE이면 추가하지 않는다. 
- Ignore Case : 대소문자 구분 여부. 영문 단어가 포함된 사전에서 사용된다.

```
불용어사전과 유사어사전의 경우는 검색시 단어 확장/제외에 사용되므로 색인과 관련이 없지만, 
사용자사전의 경우는 편집하여 사전을 적용하였다면, 전체색인을 다시 수행해야 원하는 검색결과를 기대할 수 있다.
```
 
#### 4.4.2. Search

검색을 이용하면 특정단어가 어느 사전에 어떻게 입력되어 있는지 한번에 확인할 수 있다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/378.jpg) 
 
#### 4.4.3. 사용법

상단 TAB의 사전이름을 클릭하면 해당 사전페이지로 이동한다.

기본적으로 리스트가 나타나며, PAGE NAVIGATION을 이용하여 모든단어를 조회할 수 있다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/377.jpg) 

만약 특정단어를 찾고자 한다면 검색창에 단어를 입력후 `Enter`키 를 누르면 전후방 일치로 검색결과를 보여준다.

정확하게 일치하는 단어를 찾고자 하면 `Exact Match` 옵션을 선택한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/379.jpg) 

- `Download` : 모든 단어를 CSV형식으로 파일로 내려받는다.
- `Edit` : 편집모드로 변경한다.

편집모드로 변경되면 각 단어앞에 선택박스가 나타난다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/381.jpg) 

- `Clean` : 사전내의 모든 단어를 삭제한다.
- `+` : 단어를 추가한다. 단어추가 팝업이 나타난다.
- `-` : 선택한 단어만 삭제한다.
- `View` : 편집모드를 끝내고 View모드로 변경한다.

```
사전도구의 삭제기능은 수행후 취소가 불가능하므로, 신중하게 선택하도록 한다.
```

- `+` 클릭시 단어추가 팝업이 나타난다.

단어입력후 `Enter`키 를 누르면 즉시 입력되고, 중복단어 존재시 에러메시지가 뿌려진다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/380.jpg) 

- `Put` : 단어를 추가한다
- `File Upload` ... : 파일선택창이 나타나며, 파일선택시 즉시 사전DB로 데이터가 입력된다.

```
File Upload시 미리 존재하는 단어와 중복될 경우 입력이 중단될수 있으므로, 
Clean을 이용해 기존 데이터를 지우고 upload하는 것을 추천한다.
```
 
##### 사전 텍스트 파일 포맷

줄단위로 Entry를 구분하며, 사전별로 한줄씩 사용되는 포맷은 다음과 같다.

- SET 사전 : 하나의 단어
- MAP 사전 : 키워드 [탭] 단어1[,단어2,...]

### 4.5. 컬렉션

#### 4.5.1. Overview

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/335.jpg) 

- `Create Collection` : 컬렉션을 생성할 수 있는 팝업이 나타난다.
- `STOP` : 컬렉션을 정지한다.

정지상태로 변경시 아래와 같이 InActive상태로 변경되며,  `START` | `REMOVE` 버튼이 나타난다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/336.jpg) 

- `START` : 컬렉션 시작
- `REMOVE` : 컬렉션 삭제. STOP상태에서만 삭제가능하다.
 
#### 4.5.2. Schema

##### Overview

필드, 주키, 분석기, 검색인덱스, 필드인덱스, 그룹인덱스를 한눈에 확인할 수 있다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/337.jpg) 

1) Fields

- ID : 필드아이디 (영문 숫자 조합)
- Name : 필드명 (사람이 이해하기 쉬운 문자열)
- Type : 필드타입

|타입명		|설명							|
|-----------|-------------------------------|
|ASTRING	|영문자 데이터					|
|STRING		|영문포함 2바이트문자 데이터	|
|DATETIME	|날짜형							|
|INT		|INT형 정수						|
|LONG		|LONG형 정수					|
|FLOAT		|FLOAT형 실수					|
|DOUBLE		|DOUBLE형 실수					|

- Length : 문자형 필드의 경우 문자열길이. 비워놓으면 가변길이이고, 입력시 고정길이 필드로 설정된다.
- Remove Tags : HTML태그를 자동제거 여부
- Multi Value : 다중값 필드여부
- Multi Value Delimiter : 다중값으로 변환시 사용할 구분자

2) Primary Keys

주키는 여러개 추가시 복합키로 동작한다.

- Field : 주키로 이용할 필드아이디

3) Analyzers

컬렉션 내부에서 사용할 분석기를 설정한다.

- ID : 내부 분석기 별명 아이디. Search Indexes 항목에서 이 ID를 사용한다.
- Core Pool Size : 초기 분석기 POOL에 미리 만들어 놓을 분석기 객체 갯수
- Maximum Pool Size : 분석기 POOL에 유지할 최대 분석기 객체 갯수
- Analyzer : 분석기명. 분석기 Plugin에 설정된 이름을 사용한다. 이름포맷 = `Analysis ID` . `Analyzer ID`

4) Search Indexes (검색색인)

검색할 필드를 설정한다.

- ID : 필드아이디(영문 숫자 조합)
- Name : 필드명 (사람이 이해하기 쉬운 문자열)
- Field : 검색할 필드. 위의 Fields 에서 설정해놓은 필드아이디 입력. 여러필드입력시 결합필드 검색가능하다.
```
여러 필드 입력시 한줄에 하나씩 필드아이디를 입력한다.
```
- Index Analyzer : 색인시점에 사용할 분석기. 위의 Analyzers에서 설정한 분석기 아이디를 사용한다. 여러필드입력시 분석기도 여러개 입력한다.
```
색인분석기의 갯수는 Field에서 입력한 필드갯수와 동일해야 한다.
여러개의 분석기 입력시 한줄에 하나씩 분석기아이디를 입력한다.
```
- Query Analyzer : 검색시점에 사용할 분석기. 위의 Analyzers에서 설정한 분석기 아이디를 사용한다.
```
쿼리분석기는 색인분석기와 달리 오직하나만 입력 가능하다.
```
- Ignore Case : 대소문자 구분 검색 여부
- Store Position : 색인시 단어의 위치를 저장할지 여부. 위치 저장시 인접검색이 가능하다.
- Position Increment Gap : 색인단어의 위치를 저장하여 결합 검색필드를 만들때, 각 필드간 위치차이를 설정한다. 서로다른 필드간 단어가 인접하지 않도록 조정해야 할 때 사용한다.

5) Field Indexes (필드색인)

- ID : 필드아이디(영문 숫자 조합)
- Name : 필드명 (사람이 이해하기 쉬운 문자열)
- Field : 정렬/필터링용도의 필드. 위의 Fields 에서 설정해놓은 필드아이디 입력.
- Size : 필드색인시 사용할 문자열 필드의 데이터의 길이. 문자열 앞에서부터 사용한다.
```
필드색인은 고정길이어야 하므로, 정수/실수/Datetime형은 그대로 사용가능하나, 
STRING/ASTRING 문자열 필드의 경우 가변길이라면 Size를 설정해주어야 한다.
```

6) Group Indexes (그룹색인)

- ID : 필드아이디(영문 숫자 조합)
- Name : 필드명 (사람이 이해하기 쉬운 문자열)
- Field : 그룹핑용도의 필드. 위의 Fields 에서 설정해놓은 필드아이디 입력.

7) View Work Schema

수정된 스키마를 확인한다. 수정된 스키마는 다음 전체색인시 반영되며, 전체색인후 수정스키마는 삭제된다.

##### Work Schema

다음번 전체색인시 반영을 위해 수정중인 스키마를 확인한다.

전체색인이 정상적으로 수행되었다면 내용이 비어있게 된다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/338.jpg) 

`View Schema` : 현재 서비스중인 스키마 페이지로 돌아간다.

`Edit Work Schema` : 스키마를 수정한다.

```
스키마수정시 현재 서비스중인 스키마를 직접 수정은 불가능하며, 항상 Work Schema를 수정해야 한다.
```
 
#### 4.5.3. Work Schema Edit

스키마를 편집할 수 있는 페이지이다.

- `Remove` : 수정중인 스키마를 삭제하고 수정작업을 취소한다.
- `Save` : 수정사항을 저장한다. 수정사항이 스키마 제약조건에 부합하지 않을 경우 해당 에러가 나타난다.
- `View` : Work Schema View 페이지로 이동한다. 저장하지 않은 항목은 사라지므로 저장후 이동하도록 한다.

나머지 리스트항목에 대한 설명은 상단의 Schema overview의 설명과 동일하므로 참고한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/339.jpg) 
 
####4.5.4. Data

색인수행후 저장된 색인데이터를 조회해볼 수 있다.
 
##### Raw 탭

필드별 데이터를 그대로 보여준다. 

```
저장하지 않도록 설정된 필드는 공백으로 나타나므로, 데이터가 보이지 않을 경우 Schema의 필드설정중 
Store 여부를 확인하도록 한다.
```

1) Primary Key로 설정한 필드값으로 문서를 찾을 수 있다.

2) 필드별 데이터를 보여준다. 폭이 제한되어 있으므로 항목 클릭시 하단에 모든 데이터가 나타난다.

3) Selected Column Data : 클릭한 항목의 데이터를 보여준다.
 
![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/340.jpg) 

##### Analyzed Index 탭

Search Index 필드, 즉 검색필드의 데이터를 분석하여 보여준다.

문장이 분석되어 색인되어 있는 상태를 조회해 볼 수 있으며, 잘못 분석된 경우 사용자사전에 단어를 추가하는 것으로 해결 할 수 있다.

```
검색필드만 분석기 설정이 존재하므로, 검색필드 데이터만 볼 수 있다.
분석기는 색인분석기가 사용된다.
```

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/341.jpg) 

- 1) 원본 데이터가 나타난다.
- 2) 분석된 데이터가 나타난다.
 
#### 4.5.5. Datasource

수집대상이 되는 원본데이터를 설정한다.

설정은 전체색인용 수집과 증분색인용 수집으로 나누어 진다.

각 수집설정은 여러개의 다양한 원본데이터를 가질 수 있으며, `Add Datasource` 를 통해 무제한으로 추가 가능하다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/342.jpg) 

1) Full Indexing

- Name : 데이터소스 이름
- Enable : 사용중인지 여부
- Reader / Modifier : 데이터소스 Reader Class명 / 데이터소스 Modifier Class명
- `Edit` : 데이터소스 설정 수정

2) Add Indexing

위의 Full indexing 과 동일하다.

3) JDBC List

원본 데이터가 DB에 존재할때 참조할 수 있는 JDBC 리스트이다. 컬렉션에 관계없이 전영역에서 공통으로 사용된다.

##### Edit

데이터소스 수정

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/343.jpg) 

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- JDBC : 접속할 DB에 대한 JDBC 설정. 아직 JDBC설정값이 없는 경우 Create New.. 를 클릭하여 새로 생성한다.
- Bulk Size : Reader가 DB에서 한번씩 데이터를 가져오는 갯수. Bulk Size만큼 메모리에 쌓아두기 때문에, 값이 아주 큰 경우 OutOfMemory 에러가 발생할 수 있으니, 100정도의 디폴트값을 이용하는 것을 추천한다.
- Fetch Size : JDBC의 Statement fetch-size를 가리킨다. 값이 0이면 각 JDBC별 디폴트값이 사용되고, -1이면, read-only cursor로 동작한다. JDBC수집중 OutOfMemory가 발생한다면 -1을 사용한다.
- Data SQL : 색인할 데이터를 가져오는 SQL 쿼리문. 증분색인의 경우 ${last_index_time} 값을 통해 마지막 증분색인 시작 시간, 증분 색인이 없을 경우 마지막 전체 색인 시간을 String 값으로 내보낸다. 이를 통해 이전 마지막 색인의 시작 시간을 Data SQL 쿼리에 추가할 수 있다.
```
SELECT
	id
    ,title
	....
	,view_cnt
FROM TB_XXX_XXXXX_PM M
WHERE 1=1
AND total_yn = 'Y'
AND modidate >= date_add(${last_index_time},  interval -1 minute)
```
	${last_index_time}을 사용할 경우 위의 예시처럼 사용하도록 한다.

- Delete SQL : 삭제할 아이디를 가져오는 SQL 쿼리문. 여기에 Select되는 필드는 컬렉션 스키마의 주키가 되어야 한다.
```
Delete SQL의 Select할 필드에는 오직 하나의 주키 필드만 가져오도록 한다. 
```
- Before SQL : Data SQL이 데이터를 수집하기 전에 실행되는 SQL쿼리문.
- After SQL : Data SQL이 데이터를 수집한 뒤 마지막으로 실행되는 SQL쿼리문.
```
Before SQL과 After SQL 은 오직 Update SQL만 실행할 수 있다. Select 문의 사용할 수 없다.
```
 
#### 4.5.6. Index

색인상태를 확인하고 색인관리작업을 수행할 수 있다.

##### Status

색인상태확인

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/345.jpg) 

1) 색인노드의 색인데이터 상태

- Indexing Node : 색인노드명
- Data Path : 색인데이터 위치
- Total Document Size : 색인문서수
- Total Disk Size : 색인데이터 용량
- Create Time : 색인데이터 생성시각
- Segment Size : 내부 세그먼트수
- Revision UUID : 색인데이터 버전

2) 각 노드별 색인데이터 상태

위와 동일

3) 색인결과

- Type : `FULL` = 전체색인. `ADD` = 증분색인
- Result : 색인결과
- Scheduled : `TRUE` = 자동스케쥴. `FALSE` = MANUAL실행
- Documents : 색인문서수
- Inserts : 추가문서수
- Updates : 업데이트문서수
- Deletes : 삭제문서수
- Start : 시작시각
- End : 종료시각
- Duration : 소요시간
 
##### Schedule

색인스케쥴

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/346.jpg) 

1) Full Indexing

전체색인 스케쥴 설정

- Scheduled : 스케쥴 사용여부
- Base Date : 색인작업의 기준이 되는 시각. 이 시각을 기준으로 Period만큼 반복적으로 색인작업이 실행된다.
- Period : 색인작업주기

2) Add Indexing

증분색인 스케쥴 설정

내용은 1)과 동일

3) Update Schedule

스케쥴을 검색엔진에 적용한다.
 
##### Build

색인작업실행

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/347.jpg) 
 

1) 컬렉션 색인실행

- `Run Full Indexing` : 전체색인 작업실행
- `Run Add Indexing` : 증분색인 작업실행
- `Stop Indexing` : 전체/증분 색인작업 취소

2) 색인작업상태

- Auto Update : 선택시 하단의 작업상태가 자동으로 업데이트 된다.
- Type : `FULL` = 전체색인. `ADD` = 증분색인
- State : 색인상태. SUCCESS = 정상종료. RUNNING = 색인중. FAIL = 색인실패. CANCEL = 색인취소.
- Document Count : 색인문서수
- Schedule : `TRUE` = 자동스케쥴. `FALSE` = MANUAL실행
- Start : 시작시각
- End : 종료시각
- Duration : 소요시간

##### History

색인작업 결과리스트

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/348.jpg) 
 
##### Management

색인데이터관리

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/349.jpg) 

1) Index Copy

- Source : 복사할 원본을 선택한다. 하나만 선택할 수 있다.
- Destination : Source의 데이터가 복사될 대상을 선택한다. 여러노드를 선택할 수 있다.

2) Index Restore

이전 전체색인 시점으로 복구할 노드를 선택한다. 여러노드를 선택할 수 있다.
 
#### 4.5.7. Config

컬렉션설정
 
![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/350.jpg) 

1) General Information

- Collection Name : 컬렉션명
- Index Node : 색인노드 아이디
- Search Node : 검색노드 아이디. 컴마구분 여러개 입력가능.

2) Data Plan

- Data Node List : 데이터노드 아이디. 컴마구분 여러개 입력가능.
- Data-sequence-cycle : 데이터 시퀀스 순환사이즈. 1이상의 정수. 순환사이즈가 2이면 전체색인이 실행될때마다 data0 -> data1 -> data0 식으로 번갈아가면서 데이터가 생성된다. 3이면 data0, data1, data2 를 번갈아가면서 사용한다.
- Segment-revision-backup-size : 세그먼트하위의 리비전을 백업으로 유지할 갯수. 0이상의 정수. 증분색인이 실행될때마다 리비전이 하나씩 생성되므로 크기는 계속 늘어나게 된다. 3정도의 수치로 유지하도록 한다.
- Segment-document-limit : 증분색인시 이전 세그먼트의 문서갯수가 이 수치를 넘어서면 새로운 세그먼트를 생성하면서 증분색인이 실행된다. 세그먼트가 너무 커지면 증분색인 속도가 저하될 수 있으므로, 2,000,000 정도의 수치로 유지하도록 한다.
- Full-Indexing Segment-size : 전체색인시 생성할 세그먼트 수이며, 2이상일 경우 해당갯수의 색인쓰레드가 동시에 여러개의 세그먼트로 문서를 색인한다. 이 수치가 1이면 병렬색인을 사용하지 않는다.

3) Update Settings

설정값을 저장하면, 컬렉션을 가지고 있는 전체 노드에 즉시 적용된다.
 
### 4.6. Analysis

#### 4.6.1. Plugin 전체

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/351.jpg) 
 
#### 4.6.2. 기본 분석 Plugin

검색엔진에서 기본적으로 내장된 분석 Plugin

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/354.jpg) 

1) Overview

- ID : Analysis 아이디
- Namespace : 네임스페이스. 분석Plugin의 네임스페이스는 항상 Analysis이다.
- Class : Plugin 클래스명
- Version : Plugin 버전
- Description : Plugin 설명

2) Analyzer

- ID : 분석기 아이디
- Name : 분석기 이름
- Class : 분석기 클래스명

##### 제공분석기

|#	|ID					|Name							|Class													|
|---|-------------------|-------------------------------|-------------------------------------------------------|
|1	|BASIC.STANDARD		|Standard Analyzer 				|org.apache.lucene.analysis.standard.StandardAnalyzer	|
|2	|BASIC.PRIMARY		|Primary Word Analyzer			|org.fastcatsearch.ir.analysis.PrimaryWordAnalyzer		|
|3	|BASIC.KEYWORD		|Keyword Analyzer				|org.apache.lucene.analysis.core.KeywordAnalyzer		|
|4	|BASIC.WHITESPACE	|Whitespace Analyzer			|org.apache.lucene.analysis.core.WhitespaceAnalyzer		|
|5	|BASIC.NGRAM		|NGram Analyzer					|org.fastcatsearch.ir.analysis.NGramWordAnalyzer		|
|6	|BASIC.CSV			|Comma separated value Analyzer	|org.apache.lucene.analysis.core.CSVAnalyzer			|
 
#### 4.6.3. 한글분석기 Plugin

##### Settings

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/352.jpg) 

1) Overview

위와 동일

2) Dictionary

- ID : 사전아이디
- Name : 사전이름
- Dictionary File : 사전파일명
- Dictionary Type : 사전타입
- Ignore Case : 대소문자무시 여부
- Columns : DB저장시 table 컬럼정보

3) Analyzer

위와 동일

4) Action 

액션은 HTTP서비스를 통해 분석기 서비스를 제공한다.

- URI : 접근 URI. 접근경로는 *++http://검색엔진IP:서비스PORT/[액션URI]++*
- Method : GET 또는 POST 방식으로 
- Class : 액션 클래스명

##### Tools

분석기툴을 통해 분석시뮬레이션 수행

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/353.jpg) 

1) Query Words

분석할 문장을 입력후 `Enter`키 를 누르면 하단에 분석결과가 표시된다.

- Simple : 분석된 단어들만 표시한다.
- Detail : 각 분석기 Plugin에서 제공하는 기능으로, 더 상세한 분석결과를 표시할 수 있다.
```
Detail 분석은 각 분석기 Plugin에서 구현하여 제공하는 기능이므로, 
구현이 안된 Plugin의 경우는 제공하지 않는다면 메시지가 표시된다.
```
- For Query : 쿼리 키워드 분석용도이면 선택한다. 선택하지 않으면, 색인 키워드 분석용도로 실행된다.

2) 분석결과

분석결과표시
 
### 4.7. 서버노드

#### 4.7.1. Overview

분산 노드들의 전체정보를 한번에 확인한다.
 
![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/355.jpg) 

1) Node Settings

분산시스템에 구성되어 있는 모든 노드의 정보를 보여준다.

- ID : 노드 아이디
- Name : 노드 이름
- IP Address : 노드 IP주소
- Node Port : 노드 PORT
- Service Port : 서비스 PORT
- Enabled : 노드를 사용허가되어 있는지 여부. Disabled이라면 검색엔진 시작시 Active상태가 될 수 없다.
- Active : 노드가 연결되어 있는지 여부. Inactive라면 소켓연결이 끊긴 노드이다.
```
각 노드는 시작시 마스터노드에 연결요청을 수행하므로, 연결이 끊긴 노드라도 재시작시 Active상태로 변하게 된다.
```

2) System Health

각 노드의 시스템 상태를 보여준다.

3) System Information

각 노드의 시스템 및 JVM정보를 보여준다.
 
#### 4.7.2. 개별서버정보

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/356.jpg) 

1) Node Settings

노드 정보를 보여준다.

- `Restart` : 노드를 재시작한다. 
```
Restart는 JVM이 완전히 종료되는 것이 아니며, 내부 컴포넌트들만 재시작되는 방식이다. 
OutOfMemory 와 같은 메모리부족에러 발생시에는 JVM이 완전히 종료된후 시작되어야 하므로, 
콘솔창에서 직접 재시작해야 한다. 
```
- `Shutdown` : 노드 종료

2) System Health

노드의 시스템사용률 상태를 보여준다.

3) Task Status

노드에서 실행중이거나 실행이 끝난 Task들의 상태로 보여준다.

4) System Information

노드의 시스템 정보를 보여준다.

5) Collection Status

서비스중인 검색컬렉션의 정보를 보여준다.

6) Plugin Status

서비스중인 Plugin의 정보를 보여준다.

7) Module Status

검색엔진 내부모듈의 상태를 보여준다.

- `Stop` : 모듈 정지
- `Restart` : 모듈 재시작
- `Start` : 모듈 시작. 정지상태에서만 활성화.

8) Thread Status

검색엔진 내부 쓰레드들의 상태를 보여준다.

- Group : 쓰레드 그룹명
- Name : 쓰레드 이름
- Tid : 쓰레드 번호
- Priority : 우선순위
- State : 쓰레드 상태
- Daemon : 쓰레드 종류. 데몬쓰레드인지 유저쓰레이드인지 표시.
- Alive : 쓰레드 상태. Alive인지 표시.
- Interrupted : 인터럽트를 받았는지 표시.
- `Stacktrace` : 실행중인 Call Stack Trace 표시.
 
### 4.8. 로그

#### 4.8.1. Notifications

검색엔진에서는 중요한 이벤트에 대해서 Notification으로 제공한다.

Notification은 내부DB에 기록되며, 선택한 Notification에 대해 운영자에게 이메일로 알림설정도 가능하다.

##### List

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/357.jpg) 

1) Notification 메시지를 간단하게 보여준다.

2) 리스트에서 클릭을 하면 하단에 전체 메시지를 보여준다.
 
##### 알림설정

운영자가 선택한 Notification은 이메일과 SMS로 알림등록을 할 수 있다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/358.jpg) 

- Notification Code : 알림코드
- Notification Type : 알림타입명
- Alert To : 알림을 받을 사람
- Edit : 설정을 수정할 수 있는 팝업이 나타난다.

##### 설정수정

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/359.jpg) 

- Notification Type : 알림타입을 선택한다.

알림타입은 아래와 같다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/360.jpg) 

- Alert To : 알림받을 사람. 형식은 아래와 같다.

|구분	|내용			|
|-------|---------------|
|SMS	|SMS : 아이디	|
|EMAIL	|EMAIL : 아이디	|

※ 컴마구분으로 여러개를 등록할 수 있다.
 
#### 4.8.2. 에러사항

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/361.jpg) 

1) 에러메시지를 간단하게 보여준다. 에러가 발생한 노드도 함께 표시한다.

2) 리스트에서 클릭을 하면 하단에 전체 메시지를 보여준다.
 
#### 4.8.3. 실행중인 작업

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/362.jpg) 

- Task : 작업내용
- Elapsed : 작업진행 소요시간
- Start : 작업이 시작한 시각
 
### 4.9. 테스트

#### 4.9.1. Search

##### 구조화된 쿼리검색

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/316.jpg) 

- COLLECTION ID : 검색대상 컬렉션
- FIELD LIST : Select할 필드. 컴마구분으로 여러개를 입력가능.
- SEARCH : 검색조건
- FILTER : 필터조건
- GROUP : 그룹핑조건
- RANK : 정렬조건
- START : 시작번호. 최소 1부터 시작한다.
- LENGTH : 가져온 상위 결과갯수
- HIGHLIGHT TAG : 매칭키워드에 대해 하이라이팅할 HTML 태그. : 구분으로 시작태그와 종료태그를 입력한다.
- SEARCH OPTION : 검색옵션
- QUERY MODIFIER : 쿼리모디파이어 클래스명
- RESULT MODIFIER : 결과모디파이어 클래스명
- STORED PROCEDURE : 검색 저장프로시저
- USER DATA : 사용자 데이터
- TIMEOUT : 검색요청 타임아웃시간. 초 단위.
- `노드선택 Drop-Down` : 검색노드를 선택
- `Search | Grouping` : 검색 또는 그룹핑 검색을 선택한다. 그룹핑 검색선택시 검색결과없이 그룹핑 결과만 반환한다.
- `Search` : 검색수행
- `Explain` : 검색결과에 검색과정을 유추할 수 있는 부가정보를 함께 포함한다.
- `Clear` : 폼 지우기

```
더 자세한 사항은 쿼리API 매뉴얼을 참고한다.
```
 
##### 쿼리스트링 검색

폼없이 쿼리스트링자체로 검색을 수행한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/317.jpg) 
 
#### 4.9.2. System DB 테스트

SQL문을 입력하고 Execute를 클릭하면 결과가 하단에 표시된다.

DB명에 system 입력시 시스템 DB를 사용하고, Plugin DB를 사용하기 위해서는 plugin/[플러그인ID] 를 입력한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/318.jpg) 

 
### 4.10. 검색데모

검색페이지를 만들지 않고도 검색데모페이지를 통해 검색을 시뮬레이션 해볼 수 있다. 

상단메뉴중 `Search` 를 클릭한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/369.jpg) 
 
#### 4.10.1. 검색데모페이지

상단의 검색창에 검색어를 입력하면 하단에 검색결과가 표시된다.

페이지 설정을 하지 않은 경우 검색결과가 나타나지 않으므로, 우측 상단의 `Config` 를 클릭하여 설정페이지로 유지한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/363.jpg) 

1) 검색창

2) 결과리스트

3) More result : 개별 검색페이지로 이동한다.
 
#### 4.10.2. 검색데모설정

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/364.jpg) 

1) Common Settings

- Total Search List Size : 통합검색페이지에서 각 카테고리별 검색결과의 표시갯수. 기본적으로 5를 사용.
- Search List Size : 개별 카테고리 검색결과의 표시갯수. 기본적으로 10개를 사용.

2) Category List

- Display Order : 개별 카테고리간의 표시 순서. 1부터 시작.
- `Remove` : 해당 카테고리를 삭제한다.
- Category Name : 카테고리 표시 이름 (사람이 이해하기 쉬운 문자열)
- Category ID : 카테고리 아이디(영문 숫자 조합)
- Search Query : 검색엔진 쿼리. 입력키워드는 `#keyword` 를 사용한다.
- Title Field : 제목영역에 표시할 내용. 내용중에 $로 시작하는 필드명이 존재시 검색결과 필드데이터로 치환된다.
- Body Field : 본문영역에 표시할 내용. 내용중에 $로 시작하는 필드명이 존재시 검색결과 필드데이터로 치환된다.

3) Relate Keyword

- URL : 연관검색어 호출 URL. 입력키워드는 `#keyword` 를 사용한다.

4) Realtime Popular Keyword

- URL : 실시간검색어 호출 URL. 입력키워드는 `#keyword` 를 사용한다.

5) UI Customizing

- Stylesheet : 페이지에 적용할 스타일시트 
- Javascript : 페이지에 적용할 자바스크립트
- `Add Category` : 카테고리를 하나 추가한다.
- `Save Changes` : 변경사항을 저장하고 검색데모페이지로 이동한다.
 
###4.11. 계정관리

운영자별로 권한을 설정하여 사용하기 위해서는 우측 상단의 설정버튼을 클릭한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/368.jpg) 
 
####4.11.1. 그룹

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/365.jpg) 

- `New Group` : 새로운 그룹을 추가한다.
- Group Name : 그룹이름.
- Dictionary : 사전관리권한
- Collections : 컬렉션관리 권한
- Analysis : 분석Plugin 관리 권한
- Servers : 서버관리 권한
- Logs : 로그관리 권한
- Settings : 시스템셋팅 권한
- `Edit` : 권한을 수정한다.
 
#### 권한 종류

|구분		|설명					|
|-----------|-----------------------|
|WRITABLE	|설정읽기 및 수정 가능	|
|READABLE	|설정읽기 가능			|
|NONE		|해당메뉴 접근 불가		|

#### 4.11.2. 사용자

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/366.jpg) 

- `New User` : 새로운 사용자를 추가한다.
- User Name : 사용자 이름
- User Id : 사용자 아이디
- Group : 그룹이름
- Email : 알림 설정을 받을 이메일 주소
- SMS : SMS메시지가 수신 가능한 휴대전화번호 
- `Edit` : 사용자정보 및 그룹을 수정한다.

##### 새로운 사용자 추가

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/367.jpg) 
 
### 4.12. 기타

#### 4.12.1. 실행중인 작업확인

상단 메뉴에는 실행중인 작업을 간단하게 확인할 수 있는 버튼을 제공한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/main-manual/ko/img/370.jpg) 
 
#### 4.12.2. JDBC DB Vendor 추가하기

[검색엔진설치위치] / collections / jdbc-support.xml 에 <jdbc-driver>를 추가한다.

```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<jdbc-support>
  <jdbc-drivers>
    <jdbc-driver id="mysql" name="MySQL Driver" driver="com.mysql.jdbc.Driver" 
      urlTemplate="jdbc:mysql://${host}:${port:3306}/${dbname}"/>
    <jdbc-driver id="oracle-thin" name="Oracle Thin Driver" driver="oracle.jdbc.driver.OracleDriver" 
      urlTemplate="jdbc:oracle:thin:${host}:${port:1521}:${dbname}"/>
    <jdbc-driver id="jtds-mssql" name="jTDS Microsoft SQL" driver="net.sourceforge.jtds.jdbc.Driver" 
      urlTemplate="jdbc:jtds:sqlserver://${host}:${port:1433}/${dbname}"/>
    <jdbc-driver id="cubrid" name="CUBRID Driver" driver="cubrid.jdbc.driver.CUBRIDDriver" 
      urlTemplate="jdbc:cubrid:${host}:${port:33000}:${dbname}:::"/>
  </jdbc-drivers>
</jdbc-support>
```

```xml
<jdbc-driver id="[드라이버아이디]" name="[드라이버이름]" driver="[드라이버클래스]" 
      urlTemplate="[JDBC URL 템플릿]"/>
```
JDBC URL 템플릿 예약어

|${host}			|호스트 주소		|
|-------------------|-------------------|
|${port:기본포트}	|DBMS 서비스 PORT	|
|${dbname}			|접속할 DB명		|
 
