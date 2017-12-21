운영자매뉴얼
=========

목차
---
1. 구성
2. 구동
3. 운영상태확인
4. 중단
5. 모니터링
6. 상황별조치

<span></span>
1. 구성
------

### 디렉토리 구조

fastcatsearch 검색엔진 설치 시 다음과 같은 디렉토리가 구축되어 있으며 각각의 역할은 다음과 같다.

| 구분 | 설명 |
|-----|-----|
|bin | 구동스크립트 위치 |
|collections | 색인 파일들이 위치함 |
|conf|설정 파일들이 위치함|
|db|색인기록 등을 저장할 로컬 데이터베이스|
|lib|검색엔진 구동에 필요한 jar 라이브러리 파일들|
|logs|검색엔진 로그파일들|
|plugin|플러그인 (분석기 등)|
|service|데몬서비스 구동 라이브러리|

### 핵심 파일들

|디렉토리|구분|설명|
|------|----|---|
|bin|start.sh|시작 스크립트|
||stop.sh|종료 스크립트|
||daemon.sh|시작스크립트에서 이용하는 핵심 스크립트 파일|
|collections|collections.xml|컬렉션 목록|
|conf|node-list.xml|분산서버 목록|
||id.properties|현재노드 정보 (서비스포트)|
||system.properties|검색서버 세부설정|
||logback.xml|검색엔진 로그 생성 규칙들기<br>(로그레벨, 보존기간, 로테이션 등)|
|db/system||로컬데이터베이스|
|lib|fastcatsearch-core.jar|검색엔진 핵심 라이브러리 파일|
||fastcatsearch-server.jar|검색서비스 구동 라이브러리 파일|
||document-filter.jar	문서필터 라이브러리 파일|
|logs|indexing.log|색인 수행 로그|
||search.log|검색 로그|
||system.log|시스템 로그|
|plugin/analysis||분석기 플러그인|
||{분석기ID}/plugin.xml|분석기 설정 파일|
||db / dict|분석기에서 사용하는  사전, 사전db|



2. 구동
----

### 구동스크립트
리눅스에서는 `bin/fastcatsearch start`이며, 윈도우즈에서는 `fastcatsearch.bat`이다.

### 스크립트 설정
`bin/environment.sh` 를 열면 아래와 같은 내용을 볼수 있다.
```
heap_memory_size=768m
java_path=
daemon_account=fastcat
```
`heap_memory_size`는 JVM메모리를 설정하며,
`java_path`는 java가 사용자의 PATH에 걸려있지 않을 경우 설정해주며,
`daemon_account`는 서비스등록하여 자동구동시 사용되는 계정이름이다.

3. 운영상태확인
----------

`*nix` 운영체제 에서 검색엔진의 프로세스 상태는 다음과 같이 확인 가능하다.
반드시 `<검색엔진 설치위치>/bin` 에서 실행해야 `.pid` 파일을 찾을 수 있다.
```
$ ps -ef `cat .pid`
root  27351  3749  0 11:33 pts/13   00:01:44 /usr/lib/jvm/java-7-oracle/bin/java -Xmx1g -server -Dlogback.configurationFile=........
```
다른 방법으로는 다음과 같이 확인 가능하다. 어느위치에서도 실행 가능하다.
```
$ ps -ef | grep java | grep fastcatsearch | grep Bootstrap
root  27351  3749  0 11:33 pts/13   00:01:44 /usr/lib/jvm/java-7-oracle/bin/java -Xmx1g -server -Dlogback.configurationFile=........
```

4. 중단
---

### 중단시 주의사항

증분 색인이 진행 중일 경우 검색엔진 정지시 해당 색인 전체가 손상될 염려가 있으므로, 관리도구상단의 Task리스트 에서 증분 색인 수행중이 아님을 확인한 후 검색엔진을 재시작 하도록 한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/op-manual/ko/img/219.jpg)

### 중단절차

검색엔진 중단은 스크립트 실행 또는 관리도구에서 수행이 가능하다.
관리도구를 이용하면 여러개의 노드를 Web 에서 관리하기 편리하다.

#### 중단 스크립트 실행
검색엔진 중단 및 재시작은 다음과 같이 수행 가능하며 `<검색엔진 설치위치>/bin` 디렉토리에서 실행 가능하다.
```
$ bin/fastcatsearch stop
```
또는
```
$ ps -ef |grep fastcatsearch | grep Bootstrap
```
명령을 통해 PID를 알아낸후
```
$ kill -9 <PID>
```
로 중단가능하다.

#### 관리도구에서 정지

`Manager > Servers > 노드명` 페이지에서 `Shutdown`을 클릭하여 해당 노드를 정지시킨다.
관리도구를 통한 엔진시작기능은 제공하지 않는다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/op-manual/ko/img/220.jpg)


5. 모니터링
----------

### 대시보드
관리도구의 Dashboard 페이지에서 전체적인 상황을 확인한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/op-manual/ko/img/214.jpg)

