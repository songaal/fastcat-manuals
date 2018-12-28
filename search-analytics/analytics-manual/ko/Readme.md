로그분석기 통합매뉴얼
==================

목차
---
1. 시스템개요
2. 로그분석기 설치
3. 서비스 시작/종료
4. 사이트 리포트
5. 사이트 설정관리
6. 시스템 설정관리

<span></span>
1. 시스템개요
------------

###1.1. 개요

로그분석기는 검색 시스템 내 키워드의 검색수, 클릭수 등을 기반으로 하여, 입력된 키워드에 대한 유저들의 반응 기대치를 분석하는데 초점을 맞춘 시스템이다.

검색엔진과는 별도로 독립된 서버로 운영되며, 날짜와 시간별로 유입되는 키워드를 집계하고, 일/주/월/년별로 통계를 수행한다.

###1.2. 시스템 구성

로그분석기는 웹서버등으로부터 API를 통해 검색관련 데이터를 제공받아 주기적으로 통계를 수행한다.
통계데이터는 외부 DB에 저장하고 관리도구를 통해 해당 DB를 조회함으로써 통계결과를 제공한다.


<img src="https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/390.jpg" width="500px"/>

#### 호출흐름도

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/419.jpg)
 
###1.3. 제공기능
 
#### 제공기능리스트

|기능						|관리도구 데이터조회	|HTTP API 제공	|
|---------------------------|-----------------------|---------------|
|실시간 인기검색어			|O						|O				|
|연관검색어					|O						|O				|
|검색횟수 및 응답시간 추이	|O						|				|
|검색어 순위				|O						|O				|
|검색비율					|O						|				|
|검색유입률					|O						|				|
|클릭유입률					|O						|				|

#### 기능상세

##### 실시간 인기검색어

- 최근 유입된 검색키워드를 기반으로 상위 10개의 인기검색어 제공
- 검색횟수, 순위등락폭, 검색횟수변동량 제공
 
##### 연관검색어

- 자주 유입되는 연관성이 있는 키워드 조합을 연관검색어로 제공
- 키워드 검색기능
- 연관검색어 편집기능
- 데이터 다운로드 기능
 
##### 검색횟수 및 응답시간 추이

- 검색횟수 데이터 및 추이 그래프
- 응답시간 데이터 및 추이 그래프
- 시간/일/주/월/년별 기간선택기능
- 임의기간 설정기능
- 카테고리별 조회기능
- 데이터 다운로드 기능
 
##### 검색어 순위

- 특정기간의 상위 인기 검색어 리스트
- 검색횟수, 순위등락폭, 검색횟수변동량 제공
- 전체/신규/급상승/급하강/결과없음 순위제공
- 일/주/월/년별 기간선택기능
- 카테고리별 조회기능
- 데이터 다운로드 기능
 
##### 검색비율

- 특정기간내 검색횟수와 검색비율 제공
- 임의기간 설정기능
- 일/주/월/년별 기간선택기능
- 카테고리별 조회기능
- 검색타입별 조회기능
- 검색타입간의 순위제공 기능
 
##### 검색유입률

- 임의기간 검색유입건수 제공
- 유입서비스별 검색횟수와 비율 제공
- 키워드별 검색횟수 및 비율 제공

##### 클릭유입률

- 임의기간 일별클릭횟수 제공
- 클릭타입별 클릭횟수와 비율 제공
- 키워드별 클릭횟수 및 비율 제공
- 키워드 검색기능
- 데이터 다운로드 기능
- 클릭유입률 통계파일저장
 
###1.4. 외부 API 연계기능

HTTP Rest 방식의 통신을 이용하여 JSON 형태의 결과를 외부에 제공함으로써 유용한 통계자료를 번거로운 작업없이 빠르게 활용할 수 있도록 도와준다.
 
##### 실시간 인기검색어

최근 로그분석기로 유입된 검색키워드를 기반으로 통계를 생성하여 검색어 순위와 등락폭을 제공
 
##### 검색어 순위

