검색엔진 쿼리매뉴얼
==============

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

| # | 조건명 | 필수 | 설명 |
|---|------|-----|-----|
| 1 | cn | O | 컬렉션명 |


### 출력 결과 필드

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

| 구 분 | 치환후 | 용 도 | 예 |
|------|------|------|----|
|& = ,|\& \= \, | 전체 쿼리 | {SEARCH:ALL(비밀의 門\(문\)):100:15} |







검색쿼리문법
---------

Query String은 Web-URL 의 파라미터와 동일한 형식이며, key는 모두 소문자로 한다. 
key와 value는 = 으로 연결되며 둘 사이에는 공백이 없어야 한다.

### cn

Name : 컬렉션명, Collection Name

Description:
검색하고자 하는 컬렉션의 이름을 전달한다.

Examples:
```
cn=sample_collection
```

### fl

Name: 가져올 필드리스트, Field List

Format:
`field-name[:summary-size],..`

Description:
검색후 가져와서 사용할 필드들의 리스트를 전달한다.
여러필드의 경우 , 로 연결하며, , 사이에 공백이 없어야 한다.
필드데이터가 길 경우, 요약길이를 지정하면 해당 길이만큼의 문자열만 리턴된다. 
하이라이팅기능을 사용시 요약본은 검색어가 가장 빈번히 출현된 구간으로 요약된다.

Examples:
```
fl=id,title,body:100,category
```
























