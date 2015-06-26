검색엔진 쿼리API
==============

목차
---
1. 검색쿼리API
2. 검색쿼리문법

<span></span>
검색쿼리API
---------

### 요청 URL

`http://[검색엔진 IP]:[검색엔진 ServicePort]/service/search.json`

검색엔진 ServicePort는 기본적으로 8090 이나, 설치시 변경하였을 경우 `conf/id.properties` 파일에서 확인할수 있다.

```
servicePort=8090
```

### 요청 변수

쿼리는 다음의 각 항목을 조합하여 Query를 생성하며, 필수로 표시된 항목은 검색식에 반드시 포함되어야 한다.

표1. 쿼리 항목 리스트

|#	|조건명	|필수	|설명							|
|---|-------|-------|-------------------------------|
|1	|cn		|O		|컬렉션명						|
|2	|fl		|O		|가져올 필드리스트				|
|3	|se		|		|검색조건					|
|4	|ft		|		|필터조건						|
|5	|ra		|		|정렬조건						|
|6	|gr		|		|그룹조건						|
|7	|gf		|		|그룹필터조건						|
|8	|sn		|O		|검색결과 시작번호				|
|9	|ln		|O		|검색결과 갯수					|
|10	|so		|		|검색옵션						|
|11	|ht		|		|검색 키워드 하이라이트 태그	|
|12	|ud		|		|사용자데이터					|

### 출력 결과 필드

|필드			|설명																			|
|---------------|-------------------------------------------------------------------------------|
|status			|결과값. 0이면 정상, 다른값이면 에러.											|
|error_msg		|결과값이 0 이 아닐경우 에러메시지 반환.										|
|time			|검색응답시간																	|
|start			|시작번호. 최소 1부터 시작.<br/> 쿼리에서 sn 값으로 넘어온 값을 그대로 사용.	|
|total_count	|총 결과갯수. 결과갯수를 제한하더라도 총 결과 갯수는 불변.						|
|count			|실제로 가져온 결과갯수. 쿼리에서 ln 값으로 요청한 결과갯수만큼 반환.			|
|field_count	|필드갯수. 쿼리에서 fl 값으로 요청한 필드갯수와 동일							|
|fieldname_list	|필드명 리스트. fl 값으로 요청한 필드명 리스트가 반환됨.						|
|result			|실제결과리스트. 리스트형식이며 내부에 <필드명:필드값> 의 쌍으로 이루어짐.		|
|group_result	|그룹핑 결과 리스트.															|

#### 샘플결과포맷 (정상시)
```
{
    "status": 0,
    "time": "0 ms",
    "start": 1,
    "total_count": 60,
    "count": 2,
    "field_count": 2,
    "fieldname_list": [
        "SUBJECT",
        "CONTENT"
    ],
    "result": [
        {
            "SUBJECT": "This is subject1",
            "CONTENT": "This is content1"
        },
 
        {
            "SUBJECT": "This is subject2",
            "CONTENT": "This is content2"
        }
    ],
    "group_result": []
}
```

#### 샘플결과포맷 (에러발생시)
```
{
    "status": 1,
    "time": "9 ms",
    "total_count": 0,
    "error_msg": "org.fastcatsearch.exception.FastcatSearchException: Uncategorized Error: 검색수행중 에러발생. [Cause]ERR-00552: 컬렉션 내부검색시 에러발생."
}
```

### 검색쿼리 예약 문자

검색엔진에 질의 시 검색쿼리로 예약되어 있는 문자열이 있으며,  질의 생성시 해당 문자열을 피해 작성하거나 치환 ( 사용자 문자 앞에 슬래시 : \ 를 배치하여 처리 ) 해야 한다.

표2. 예약문자 목록

|구분		|치환후				|용도 																			|예														|
|-----------|-------------------|-------------------------------------------------------------------------------|-------------------------------------------------------|
|& = ,		|\\& \\= \\,		|전체 쿼리																		|{SEARCH:ALL(E\\=mc2공식에 대한 고찰):100 :15}			|
|( ) { }	|\\( \\) \\{ \\}	|se문(검색)																		|{SEARCH:ALL(비밀의 門\\(문\\)):100 :15}				|
|:			|\\:				|se문(검색), ft문(필터), gr문(그룹),<br/>  ht문(하이라이팅), ud문(유저데이터)	|keyword:화면비10\\:9									|
|;			|\\;				|se문(검색), ft문(필터), gr문(그룹)												|SUBJECT:MATCH:정말 난감했다\\;\\;\\;;WRITER:PREFIX:김	|
|~			|\\~				|ft문(필터)																		|MATCH:잘가\\~하고 말했다.;HIT:SECTION:0~10				|

