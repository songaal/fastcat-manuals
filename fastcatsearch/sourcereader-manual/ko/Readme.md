소스리더 매뉴얼
===================

목차
---
1. [DBMS](#DBMS)
2. [JSON_FILE](#JSON_FILE)
3. [JSON_LIST_FILE](#JSON_LIST_FILE)
4. [CSV_FILE](#CSV_FILE)
5. [WEBPAGE](#WEBPAGE)
6. [WEBPAGE_CONFIG](#WEBPAGE_CONFIG)
7. [INDEX_CLONE](#INDEX_CLONE)


<a name="DBMS"></a>
## 1. DBMS

#### 1.1. 설명

데이터베이스에서 데이터를 가져와서 색인을 진행한다.

#### 1.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_005.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- JDBC : 접속할 DB에 대한 JDBC 설정. 아직 JDBC설정값이 없는 경우 Create New.. 를 클릭하여 새로 생성한다.
- Bulk Size : Reader가 DB에서 한번씩 데이터를 가져오는 갯수. Bulk Size만큼 메모리에 쌓아두기 때문에, 값이 아주 큰 경우 OutOfMemory 에러가 발생할 수 있으니, 100정도의 디폴트값을 이용하는 것을 추천한다.
- Fetch Size : JDBC의 Statement fetch-size를 가리킨다. 값이 0이면 각 JDBC별 디폴트값이 사용되고, -1이면, read-only cursor로 동작한다. JDBC수집중 OutOfMemory가 발생한다면 -1을 사용한다.
- Data SQL : 색인할 데이터를 가져오는 SQL 쿼리문.
- Delete SQL : 삭제할 아이디를 가져오는 SQL 쿼리문. 여기에 Select되는 필드는 컬렉션 스키마의 주키가 되어야 한다.

```
Delete SQL의 Select할 필드에는 오직 하나의 주키 필드만 가져오도록 한다.
```

- Before SQL : Data SQL이 데이터를 수집하기 전에 실행되는 SQL쿼리문. Update문을 통해 DB에 직접 변경사항을 적용할 수 있다.
- After SQL : Data SQL이 데이터를 수집한 뒤 마지막으로 실행되는 SQL쿼리문. Update문을 통해 DB에 직접 변경사항을 적용할 수 있다.

```
Before SQL과 After SQL은 오직 Update SQL만 실행할 수 있다. Select 문은 사용할 수 없다.
```

- LOB as File :


<a name="JSON_FILE"></a>
## 2. JSON_FILE

#### 2.1. 설명

JSON 파일에서 데이터를 가져와 색인을 진행한다.

#### 2.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_002.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- File or Dir Path : JSON 파일 또는 JSON 파일들이 저장된 디렉토리 경로를 작성한다. 디렉토리 경로를 지정할 경우 해당 폴더 내의 있는 모든 파일을 색인한다.

JSON 파일은 다음과 같이 작성한다.
```
[
	{
		"ID" : "1",
		"NAME" : "테스트1",
		"PHONENUMBER" : "01077476754"
	},
	{
		"ID" : "2",
		"NAME" : "테스트2",
		"PHONENUMBER" : "11111112222"
	}
]
```
JSONReader는 JSON ARRAY 형식으로 리스트를 가져오기 때문에 array 내에 JSON 객체가 나열되어야 한다.

- Encoding : 파일 인코딩 타입 설정


<a name="JSON_LIST_FILE"></a>
## 3. JSON_LIST_FILE

#### 3.1. 설명

JSON LIST 파일에서 데이터를 가져와 색인을 진행한다.

#### 3.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_008.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- File or Dir Path : JSON LIST 파일 또는 JSON LIST 파일들이 저장된 디렉토리 경로를 작성한다. 디렉토리 경로를 지정할 경우 해당 폴더 내의 있는 모든 파일을 색인한다.

JSON LIST 파일은 다음과 같이 작성한다.
```
{"ID":"1","NAME":"테스트1","PHONENUMBER":"010774767541"}
{"ID":"2","NAME":"테스트2","PHONENUMBER":"010774767542"}
{"ID":"3","NAME":"테스트3","PHONENUMBER":"010774767543"}
{"ID":"4","NAME":"테스트4","PHONENUMBER":"010774767544"}
{"ID":"5","NAME":"테스트5","PHONENUMBER":"010774767545"}
```
JSON 객체는 꼭 한 줄 내에서 표시되어야 한다. JSON 객체를 한 줄에 하나씩 읽는 형식으로 데이터를 색인하기 때문에 JSON 객체 내부에 정규식이 아닌 개행문자가 포함되서는 안된다.

- Buffer Size : 색인 시 한 번에 몇 줄의 데이터를 가져와 색인할 지 설정한다. 서버 사양 및 메모리 설정이 높을수록 Buffer Size를 높게 설정해도 색인이 안정적으로 진행 가능하며, 색인 속도가 빨라진다.
- Limit Size : 최대 색인 가능한 데이터를 정한다. 해당 값을 설정할 경우 색인 데이터가 아무리 많아도 Limit Size 이상으로 색인을 하지 않는다.


<a name="CSV_FILE"></a>
## 4. CSV_FILE

#### 4.1. 설명

CSV 파일에서 데이터를 가져와 색인을 진행한다.

#### 4.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_007.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- File or Dir Path : CSV 파일 또는 CSV 파일들이 저장된 디렉토리 경로를 작성한다. 디렉토리 경로를 지정할 경우 해당 폴더 내의 있는 모든 파일을 색인한다.

CSV 파일은 다음과 같이 작성한다. 첫 번째 줄에는 값 순서별로 필드명을 작성해 주어야 한다. 2번 줄부터 작성되는 데이터 필드 개수와 숫자가 맞아야 한다.
```
ID,NAME,AREA,TEL
1,홍길동,인천,010-1111-1111
2,김철수,강원,010-1111-1111
3,강호동,서울,010-1111-1111
4,돌머리,경기,010-1111-1111
```

- Encoding : 파일 인코딩 타입 설정
- Buffer Size : 색인 시 한 번에 몇 줄의 데이터를 가져와 색인할 지 설정한다. 서버 사양 및 메모리 설정이 높을수록 Buffer Size를 높게 설정해도 색인이 안정적으로 진행 가능하며, 색인 속도가 빨라진다.
- Limit Size : 최대 색인 가능한 데이터를 정한다. 해당 값을 설정할 경우 색인 데이터가 아무리 많아도 Limit Size 이상으로 색인을 하지 않는다.
- Field List : 색인할 필드명을 콤마(,)로 구분하여 작성한다. 첫 번째 줄에서 작성한 필드명 중 색인할 필드명만 작성해도 되고, *를 입력하여 모든 필드를 가져와 색인할 수도 있다.


<a name="WEBPAGE"></a>
## 5. WEBPAGE

#### 5.1. 설명

웹페이지에 접속한 뒤 해당 페이지의 내용을 가져와 색인을 진행한다.

#### 5.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_004.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- URL List : 색인을 시도할 주소 리스트를 작성한다. 페이지 하나 당 데이터 한 건 씩 색인하며 URL, 제목, 인코딩 설정, 링크 URL 순으로 입력한다. 맨 처음의 URL은 필수로 입력하며, 그 외에는 입력하지 않아도 색인이 가능하다. 타이틀의 경우 입력하지 않으면 페이지의 TITLE 태그에 존재하는 값을 가져오며 인코딩 설정을 입력하지 않을 경우 UTF-8이 디폴트로 설정된다. 링크 URL의 경우 별도의  하이퍼링크용 주소가 필요할 경우 입력한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_010.png)

웹페이지 소스 리더를 사용할 경우 스키마 필드 설정은 다음과 같이 설정한다.
링크 URL 값을 별도로 입력하지 않을 경우 URL과 LINK 필드는 동일하게 URL 값이 들어간다.


<a name="WEBPAGE_CONFIG"></a>
## 6. WEBPAGE_CONFIG

#### 6.1. 설명

설정 파일에서 웹페이지 리스트를 읽어온 뒤 웹페이지에 접근하여 해당 페이지의 내용을 가져와 색인을 진행한다.

#### 6.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_011.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- Full indexing Config File Type : 색인 시 참고할 설정 파일의 타입을 입력한다. 설정 파일은 XML, YML, JSON 중 하나를 선택할 수 있다.
- Full indexing Config File Path : 설정 파일의 경로를 입력한다.

필드를 추가할 때 참고사항으로는, ID, TITLE, CONTENT, CHARSET, URL, WDATE 필드는 반드시 들어가야 하므로 필드를 추가할 시 앞의 필드들이 생략, 또는 중복되지 않도록 한다. 추가 필드를 등록할 때도 마찬가지로 필드명이 중복되지 않도록 한다.

XML 파일의 경우 다음과 같이 설정한다.
```
<?xml version="1.0" encoding="UTF-8"?>
<dataConfig>
	<document>

		<entity charset="utf-8" cat1="01" cat2="01" cat3="01" etc1="한국인터넷진흥원" etc2="한국인터넷진흥원&gt;주요사업&gt;인터넷 정책기획"
			url="http://www.kisa.or.kr/business/policy/main.jsp">
		</entity>

		<entity charset="utf-8" cat1="01" cat2="01" cat3="01" etc1="한국인터넷진흥원" etc2="한국인터넷진흥원&gt;주요사업&gt;인터넷 정책기획&gt;인터넷ㆍ정보보호 정책연구"
			url="http://www.kisa.or.kr/business/policy/policy1.jsp">
		</entity>

		<entity charset="utf-8" cat1="01" cat2="01" cat3="01" etc1="한국인터넷진흥원" etc2="한국인터넷진흥원&gt;주요사업&gt;인터넷 정책기획&gt;인터넷ㆍ정보보호 조사분석"
			url="http://www.kisa.or.kr/business/policy/policy2.jsp">
		</entity>

        ...

	</document>
</dataConfig>
```
각 데이터를 Entity에 넣으며, url은 필수적으로 포함되어야 한다. charset의 경우 지정하지 않으면 utf-8이 디폴트로 설정된다. 그 외의 파라미터는 필요에 따라 추가해준다. 추가적인 필드의 경우 스키마에서 동일한 이름으로 필드를 설정하지 않으면 색인되지 않는다.

YML 파일의 경우 다음과 같이 설정한다.

```
url : http://www.kisa.or.kr/business/policy/main.jsp
cat1 : 01
cat2 : 01
cat3 : 01
etc1 : 한국인터넷진흥원
etc2 : 한국인터넷진흥원>주요사업>인터넷 정책기획
---
url : http://www.kisa.or.kr/business/policy/policy1.jsp
cat1 : 01
cat2 : 01
cat3 : 01
etc1 : 한국인터넷진흥원
etc2 : 한국인터넷진흥원>주요사업>인터넷 정책기획>인터넷ㆍ정보보호 정책연구
---
url : http://www.kisa.or.kr/business/policy/policy2.jsp
cat1 : 01
cat2 : 01
cat3 : 01
etc1 : 한국인터넷진흥원
etc2 : 한국인터넷진흥원>주요사업>인터넷 정책기획>인터넷ㆍ정보보호 조사분석
---
...
```
각 데이터는 ------로 구분하며, 필드명 : 값 형식으로 작성한다. url은 반드시 포함되어야 하며 charset은 별도로 설정하지 않을 경우 UTF-8로 설정된다.

JSON 파일의 경우 다음과 같이 설정한다.

```
{"url":"http://www.kisa.or.kr/business/policy/main.jsp", "cat1":"01", "cat2":"01", "cat3":"01", "etc1":"한국인터넷진흥원","etc2":"한국인터넷진흥원>주요사업>인터넷 정책기획"}
{"url":"http://www.kisa.or.kr/business/policy/policy1.jsp", "cat1":"01", "cat2":"01", "cat3":"01", "etc1":"한국인터넷진흥원","etc2":"한국인터넷진흥원>주요사업>인터넷 정책기획>인터넷ㆍ정보보호 정책연구"}
{"url":"http://www.kisa.or.kr/business/policy/policy2.jsp", "cat1":"01", "cat2":"01", "cat3":"01", "etc1":"한국인터넷진흥원","etc2":"한국인터넷진흥원>주요사업>인터넷 정책기획>인터넷ㆍ정보보호 조사분석"}
```

JSON LIST와 동일한 형식으로 설정 파일을 작성한다.

<a name="INDEX_CLONE"></a>
## 7. INDEX_CLONE

#### 7.1. 설명

다른 컬렉션의 색인 데이터를 복사한다.

#### 7.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_006.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- collection : 데이터를 복제할 컬렉션명을 입력한다.
