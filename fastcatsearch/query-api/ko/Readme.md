검색엔진 쿼리API
==============

목차
---
1. 검색쿼리API
2. 검색쿼리문법

## 1. 검색쿼리API

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

## 2. 검색쿼리문법

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

기본적으로 제공되는 내부필드로는 _score가 있으며, 정확도 점수를 확인할 수 있다.
GEO 필터를 사용했다면 내부필드 _distance에 거리값이 채워진다.

Examples:
```
fl=id,title,body:100,category,_score
```
주소와 위도 및 경도가 포함된 데이터의 경우 GEO 필터를 사용하면 _distance필드로 거리차이를 확인할 수 있다.
```
fl=address,latitude,longitude,_distance
```



### se

Name: 검색조건, Search Entry

Format: `{search-index-id,[search-index-id2,..]:[ALL|ANY](search-keyword)[~proximity][:score:option]}[AND|OR|NOT{another-search-entry}]`

Description: 검색엔진에서 설정한 검색필드에 대해 검색조건을 적용한다.
검색어는 (형태소)분리된 단어가 모두 출현하는 문서를 검색시 `ALL` 을 사용하며, 하나라도 출현하는 문서를 검색시 `ANY` 를 사용한다.
검색조건은 `AND` `OR` `NOT` 으로 연결할 수 있으며, 연결시 `{ }` 의 중괄호를 이용하여 감싸주어 선후관계를 명시해야 한다.
검색필드를 여러개 사용시 `,` 로 연결하며, `,` 사이에 공백이 없어야 한다.
검색조건에 가중치를 지정할 경우 문서별 검색결과점수는 `가중치 x 출현빈도` 이다. 조건이 여러개일 경우 결과점수를 모두 더한 값이 최종점수가 된다.
인접검색을 사용할 시에는 `(search-keyword)` 다음에 `~인접거리`을 입력해야 하며, 검색조건에 들어가는 필드가 Search Indexes의 Store Position에 체크가 되어있어야 한다. 인접단계는 검색어 분석시 분석된 단어들이 인접해 있는 정도를 뜻하며, 예를 들어 첫번째 단어와 두번째 단어의 인접단계는 1로 볼 수 있다.

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

여러 옵션을 적용할 시 해당 옵션의 숫자값을 더하여 사용한다.

Examples:

body 검색필드에서 "핸드폰"을 검색한다.

    se={body:핸드폰}

title과 body 에서 "핸드폰"과 "필름" 중 하나라도 출현하는 문서를 찾는다. 가중치는 100이며 옵션은 기본옵션을 사용한다.

    se={title,body:ANY(핸드폰):100}

title 에서 "핸드폰"과 "필름" 이 모두 출현하는 문서를 찾는다. 옵션은 유사어+금지어+하이라이팅+결과요약을 모두 사용한다. 점수는 tf-idf 를 사용한다.

    se={title:ALL(핸드폰 필름):-1:15}

"핸드폰"과 "필름"이 title에서 출현시 100점을 부여하고, body에서 출현시 50점을 부여하고, author에서 "홍길동"이 발견시 10점을 부여한다.

    se={title:ALL(핸드폰 필름):100:15}OR{body:ALL(핸드폰 필름):50:15}OR{author:ALL(홍길동):10}

인접검색을 사용하여 검색어 "핸드폰필름"을 검색한다. ~ 뒤의 숫자가 양수일 경우 분석된 단어들이 올바른 순서대로 있어야만 검색 결과가 나온다. "핸드폰필름"은 "핸드폰"과 "필름"의 두단어로 분석되므로, 검색 결과 중 "핸드폰"과 "필름"이 순차적으로 붙어있는 결과만 나오게 된다.

	se={title:ALL(핸드폰필름)~1}