검색쿼리문법
------------

Query String은 Web-URL 의 파라미터와 동일한 형식이며, key는 모두 소문자로 한다. 
key와 value는 = 으로 연결되며 둘 사이에는 공백이 없어야 한다.

### cn

Name : 컬렉션명, Collection Name

Description: 검색하고자 하는 컬렉션의 이름을 전달한다.

Examples:
```
cn=sample_collection
```

### fl

Name: 가져올 필드리스트, Field List

Format: `field-name[:summary-size],..`

Description: 검색후 가져와서 사용할 필드들의 리스트를 전달한다.
여러필드의 경우 `,` 로 연결하며, `,` 사이에 공백이 없어야 한다.
필드데이터가 길 경우, 요약길이를 지정하면 해당 길이만큼의 문자열만 리턴된다. 
하이라이팅기능을 사용시 요약본은 검색어가 가장 빈번히 출현된 구간으로 요약된다.

Examples:
```
fl=id,title,body:100,category
```
### se

Name: 검색조건, Search Entry

Format: `{search-index-id,[search-index-id2,..]:[ALL|ANY](search-keyword)[:score:option]}[AND|OR|NOT{another-search-entry}]`

Description: 검색엔진에서 설정한 검색필드에 대해 검색조건을 적용한다.
검색어는 (형태소)분리된 단어가 모두 출현하는 문서를 검색시 `ALL` 을 사용하며, 하나라도 출현하는 문서를 검색시 `ANY` 를 사용한다.
검색조건은 `AND` `OR` `NOT` 으로 연결할 수 있으며, 연결시 `{ }` 의 중괄호를 이용하여 감싸주어 선후관계를 명시해야 한다.
검색필드를 여러개 사용시 `,` 로 연결하며, `,` 사이에 공백이 없어야 한다.
검색조건에 가중치를 지정할 경우 문서별 검색결과점수는 `가중치 x 출현빈도` 이다. 조건이 여러개일 경우 결과점수를 모두 더한 값이 최종점수가 된다.

Score:

- -1 : 벡터모델점수 - tf-idf 방식으로 점수가 계산됨.
- 그 이외의 정수 : 매칭점수 - 매칭되는 단어가 존재시 해당점수가 부여된다. 동일단어가 중복 매칭시에도 점수는 한번만 부여되나, 여러 단어가 매칭시에는 해당 단어의 갯수만큼 점수가 더해진다.

Score를 지정하지 않으면, 기본값은 -1이 된다.

Options:

- `1` 유사어확장
	유사어로 검색된 문서도 최종결과리스트에 함께 추가된다.
- `2` 금지어적용
	검색키워드가 금지어로 설정되어 있다면 해당 단어는 검색에 사용되지 않는다. 즉, 없는 단어로 간주된다.
- `4` 검색어 하이라이팅
	문서에서 검색어 발견시 쿼리에서 설정한 태그로 감싸준다. 태그설정은 ht 쿼리항목으로 전달한다.
- `8` 결과요약
	키워드가 가장 빈번히 발견된 문서의 일부를 발췌하여 요약길이로 잘라준다.

옵션을 지정하지 않을 경우 디폴트로 `유사어확장 + 금지어적용` 이 사용되며, 숫자는 3이 된다.

모든 옵션을 사용한다면 숫자는 15가 된다.

여러옵션적용시 해당옵션의 숫자값들을 더해서 사용한다.

Examples:

body 검색필드에서 "핸드폰"을 검색한다.

    se={body:핸드폰}

title과 body 에서 "핸드폰"과 "필름" 중 하나라도 출현하는 문서를 찾는다. 가중치는 100이며 옵션은 기본옵션을 사용한다.

    se={title,body:ANY(핸드폰):100}

