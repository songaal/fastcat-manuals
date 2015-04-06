운영자매뉴얼
=========

목차
---
1. 구성
2. 구동
3. 운영상태 확인
4. 중단
5. 모니터링 방법
6. 상황 별 조치



1. 구성
---

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
bin/environment.sh 를 열면 아래와 같은 내용을 볼수 있다.
```
heap_memory_size=768m
java_path=
daemon_account=fastcat
```
`heap_memory_size`는 JVM메모리를 설정하며,
`java_path`는 java가 사용자의 PATH에 걸려있지 않을 경우 설정해주며,
`daemon_account`는 서비스등록하여 자동구동시 사용되는 계정이름이다.


3. 운영상태 확인
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

![](http://)

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

![](http://)



5. 모니터링 방법
----------

### 대시보드
관리도구의 Dashboard 페이지에서 전체적인 상황을 확인한다.

||작업명|내용|
1	검색요청 처리상황을 확인한다.	
대시보드 상단에 QPS를 확인하면서 검색요청이 잘 들어오고 있는지 확인
요청이 들어오고 있지 않으면 검색엔진이 정상인지 확인필요
2	컬렉션 서비스 상황을 확인한다.	
Collections 항목에서 컬렉션별 문서수가 정상인지 확인
Update Time이 너무 오래되지 않았는지 확인
3	색인처리 상황을 확인한다.	
Indexing Result 항목에서 컬렉션별 색인 Status가 Success인지 확인, Fail의 경우 원인을 로그에서 찾아봄
색인 Time이 너무 오래되지 않았는지 확인
색인이 실패하였거나 검색이 되지 않을 경우 컬렉션 개별 페이지로 이동하여 색인조치필요
4	서버상태와 디스크, CPU, Memory 사용현황을 확인한다.	
Server Status 항목에서 서버별로 Alive인지, Disk와 CPU 및 Memory사용량이 너무 높지 않은지 확인
Disk는 90%이상이면 증설필요
CPU는 80%이상이 계속 지속되면 원인 확인필요
Memory는 100%사용시에도 정상적으로 동작가능하나, 오랜시간 계속 지속될 경우 원인 파악 및 메모리 증설 필요.
5	최근 이벤트를 확인한다.	최근 색인메시지등의 이벤트를 확인하여 에러발생의 경우 원인 파악 및 조치필요