인접검색을 사용하여 "search fast engine"을 검색한다. ~ 뒤의 숫자가 음수이므로 단어 순서에 관계없이 "fast", "search", "engine"가 포함되며, 각 단어가 단어가 2 이하로 인접하여 있는 검색 결과가 출력된다. 예를 들어 "fast cat solution search engine" 같은 검색결과는 인접검색 적용 시 fast가 0번째, search가 3번째가 되어 fast와 search의 인접 단계가 3이 되므로 검색이 되지 않지만 "fast solution search engine" 같은 결과는 fast가 0번째, search가 2번째가 되어 인접 단계가 2가 되므로 검색 결과에 나오게 된다.

	se={title:ALL(search fast engine)~-2}

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
- `GEO_RADIUS` 위경도 거리반경. 경도 위도 필드는 Float형이어야 한다. 반경의 단위는 km 이며, 소수점까지 사용가능. 예) 0.3 -> 300m, 1.5 -> 1500m
- `GEO_RADIUS_BOOST` 위경도 거리반경내 일치시 결과점수 가산
- `EMPTY` NULL값 여부에 따라 NULL값을 가진 문서만 검색 또는 제외
- `SECTION_EXCLUDE` 범위일치시 제외

필터타입은 대소문자를 구분하지 않는다.

Examples:

검색결과를 category 가 shirts 인 문서로 제한한다.

    ft=category:MATCH:shirts

price가 500이상 2000이하인 문서로 필터링한다.
```
ft=price:SECTION:500~2000
```
```
ft=price:SECTION:500~2000;10000~20000
```
price 값이 500이상 2000미만, 100000이상 200000미만일 경우 점수에 100점을 가산한다.
```
ft=price:SECTION_BOOST:500~2000;10000~20000:100
```
code가 "A000"으로 시작하는 문서로 필터링한다.

    ft=code:PREFIX:A000

category가 shirts인 문서의 점수를 1000점 더해준다.

    ft=category:MATCH_BOOST:shirts:1000

code가 "A100"인 문서는 제외한다.

    ft=code:EXCLUDE:A100

popularScore 를 최종결과점수에 대해준다.

    ft=popularScore:BOOST

lat과 lon은 float형 필드인덱스이며, 위도 37.513, 경도 127.056를 기준으로 lat와 lon의 값이 500m이 내에 들어오는 문서만 필터링 한다. lat와 lon의 구분자가 세미콜론임에 유의한다.
```
ft=lat;lon:GEO_RADIUS:37.513;127.056;0.5
```

위와 동일한 조건에 boosting 점수로 10000점을 가산한다.
```
ft=lat;lon:GEO_RADIUS_BOOST:37.513;127.056;0.5;10000
```
price 값이 500이상 1000이하일 경우 검색 결과에서 제외한다.
```
ft=price:SECTION_EXCLUDE:500~1000
```
value 필드가 NULL값일 경우로 검색 결과를 제한한다. filter-keyword의 경우 Y(대소문자 구분 없음)로 시작하는 글자를 입력할 경우 NULL값인 경우만으로 검색결과를 제한하고, N(역시 대소문자 구분 없음)일 경우 NULL값이 아닌 경우로 검색 결과를 제한한다.
```
ft=value:EMPTY:y
```



### ra

Name: 정렬조건, Ranking

Format: `field-index-id[:ASC|DESC][_SHUFFLE],..`

Description: 필드명의 데이터로 정렬을 하며, 다중필드정렬시 `,` 를 사용하여 연결한다.
문서점수로 정렬시 필드명에 내부필드인 `_SCORE` 를 사용한다.
GEO 필터를 사용하였다면, `_DISTANCE`라는 내부필드가 생성되며 정렬에 사용이 가능하다.
정렬옵션에서 `ASC` 는 오름차순으로, `DESC` 는 내림차순으로 정렬하며 생략시 `ASC` 가 디폴트로 사용된다.
정렬 시 필드 값이 동일한 경우에 같은 값 중에서 출력되는 순서를 랜덤으로 하고 싶다면 `DESC_SHUFFLE` 또는 `ASC_SHUFFLE`를 적용한다. 다중필드에서 `_SHUFFLE` 적용 시 해당 옵션을 적용한 필드 후의 정렬 옵션은 무시된다.

Examples:

정확도로 내림차순 정렬한다.
```
ra=_score:desc
```

가까운 거리순으로 정렬한다.(GEO 필터 사용시)
```
ra=_distance:asc
```

price 필드로 오름차순 정렬한다.

    ra=price

