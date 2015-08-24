검색엔진 개발환경구축 가이드
=================
 
### Java 버전

Fastcatsearch 는 최소 `Java 1.6` 버전부터 지원을 하므로, 개발환경에 `JDK 1.6` 을 추가로 설치하여 컴파일에 사용하도록 한다.
`JDK 1.7` 을 사용해도 컴파일은 가능하나, `JDK 1.7` 이상에서만 지원하는 신규 메소드나 클래스가 사용되지 않도록 주의해야 한다.
`javac` 컴파일 옵션은 `target` 과 `source` 가 모두 `1.6` 이어야 한다.
Fastcatsearch 공식 저장소는 `JDK 1.6` 이하에 해당하는 클래스와 메소드가 사용된 소스만을 커밋할 수 있다.

### 프로젝트 관리시스템

Apache Maven을 사용하여 프로젝트를 관리한다.
빌드시 명령어는 다음과 같다.
```
$ mvn clean install
```
위의 명령을 수행하면, `target` 디렉토리에 컴파일된 패키지파일이 생성된다.


### 저장소

소스코드 관리는 Git을 사용하며, Fastcatsearch 의 공식 소스코드는 `Github` 에 유지된다.

- 저장소홈 : https://github.com/fastcat-co
- 검색엔진 : https://github.com/fastcat-co/fastcatsearch
- 한글분석기 : https://github.com/fastcat-co/analyzer-korean
- 상품명분석기 : https://github.com/fastcat-co/analyzer-product
- 로그분석기 : https://github.com/fastcat-co/analytics

개인 PC에서 git 프로젝트를 쉽게 관리하기 위해서는 `Atlassian SourceTree` 를 사용할 수 있다.
- SourceTree : https://www.sourcetreeapp.com/

사내 저장소와 fastcat 공식저장소를 동시에 사용하기 위해서는 아래와 같이 Remote에 fastcat을 추가한다.

```
$ git remote add origin git://<사내 GIT IP>/fastcatsearch
$ git remote add fastcat https://github.com/fastcat-co/fastcatsearch
$ git push -u origin master
$ git push -u fastcat master
```

### 릴리즈 배포본

릴리즈는 `Github`의 각 프로젝트별 릴리즈 페이지에 업로드되어 관리된다.

- 검색엔진 : https://github.com/fastcat-co/fastcatsearch/releases
- 한글분석기 : https://github.com/fastcat-co/analyzer-korean/releases
- 상품명분석기 : https://github.com/fastcat-co/analyzer-product/releases
- 로그분석기 : https://github.com/fastcat-co/analytics/releases

### IDE

`IntelliJ IDEA Community Edition` 을 사용한다.
커뮤니티 에디션은 `Apache2 License` 로 개인과 기업이 무료로 사용할 수있다
- ItelliJ : https://www.jetbrains.com/idea/download/