|#|작업명|내용|
|---|---|---|
|1	|검색요청 처리상황을 확인한다.|대시보드 상단에 QPS를 확인하면서 검색요청이 잘 들어오고 있는지 확인.<br>요청이 들어오고 있지 않으면 검색엔진이 정상인지 확인필요|
|2	|컬렉션 서비스 상황을 확인한다.|Collections 항목에서 컬렉션별 문서수가 정상인지 확인.<br>Update Time이 너무 오래되지 않았는지 확인. |
|3	|색인처리 상황을 확인한다.|Indexing Result 항목에서 컬렉션별 색인 Status가 Success인지 확인, Fail의 경우 원인을 로그에서 찾아봄.<br>색인 Time이 너무 오래되지 않았는지 확인.<br>색인이 실패하였거나 검색이 되지 않을 경우 컬렉션 개별 페이지로 이동하여 색인조치필요. |
|4	|서버상태와 디스크, CPU, Memory 사용현황을 확인한다. |Server Status 항목에서 서버별로 Alive인지, Disk와 CPU 및 Memory사용량이 너무 높지 않은지 확인.<br>Disk는 90%이상이면 증설필요.<br>CPU는 80%이상이 계속 지속되면 원인 확인필요.<br>Memory는 100%사용시에도 정상적으로 동작가능하나, 오랜시간 계속 지속될 경우 원인 파악 및 메모리 증설 필요.|
|5	|최근 이벤트를 확인한다.	|최근 색인메시지등의 이벤트를 확인하여 에러발생의 경우 원인 파악 및 조치필요|

### 전체 컬렉션 상태

`Manager > Collections > Overview` 페이지에서 모든 컬렉션이 `Active`상태인지 확인한다.
`Active`가 아닌 컬렉션은 오른쪽의 `START` 를 클릭하여 컬렉션서비스를 시작한다.
시작상태에서는 `STOP` 만 가능하다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/op-manual/ko/img/211.jpg)

### 색인상태

`Manager > Collections > 컬렉션명 > Indexing` 페이지에서 `Status`을 누르면, 색인상태확인이 가능하다.


![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/op-manual/ko/img/216.jpg)


|#	|작업명			|내용		|
|-------------------|-------------------|-------------------|
|1	|노드별 색인 상태를 확인한다. |노드간 색인정보가 일치하는지 확인한다.<br>문서갯수, Revision UUID가 동일한지 확인.<br>Update Time이 최신인지 확인	|
|2	|전체색인 및 증분색인의 결과를 확인한다. |전체색인과 증분색인이 모두 Sucess인지 확인.<br>증분색인의 경우 추가문서가 없을 경우 Cancel로 나타날수 있음.<br>색인문서 갯수가 정상적인지 확인.<br>색인시간이 너무 오래걸리지 않았는지 확인	|


### 에러로그확인

`Logs > Exceptions` 에서 에러로그를 확인할 수 있다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/op-manual/ko/img/217.jpg)

|#	|작업명	|내용	|
|-------------------|-------------------|-------------------|
|1	|에러사항을 확인후 클릭한다.|검색에러, 내부 시스템 에러등을 확인할 수 있으며 검색에러는 대부분 검색어 이상으로 인한 파싱에러이므로, 내부 시스템에러를 중점적으로 확인하도록 한다.|
|2	|자세한 에러 Trace를 확인한다.|에러로그중 시스템에 이상을 줄 만한 에러는 OutOfMemory Error 또는 Heap space error이다.<br>메모리 관련 에러 발생시 검색엔진을 재시동하도록 한다.|

6. 상황별조치
------------

### 검색이 장시간 반응하지 않거나 아무런 결과를 내지 않는 경우

#### 1) 프로세스 과부하로 인한 지연 확인
터미널에 접속하여 프로세스 점유상태를 확인한다. (Linux : `top` / AIX : `topas` / windows : `작업관리자`)
다음과 유사한 화면이 출력되면, java 의 프로세스 상태와 메모리 점유 상태를 확인 한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/op-manual/ko/img/218.jpg)

> 만약 지나치게 높다면 해당 프로세스를 재시작 한다.  (해당 프로세스가 검색엔진이 아닐경우, 검색엔진 이 cpu 할당을 정상적으로 받지 못해 지속적으로 지연되는 현상)

> 검색엔진의 프로세스가 1개의 코어를 지속적으로 100% 로 사용 유지하고 있다면 검색엔진 다운을 의심해 볼 수 있으며, 이 경우 검색엔진을 재시작해야 한다.


#### 2) 검색엔진의 정상 작동 여부 확인

검색엔진 이상이 생겼을 경우 가장 먼저 확인해야 할 사항으로 `ps` 명령어로 프로세스의 작동 이상유무를 점검한다.

- 메모리 부족 오류로 인한 엔진 다운
메모리부족 오류로 인한 엔진 다운의 경우 프로세스가 아직 남아있을 수도 있으므로 `ps` 명령으로는 검출되지 않을 수도 있다, 이경우 검색엔진설치 위치 에 `hs_err_pidxxxx.log` 같은 파일이 남아 있는지 확인 해야 한다.
순간 메모리 부족인 경우 관련 스레드만 영향을 받아 전체 시스템은 영향을 받지 않으나, 차후 오류의 소지가 있을 수 있으므로 재시작 해 준다.