price 필드로 내림차순 정렬후 category 필드로 오름차순 정렬한다.

    ra=price:desc,category

price 필드로 내림차순 정렬후 문서점수가 높은순으로 정렬한다.

    ra=price:desc,_SCORE:desc

정확도로 내림차순 정렬하며, 동일한 정확도 내에서의 정렬 순서는 검색 시마다 랜덤으로 정렬한다.

    ra=_score:desc_shuffle

name 필드로 오름차순 정렬후 price 필드 내림차순으로 정렬하며 동일한 price 값 내에서의 정렬 순서는 랜덤으로 정한다.
_shuffle 옵션 추가 시 다중필드 정렬에서 적용한다면 맨 마지막 필드 옵션에 붙여주어야 한다.

    ra=name:asc,price:desc_shuffle

예를 들어, 다음과 같이 shuffle 적용 후 category 필드를 오름차순 정렬로 한다고 적어도, category 필드 기준으로 정렬하지 않고 무시당한다.

    ra=name:asc,price:desc_shuffle,category:asc



### gr

Name: 그룹조건, Group

Format: `field-index-id:[group-function[(field-index)];..]:sort-type[:count-limit-size]`

Description: 그룹핑은 검색결과내에 그룹핑결과를 추가로 제공한다. 검색조건과 필터조건이 모두 적용된 최종결과리스트에 대해서 group 필드값에 group-function을 수행한다.
group-function 중 count를 제외하고는 모두 소문자로만 작성해야 한다. group-function의 경우 다중으로 사용이 가능하다. count를 제외한 나머지 기능은 인자값으로 필드 인덱스 1개를 요구한다.

표. group-function

|Function Name	|Parameter	|Description			|
|---------------|-----------|-----------------------|
|count			|없음		   |그룹갯수를 반환한다		|
|first			|Field-Index|그룹 내 첫번째 문서의 Field 값	|
|last 			|Field-Index|그룹 내 마지막 문서의 Field 값	|
|max			|Field-Index|그룹 내 Field 중 가장 큰 값	|
|min 			|Field-Index|그룹 내 Field 중 가장 작은 값	|
|sum			|Field-Index|그룹 내 Field 값들을 모두 더한 값	|

표. sort-type

|Function Name	|Description						|
|---------------|-----------------------------------|
|KEY_ASC 		|그룹명으로 오름차순 정렬한다.		  |
|KEY_DESC 		|그룹명으로 내림차순 정렬한다.		|
|VALUE_ASC 		|그룹별 갯수로 오름차순 정렬한다.	|
|VALUE_DESC 	|그룹별 갯수로 내림차순 정렬한다.	|
|KEY_NUMERIC_ASC 		|그룹명이 숫자일 경우 숫자 기준으로 오름차순 정렬한다.		|
|KEY_NUMERIC_DESC 		|그룹명이 숫자일 경우 숫자 기준으로 내림차순 정렬한다.		|

기존에 sort-type에서 COUNT_ 로 사용된 기능이 검색엔진 버전 v2.34.4 및 v3.9.1 이후부터 VALUE_ASC, VALUE_DESC 로 사용할 수 있도록 변경되었다. 또한 sort-type 명칭 변경의 혼동을 막기 위해 기존에 사용하던 COUNT_ASC, COUNT_DESC도 VALUE_ASC, VALUE_DESC의 하위호환으로 계속 사용할 수 있도록 했다. (명칭만 다를 뿐 사용법은 같음)
KEY_NUMERIC_ASC 및 KEY_NUMERIC_DESC의 경우 그룹명이 숫자로 이루어진 데이터들의 집합일 경우 기본 정렬 방식으로는 숫자도 문자열로 인식하여 숫자 기준으로 정렬하지 않았던 문제를 해결하기 위해 새로 추가된 정렬 옵션이다. 이  옵션을 사용하기 위해서는 그룹명 대상이 되는 필드가 숫자로만 이루어진 데이터 필드여야 한다.

count-limit-size:

그룹핑 결과가 너무 길경우 상단 count-limit-size개만 사용할 때 사용한다. 사용하지 않을 경우 옵션을 주지 않는다.