일/주/월/년별 상위 인기 검색어 리스트 제공. 운영자가 설정한 카테고리별로 제공
 
##### 연관검색어

자주 유입되는 연관성이 있는 키워드 조합을 연관검색어로 제공

2. 로그분석기 설치
----------------

###2.1. 설치환경

##### 소스빌드

시스템에 MAVEN이 설치되어 있어야 한다.

##### 지원운영체제

Java 1.6이상이 설치된 모든 운영체제에 설치가 가능하다.

##### 하드웨어사양

|구분	|최소사양	|권장사양				|
|-------|-----------|-----------------------|
|CPU	|Pentium 4	|Intel Xeon 1.8GHz이상	|
|Memory	|2G			|4G						|
|Disk	|10GB		|100GB					|
 
###2.2. 소스빌드

####2.2.1. 준비환경

##### 소스빌드 준비환경

|구 분	|내 용																						|
|-------|-------------------------------------------------------------------------------------------|
|Java	|JDK 1.6 이상																				|
|Maven	|Ver 2.0 이상																				|
|기타	|인터넷 접속 가능환경<br/> Maven 빌드시 의존 라이브러리를 받아올 수 있도록 인터넷 환경 필요	|
 
####2.2.2. 다운로드와 빌드
 
##### 소스다운로드

소스는 github에 위치해 있으며, `https://github.com/fastcatsearch/analytics` 에서 다운로드 받는다.

##### 빌드

maven을 사용하여 소스를 빌드한다. 해당위치에서 mvn이 실행가능하도록 환경 Path 에 설정되어 있어야 한다.

`mvn clean package` 명령을 실행하면 아래와 같은 로그를 볼 수 있다.

```
$ mvn clean package
[INFO] Scanning for projects...
[INFO] ------------------------------------------------------------------------
[INFO] Reactor Build Order:
[INFO]
[INFO] analytics
[INFO] analytics-server
[INFO] analytics-web
[INFO] analytics-package
[INFO]
[INFO] ------------------------------------------------------------------------
[INFO] Building analytics 1.14.6
[INFO] ------------------------------------------------------------------------

..중략..

[INFO] ------------------------------------------------------------------------
[INFO] Reactor Summary:
[INFO]
[INFO] analytics ......................................... SUCCESS [0.270s]
[INFO] analytics-server .................................. SUCCESS [7.855s]
[INFO] analytics-web ..................................... SUCCESS [3.246s]
[INFO] analytics-package ................................. SUCCESS [0.942s]
[INFO] ------------------------------------------------------------------------
[INFO] BUILD SUCCESS
[INFO] ------------------------------------------------------------------------
[INFO] Total time: 12.517s
[INFO] Finished at: Tue Jul 01 20:44:58 KST 2014
[INFO] Final Memory: 27M/67M
[INFO] ------------------------------------------------------------------------
```
 
##### 패키지

빌드가 성공적으로 끝났다면 `taget/` 하위에 `analytics-버전명` 으로 디렉토리가 생성된다.

###2.3. 로그분석기 설치

##### 파일복사

위에서 빌드된 패키지를 설치위치에 단순히 복사하는 것으로 설치는 완료된다.

```bash
$ mv -r target/analytics-*  <설치위치>/
```

##### 로그분석기 통신 포트 설정

로그분석기는 API를 이용한 통신은 8050포트를, 웹관리도구는 8081포트를 사용한다. 만약 기존에 사용하는 포트와 겹쳐서 수정이 필요할때는 관련된 설정파일을 수정한다.

##### 포트수정 설정파일

|구분		|통신 PORT	|설정파일							|항목					|
|-----------|-----------|-----------------------------------|-----------------------|
|API 통신	|8050		|analytics/conf/system.properties	|servicePort=8050		|
|웹관리도구	|8081		|analytics/conf/system.properties	|admin.web.port=8081	|

```
##### 포트오픈시 점검사항
8050포트는 API를 사용하는 WAS와 열려있어야 함.
8081포트는 웹관리도구를 사용하는 사용자 PC와 열려있어야 함.
```
 
