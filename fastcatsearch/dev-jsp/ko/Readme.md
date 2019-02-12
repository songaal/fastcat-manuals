JSP 개발가이드
==============

목차
---
1. 준비사항
2. API 라이브러리
3. 검색 페이지
4. search_item 페이지 예제
5. 기타서비스 페이지

## 1. 준비사항

### 하드웨어 요구사항
jsp 인터페이스를 구성하기 위해 특별히 하드웨어 의존적이지는 않다.

### 소프트웨어 요구사항
jsp 상에서 fastcatsearch 엔진을 이용하기 위해서는 다음과 같은 소프트웨어 사항이 요구된다.

- jsp 2.0 이상
- json 혹은 xml 라이브러리 (json 권장)
- socket 사용가능

## 2. API 라이브러리
common.jsp 는 SearchParameter 를 이용해서 다음과 같이 검색엔진과 통신한다.

### SearchParameter
검색엔진 Query 구성을 용이하기 위해 SearchParameter 를 구현한다.

```java
searchStr = "검색어";
//컬렉션 선택
String cn = "collection";
String ht = "<b>:</b>";
String sn = "1";
String ln = "10"
String so = "";
String ud = "";
//출력필드 선택
String fl = "category,title,_score";
String se = "{category,title:ALL("+searchStr+"):100:15}";
String gr = "";
String gc = "";
String gf = "";
String ra = "_score:desc";
String ft = "";
 
//파라미터 설정
String urlStr = "cn="+e(cn)+
"&ht="+e(ht)+"&sn="+e(sn)+"&ln="+e(ln)+"&so="+e(so)+"&ud="+e(ud)+
"&fl="+e(fl)+"&se="+e(se)+"&gr="+e(gr)+"&gc="+e(gc)+"&gf="+e(gf)+
"&ra="+e(ra)+"&ft="+e(ft)+"&timeout=999";
 
public JSONObject communicateSearchEngine(String urlStr) {
    URL url = null;
    HttpURLConnection con = null;
    InputStream is = null;
    BufferedReader br = null;
    JSONObject ret = null;
 
    try {
        //URL 로 호출할 주소와 파라미터를 전송
        url = new URL(urlStr);
        con = (HttpURLConnection)url.openConnection(); 
        is = con.getInputStream();
        br = new BufferedReader(new InputStreamReader(is,"utf-8"));
        StringBuilder sbuilder = new StringBuilder();
 
        for ( String rline = null; ( rline = br.readLine() ) != null ; ) {
            sbuilder.append(rline).append("\n");
        }
        ret = new JSONObject(sbuilder.toString());
    } catch (JSONException e) {
        throw new RuntimeException(e);
    } catch (MalformedURLException e) {
        throw new RuntimeException(e);
    } catch (IOException e) {
        throw new RuntimeException(e);
    } finally {
        if(br!=null) try { br.close(); } catch (IOException e) { }
        if(is!=null) try { is.close(); } catch (IOException e) { }
        if(con!=null) { con.disconnect(); }
    }
    //JSONObject 로 결과값을 반환
    return ret;
}
```

### PageNavigator
페이징 기능을 구현하기 위해 제작된 클래스로 고유한 페이징 기법이 있다면 사용하지 않아도 무방함. 다음과 같이 사용한다.

```java
//초기화
PageNavigator pn = new PageNavigator(10,9);
//총갯수 입력
pn.setTotal(100);
//페이지 내비게이션 출력
<% for (intpageInx=pn.startPage(cpage);pageInx <=pn.endPage(cpage); pageInx++) { %>
    <%if(pageInx==cpage) { %>
        <b><%=pageInx%></b>
    <% } else { %>
        <span class="nav"onclick="goPage(<%=pageInx%>)">[<%=pageInx%>]</span>
    <% } %>
<% } %>
```


## 3. 검색 페이지

### search 페이지
사용자 ui 와 전체 구조를 나열해 놓은 레이아웃 페이지, css와 javascript 등을 이용해 html 을 구성한다.