Examples:

category필드를 그룹핑하여 갯수를 계산한다.

    gr=category:COUNT

category 필드를 그룹핑하여 category로 묶인 그룹별로 model 필드의 첫 번째 값을 출력한다.

	gr=category:first(model)

그룹핑 결과를 그룹별 결과갯수로 내림차순 정렬한다.

    gr=category:COUNT:COUNT_DESC

그룹핑 결과를 그룹별 결과갯수로 오름차순 정렬한다. VALUE_ASC는 기존에 사용하던 COUNT_ASC와 동일한 기능이다.

    gr=category:COUNT:VALUE_ASC

그룹핑 결과가 너무 많을땐 상위 10개만 사용한다.

    gr=category:COUNT:COUNT_DESC:10

그룹핑 결과를 amount 필드의 최대값, 최소값, 그리고 갯수가 나오도록 하며 결과갯수 내림차순으로 5개를 사용한다.

	gr=category:max(amount);min(amount);COUNT_DESC:5

그룹핑 결과를 name 필드의 ranking 값에 해당되는 name 필드 값이 나오도록 하며 그룹명을 숫자 기준으로 내림차순 정렬한다.

	gr=ranking:max(name);KEY_NUM_DESC


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
| lowercase | ( v2.21.5 ) 검색결과의 필드아이디는 기본적으로 대문자로 리턴되나, 소문자로 받고 싶을 때 사용하는 옵션이다.  |
| nounicode | ( v2.21.6 ) No Unicode의 뜻으로, JSON 형식의 검색결과에서는 특수문자를 (\u0000와 같은) 유니코드로 변환하여 리턴하는데, 이들을 문자그대로 받고 싶을 때 사용하는 옵션이다. |

Examples:

검색캐시를 사용하지 않는다.

    so=nocache

컴마로 구분하여 여러 옵션을 동시에 적용한다.

	so=nocache,lowercase,nounicode

### ht

Name: 검색키워드 하이라이트 태그, Highlight Tags

Format: `start-tag:end-tag`

Description: 하이라이팅 옵션 사용시 시작태그와 종료태그를 정의하면 일치된 검색어에 대해서 해당 태그로 감싸준다.

Examples:

B 태그로 검색어를 감싸주어 bold 처리한다.

    ht=<b>:</b>

css에 text-red라는 class를 정의하여 사용한 경우 설정.

    ht=<span class='text-red'>:</span>

### bd

Name: 검색결과 묶음, Bundle

Format: `field-index-id:COUNT:main-including-option;other-field-index-id[:ASC|DESC],.. `

Description: 검색 결과 중 선택한 field-index에 중복되는 값이 존재하는 경우 중복 값들을 번들 배열로 묶어 출력한다.
번들 옵션을 적용하기 위해서는 해당 필드를 필드 인덱스로 설정해야 한다.
번들 배열의 길이는 `_bundleSize`로 별도로 제공한다.
`main-including-option`의 값은 번들 배열에 대표를 포함할 지에 대한 여부를 묻는 옵션으로서, 포함 시 1, 미포함 시 0이다. 해당 값을 생략시 Default는 1로서 번들 배열에 포함된다.
번들 필드는 하나만 선택이 가능하다. 하나의 검색 쿼리에 다중 번들을 적용할 수 없다.

Examples:

category 필드로 묶은 뒤 하위 묶음 상품을 최대 10개까지 가져온다. 해당 쿼리는 대표 포함 옵션을 준 `bd=category:10:1`과 동일하다.

    bd=category:10

category 필드로 묶은 뒤 카테고리 별로 동일한 값을 가진 결과값끼리 번들 배열로 묶는다. 다만 각 대표값은 번들 배열에는 대표값이 포함되지 않고 출력된다. 번들 배열 사이즈 `_bundleSize` 또한 대표값을 제외한 나머지 배열의 크기로 나온다.

	bd=category:10:0

검색 결과를 item 필드로 묶고 결과값을 보낸다. 단, 번들 배열 내부의 값은 name 오름차순, 정확도 내림차순하여 정렬한다.

	bd=item:20:1;name:asc,_score:desc

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