3. 서비스 시작/종료
-----------------

###3.1. 서비스 시작

##### 스크립트

- Linux : 시작스크립트는 bin/daemon.sh start 이다.
- Windows : bin/start.cmd 파일을 더블 클릭하여 실행한다.
 
##### 메모리설정

- Linux : `bin/daemon.sh` 에서는 다음과 같이 jvm 등의 메모리 설정을 할 수 있다.
 `HEAP_MEMORY_SIZE=4g`

- Windows : `bin/start.cmd` 파일에서 수정가능하다.
 `java -Xmx4g ... `

##### 프로세스확인

- Linux : ps -ef | grep java | grep analytics | grep Bootstrap
- Windows : 작업관리자 또는 프로세스가 실행된 cmd 창을 확인한다.
 
##### 로그확인

- Linux : tail -f logs/output.log 를 통해 시스템로그를 확인한다.
- Windows :  logs/output.log 파일을 윈도우용 tail 프로그램 또는 텍스트편집기로 열어본다.
 
###3.2. 서비스 종료

##### 스크립트

- Linux : bin/daemon.sh stop 를 호출한다. 또는 bin/stop.sh 를 호출하면 process를 찾아서 kill 한다.
- Windows : 실행중인 cmd창을 닫는다.
 
4. 사이트 리포트
---------------

###4.1. 시작

##### 로그인

로그분석기 설치 후 웹브라우저에서 http://[관리도구서버 IP]:[관리도구 PORT]/analytics 으로 접속하면 다음과 같이 관리도구의 로그인 화면으로 접속된다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/391.jpg)

기본 아이디 패스워드는 아래와 같다. 패스워드 수정은 "시스템설정관리 > 사용자설정" 항목을 참고한다.

|구분		|기본값	|
|-----------|-------|
|아이디		|admin	|
|패스워드	|1111	|

##### 로그아웃

관리를 마치고 로그아웃하려면 우측 상단의 "사람모양 아이콘" 을 눌러서 Log Out 을 클릭한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/394.jpg)
 
##### 사이트선택

모든 통계는 기본적으로 사이트별로 생성이 되며, 리포트도 사이트별로 제공된다. 
우측상단에서 사이트를 선택하도록 한다. 

```
처음 설치해서 사이트가 없을 경우 "시스템설정관리 > 사이트설정" 항목으로 이동하여 사이트를 먼저 추가해준다.
```

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/392.jpg)
 
###4.2. 대시보드

대시보드는 리포트 하위의 통계정보를 한눈에 정리해서 볼 수 있는 기능이다.
대시보드는 "주" 또는 "월" 을 선택하여 해당기간의 데이터를 그래프와 수치로 확인할 수 있다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/393.jpg)

대시보드는 크게 아래의 4가지로 나누어진다.

####4.2.1. Hit Progress

검색횟수추이 이며, 선택한 기간의 일자별 검색횟수와 이전 기간의 검색횟수를 함께 그래프로 보여준다. 

- Search PV : 해당기간내 로그분석기로 입력된 검색횟수의 총합
- Last Period Search PV : 이전 기간의 검색횟수의 총합
- Change : 현재기간과 이전기간과의 변동비율
 
####4.2.2. Keyword Rank

검색어순위이며, 선택한 기간내의 모든 검색어별 검색순위를 종합하여 상위 10위를 보여준다.

- KEWORD : 검색어
- COUNT : 해당기간내 검색횟수 총합
- CHANGE : 순위변동량. (+) 이면 상승, (-) 이면 하강을 의미한다.
 
####4.2.3. 검색어 비율

검색어 비율은 사이트설정을 통해 어떤 데이터를 보여줄지 선택할 수 있다. 
카테고리분류, 정렬분류 등을 선택할 수 있으며, PIE그래프로 데이터를 표현한다.

####4.2.4. Click-through Rate

