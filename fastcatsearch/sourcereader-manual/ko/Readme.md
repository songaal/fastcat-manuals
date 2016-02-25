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
- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명

2. JSON_FILE
-------------

####1.1. 설명

####1.2. Edit
- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명


3. SIMPLE_FILE
-------------

####1.1. 설명

####1.2. Edit
- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명

4. WEBPAGE
-------------

####1.1. 설명

####1.2. Edit
- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명

5. DBMS
-------------

####1.1. 설명

####1.2. Edit
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
- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명

7. CSV_FILE
-------------

####1.1. 설명

####1.2. Edit
- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명

8. JSON_LIST_FILE
-------------

####1.1. 설명

####1.2. Edit
- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명

9. DIRECTORY_PATH
-------------

####1.1. 설명

####1.2. Edit
- Name : 데이터소스이름
- Enabled : 활성화 여부. 체크해제시 수집대상에서 제외된다.
- Reader Class : 소스타입. 데이터가 DB에 존재하는 경우 DBMS를 선택한다.
- Modifier Class : 모디파이어 클래스명
