# :llama: Paka (v2)

![Cover image of Paka](https://i.imgur.com/XXuMFlA.jpg)

### :zap: Using

```bash
# Clone this project first
git clone https://github.com/alpaca0x0/paka.git -b v2 paka
```

```bash
# Enter project folder
cd paka
# Copy config example files
cp config.example.php config.php
cp configs/db.example.php configs/db.php
cp configs/email.example.php configs/email.php
# Edit config files (choose your own editor)
vim config.php
vim configs/db.php
vim configs/email.php
```

```bash
# Set database
# Import the sql file
# Need to rename by yourself
src/alpacatech.sql
```

```bash
# Setting router in web server
# For example, nginx:
vim /etc/nginx/conf.d/default.conf
```

```nginx
# Route all traffic to router.php in project root path
# p.s. The "root" value set as your own path of project root
#      The same goes for other fields...
location ^~ /paka/ {
    root /var/www/html/paka;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/router.php;
    fastcgi_pass unix:/run/php/php-fpm.sock;
}
```

:grin: Have fun.

---

## :cactus: Update

### :bug: Bugs

- [x] `Profile` 中的 nickname 輸入中文時，前端與後端判斷其長度不相同(後端判斷中文並非 1 長度)。
- [x] `Profile` 中的 nickname 輸入空值後，後端回傳新的資料為 `null`，而前端在字串處理上，將會報錯「`fields.nickname.value is null`」。
- [x] 未經過驗證的帳戶(`status` 為 `unverified`)可以被登入。
- [x] `Forum` 中的 `post action` 失效，原因是 `vue-click-alway` 觸發點錯誤。
- [x] 用戶`token`憑證更新時，cookie 不會更新。
- [x] 貼文文字過多時不會自動換行。(add `overflow-wrap: break-word`)
- [x] 修復 `Forum::editPost()` 在 `MySQL 5.7+` 會出現「`Syntax error or access violation: 1140 In aggregated query without GROUP BY, expression`」的錯誤。

<!-- ### :wrench: Issues -->

### :seedling: Optimization, Beautify

- [x] `Sweetalert2` 因使用了 dark theme，與 Tocas-UI dark mode 色調相近，視覺上難以直覺區分。(暫時關閉 sweetalert2 dark theme)
- [x] 將 `account/login.php` 中的變數 `refUsername`, `refPassword`, `refCaptcha`, `refSubmit` 作為陣列存放於變數 `refs` 中。
- [ ] `Register` 驗證信內容排版不好看QQ
- [x] `Edit profile` 時，若收到欄位資料的 warning 回應，應該自動 focus 欄位
- [x] `Register` 與 `login` 時，界面 loading 應該明顯一點。
- [x] `Login page` 驗證碼不正確時，其欄位 status 外觀不會反應。
- [x] 在登入後，`navbar` 上 `account` 選項的下拉式選單，其關閉事件改為由 `vue-click-away` 觸發。
- [x] Class `Forum` 中，獲取文章的 `getPosts` 或 `getPost`，不應該每次都返回全部資料，這對 sql server 負擔很大。
- [X] 當前 `forum` 指定返回欄位的功能在後端程式邏輯十分雜亂，可讀性不高，難以維護。(已模組化)
- [x] `Forum` 當中的文章若非使用者所有，則不應該出現 `postActions` 按鈕。
- [x] `Forum` 在發出 `create` 或 `delete` 請求後，UI 應呈現 loading 狀態。
- [x] `放棄了 = =` ~~`Forum` class 架構難於 `post` 及 `comment` 調用，需要再更改。~~
- [x] 不該使用 `times` 作為數量統計的命名，用 `count` 會更好一些。

### :memo: Todo list

- Account
  - [x] login
  - [x] register
  - [x] register-SMTP
  - [ ] change email
  - [x] profile
  - [x] edit profile
  - [ ] edit avatar
  - [ ] events note
- Forum
  - [x] posts api & view post `(UI not all)`
  - [x] create post
  - [X] delete post
  - [x] edit post
  - [x] comment post
  - [x] delete comment
  - [ ] edit comment
  - [x] reply comment
  - [x] delete reply
  - [ ] edit reply
  - [ ] like post
  - [ ] share post
  - [ ] popular posts
- RWD
  - [x] login & register pages
  - [x] profile
  - [ ] forum

---

## :gear: Structures

說明一些關於該專案的架構，僅僅解釋較為主要或有疑慮的部份。

### :sassy_woman: `router`

相較於前一個版本，多了路由的設計，使後端程式更加簡潔乾淨，在 URI 上也較直覺。所有流量都會被導向至`/router.php`，其稱作`Main Router`，再由此路由判斷請求的類型，並將其導向至其類型專屬的子路由(`Sub Router`)。

### :clipboard: Files

- `config.php` 用於存放該站點的核心參數，如設定站點專案根目錄 `ROOT`，或是開關 `DEBUG` 或 `DEV` 模式等。
- `init.php` 用於初始化站點的核心檔案，會自動的引入`config.php`。
- `router.php` 主要的路由(`Main Router`)，所有流量必須經過這，由該檔案將流量導至其他子路由(`Sub Router`)。該檔案會自動引入`init.php`。

### :open_file_folder: Folders

#### :small_blue_diamond: `api`

一些專門用於獲取資料的頁面。

- `captcha` 取得驗證碼的值，當然這只能用於 DEV 模式下。

#### :small_blue_diamond: `assets`

前端所會用到的資源，包含常見的 JS、CSS，以及圖片、插件庫等。

#### :small_blue_diamond: `auth`

用於驗證的頁面，通常採用 Ajax 請求，並回應 Json 格式。通常情況下，回應欄位有以下幾種：

- `type` 作為回應類別，通常有如下幾種常見的類型：
  - `success` 成功執行
  - `warning` 請求存在問題，而無法完成
  - `error` 伺服端錯誤
  - `info` 單純的顯示訊息

    當然這並非固定格式，在某些特定功能中，也會有其獨有的回應格式。

- `status` 回應狀態碼，同個功能下，必須是唯一值，用於表明請求的處理狀態，且不能含有空格。例如`is_login`或`data_not_found`...等。
- `data` 用於回傳相關資料，如執行更新請求後，回傳新值等。\
  當然，若是沒有需要回傳的欄位，該欄位可以為`NULL`。
- `message` 用於顯示的回應訊息，可以含有任意字元，但通常結尾並不會有標點符號。大多數情況下，該欄位被要求必須設定，但在少數情況，該欄位被允許為`NULL`。

#### :small_blue_diamond: `classes`

一些核心的功能，以 Class 的方式包裝資料與函式，通常為靜態呼叫。

- `init` 在該目錄下的檔案會在初始時自動的被載入。

#### :small_blue_diamond: `components`

一些常用的頁面部件，如`header`、`footer`等。

#### :small_blue_diamond: `configs`

用於存放設定檔的目錄，其`.example`為範例檔案，需要將檔名中的該字節刪除。如`db.example.php`修改內容後更名為`db.php`。

#### :small_blue_diamond: `functions`

一些核心的函式。

- `init` 在該目錄下的檔案會在初始時自動的被載入。

#### :small_blue_diamond: `libraries`

`libraries` 與 `assets/plugin` 不同的在於，`plugin` 資源可於前端調用，而 `libraries` 僅供後端使用。

#### :small_blue_diamond: `pages`

存放站點的主要頁面。

#### :small_blue_diamond: `routers`

用於存放`Sub Router`的目錄。

#### :small_blue_diamond: `src`

用於存放站點架設所需的資源，該目錄並未有嚴格的規定，總之... ~~有需要就塞~~。

---

## :sparkles: Frameworks && Libraries

### :art: CSS

- [`Tocas-UI`](https://tocas-ui.com) (v4.1.0)
- [`Animate.css`](https://animate.style/) (v4.1.1)

### :magic_wand: JS

- [`Vue3`](https://vuejs.org) (v3.0.2)
- [`Sweetalert2`](https://sweetalert2.github.io/) (v11.6.16)
- [`Moment.js`](https://momentjs.com/) (v2.29.4)
- [`Ajax`](https://projects.jga.me/jquery-builder/) (`Jquery-ajax-only` from [`jquery-builder`](https://projects.jga.me/jquery-builder/))
- [`Cropper.js`](https://fengyuanchen.github.io/cropperjs/) (v1.5.13)

### :link: Libraries

- [`PHPMailer`](https://github.com/PHPMailer/PHPMailer) (v6.7.1)

---

### :coffee: Developer(s)

<sub>QQ 獨立開發好累，幸好還有很多方便的現成工具...</sub>

- [`Alpaca0x0`](https://github.com/alpaca0x0)

---