클릭 유입율이며, 해당 기간내 일자별 클릭 유입건수와 총 검색건수를 선 그래프로 보여준다.
두 데이터의 비율이 클릭유입률이 되며, 이 비율을 막대그래프로 보여준다.
그래프에서 왼쪽축은 건수이며, 오른쪽축은 비율을 나타낸다.
하단의 수치항목에는 분류별로 클릭한 갯수도 함께 보여준다.

- Search PV : 총 검색건수
- Click-through Count : 클릭횟수
- Click-through Rate : 클릭유입률이며, Click-through Count 를 Search PV 로 나눈 백분율 수치이다.

###4.3. 검색추이

카테고리별 검색횟수와 응답시간의 추이를 제공한다. 

##### 메뉴설명

카테고리선택 : 선택시 해당 카테고리의 검색추이를 볼 수 있으며, 전체선택시 카테고리구분없이 본다.
기간선택 : 사용자가 원하는 날짜 범위를 선택한다.

|구분		|설명									|
|-----------|---------------------------------------|
|Hourly		|시간별 데이터. X축이 1시간을 나타낸다.	|
|Daily		|일별 데이터. X축이 1일을 나타낸다.		|
|Weekly		|주별 데이터. X축이 1주를 나타낸다.		|
|Monthly	|월별 데이터. X축이 1개월을 나타낸다.	|
|Yearly		|년별 데이터. X축이 1년을 나타낸다.		|

키워드 : 특정 키워드의 검색추이만 보여준다. 비워놓으면 키워드구분없이 모든 키워드의 데이터를 본다.
`Download` : 선택한 구간의 검색추이 데이터를 CSV형식의 파일로 내려받는다.
 
##### 데이터 테이블 설명

- Time : 시/일/주/월/년 별 단위시간
- Count : 검색횟수
- Max Time : 최대응답시간
- Average Time : 평균응답시간

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/395.jpg)
 
###4.4. 검색어순위

####4.4.1. 실시간 검색어순위

최근 30분간의 검색어순위를 보여준다. 실시간 검색어순위는 현재를 기준으로 이전 30분간 데이터에 대하여 통계를 내며, 이 작업은 주기적으로 5분마다 실행된다.
이전 데이터의 범위를 수정하려면 "사이트설정" 항목을 참고한다.
 
##### 메뉴설명

카테고리 선택 : 선택시 해당 카테고리의 검색추이를 볼 수 있으며, 전체선택시 카테고리구분없이 본다.
 
##### 데이터 테이블 설명

- KEYWORD : 검색키워드
- COUNT : 검색횟수
- RANK CHANGE : 순위변동량. UP = 상승. DN = 하강. EQ = 동일. NEW = 신규.
- COUNT CHANGE : 검색횟수변동량

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/396.jpg)
 
####4.4.2. 기간별 검색어순위

선택한 기간에 생성된 검색어 순위를 보여준다. 검색어 순위는 통계조회시 계산되는 수치가 아니므로, 정해진 기간의 순위만 볼 수 있다. 즉, 구간설정에 따른 순위통계는 제공하지 않는다.

##### 메뉴설명

- 카테고리선택 : 선택시 해당 카테고리의 검색추이를 볼 수 있으며, 전체선택시 카테고리구분없이 본다.
- 기간선택 : 사용자가 원하는 기간을 선택한다. 범위선택은 제공하지 않는다.
 
|구분		|설명		|
|-----------|-----------|
|Daily 		|하루 순위	|
|Weekly 	|주간 순위	|
|Monthly 	|월간 순위	|
|Yearly 	|년간 순위	|

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/397.jpg)

###4.5. 검색비율

선택한 기간내의 검색비율을 PIE 그래프로 보여준다. 조회할 타입의 설정방법은 "사이트설정 > 속성 설정" 항목을 참고한다.

##### 메뉴설명

카테고리선택 : 선택시 해당 카테고리의 검색비율을 볼 수 있으며, 전체선택시 카테고리구분없이 본다.

```
카테고리분류는 그 자체가 카테고리가 선택되어 있는 것이므로, 카테고리 선택이 불가능하다.
```