### 공통 라이브러리 예제
검색페이지 작성시 검색에 사용되는 공통적인 함수들을 모아놓고 각 페이지에서 include하여 사용하도록 한다.

[예제소스보기](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-jsp/ko/sample.zip)

총괄적인 검색로직을 기술한 페이지로, 검색을 위한 옵션등을 조정해 놓으며, 기타 필요한 함수들을 정의함.

핵심 내용은 다음과 같다.

```java
// 샘플, 퍼플, 첨부파일 의 3개 검색을 모두 수행할 수 있도록 구성해 놓은 함수
<% if(colinx==0) { %>
<% doc = searchSample(cfg,stype,keyword, hkeyword,otype,interval,startItem,lengthItem); %>
<% } else if(colinx==1) { %>
<% doc = searchPurple(cfg,stype,keyword, hkeyword,otype,interval,startItem,lengthItem); %>
<% } else if(colinx==2) { %>
<% doc = searchAttach(cfg,stype,keyword, hkeyword,otype,interval,startItem,lengthItem); %>
<% } %>
 
//실질적인 검색기능을 구현해 놓은 함수, 모든 검색 옵션을 여기에서 설정한다.
public JSONObject searchSample(Map cfg, String stype, String searchStr, String oldStr, String otype, String interval, int start,
 int length) {
    searchStr = searchStr.replaceAll("\\&","\\\\&");
    String cn = (String)cfg.get("cn");
    String ht = MARKING;
    String sn = ""+start;
    String ln = ""+length;
    String so = "";
    String ud = "";
    String fl = "category,title,_score";
    String se = "{category,title:ALL("+searchStr+"):100:15}";
    String gr = "";
    String gc = "";
    String gf = "";
    String ra = "_score:desc";
    String ft = "";
    ud="keyword:"+searchStr;
 
    searchStr = searchStr+" "+oldStr;
    
    // 검색해올 필드를 정한다.
    if("all".equals(stype)) {
        // 전체내용에서 검색
        se = "{category,title:ALL("+searchStr+"):100:15}";
    } else if("title".equals(stype)) {
        // 카테고리에서 검색
        se = "{category:ALL("+searchStr+"):100:15}";
    } else if("content".equals(stype)) {
        // 제목에서 검색
        se = "{title:ALL("+searchStr+"):100:15}";
    }
    
    // 정렬
    if("score".equals(otype)) {
        ra = "_score:desc";
    } else if("date".equals(otype)) {
        ra = "regdate:desc";
    }
 
    Calendar calFrom = Calendar.getInstance();
    Calendar calTo = Calendar.getInstance();
 
    // 필터링
    if("all".equals(interval)) {
        interval="";
    } else if("1d".equals(interval)) {
        // 하루동안으로 필터링
        calFrom.add(Calendar.DAY_OF_MONTH, -1);
    } else if("1w".equals(interval)) {
        // 일주일동안으로 필터링
        calFrom.add(Calendar.DAY_OF_MONTH, -7);
    } else if("1m".equals(interval)) {
        // 한달동안으로 필터링
        calFrom.add(Calendar.MONTH, -1);
    } else if("1y".equals(interval)) {
        // 일년동안으로 필터링
        calFrom.add(Calendar.YEAR, -1);
    } else {
        String[] intervalArray = interval.split("~");
        if(intervalArray.length > 0) {
            calFrom.setTime(parseDate(intervalArray[0]));
        }
        if(intervalArray.length > 1) {
            calTo.setTime(parseDate(intervalArray[1]));
        }
    }
 
    if(!"".equals(interval)) {
        ft = "regdate:section:"+formatDate(calFrom.getTimeInMillis(),3)+"~"+formatDate(calTo.getTimeInMillis(),4);
    }
 
    if(!"1".equals(sn)) { ud = ""; }
 
    String urlStr = "cn="+e(cn)+
        "&ht="+e(ht)+"&sn="+e(sn)+"&ln="+e(ln)+"&so="+e(so)+"&ud="+e(ud)+
        "&fl="+e(fl)+"&se="+e(se)+"&gr="+e(gr)+"&gc="+e(gc)+"&gf="+e(gf)+
        "&ra="+e(ra)+"&ft="+e(ft)+"&timeout=999";
 
    urlStr = "http://"+HOST_SEARCH_ENGINE+"/service/search.json?"+urlStr;
 
    return communicateSearchEngine(urlStr);
}
```



