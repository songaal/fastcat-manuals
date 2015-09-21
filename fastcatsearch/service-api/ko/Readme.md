검색엔진 서비스API
==============

목차
---
1. 전체색인실행
2. 증분색인실행
3. 색인작업확인
4. 색인스케쥴 on/off 설정
5. 색인스케쥴 on/off 확인

---

검색엔진 ServicePort는 기본적으로 8090 이나, 설치시 변경하였을 경우 `conf/id.properties` 파일에서 확인할수 있다.
```
servicePort=8090
```
---

전체색인실행
---------

#### 요청 URL

	http://[검색엔진 IP]:[검색엔진 ServicePort]/service/indexing/full.json?=collectionId=[Collection ID]

#### Param

`collectionId` : 컬렉션 아이디

#### METHOD

`POST`

#### Example:

    Request
    POST http://localhost:8090/service/indexing/full.json
    PARAM : collectionId=test

    http://localhost:8090/service/indexing/full.json?collectionId=mycollection

위의 주소와 같이 mycollection 컬렉션에 전체색인 요청을 보낼 때, 다음과 같이 status가 0이 나오면 작업등록이 정상적으로 이루어진다.

	Response :
    {
      "collectionId": "test",
      "status": "0"
    }

status가 0이면 작업등록 정상, 1이면 에러이다.

증분색인실행
---------

#### 요청 URL

	http://[검색엔진 IP]:[검색엔진 ServicePort]/service/indexing/add.json?=collectionId=[Collection ID]

#### Param

`collectionId` : 컬렉션 아이디

#### METHOD

`POST`

#### Example:

    Request
    POST http://localhost:8090/service/indexing/add.json
    PARAM : collectionId=test

    http://localhost:8090/service/indexing/add.json?collectionId=mycollection

위의 주소와 같이 mycollection 컬렉션에 증분색인 요청을 보낼 때, 다음과 같이 status가 0이 나오면 작업등록이 정상적으로 이루어진다.

	Response :
    {
      "collectionId": "test",
      "status": "0"
    }

status가 0이면 작업등록 정상, 1이면 에러이다.

색인작업확인
---------

#### 요청 URL

	http://[검색엔진 IP]:[검색엔진 ServicePort]/service/indexing/status.json?=collectionId=[Collection ID]

#### Param

`collectionId` : 컬렉션 아이디

#### METHOD

`GET`

#### Example:

    Request
    GET localhost:8090/service/indexing/status.json?collectionId=mycollection

    http://localhost:8090/service/indexing/status.json?collectionId=mycollection

위의 주소와 같이 mycollection 컬렉션에 색인작업 확인 요청을 보내면 두 가지 경우의 값을 받는다.

    Response :
    {
      "indexingState": {
        "collectionId": "test",
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

색인스케쥴 on/off 설정
---------

#### 요청 URL

	http://[검색엔진 IP]:[검색엔진 ServicePort]/service/indexing/schedule?=collectionId=[Collection ID]

#### Param

`collectionId` : 컬렉션 아이디
`type` : 색인타입. 전체색인, 증분색인 구분. full, add중 택일
`flag` : on, off 중 택일

#### METHOD

`POST`

#### Example:

    Request
    POST localhost:8090/service/indexing/schedule
    PARAM : collectionId=test&type=add&flag=on

    http://localhost:8090/service/indexing/schedule?collectionId=mycollection&type=add&flag=on

위의 주소와 같이 mycollection 컬렉션에 색인스케쥴 요청을 보내면 다음과 같은 응답이 온다.

    Response
    {
        "test" : true
    }

true이면 컬렉션의 스케쥴이 On 상태이며, false이면 Off상태임을 나타낸다.

색인스케쥴 on/off 확인
---------

#### 요청 URL

	http://[검색엔진 IP]:[검색엔진 ServicePort]/service/indexing/schedule?=collectionId=[Collection ID]

#### Param

`collectionId` : 컬렉션 아이디 (필수)
`type` : 색인타입. 전체색인, 증분색인 구분. full, add중 택일 (필수)

#### METHOD

`GET`

#### Example:

    Request
    GET localhost:8090/service/indexing/schedule
    PARAM : collectionId=test&type=full

    http://localhost:8090/service/indexing/schedule?collectionId=mycollection&type=full

위의 주소와 같이 mycollection 컬렉션에 색인스케쥴 확인 요청을 보내면 다음과 같은 응답이 온다.

    Response
	{
	"test" : false
	}

현재 mycollection의 전체색인 스케쥴이 Off상태임을 알 수 있다. 색인스케쥴 설정과는 달리, flag 값을 넣지 않은 경우 색인스케쥴의 설정 여부만을 알 수 있다.