기간선택 : 사용자가 원하는 날짜 범위를 선택한다.

|구분		|설명							|
|-----------|-------------------------------|
|Daily	 	|일자별 범위의 검색비율을 본다	|
|Weekly 	|주별 범위 검색비율을 본다		|
|Monthly 	|월별 범위 검색비율을 본다		|
|Yearly 	|년별 범위 검색비율을 본다		|
 
##### 데이터 테이블 설명

- RANK : 순위
- TYPE : 타입명
- HIT COUNT : 검색횟수
- RATIO : 검색비율

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/398.jpg)

###4.6. 검색유입률/클릭유입률

- 검색유입률 : 어떤 경로로 검색어가 유입되었는지를 확인하여 검색유입률을 계산한다.
- 클릭유입률 : 검색후 어떠한 문서를 클릭했는지의 연관관계 데이터를 기반으로 클릭유입률을 계산한다.

####4.6.1. Overview
 
##### 메뉴설명

기간선택 : 사용자가 원하는 날짜 범위를 선택한다.

|구분		|설명									|
|-----------|---------------------------------------|
|Daily 		|일별 데이터. X축이 1일을 나타낸다.		|
|Weekly 	|주별 데이터. X축이 1주를 나타낸다.		|
|Monthly 	|월별 데이터. X축이 1개월을 나타낸다.	|
|Yearly 	|년별 데이터. X축이 1년을 나타낸다.		|

```
유입률통계는 카테고리별로 계산하지 않고 사이트 전체로 통계를 내므로 카테고리 선택은 제공하지 않는다.
```

##### 데이터 테이블 설명

- Search PV : 해당기간내 총 검색횟수
- Click-through Count : 총 클릭횟수
- Click-through Rate : 클릭유입률이며, Click-through Count 를 Search PV 로 나눈 백분율 수치이다.
- 타입별 검색횟수 : 클릭타입별로 검색횟수를 보여준다.

```
클릭타입설정은 "사이트설정 > 속성설정" 항목을 참고한다.
```

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/399.jpg)
 
####4.6.2. 상세 클릭유입

월간 상세 클릭유입 데이터를 확인해볼 수 있다.

##### 메뉴설명

기간선택 : 달력에서 날짜를 선택하면 해당 일이 속한 월의 데이터를 보여준다.

```
달력은 일자로 선택해도 해당월을 선택하는 효과를 가져온다.
```

`Download` : 월간 클릭유입 데이터를 CSV형식의 파일로 내려받는다.
 
##### 데이터 테이블 설명

- Click-through Rate : 총 클릭유입률 통계
- Detail Click-through Rate : 타입별 클릭유입건수
 
##### Keyword List 테이블 설명

- 리스트에는 상위 20개의 클릭키워드만 보여준다.
- Keyword : 검색키워드
- Search count : 검색횟수
- Click-through count : 클릭횟수
- Click-through Rate : 클릭횟수비율
- 타입이름 : 타입별 클릭횟수

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/400.jpg)
 
####4.6.3. 키워드 클릭유입

키워드별 클릭횟수와 유입률을 보여준다. 

##### 메뉴설명

- 기간선택 : 일자 선택시 해당하는 월로 선택되어 동작한다.
- 키워드 : 조회하고자하는 검색어
 
##### 데이터 테이블 설명

- Click Target : 클릭한 문서의 아이디
- 타입명 : 문서클릭타입별 클릭횟수

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/401.jpg)
 
###4.7. 키워드서비스

키워드 서비스에서는 연관검색어 통계를 제공한다.

####4.7.1. 연관검색어

연관검색어는 로그분석기로 통계데이터 입력시 이전검색어와의 연관 빈도에 따라 연관관계로 자동등록이 된다. 

```
연관검색어 등록시 빈도설정은 "사이트 설정관리 > 사이트 설정"  항목을 참조한다.
```

##### 메뉴설명