- 기타 시스템 이상으로 인한 프로세스 halt
이 경우 프로세스가 남아있지 않으므로 bin/fastcatsearch start 로 시작 시켜 주고, 검색엔진 이외의 다른 프로세스가 cpu 혹은 메모리를 얼마나 점유하는지를 확인 하여, 검색엔진이 원활히 구동될 수 있도록 서비스를 옮겨 둔다.


#### 3) 네트워크 방화벽 상태 점검

마이그레이션 이후 가장 먼저 확인해야 할 사항으로, 서비스 포트 ( 검색엔진 서비스 : 8090 / 노드연동 : 9090 등) 이 정상적으로 열려 있는지 확인한다.

#### 4) 디스크 파손 점검
간혹 서버가 물리적인 외부 충격을 받는다거나 하여 디스크 손상이 있는 경우, 혹은 자기 디스크의 수명이 다하여 디스크의 해당 부분을 접근하는 경우 검색엔진이 순단 현상이 있을 수 있다.
이 경우 간헐적인 응답지연이 있을 수 있으므로, 디스크 점검 프로그램으로 확인해 본다.

### 관리도구가 응답하지 않는 경우

#### 1) 검색엔진 정상 작동 여부 확인
관리도구 첫 화면은 작동하는데, 로그인이 되지 않는다던가 하는 경우 검색엔진 정상 작동 여부를 먼저 확인 해야 한다.

#### 2) 관리도구 정상 작동 여부 확인

- 404 오류 발생시
콘솔에 접속하여 다음 명령어로 관리도구 작동 여부를 확인한다.
```
$ ps -ef | grep java | grep start.jar
localhost    152679      1  0 Feb25 ?        00:13:44 /usr/bin/java -Djetty.home=/home/joara/fastcatsearch-console -D ...
```
프로세스가 발견되지 않는 경우 관리도구가 다운되어 정상 작동되지 않고 있으므로 재시작 해 준다.

- 오류는 발생하지 않으나, 빈 화면 출력시
관리도구 접근 시 404 오류가 발생하거나, 공백페이지가 출력되는 경우, 이 경우 관리도구 컨텍스트가 지워지거나 (내장 웹서버가 `/tmp` 디렉토리를 이용하며, 이 `/tmp` 디렉토리가 지워진 경우 간혹 이러한 오류가 발생) 하여 발생하므로,
다음과 같이 관리도구를 재시작 해 주어야 한다.
```
$ ./fastcatsearch-console restart
Stopping Jetty: OK
Starting Jetty: STARTED Jetty Tue Mar  4 19:17:23 KST 2014
```
만약 /tmp 가 주기적으로 지워져야 한다면 쉘파일 `bin/jetty.sh` 을 수정하여 내장 웹서버의 `tmp` 공간을 다른 곳으로 잡아 둔다.
쉘파일 첫 머리에 다음 한 줄을 추가해 준다.
```
TMPDIR=/home/fastcatsearch/tmp
```

#### 3) 네트워크 방화벽 상태 점검
관리도구 사용포트 ( 8080 ) 과 검색엔진 서비스포트 ( 8090 ) 가 서로 열려 있는지 확인 한다.


### 최신데이터가 검색되지 않는 경우 (색인 미실행)


#### 1) 색인 정상 수행 확인
관리도구를 확인하여 최근에 색인이 정상적으로 수행된 기록을 확인 한다.
색인 확인 방법은 [모니터링 방법 5-3 색인상태] 참조

#### 2) 색인대상 데이터 확인
색인이 수행되지 않았다면 Data-source 에서 색인 대상 데이터가 정상적으로 입력 되어 있는지 확인 한다.
File 색인인 경우 해당 디렉토리가 비어있는지 확인해야 하며,
데이타베이스 색인인 경우 dataSql 프로퍼티가 정상적으로 입력되어 있는지 확인한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/op-manual/ko/img/196.jpg)

#### 3) Database Connection 인증 및 접속 확인

데이터베이스 색인 인 경우, 주기적으로 계정 비밀번호를 변경하여 접속하지 못하는 경우가 있다, 색인에 이용하는 JDBC 계정과 비밀번호를 확인한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/op-manual/ko/img/195.jpg)

#### 4) 디스크 용량 확인

검색엔진의 원활한 활용을 위해 색인 용량은 원본 용량의 약 3배를 확보해야 하며 (운영, 백업, 색인시) 용량이 작은 경우 색인이 정상적으로 만들어 지지 않을 수가 있다.
`df` 명령어를 통해 디스크 용량이 얼마나 남았는지 확인한다.

```
$ df -h
Filesystem            Size  Used Avail Use% Mounted on
/dev/sda             101G  2.6G   94G   3% /
tmpfs                 7.8G     0  7.8G   0% /dev/shm
```

