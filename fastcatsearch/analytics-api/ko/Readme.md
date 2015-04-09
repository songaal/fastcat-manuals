Analytics-API 매뉴얼
===================

목차
----
1. [통계입력 API](#통계입력 API)
2. [통계결과 API](#통계결과 API)


1. 통계입력 API
--------------

이 장에서는 통계데이터의 입력방법과 그 역할에 대해 설명한다.

###1.1. 검색통계데이터 입력

검색 클라이언트 에서 검색 직후에 검색결과를 통계서버에 전달 해 주어야 한다, fastcat-analytics 는 전달 방법으로 http 프로토콜을 이용하며, 전달 방법은 다음과 같다.

URL : `http://[서버 IP]:[서버 PORT]/service/keyword/hit/post`

표 1 파라메터 목록

<table>
<tr> <th>용도구분</th> <th>파라메터</th> <th>역할</th> <th>예</th> <th>기타</th> </tr>
<tr> <td rowspan=3>기본</td> <td>type</td> <td>search</td> <td>search</td> <td>search로 고정값</td> </tr>
<tr><td>siteId</td><td>siteId 값</td><td>www</td><td>conf/sites.xml 의 siteId</td></tr>
<tr><td>categoryId</td><td>categoryId 값</td><td>cat1</td><td>Site하위 카테고리 ID</td></tr>
<tr><td>검색유입경로</td><td>searchService</td><td>검색유입 서비스ID</td><td>totalSearch</td><td> 검색이 유입된 서비스명.<br/>  Attibute설정에 입력해둔 서비스ID를 사용한다. </td></tr>
<tr><td rowspan=3>검색횟수통계<br/>  인기키워드<br/>  연관키워드</td><td>keyword</td><td>입력된 키워드</td><td>12인치 노트북</td><td>utf-8로 인코딩된 문자</td></tr>
<tr><td>prev</td><td>이전 키워드</td><td>최신 노트북</td><td>utf-8로 인코딩된 문자</td></tr>
<tr><td>resptime</td><td>검색엔진 응답시간</td><td>130</td><td>millisecond 단위로 전달</td></tr>
<tr><td rowspan=8>비율통계</td><td>category</td><td>타입별 분류 입력</td><td>가전/컴퓨터</td><td>카테고리명 전달용</td></tr>
<tr><td>page</td><td>페이지구분</td><td>1</td><td>검색페이지 이동시 페이지 값 전달용</td></tr>
<tr><td>sort</td><td>정렬구분</td><td>가격순</td><td>검색옵션 변경 시 정렬값 전달용</td></tr>
<tr><td>service</td><td>서비스 구분</td><td>통합검색</td><td>통합검색인지, 그 밖에 상세검색인지 전달용</td></tr>
<tr><td>login</td><td>로그인여부 구분</td><td>로그인</td><td>검색대상이 로그인 된 사용자인지 구분 전달용</td></tr>
<tr><td>age</td><td>연령대 구분</td><td>30대</td><td>로그인 사용자의 연령대 구분 전달용</td></tr>
<tr><td>gender</td>성별구분</td><td>남자</td>로그인 된 사용자의 성별구분 전달용</td></tr>
</table>


※ 예: `http://localhost:8050/service/keyword/hit/post?type=search&siteId=www&categoryId=cat1&searchService=totalSearch&keyword=12인치 노트북&prev=최신 노트북&resptime=130&category=가전/컴퓨터&page=1&sort=가격순&age=30대&service=통합검색&login=일반&gender=남성`

 
###1.2. 클릭통계데이터 입력

사용자가 검색결과문서를 클릭한 내용은 어떠한 문서가 검색키워드의 결과로 적합한지를 결정하는데 도움을 줄 수 있다. fastcat-analytics는 사용자의 클릭로그를 분석하여 통계내는 기능을 제공한다.

호출 API는 다음과 같다.
URL : `http://[서버 IP]:[서버 PORT]/service/ctr/click/post`

| 용도구분 | 파라메터 | 역할 | 예 | 기타 |
|---------|---------|------|----|------|
| Click-through Rate | siteId | siteId 값 | www | conf/sites.xml 의 siteId |
| | keyword | 검색키워드 | USB메모리 | |
| | clickId | 클릭문서 아이디 | 462496566 |어떠한 문서를 클릭했는지 정보를 기록한다. |
| | clickType | 클릭문서 타입 | view_blog |Attibute설정에 입력해둔 클릭타입ID를 사용한다. |
 
###1.3. 통계 데이터의 처리

실시간 인기 키워드는 5분마다 1번씩 집계되며, 나머지 일별 통계는 1일 1회 (0시) 집계 된다.
 
2. 통계결과 API
--------------

인기키워드, 연관검색어의 API

###2.1. 인기키워드

####2.1.1. 실시간 인기키워드

URI : `/service/keyword/popular/rt.xml`

표 1 실시간 인기검색어 파라미터 목록

|Parameter|설명|Nullable|
|---------|---|--------|
|siteId|사이트 ID|N|
|categoryId|카테고리 ID|Y|
 
**샘플요청URL**

    http://localhost:8050/service/keyword/popular/rt.xml?siteId=total&categoryId=_root

**샘플결과포맷**

```xml
    <?xml version="1.0" encoding="UTF-8"?><response>
      <siteId>total</siteId>
      <categoryId/>
      <list>
        <item>
          <rank>1</rank>
          <word>해리포터</word>
          <diffType>EQ</diffType>
          <diff>0</diff>
          <count>89</count>
          <countDiff>0</countDiff>
        </item>
        ..중략..
        <item>
          <rank>10</rank>
          <word>원피스</word>
          <diffType>EQ</diffType>
          <diff>0</diff>
          <count>62</count>
          <countDiff>-18</countDiff>
        </item>
      </list>
    </response>
```
 
####2.1.2. 인기검색어

URI : `/service/keyword/popular.xml`
표 2 서비스용 인기검색어 파라미터 목록

|Parameter|설명|필수|
|---------|---|---|
|siteId|사이트 ID|O|
|categoryId|카테고리 ID|X|
|timeType|일자별 = D<br/>주간별 = W<br/> 월별 = M<br/>년도별 = Y|O|
|interval|이전 시간대.<br/>timeType이 D일때 interval이 1이면 이전날짜.<br/> timeType이 W일때 interval이 1이면 이전주.|O|
 
**샘플 이전일 일간 인기검색어 요청URL**

    http://localhost:8050/service/keyword/popular.xml?siteId=total&categoryId=_root&timeType=D&interval=1

**샘플 이전주 주간 인기검색어 요청URL**

    http://localhost:8050/service/keyword/popular.xml?siteId=total&categoryId=_root&timeType=W&interval=1

**샘플결과포맷**

```xml
    <?xml version="1.0" encoding="UTF-8"?><response>
      <siteId>total</siteId>
      <categoryId>_root</categoryId>
      <timeType>D</timeType>
      <time>D</time>
      <list>
        <item>
          <rank>1</rank>
          <word>축구</word>
          <diffType>UP</diffType>
          <diff>1</diff>
          <count>1555</count>
          <countDiff>0</countDiff>
        </item>
        .. 중략 ..
        <item>
          <rank>10</rank>
          <word>하이스쿨</word>
          <diffType>DN</diffType>
          <diff>2</diff>
          <count>713</count>
          <countDiff>0</countDiff>
        </item>
      </list>
    </response>
```
 
표. 날짜조회용 인기검색어 파라미터 목록

|Parameter|설명|필수|
|---------|---|---|
|siteId|사이트 ID|O|
|categoryId|카테고리 ID|X|
|interval| 날짜 조회용은 고정값 0|O|
|timeId|특정조회일자. 형식은 [Type]yyyyMMdd.<br/>예) D20140705, W201428, M201407, Y2014|O|
|sn|시작번호. 1부터 시작.|O|
|ln|가져올 키워드 갯수|O|
 
**덤프용 인기검색어**
일자 기간별 인기검색어를 일괄적으로 내려받아야 할때 사용한다.
URI : `/service/keyword/popular/dump.xml`

표. 덤프용 인기검색어 파라미터 목록

|Parameter|설명|필수|
|---------|---|---|
|siteId|사이트 ID|O|
|categoryId|카테고리 ID|X|
|from|시작일자. 형식은 yyyy.MM.dd|O|
|to|끝일자. 형식은 yyyy.MM.dd|O|
|ln|가져올 키워드 상위 N개의 갯수.|O|
 
####2.1.3. 연관검색어
URI : `/service/keyword/relate.xml`

표 3 연관검색어 파라미터 목록

|Parameter|설명|Nullable|
|---------|----|--------|
|siteId|사이트 ID|N|
|keyword|기준 검색어|N|
 
**샘플요청URL**

    http://localhost:8050/service/keyword/relate.xml?siteId=total&keyword=원피스

**샘플결과포맷**

```xml
    <?xml version="1.0" encoding="UTF-8"?><response>
      <siteId>total</siteId>
      <service>RELATE_KEYWORD</service>
      <keyword>원피스</keyword>
      <relate>
        <item>ts</item>
        <item>나루토</item>
        <item>동방</item>
        <item>헌터</item>
      </relate>
    </response>
``` 