PHP 개발가이드
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
php 인터페이스를 구성하기 위해 특별히 하드웨어 의존적이지는 않다.

### 소프트웨어 요구사항
php 상에서 fastcatsearch 엔진을 이용하기 위해서는 다음과 같은 소프트웨어 사항이 요구된다.

- php 5.3 이상
- json 혹은 xml 라이브러리 (json 권장)
- socket 사용가능
- iconv 사용가능

웹페이지 상에서 `<?phpinfo();?>` 를 입력하고 웹서버에서 실행해 보았을 때 다음과 유사한 항목이 발견되면 된다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-php/ko/img/206.jpg)

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-php/ko/img/202.jpg)

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-php/ko/img/203.jpg)

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-php/ko/img/204.jpg)

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-php/ko/img/205.jpg)

## 2. 개 요

php를 이용한 검색은 commons-library 를 거쳐 communicator 를 통해 검색엔진과 소통한다.
검색되어진 결과는 search_item.php 를 통해 표현되어 진다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-php/ko/img/207.jpg)

## 3. API 라이브러리

fastcat_api.php 는 SearchQueryStringer / FastcatCommunicator / PageNavigator 의 3개 클래스로 이루어진다.

[fastcat_api.php 소스보기](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-php/ko/fastcat_api.php)

### SearchQueryStringer

검색엔진 Query 구성을 용이하기 위해 제작된 클래스, 다음과 같이 사용한다.

```php
//초기화
$query = new SearchQueryStringer("");
//컬렉션 선택
$query->setCollection({컬렉션 id})
//출력필드 선택
$query->setFieldList("title,content:50");
```
세부 기능은 다음과 같다.

### FastcatCommunicator

검색엔진과 socket 통신을 수행하는 클래스로, 다음과 같이 사용한다.

```php
//초기화
$fastcat = new FastcatCommunicator("http://{검색엔진 ip}:{검색엔진 포트}");
//검색엔진과 통신 (검색)
$jsonResult = json_decode($fastcat->communicate("/service/search.json",$query->getQueryString(),""),true);
//로그분석기와 통신
$jsonResult = json_decode($logger->communicate("/service/keyword/popular/rt.json","type=search&siteId=total&categoryId=".$category);
```

### PageNavigator

페이징 기능을 구현하기 위해 제작된 클래스로 고유한 페이징 기법이 있다면 사용하지 않아도 무방함. 다음과 같이 사용한다.

```php
//초기화
$pn = new PageNavigator({한페이지에표현될 게시물수},{한페이지에 표현될 페이지수})
//총갯수 입력
$pn->setTotal({총게시물갯수});
//페이지 네비게이션 출력
<? for($pageInx=$pn->startPage($cpage);$pageInx<=$pn->endPage($cpage);$pageInx++) { ?>
	<? if($pageInx==$currentPage) { ?>
		<b><?=$pageInx?></b>
 	<? } else { ?>
 		<span class="nav" onclick="goPage(<?=$pageInx?>)">[<?=$pageInx?>]</span>
	<? } ?>
<? } ?>
```

## 4. 검색페이지

### search 페이지

사용자 ui 와 전체 구조를 나열해 놓은 레이아웃 페이지, css와 javascript 등을 이용해 html 을 구성한다.

### 공통 라이브러리 예제

검색페이지 작성시 검색에 사용되는 공통적인 함수들을 모아놓고 각 페이지에서 include하여 사용하도록 한다.

[예제소스보기](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-php/ko/commons.php)

총괄적인 검색로직을 기술한 라이브러리로, 검색을 위한 옵션등을 조정해 놓으며, 기타 필요한 함수들을 정의함.

핵심 내용은 다음과 같다.