title 에서 "핸드폰"과 "필름" 이 모두 출현하는 문서를 찾는다. 옵션은 유사어+금지어+하이라이팅+결과요약을 모두 사용한다. 점수는 tf-idf 를 사용한다.

    se={title:ALL(핸드폰 필름):-1:15}

"핸드폰"과 "필름"이 title에서 출현시 100점을 부여하고, body에서 출현시 50점을 부여하고, author에서 "홍길동"이 발견시 10점을 부여한다.

    se={title:ALL(핸드폰 필름):100:15}OR{body:ALL(핸드폰 필름):50:15}OR{author:ALL(홍길동):10}
 
### ft

Name: 필터조건, Filter

Format: `field-index-id:filter_type:filter-keyword[;filter-keyword][:boost-score],.. `

Description: 검색조건을 통해 만들어진 결과리스트에 필터를 수행하여 최종검색결과리스트가 생성된다.
여러 필터조건을 적용할 경우 조건들을 `,` 로 연결한다.
필터타입은 일반타입과 BOOST 타입으로 나뉘며, BOOST 는 검색된 문서에 BOOST 점수를 더해준다.
BOOST 필터사용시 점수로 정렬하면 BOOST 된 문서가 결과상위에 출현하게 된다.

Filter Type:
- `MATCH` 완전일치
- `SECTION` 범위일치 - 범위에서 시작키워드와 종료키워드가 모두 포함된 데이터를 찾는다. 범위구분은 ~ 로 한다.
- `PREFIX` 전방일치
- `SUFFIX` 후방일치
- `EXCLUDE` 완전일치시 제외
- `MATCH_BOOST` 완전일치시 결과점수 가산
- `SECTION_BOOST` 범위일치시 결과점수 가산. 범위구분은 ~ 로 한다.
- `PREFIX_BOOST` 전방일치시 결과점수 가산
- `SUFFIX_BOOST` 후방일치시 결과점수 가산
- `EXCLUDE_BOOST` 완전일치하지 않을시 결과점수 가산
- `BOOST` 결과점수에 필드값 가산. 단, 필드가 숫자형(Int, Long, Float, Double) 일 경우에만 사용가능.

필터타입은 대소문자를 구분하지 않는다.

Examples:

검색결과를 category 가 shirts 인 문서로 제한한다.

    ft=category:MATCH:shirts

price가 500이상 2000이하인 문서로 필터링한다.

    ft=price:SECTION:500~2000

code가 "A000"으로 시작하는 문서로 필터링한다.

    ft=code:PREFIX:A000

category가 shirts인 문서의 점수를 1000점 더해준다.

    ft=category:MATCH_BOOST:shirts:1000

code가 "A100"인 문서는 제외한다.

    ft=code:EXCLUDE:A100

popularScore 를 최종결과점수에 대해준다.

    ft=popularScore:BOOST

### ra

Name: 정렬조건, Ranking

Format: `field-index-id[:ASC|DESC],..`

Description: 필드명의 데이터로 정렬을 하며, 다중필드정렬시 `,` 를 사용하여 연결한다.
문서점수로 정렬시 필드명에 `_SCORE` 를 사용한다.
정렬옵션에서 `ASC` 는 오름차순으로, `DESC` 는 내림차순으로 정렬하며 생략시 `ASC` 가 디폴트로 사용된다.

Examples:

price 필드로 오름차순 정렬한다.

    ra=price

price 필드로 내림차순 정렬후 category 필드로 오름차순 정렬한다.

    ra=price:desc,category

price 필드로 내림차순 정렬후 문서점수가 높은순으로 정렬한다.

    ra=price:desc,_SCORE:desc
 
### gr

Name: 그룹조건, Group

Format: `field-index-id:group-function[(parameters)]:sort-type[:count-limit-size]`

Description: 그룹핑은 검색결과내에 그룹핑결과를 추가로 제공한다. 검색조건과 필터조건이 모두 적용된 최종결과리스트에 대해서 group 필드값에 group-function을 수행한다.

표. group-function

|Function Name	|Parameter	|Description			|
|---------------|-----------|-----------------------|
|COUNT			|  			|그룹갯수를 반환한다	|

표. sort-type