4. search_item 페이지 예제
---------------------------

search_item 페이지는 각각 검색 컬렉션 별 로 제작하도록 한다. (search_item_bbs.jsp / search_item_new.jsp 등)
검색엔진과 통신해서 가지고 온 데이터를 이용해 화면에 출력해 주는 역할을 한다.
search 페이지는 전체적인 레이아웃을 출력하며, search_item 페이지는 검색결과 각각의 항목을 출력하는 역할로, 예제는 다음과 같다.

```java
    <div class="search_title"> <%=collections[colinx][1]%> </div>
    // 검색결과의 갯수가 0보다 큰 경우
    <% if(cntArray[colinx][1] > 0) { %>
        <% pn.setTotal(cntArray[colinx][1]); %>
        <div class="search_summary">
        // 검색결과의 총갯수와 현재갯수가 다른 경우
        <% if (cntArray[colinx][1] != cntArray[colinx][0] && !"all".equals(sfrom)) { %>
            <span class="search_keyword"><%=keyword%></span> 에 대한 검색 결과 (총 <%=cntArray[colinx][1]%>건 중
            <%=cntArray[colinx][0]%> 건)
        <% } else { %>
            <span class="search_keyword"><%=keyword%></span> 에 대한 검색 결과 (총 <%=cntArray[colinx][1]%>건)
        <% } %>
        </div>
        <% JSONArray resultBody = doc.optJSONArray("result"); %>
        // 검색결과가 존재하는 경우
        <% if(resultBody!=null) { %>
            <% for(int inx=0; inx < resultBody.length(); inx++) { %>
                <% JSONObject item = resultBody.optJSONObject(inx); %>
                <div class="result_item_title"> 
                <% String goUrl=""; %>
                <a class="result_item_title" onclick="viewDocument('<%=goUrl%>')"><%=item.optString("CONTENT_TITLE")%>
                </a> 
                </div>
                <div class="result_item_contents">
                <%=item.optString("CONTENT")%>
                <br/>
                작성일:<%=item.optString("CONTENT_CREATE_AT")%>
                </div>
            <% } %>
        <% } %>
    // 검색결과가 없는 경우
    <% } else if(!"".equals(sfrom)) { %>
        <div class="not_found"> <p><b><font color="#EB5629">'<%=keyworDisp %>'</font>에 대한 검색결과가 없습니다.</b>
        </p>
            <ul>
                <li>단어의 철자가 정확한지 확인해 보세요.</li>
                <li>한글을 영어로 혹은 영어를 한글로 입력했는지 확인해 보세요.</li>
                <li>검색어의 단어 수를 줄이거나, 보다 일반적인 검색어로 다시 검색해 보세요.</li>
                <li>두 단어 이상의 검색어인 경우, 띄어쓰기를 확인해 보세요.</li>
            </ul>
        </div>
    <% } %>
```

## 5. 기타서비스 페이지

### 검색어 자동완성

자동완성 구현을 위해서는 하나의 자동완성용 컬렉션을 구성해야 하며, 자소분리 검색과 초성검색이 기능하도록 하기 위해 Source-Modifier 를 이용한다.
보통은 다음과 같이 keyword 항목과 search 항목으로 검색스키마를 구성한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-jsp/ko/img/264.jpg)


실제 데이터는 KEYWORD 필드에 집어 넣으며, SEARCH 필드에는 KEYWORD 에서 가공된 데이터를 입력하도록 Source-Modifier 를 구성한다, 다음은 색인된 결과이다. (모디파이어가 제대로 작동하지 않았다면 Search 필드는 공백으로 나온다)

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-jsp/ko/img/213.jpg)

따라서 검색 식은 다음과 같이 검색하면, 자소분리 및 초성검색이 구현된다.
```
{KEYWORD,SEARCH:ALL({검색키워드}):100:15}
```