- 키워드 : 연관검색어로 등록된 단어
- Exact Match : 조회시 완전일치여부
- `Download` : 데이터를 CSV형식의 파일로 내려받는다.
- `Edit` : 편집모드로 이동한다.
- `Apply` : 수정사항을 모두 저장한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/402.jpg)
 
5. 사이트 설정관리
----------------

###5.1. 통계 설정

통계에 사용되는 파라미터들을 설정한다.

##### 공통 (CommonProperties)

- 금지어 (Banwords) : 통계에 사용하지 않을 단어들을 등록한다. 한줄에 한단어씩 입력한다.
- 검색어 최대길이 (Max Keyword Length) : 통계대상인 단어가 이 길이보다 크다면 통계에 사용하지 않고 무시한다.
- 매일 통계스케줄 시각 (Daily Schedule Time) : 일일 스케쥴 통계시 시작시간을 설정한다. 0~23사이의 정수를 입력한다.
- 통계스케줄 지연시간(초) (Schedule Delay in seconds) : 통계시작시 Delay 시간을 초 단위로 입력한다. Delay를 사용하지 않으려면 0으로 입력한다.

##### 실시간 인기검색어 (Realtime Popular Keyword)

- 최소 검색횟수 (Minimum Hit Count) : 최소 검색횟수가 이 수치이상일때 통계에 반영한다.
- 최근 로그 사용갯수 (Recent Log Using Size) : 이전 몇개의 로그를 실시간 검색어순위 통계에 반영하지 결정한다. 5분단위로 로그가 생성되므로 5 * Size 가 시간범위를 결정한다.

```
Recent Log Using Size에 5로 입력시 이전 30분의 데이터로 실시간 검색어 순위가 생성된다.
```

- 보기 갯수 (View Size) : 상위 몇개의 검색어를 순위로 제공할것인지 결정한다.
- 주기(초) (Period in seconds) : 몇 초마다 순위를 갱신할지 결정한다.

##### 인기검색어 (Popular Keyword)

- 최소 검색횟수 (Minimum Hit Count) : 최소 검색횟수가 이 수치이상일때 통계에 반영한다.
- 보기 갯수 (View Size) : 상위 몇개의 검색어를 순위로 제공할것인지 결정한다.
- 최상위 저장갯수 (Root Store Size) : 카테고리구분없는 전체통계를 DB에 저장시 상위 몇개를 저장할지 결정한다.
- 카테고리별 저장갯수 (Category Store Size) : 카테고리별 통계를 DB에 저장시 상위 몇개를 저장할지 결정한다.

##### 연관검색어 (Relate Keyword)

- 최소 검색횟수 (Minimum Hit Count) : 최소 검색횟수가 이 수치이상일때 통계에 반영한다.

##### 클릭유입률 (Click-through Rate)

- 일별 덤프파일 갯수 (Dump-file Day Size) : CTR데이터를 파일로 만들때 이전 몇일까지의 데이터를 통계내어 만들지 결정한다.
- 결과파일 저장경로 (Target-file Path) : CTR데이터를 파일로 만들때 파일명.
- 최소 클릭수 (Minimum Click Count) : 클릭횟수가 이 수치 이상일때 통계에 반영한다.
- 일별 감쇠비율 (File Daily Decay-factor) : CTR데이터를 파일로 만들때 일간 클릭횟수에 이 수치를 곱해서 더한다. 즉, 에전 데이터에 대해 클릭횟수를 감소시키는 역할을 한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/403.jpg)

##### 추천엔진

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/403_2.png)

- Spark 마스터 주소: 분석프레임워크 Spark의 마스터 주소를 설정합니다. 로컬의 경우 local[*] 또는 local[(숫자)] 같이 입력한다.

- 학습할 로그의 기간: 통계 시 얼마간의 기간을 잡고 통계를 낼 지를 입력한다. 기본적으로는 7일이며, 통계를 낸 일자 기준으로 이전날부터 설정한 수치 만큼의 기간의 이벤트 로그를 합산하여 통계를 낸다.

- 학습 스케줄 시각(시): 이벤트 로그의 통계를 진행할 시각을 입력한다. 0~23 중에서 입력.