|Function Name	|Description						|
|---------------|-----------------------------------|
|KEY_ASC 		|그룹명으로 오름차순 정렬한다.		|
|KEY_DESC 		|그룹명으로 내림차순 정렬한다.		|
|COUNT_ASC 		|그룹별 갯수로 오름차순 정렬한다.	|
|COUNT_DESC 	|그룹별 갯수로 내림차순 정렬한다.	|

count-limit-size: 

그룹핑 결과가 너무 길경우 상단 count-limit-size개만 사용할 때 사용한다. 사용하지 않을 경우 옵션을 주지 않는다.
 
Examples:

category필드를 그룹핑하여 갯수를 계산한다.

    gr=category:COUNT

그룹핑 결과를 그룹별 결과갯수로 내림차순 정렬한다.

    gr=category:COUNT:COUNT_DESC

그룹핑 결과가 너무 많을땐 상위 10개만 사용한다.

    gr=category:COUNT:COUNT_DESC:10
 
 
### gf

Name: 그룹핑 필터조건, GroupFilter

Format: `field-index-id:filter_type:filter-keyword[;filter-keyword][:boost-score],.. `

Description: 필터조건인 ft와 동일한 문법을 제공한다. 일반 필터조건과의 차이점은 gf는 그룹핑 결과생성후에 적용되어, 그룹핑 결과를 제한하지 않는다는 점이다. BOOST 기능은 사용이 가능하지만, gf조건의 목적은 순위변경이 아닌 결과의 제한이므로, BOOST를 사용하고자 하면, ft조건으로 사용하는 것이 바람직하다.
사용법은 ft와 동일하므로 ft의 설명을 참고한다.

Examples:

아래의 조건을 쿼리에 함께 적용하면, 먼저 category필드로 그룹핑하여 그룹결과를 생성하며, 그후에 카테고리가 shirts인 문서만 필터링하여 검색결과를 생성한다.

	gr=category:COUNT
    ft=category:MATCH:shirts

### sn

Name: 검색결과 시작번호, Start Number

Description: 시작번호는 1부터 시작한다.

Examples:

    sn=1

### ln

Name: 검색결과갯수, Length

Description: 시작번호부터 시작하여 가져올 검색결과 갯수를 전달한다.

Examples:

    ln=50

### so

Name: 검색옵션, Search Option

Format: `option,..`

Description: 여러옵션을 `,` 로 연결하여 검색엔진으로 전달하면 검색시 사용된다.

표. search-option

|Option		|Description																					|
|-----------|-----------------------------------------------------------------------------------------------|
|nocache	|검색캐시를 사용하지 않는다. <br/>  이 옵션이 없다면 검색엔진은 기본적으로 검색캐시를 사용한다.	|

Examples:

    so=nocache
 
### ht

Name: 검색키워드 하이라이트 태그, Highlight Tags

Format: `start-tag:end-tag`

Description: 하이라이팅 옵션 사용시 시작태그와 종료태그를 정의하면 일치된 검색어에 대해서 해당 태그로 감싸준다.

Examples:

B 태그로 검색어를 감싸주어 bold 처리한다.

    ht=<b>:</b>

css에 text-red라는 class를 정의하여 사용한 경우 설정.

    ht=<span class='text-red'>:</span>
 
### ud

Name: 사용자데이터, User Data

Format: `key[:value],..`

Description: key, value형식의 파라미터를 검색엔진으로 전송한다.

Examples:

    ud=keyword:핸드폰

### qm

Name : 쿼리모디파이어, Query Modifier

Format: `클래스명`

Description: 쿼리모디파이어는 운영자가 구현한 로직으로 검색을 수행한다.

Examples:

	qm=com.fastcatsearch.demo.DemoQueryModifier
 
### rm

Name : 결과모디파이어, Result Modifier

Format: `클래스명`

Description: 결과모디파이어는 운영자가 구현한 대로 결과를 수정한다.

Examples:

	rm=com.fastcatsearch.demo.DemoResultModifier

### sp

`Beta`

Name : 저장프로시저, Stored Procedure

Format: `클래스명`

Examples:

	sp=com.fastcatsearch.demo.DemoStoredProcedure
 
### timeout

Name : 시간제한, Time Out

Format:

Description:

Examples:

검색시간제한을 30초로 설정한다.

	timeout=30
