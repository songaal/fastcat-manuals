검색엔진 서비스API
==============

목차
---
1. 전체색인실행
2. 증분색인실행
3. 색인작업확인
4. 색인 스케쥴 On/Off
5. 동적색인 API
6. 사전 서비스 API



검색엔진 ServicePort는 기본적으로 8090 이나, 설치시 변경하였을 경우 `conf/id.properties` 파일에서 확인할수 있다.
```
servicePort=8090
```

## 1. 전체색인실행

#### 요청 URL

	http://[검색엔진 IP]:[검색엔진 ServicePort]/service/indexing/full.json

#### Param

`collectionId` : 컬렉션 아이디

#### METHOD

`POST`

#### Example:

**Request**

    POST http://localhost:8090/service/indexing/full.json
    PARAM : collectionId=mycollection

위의 주소와 같이 mycollection 컬렉션에 전체색인 요청을 보낼 때, 다음과 같이 status가 0이 나오면 작업등록이 정상적으로 이루어진다.

**Response**

    {
      "collectionId": "mycollection",
      "status": "0"
    }

status가 0이면 작업등록 정상, 1이면 에러이다.

## 2. 증분색인실행

#### 요청 URL

	http://[검색엔진 IP]:[검색엔진 ServicePort]/service/indexing/add.json

#### Param

`collectionId` : 컬렉션 아이디

#### METHOD

`POST`

#### Example:

**Request**

    POST http://localhost:8090/service/indexing/add.json
    PARAM : collectionId=mycollection

위의 주소와 같이 mycollection 컬렉션에 증분색인 요청을 보낼 때, 다음과 같이 status가 0이 나오면 작업등록이 정상적으로 이루어진다.

**Response**

    {
      "collectionId": "mycollection",
      "status": "0"
    }

status가 0이면 작업등록 정상, 1이면 에러이다.

## 3. 색인작업확인

#### 요청 URL

	http://[검색엔진 IP]:[검색엔진 ServicePort]/service/indexing/status.json?collectionId=[Collection ID]

#### Param

`collectionId` : 컬렉션 아이디

#### METHOD

`GET`

#### Example:

**Request**

    GET localhost:8090/service/indexing/status.json?collectionId=mycollection

    http://localhost:8090/service/indexing/status.json?collectionId=mycollection

위의 주소와 같이 mycollection 컬렉션에 색인작업 확인 요청을 보내면 두 가지 경우의 값을 받는다.

**Response**

    {
      "indexingState": {
        "collectionId": "mycollection",
        "indexingType": "FULL",
        "isScheduled": false,
        "state": "RUNNING",
        "step": "INDEXING",
        "count": 159,
        "startTime": "2015.09.19 16:48:00",
        "endTime": "",
        "elapsed": "32.0 s"
      }
    }

또는

    {
      "indexingState": {}
    }

indexingType은 FULL 또는 Add이며, 각각 전체색인과 증분색인을 나타낸다. isScheduled 는 매뉴얼 실행이면 false이고, 자동스케쥴링으로 실행되었다면 true이다.

위의 예제에서 state는 RUNNING이며, 이는 색인 실행 중을 뜻한다. step 은 내부진행상태를 보여준다.

색인작업이 모두 종료되거나 실행중이 아니면, 아래 예제와 같이 indexingState는 빈 Object가 반환된다.


## 4. 색인 스케쥴 on/off

#### 요청 URL

	http://[검색엔진 IP]:[검색엔진 ServicePort]/service/indexing/schedule.json

#### Param

`collectionId` : 컬렉션 아이디 (필수)

`type` : 색인타입. 전체색인, 증분색인, 동적색인중 구분. full, add, dynamic중 택일 (필수)

`flag` : on, off 중 택일. Flag값이 없을 경우 조회용으로 사용되고, flag값이 존재하면, 업데이트한다.(조회시 생략)

#### METHOD

`POST` : On/Off

`GET`  : 조회

#### Example:

V1컬렉션의 증분색인 스케줄을 On상태로 변경한다.

**Request**

    POST /service/indexing/schedule
    PARAM : collectionId=V1&type=add&flag=on

    http://localhost:8090/service/indexing/schedule.json?collectionId=V1&type=add&flag=on

**Response**

	{
	"V1" : true
	}