```php
// 예제 게시판, 뉴스, faq 의 3개 검색을 모두 수행할 수 있도록 구성해 놓은 함수
function searchMaster($sfrom,$collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult ,$chapterSize,$startItem,$lengthItem) {
	if($collection=="board" ) {
		return searchBoard($collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem);
	} else if($collection=="news") {
		return searchNews($collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem);
	} else if($collection=="faq") {
		return searchFAQ($collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem);
	}
} 

//실질적인 검색기능을 구현해 놓은 함수, 모든 검색 옵션을 여기에서 설정한다.
function searchBoard($collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem) {		
	$searchField = array();
	// 검색해올 필드를 정한다.
	if($stype=="all") {
		// 전체내용에서 검색
		$searchField = array("title","content","member_name");
	} else if($stype=="subject") {
		// 제목에서 검색
		$searchField = array("title");
	} else if($stype=="content") {
		// 내용에서 검색
		$searchField = array("content");
	} else if($stype=="keyword") {
		// 키워드에서 검색
		$searchField = array("content");
	} else if($stype=="member") {
		// 회원명으로 검색
		$searchField = array("member_name");
	}
	// 정렬방식   
	if($otype=="date") {
		// 날자기준 내림차순 정렬
		$query->addRankingEntry("wdate");
	} else {
		// 정확도 기준 내림차순 정렬
		$query->addRankingEntry("_score");
	} 
	// 날자필터링
	preg_match_all("/([0-9]+)([a-z]+)/", $interval, $matches);
	if($interval=="all") {
	} else if($matches[2][0]=="d") {
		// 일별 필터링 (1일전, 2일전....)
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." days")),date("YmdHis"));
	} else if($matches[2][0]=="w") {
		// 주별 필터링 (1주전, 2주전....)
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." weeks")),date("YmdHis"));
 	} else if($matches[2][0]=="m") {
 		// 월별 필터링 (1달전, 2달전....)
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." months")),date("YmdHis"));
	} else if($matches[2][0]=="y") {
		// 년별 필터링 (1년전, 2년전....)
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." years")),date("YmdHis"));
	} else {
		// 날자 범위로 필터링
 		$intervalArray = explode("~",$interval);
  		if(count($intervalArray)>1) {
			$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,$intervalArray[0],$intervalArray[1]);
  		} else if(count($intervalArray)>0) {
			$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,$intervalArray[0]);
		}
	}
	//검색필드를 이용해 검색 식 구성 ( 검색어 매칭 점수 : 10점)
	$query->andSearchEntry($searchField,$hkeyword." ".$keyword,SearchQueryStringer::KEYWORD_AND,10);
	if($stype=="all") {
		// 제목 가중치를 높이기 위해 OR 블럭으로 제목 검색어 매칭 점수를 500점을 주어 검색식 구성
		$query->orSearchEntry(array("title"),$hkeyword." ".$keyword,SearchQueryStringer::KEYWORD_AND,500);
 	}
	// SearchQueryStringer 를 이용해 검색식 구성
	$query->setCollection($collection)
		->setFieldList("idx,title,content:200,subject,bookimg,cnt_read,cnt_page_read,cnt_recom,wdate,member_name,member_id,category,bbsid,sub_bbsid,_score")
		->setUserDataKeyword($keyword)
		->setLength($startItem,$lengthItem);
	// FastcatCommunicator 를 이용해 검색엔진과 통신
	$jsonStr = $fastcat->communicate("/service/search.json",$query->getQueryString(),"");
	// json_decode 를 이용해 받아온 검색결과 파싱
	return json_decode($jsonStr,true);
}
``` 

### search_item_xxx 페이지 예제

search_item 페이지는 각각 검색 컬렉션 별 로 제작하도록 한다, (search_item_bbs.php / search_item_new.php 등)
검색엔진과 통신해서 가지고 온 데이터를 이용해 화면에 출력해 주는 역할을 한다. ᅟ
search 페이지는 전체적인 레이아웃을 출력하며, search_item 페이지는 검색결과 각각의 항목을 출력하는 역할로, 예제는 다음과 같다.

```php
// $cntArray 에는 검색된 컬렉션들의 검색갯수가 들어있음.
// $resultBody 는 검색된 컬렉션의 결과 json 객체가 들어있음
// $pn 은 페이지 네비게이션 객체
<div class="search_title"> <?=$collections[$colinx][1]?> </div>
<? if($cntArray[$colinx][1] > 0) { ?>
	<? $pn->setTotal($cntArray[$colinx][1]); ?>
	<div class="search_summary">
	<? if ($cntArray[$colinx][1] != $cntArray[$colinx][0] && $sfrom!="all") { ?>
		"<span class="search_keyword"><?=$keyword?></span>" 에 대한 검색 결과 (총 <?=number_format($cntArray[$colinx][1])
	<? } else { ?>
		"<span class="search_keyword"><?=$keyword?></span>" 에 대한 검색 결과 (총 <?=number_format($cntArray[$colinx][1])
	<? } ?>
	</div>
	<?  $resultBody = $doc["result"]; ?>
	<? if($resultBody!=null) { ?>
		<? for ($inx=0; $inx < count($resultBody); $inx++) { ?>
			<? 
			$item = $resultBody[$inx];
			$goUrl = ""; //클릭했을 경우 이동할 url 을 입력.
			?>
			<div class="result_item_title"> 
			<a class="result_item_title" onclick="viewDocument('<?=$goUrl?>')"><?=$item["SUBJECT"]?></a> 
			</div>
			<div class="result_item_contents">
			<?=$item["CONTENT"]?>
			<br/>
			<br/>
			작성자:<?=$item["AUTHOR"]?> / 
			작성일:<?=$item["REGDATE"]?> 
			</div>
		<? } ?>
	<? } ?>
<? } else if($sfrom!="") { ?>
	<div class="not_found"> <p><b><font color="#EB5629">'<?=$keyworDisp ?>'</font>에 대한 검색결과가 없습니다.</b></p>
		<ul>
			<li>단어의 철자가 정확한지 확인해 보세요.</li>
			<li>한글을 영어로 혹은 영어를 한글로 입력했는지 확인해 보세요.</li>
			<li>검색어의 단어 수를 줄이거나, 보다 일반적인 검색어로 다시 검색해 보세요.</li>
			<li>두 단어 이상의 검색어인 경우, 띄어쓰기를 확인해 보세요.</li>
		</ul>
	</div>
<? } ?>
```