- 학습 스케줄 요일: 통계를 진행할 요일을 입력한다. 요일을 MON과 같이 세 글자로 입력하며, 요일을 여러 개 적을 경우 콤마(,)로 구분한다.

###5.2. 카테고리 설정

사이트하위 개념인 카테고리를 설정할 수 있다.
카테고리가 추가되면 통계생성시 카테고리별로 통계데이터를 따로 생성하므로, 카테고리별로 데이터를 조회해 볼 수 있다.
그리고 외부로 서비스되는 항목인 검색어순위 기능도 카테고리별로 제공이 가능하다.
카테고리의 기본 카테고리는 구분없는 `_root` 이며 이 항목은 항상 존재해야 하고 삭제할 수 없다.

```
통계입력시 카테고리명이 잘못 들어온 경우에도 통계데이터는 _root 에 반영된다.
```

- CategoryID : 카테고리 아이디
- Name : 표시할 카테고리 라벨
- Use Realtime Popular Keyword : 해당 카테고리에서 실시간 검색어순위 통계를 생성할지 여부
- Use Popular Keyword : 해당 카테고리에서 검색어순위 통계를 생성할지 여부
- Use Relate Keyword : 해당 카테고리에서 연관검색어 통계를 생성할지 여부
모든 수정을 마치면 `Update Settings` 를 클릭하여 저장한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/404.jpg)
 
###5.3. 속성(Attribute)설정

로그분석기의 API를 통해 통계데이터가 입력될때 사용되는 값들은 여기에서 미리 설정해두어야 올바르게 통계처리가 된다. 
속성은 크게 3가지영역에 대해 설정이 가능하다.
 
##### 검색비율속성
검색비율 통계생성시 속성별로 검색비율을 계산한다.

- ID : 속성아이디
- Name : 속성라벨명
- IsPrime : 선택되면 대시보드의 검색비율영역에 표시된다.
- `+` : 하단에 한줄 추가
- `-` : 줄 삭제

##### 서비스속성
검색시 이용되는 서비스를 나타내며, 서비스 검색유입률과 클릭유입률에서 표시된다.

- ID : 속성아이디
- Name : 속성라벨명
- IsPrime : 선택되면 검색유입률과 클릭유입률 그래프의 대표 검색횟수 값으로 사용된다.
 
##### 클릭타입속성
클릭유입률의 통계와 데이터 조회시 사용된다.

- ID : 속성아이디
- Name : 속성라벨명

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/405.jpg)
 
###5.4. 통계실행

자동스케쥴 작업이 실행되지 않았거나, 스케쥴을 기다리지 않고 즉시 통계를 실행하고자 할때 사용된다.
각 통계는 시/일/주/월/년 으로 나누어 실행되며 원하는 항목을 선택하여 한꺼번에 통계를 실행할 수 있다.
기간은 하나의 일자만 선택할 수 있으며, 해당 일자가 속한 일/주/월/년에 대해서 통계가 진행된다.

- Search Progress(Hour) : 시간대별 검색추이 통계작업
- Search Progress & Keyword Rank : 검색추이 및 검색어순위 통계작업
- Search Type Rate : 검색비율 통계작업
- Relate Keyword : 연관검색어 통계작업
- Click-through Rate : CTR 통계작업
- Click-through Rate File : CTR 통계 파일생성작업

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/406.jpg)
 
###5.5. 고급통계실행

기간 범위를 지정하여 모든 통계를 한꺼번에 실행하는 기능이다. 
평상시에는 사용할 일이 없지만, 통계저장 DB가 사라졌거나, 초기 데이터 입력시등에 사용될 수 있다.
기간선택은 일자별 기간범위를 지정할 수 있다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/407.jpg)
 
6. 시스템 설정관리
----------------

우측 상단의 `톱니바퀴`를 클릭하면 시스템 설정관리 화면으로 이동한다.
 
###6.1. 사용자 설정

운영자별로 계정을 만들어 사용할 수 있다. 권한설정 기능은 제공하지 않는다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/408.jpg)