V1컬렉션의 증분색인 스케줄을 Off상태로 변경한다.

**Request**

    POST /service/indexing/schedule
    PARAM : collectionId=V1&type=add&flag=off

    http://localhost:8090/service/indexing/schedule.json?collectionId=V1&type=add&flag=off

**Response**

	{
	"V1" : false
	}

V1컬렉션의 동적색인 스케줄을 On상태로 변경한다.

**Request**

    POST /service/indexing/schedule
    PARAM : collectionId=V1&type=dynamic&flag=on

    http://localhost:8090/service/indexing/schedule.json?collectionId=V1&type=dynamic&flag=on

**Response**

	{
	"V1" : true
	}

V1컬렉션의 증분색인 스케줄 설정상태를 조회한다.

**Request**

    GET /service/indexing/schedule
    PARAM : collectionId=V1&type=add

    http://localhost:8090/service/indexing/schedule.json?collectionId=V1&type=add

**Response**

	{
	"V1" : false
	}

V1컬렉션의 동적색인 스케줄 설정상태를 조회한다.

**Request**

    GET /service/indexing/schedule
    PARAM : collectionId=V1&type=dynamic

    http://localhost:8090/service/indexing/schedule.json?collectionId=V1&type=dynamic

**Response**

	{
	"V1" : true
	}


## 5. 동적색인 API

주기적 색인이 아닌 동적으로 필드를 업데이트하고, 문서를 추가 및 삭제할 수 있는 API이다. 성공시 `status` 값은 0이 리턴된다.

#### 요청 URL

	http://[검색엔진 IP]:[검색엔진 ServicePort]/service/index

#### Param

`collectionId` : 컬렉션 아이디 (필수)

`Request Body` : 한줄에 기록된 JSON 형식의 문서. 다수의 문서를 BULK로 요청시 한줄에 하나의 문서를 여러줄로 나열한다.

#### METHOD

`POST` : 문서추가

`PUT`  : 필드업데이트

`DELETE` : 문서삭제

`GET` : 문서확인

#### Example:


문서를 한건 추가한다.

**Request**

```
POST /service/index?collectionId=VM
{ "ID":"12000","PRODUCTCODE":"12345", "PRODUCTNAME":"웨스턴디지털 외장하드 1TB", "PRICE": 52000, "MAKER":"웨스턴디지털"}
```

**Response**

```
# 성공
{
  "collectionId": "VM",
  "status": "0"
}
```

```
# 실패
{
  "success": false,
  "errorMessage": "org.fastcatsearch.exception.FastcatSearchException: Uncategorized Error: Collection [VM] is not exist."
}
```

문서를 여러건 추가한다.

**Request**

```
POST /service/index?collectionId=VM
{ "ID":"12000","PRODUCTCODE":"12345", "PRODUCTNAME":"웨스턴디지털 외장하드 1TB", "PRICE": 52000, "MAKER":"웨스턴디지털"}
{ "ID":"12001","PRODUCTCODE":"12346", "PRODUCTNAME":"웨스턴디지털 외장하드 512GB", "PRICE": 30000, "MAKER":"웨스턴디지털"}
{ "ID":"12002","PRODUCTCODE":"12347", "PRODUCTNAME":"웨스턴디지털 외장하드 2TB", "PRICE": 89000, "MAKER":"웨스턴디지털"}
```

**Response**

```
# 성공
{
  "collectionId": "VM",
  "status": "0"
}
```

문서를 여러건 업데이트 한다.

**Request**

```
PUT /service/index?collectionId=VM
{ "ID":"12000", PRICE": 51000 }
{ "ID":"12001", "PRICE": 29000 }
{ "ID":"12002", "PRICE": 87000 }
```

**Response**

```
# 성공
{
  "collectionId": "VM",
  "status": "0"
}
```

**Request**

문서를 한건 삭제한다.
```
DELETE /service/index?collectionId=VM
{ "ID": "12001" }
```

**Response**

```
# 성공
{
  "collectionId": "VM",
  "status": "0"
}
```

## 6. 사전 서비스 API

한글분석기, 상품명 분석기 등을 서비스 원격으로 사용하기 위한 API이다.

#### 제공 API 목록

##### 단어 리스트 호출

###### 요청 URL

	GET, POST http://[검색엔진 IP]:[검색엔진 ServicePort]/service/dictionary/list