## 5. 기타서비스 페이지

### 검색어 자동완성

자동완성 구현을 위해서는 하나의 자동완성용 컬렉션을 구성해야 하며, 자소분리 검색과 초성검색이 기능하도록 하기 위해 Source-Modifier 를 이용한다.
보통은 다음과 같이 keyword 항목과 search 항목으로 검색스키마를 구성한다.

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-php/ko/img/212.jpg)

실제 데이터는 KEYWORD 필드에 집어 넣으며, SEARCH 필드에는 KEYWORD 에서 가공된 데이터를 입력하도록 Source-Modifier 를 구성한다, 다음은 색인된 결과이다. (모디파이어가 제대로 작동하지 않았다면 Search 필드는 공백으로 나온다)

![](https://raw.githubusercontent.com/fastcat-co/fastcat-manuals/master/fastcatsearch/dev-php/ko/img/213.jpg)

따라서 검색 식은 다음과 같이 검색하면, 자소분리 및 초성검색이 구현된다.

```
{KEYWORD,SEARCH:ALL({검색키워드}):100:15}
```

### 인기검색어

인기검색어와 연관키워드는 별도로 추가되는 로그분석기 를 이용하여 다음과 같이 구현한다.

```php
// 먼저 로그분석기와의 커넥션을 형성한다.
$logger = new FastcatCommunicator("http://{로그분석기 ip}:{로그분석기 포트}");

// 통신모듈로 인기검색어 목록을 가져온다.
function getPopularKeyword($logger, $sfrom) {
	$jsonStr = $logger->communicate("/service/keyword/popular/rt.json","type=search&siteId=total&categoryId=".$sfrom);
	return json_decode($jsonStr,true);
}

// html로 표현해 준다.
<ul class="hit_keyword_body">
<?
$kdoc = getPopularKeyword($logger, $collections[$searchCollection][0]);
$keywords = $kdoc["list"];
if($keywords) {
?>
	<?
	for($inx=0; $inx < count($keywords); $inx++) {
	?>
		<?
		$entry = $keywords[$inx];
		$word = $entry["word"];
		$rank = $entry["rank"];
		$diffType = $entry["diffType"];
		$diff = $entry["diff"];
		?>
		<li class="hit_keyword">
			<div class="hit_keyword_no"> <?=$rank?> </div>
			<div class="hit_keyword_str"><?=$word?></div>
			<? if($diffType=="EQ") { ?>
				<div class="hit_keyword_rank"></div>
				<div class="hit_keyword_arrow">-</div>
			<? } else if($diffType=="UP") { ?>
				<div class="hit_keyword_rank"><?=$diff?></div>
				<div class="hit_keyword_arrow hit_keyword_up">↑</div>
			<? } else if($diffType=="DN") { ?>
				<div class="hit_keyword_rank"><?=$diff?></div>
				<div class="hit_keyword_arrow hit_keyword_down">↓</div>
			<? } else if($diffType=="NEW") { ?>
				<span class="keyword_new">new</span>
			<? } ?>
		</li>
	<?
	}
	?>
<?
}
?>
</ul>
```
 
### 연관키워드

로그분석기와 통신하여 연관키워드를 추출하는 방법을 기술한다.

```php
// 먼저 로그분석기와의 커넥션을 형성한다.
$logger = new FastcatCommunicator("http://{로그분석기 ip}:{로그분석기 포트}");

// 통신모듈로 검색어에 대한 연관키워드 목록을 가지고 온다.
function getRelateKeyword($logger,$sfrom, $keyword) {
	$keyword = urlencode($keyword);
	$jsonStr = $logger->communicate("/service/keyword/relate.json","siteId=total&category=".$sfrom."&keyword=".$keyword."");
	return json_decode($jsonStr,true);
}

// html 로 표현해 준다.
<?
$relateKeyword = "";
$keywordMap = getRelateKeyword($logger,{검색컬렉션}, urldecode({현재 검색한 키워드}));
if($keywordMap) {
	foreach($keywordMap["relate"] as $v) {
		$relateKeyword.=",".$v;
	}
	if($relateKeyword) {
		$relateKeyword = substr($relateKeyword,1);
	}
}
?>
<div class="relate_keyword">
연관검색어 :  <?=$relateKeyword?>
</div>
``` 
