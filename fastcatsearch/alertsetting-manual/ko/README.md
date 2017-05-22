# SNS를 통한 검색엔진 Alert 전달

SNS 봇을 사용하여 검색엔진 로그를 전달한다.

## 지원 SNS

- Telegram
- Slack

## 적용 매뉴얼

### 1. TELEGRAM

텔레그램은 봇을 생성 후 해당 봇이 사용자에게 알람을 보내는 방식으로 검색엔진 로그를 보내도록 합니다.
텔레그램 봇을 생성한 후 봇 토큰을 검색엔진 설정에 등록해야 합니다.

```
telegram-config.class=kr.gncloud.fastcatsearch.alert.TelegramBotAlert
telegram-config.token={bot_token}
```

#### 1-1. 텔레그램 봇 만들기

- 참고 URL: http://bakyeono.net/post/2015-08-24-using-telegram-bot-api.html
- 텔레그램 봇 API 매뉴얼: https://core.telegram.org/bots/api

![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img001.png)

텔레그램에서 봇을 만들기 위해서는 BotFather를 채팅목록에 추가할 필요가 있다. BotFather를 찾아서 채팅목록에 추가한다. 텔레그램 클라이언트에서 @BotFather를 검색하거나 웹 브라우저에서 https://telegram.me/botfather 주소로 접속하면 @BotFather 사용자를 추가할 수 있다.

![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img002.png)

@BotFather에게 /newbot 메시지를 보내면 봇을 등록할 수 있다.

1. BotFather에 /newbot 명령을 보낸다.
2. 봇의 이름을 입력한다. 봇 이름에는 한글도 사용 가능하다 예) 시험용 로봇
3. 봇의 아이디를 입력한다. 한글은 쓸 수 없으며, 반드시 bot, Bot 등으로 끝나야 한다. 예) test_bot

![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img003.png)

봇의 아이디까지 입력하면 BotFather가 봇 생성 안내 메세지를 토큰 및 봇 주소와 함께 보낸다. 토큰은 검색엔진 설정 파일에 등록해야 하며, 봇 주소는 개인별로 봇을 채팅목록에 추가 시 필요하다.

#### 1-2. 텔레그램 설정 등록

- 텔레그램 Alert 라이브러리 프로젝트: https://github.com/gncloud/fastcat_alert_message

검색엔진 설정 파일 ``conf/system.properties``에서 다음과 같이 봇 토큰 및 텔레그램 Alert 라이브러리 클래스명을 입력한다.

![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img004.png)

``lib`` 폴더에 텔레그램 Alert 라이브러리 파일을 넣어야 정상적으로 동작된다.

![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img005.png)

#### 1-3. 사용자 채팅 아이디 확인 및 텔레그램 아이디 등록

실제 Alert를 받기 위해서는 패스트캣 관리도구 계정에 텔레그램 채팅 아이디를 등록한 후, Notifications 설정에 해당 아이디를 등록해야 한다.

- 텔레그램 채팅 아이디 확인 방법

	텔레그램의 채팅 아이디는 봇 API를 통해서 확인해야 한다. 우선 생성된 봇을 채팅목록에 추가한 후 해당 봇에 메세지를 하나 전달한다.
    
    ``https://api.telegram.org/bot{{token}}/getUpdates``
    
    {{token}} 부분에서 앞에 BotFather에게 받은 토큰을 입력한 후 GET으로 전달하면 다음과 같은 JSON 값을 받을 수 있다.
    (요청을 보낼 시에는 JSON Reader 플러그인을 설치한 크롬이나 POSTMAN을 사용하는 것을 권장한다.)
    
    ![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img006.png)

- 채팅 아이디 등록 및 Notifications 설정 방법

	![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img007.png)

	API를 통해 체크한 채팅 아이디를 검색엔진 관리도구의 계정에 입력한다. 추가로 텔레그램 계정을 입력하기 위해서는 관리도구에 별도의 계정을 생성하여 채팅 아이디를 입력해야 한다. 이 점은 이메일을 알람에 등록하는 것과 같다.
    
    ![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img008.png)
    
    Notifications > Alert Setting에 ```TELEGRAM:(계정)``` 을 등록한다.

### 2. SLACK

슬랙은 특정 그룹의 봇 토큰 및 그룹명, 채널을 통해 특정 그룹의 채널에 있는 모든 사람들에게 검색엔진 로그를 전달하는 방식으로 검색엔진 Alert를 처리하도록 되어 있습니다.
새로운 슬랙 그룹을 생성하여 사용하든 기존 슬랙 그룹을 사용하든 둘 다 슬랙봇 토큰을 받아 검색엔진 설정에 입력을 해 줍니다.

```
slack-config.class=kr.gncloud.fastcatsearch.alert.SlackBotAlert
slack-config.token={bot_token}
slack-config.group_id={group_name}
```

#### 2-1. Slack 봇 토큰 받아오기

기본적인 Slack 봇의 토큰을 받기 위해서는 다음 페이지로 이동한다.

https://(그룹명).slack.com/apps/manage/custom-integrations

여기서 Slackbot을 선택하면 다음과 같은 페이지가 뜹니다.

![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img009.png)

```Add Configuration``` 클릭 시 Slackbot에 대한 새로운 설정에 대한 페이지가 뜨는데, 이 페이지에서 Slackbot token을 가져올 수 있습니다.

![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img010.png)

#### 2-2. Slack Notifications Alert Setting

검색엔진에서 Slack을 통해 Notifications Alert Message를 보내기 위해서는 3개의 값이 필요합니다.

- Slack 그룹명
- Slackbot 토큰값
- 메세지를 호출할 채널 ID

검색엔진 ```system.properties``` 설정에서 Slack 그룹명과 Slack 토큰값을 입력합니다.

![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img011.png)

관리도구에서는 Notifications > Alert Setting 페이지에 SLACK:(#을 제외한 채널 아이디)를 입력합니다. 이메일이나 텔레그램과는 달리 관리도구 계정을 입력하는 것이 아니라 Slack 채널명을 입력하는 것이 차이점입니다.

![](https://raw.githubusercontent.com/gncloud/fastcat_alert_message/master/manual/img/img012.png)
