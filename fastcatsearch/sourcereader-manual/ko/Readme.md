소스리더 매뉴얼
===================

1. DUMP_FILE
2. JSON_FILE
3. SIMPLE_FILE
4. WEBPAGE
5. DBMS
6. INDEX_CLONE
7. CSV_FILE
8. JSON_LIST_FILE
9. DIRECTORY_PATH

<span></span>
1. DUMP_FILE
-------------

####1.1. 설명

####1.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_001.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- File Path :
- Encoding :

2. JSON_FILE
-------------

####1.1. 설명

####1.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_002.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- File or Dir Path :
- Encoding :

3. SIMPLE_FILE
-------------

####1.1. 설명

####1.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_003.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- Data Root Path :
- Delimiter :
- Mapping Field Id :
- Encoding :
- Buffer Size :


4. WEBPAGE
-------------

####1.1. 설명

웹페이지에 접속한 뒤 해당 페이지의 내용을 가져와 색인을 진행한다.

####1.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_004.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- URL List : 색인을 시도할 주소 리스트를 작성한다. 페이지 하나 당 데이터 한 건 씩 색인하며 URL, 제목, 인코딩 설정, 링크 URL 순으로 입력한다. 맨 처음의 URL은 필수로 입력하며, 그 외에는 입력하지 않아도 색인이 가능하다. 타이틀의 경우 입력하지 않으면 페이지의 TITLE 태그에 존재하는 값을 가져오며 인코딩 설정을 입력하지 않을 경우 UTF-8이 디폴트로 설정된다. 링크 URL의 경우 별도의  하이퍼링크용 주소가 필요할 경우 입력한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_010.png)

웹페이지 소스 리더를 사용할 경우 스키마 필드 설정은 다음과 같이 설정한다.
링크 URL 값을 별도로 입력하지 않을 경우 URL과 LINK 필드는 동일하게 URL 값이 들어간다.


5. DBMS
-------------

####1.1. 설명

데이터베이스에서 데이터를 가져와서 색인을 진행한다.

####1.2. Edit

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
- Before SQL :
- After SQL :
- LOB as File :

6. INDEX_CLONE
-------------

####1.1. 설명

####1.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_006.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- collection : 데이터를 복제할 컬렉션명을 입력한다.

7. CSV_FILE
-------------

####1.1. 설명

####1.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_007.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- File or Dir Path :
- Encoding :
- Buffer Size :
- Limit Size :
- Field List :

8. JSON_LIST_FILE
-------------

####1.1. 설명

####1.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_008.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- File or Dir Path :
- Buffer Size :
- Limit Size :


9. DIRECTORY_PATH
-------------

####1.1. 설명

####1.2. Edit

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/sourcereader-manual/ko/img/sr_manual_009.png)

- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
- Data Root Path :
- Mapping Field Id :
- Max Depth :
- Skip Patterns :
- Accept Only Patterns :
- Buffer Size :
- Max Count :