`New User` 를 클릭하여 새로운 사용자추가한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/409.jpg)
 
###6.2. 시스템 설정

전체 시스템에서 사용되는 고급 항목을 설정한다.

|항목						|설명																				|
|---------------------------|-----------------------------------------------------------------------------------|
|action-base-package		|액션 클래스의 BASE 패키지 명.														|
|admin.web.port				|관리도구의 웹서비스 PORT.															|
|db.option					|통계저장 DB JDBC URL의 추가옵션													|
|db.password				|통계저장 DB 사용자 암호.															|
|db.rankList				|검색어 순위통계시 구분할 종류명<br/> all,new,hot,down,empty등으로 컴마구분 나열	|
|db.type					|통계저장 DB 타입																	|
|db.url						|통계저장 DB JDBC URL																|
|db.user					|통계저장 DB 사용자 아이디															|
|download.characterEncoding	|다운로드 파일 인코딩																|
|download.delimiter			|CSV형식의 파일 다운로드시 구분자													|
|download.fileExt			|다운로드 파일 확장자																|
|servicePort				|서비스 포트번호																	|
|statistics.encoding		|통계 로그파일 인코딩																|
|statistics.rootId			|Root 카테고리 아이디																|
|statistics.runKeySize		|중간 통계파일의 Key Size															|
|tcp_receive_buffer_size	|네트워크 수신 버퍼 사이즈															|
|tcp_send_buffer_size		|네트워크 전송 버퍼 사이즈															|

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/410.jpg)
 
###6.3. 사이트 설정

통계대상이 되는 사이트를 추가한다.
사이트를 추가하면 우측 상단 메뉴에서 사이트를 선택할 수 있다.
`Add New Site` : 사이트 추가
`-` : 사이트 삭제

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/411.jpg)

사이트추가시 팝업이 나타나며, ID와 사이트명을 입력하고 Create Site 를 누른다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/412.jpg)
 
###6.4. 작업결과

카테고리별로 실행된 작업결과를 일자별 달력으로 확인한다.

```
성공한 작업은 녹색아이콘으로, 실패한 작업은 빨강색 아이콘으로 표시된다.
```

##### 표시되는 항목 설명

- REALTIME_SP : 실시간 검색어순위 통계
- HOURLY_SP : 시간대별 검색추이/검색어순위 통계
- DAILY_RELATE : 일별 연관검색어 통계
- DAILY_SP : 일별 검색추이/검색어순위 통계
- DAILY_TYPE : 일별 검색비율 통계
- WEEKLY_SP : 주별 검색추이/검색어순위 통계
- WEEKLY_TYPE : 주별 검색비율 통계
- MONTHLY_SP : 월별 검색추이/검색어순위 통계
- MONTHLY_TYPE : 월별 검색비율 통계
- YEARLY_SP : 년별 검색추이/검색어순위 통계
- YEARLY_TYPE : 년별 검색비율 통계
- DAILY_CLICK : 일별 클릭율 통계
- MONTHLY_CLICK : 월별 클릭율 통계
- CTR_CLICK_FILE : 일별 클릭율 파일 생성 작업

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/413.jpg)

일별 항목을 클릭시 세부 데이터가 표시된다.

- Start : 작업시작시각
- End : 작업종료시각
- Duration : 진행소요시간
- Task : 작업 Task 클래스명
- Explain : 작업 상세 로그
 
###6.5. 시스템 에러

로그분석기의 시스템에러를 확인할 수 있으며, 내부 통계작업시의 에러도 포함된다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/415.jpg)
 
###6.6. 원본로그파일

로그분석기에서 API를 통해 입력받는 원본 로그파일은 관리도구를 통해 조회가 가능하다.
로그파일은 일별로 유지되기 때문에, 달력형태로 일별 로그파일을 확인해볼 수 있다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/search-analytics/analytics-manual/ko/img/416.jpg)

파일명을 클릭하면 로그데이터가 팝업창에 나타난다.