###### Param

`pluginId` : 사전 플러그인 ID (필수)

`dictionaryId` : 사전 ID (필수)

`start` : 시작 번호 (필수)

`length` : 리스트 길이 (필수)

`search` : 검색 키워드

`searchColumns` : 검색 대상 필드

`sortAsc` : 정렬기준 (등록시간 기준, 등록시간 역순 정렬(DESC) 시에는 false을 입력한다)

###### Example

사용자사전( dictionaryId user ) 단어 리스트를 가지고 온다.

**Request**

	GET /service/dictionary/list?
    pluginId=KOREAN&dictionaryId=user&start=1&length=40

**Response**

	{
        "user": [
            {
                "ID": 23308,
                "KEYWORD": "단어1"
            },
            {
                "ID": 23309,
                "KEYWORD": "단어2"
            }
        ],
        "totalSize": 2,
        "filteredSize": 2,
        "searchableColumnList": [
            "KEYWORD"
        ],
        "columnList": [
            "KEYWORD"
        ]
    }


##### 단어 입력

###### 요청 URL

	POST http://[검색엔진 IP]:[검색엔진 ServicePort]/service/dictionary/put

###### Param

`pluginId` : 사전 플러그인 ID (필수)

`dictionaryId` : 사전 ID (필수)

사전 별로 컬럼 ID 값을 key로 입력하고 입력할 값을 value로 넣는다.
검색엔진 관리도구에서 관리 > 분석기 > (플러그인 내 pluginId 값에 해당되는 메뉴) > 설정 > 사전 > 컬럼의 View를 참고하여 확인 가능하다.

###### Example

'검색엔진' 단어를 사용자사전(set 타입)에 등록한다.

**Request**

	POST /service/dictionary/put?pluginId=KOREAN&dictionaryId=user&KEYWORD=검색엔진

**Response**

	{
        "success": true
    }

'화랑' 단어의 유사어로 등록할 'KBS드라마' 및 '신라' 유사어사전(synonym 타입)에 등록한다.

**Request**

	POST /service/dictionary/put?pluginId=KOREAN&dictionaryId=user&KEYWORD=검색엔진

**Response**

	{
        "success": true
    }


##### 단어 수정

###### 요청 URL

	POST http://[검색엔진 IP]:[검색엔진 ServicePort]/service/dictionary/update

###### Param

`pluginId` : 사전 플러그인 ID (필수)

`dictionaryId` : 사전 ID (필수)

`ID` : 단어 ID (필수)

사전 별로 컬럼 ID 값을 key로 입력하고 입력할 값을 value로 넣는다.
검색엔진 관리도구에서 관리 > 분석기 > (플러그인 내 pluginId 값에 해당되는 메뉴) > 설정 > 사전 > 컬럼의 View를 참고하여 확인 가능하다.

###### Example

ID 값이 23308인 단어를 '무한도전'으로 수정한다.

**Request**

	POST /service/dictionary/update?pluginId=KOREAN&dictionaryId=user&ID=23308&KEYWORD=무한도전

**Response**

    {
        "success": true
    }


##### 단어 삭제

###### 요청 URL

	POST http://[검색엔진 IP]:[검색엔진 ServicePort]/service/dictionary/delete

###### Param

`pluginId` : 사전 플러그인 ID (필수)

`dictionaryId` : 사전 ID (필수)

`deleteIdList` : 삭제할 단어 아이디 리스트 (콤마로 구분한다.)

###### Example

**Request**

	POST /service/dictionary/delete?pluginId=KOREAN&dictionaryId=user&deleteIdList=23308,23309

**Response**

    {
        "success": true,
        "result": 2
    }

##### 사전 APPLY

###### 요청 URL

	POST http://[검색엔진 IP]:[검색엔진 ServicePort]/service/dictionary/apply

###### Param

`pluginId` : 사전 플러그인 ID (필수)

`dictionaryId` : 사전 ID (필수) (여러 개의 사전 APPLY 시 콤마로 아이디 구분)

###### Example

사용자 사전(user) 및 유사어 사전(synonym)을 Apply한다.

**Request**

	POST /service/dictionary/apply?pluginId=KOREAN&dictionaryId=user,synonym

**Response**

    {
        "success": true,
        "successList": [
            "user",
            "synonym"
        ],
        "failList": []
    }
