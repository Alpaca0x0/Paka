<?php
Inc::clas('user');
!User::isLogin() or die(header('Location: '.Root));
Inc::component('header');
Inc::component('navbar');
$config = Inc::config('account');
?>

<div class="ts-content is-tertiary is-vertically-padded">
    <!-- <div class="ts-space"></div> -->
    <div class="ts-container is-very-narrow">
        <div class="ts-header is-big is-heavy">Register</div>
        <div class="ts-text is-secondary">已有帳號？馬上<a href="<?=htmlentities(Uri::page('account/login'))?>">登入</a>吧！</div>
    </div>
    <!-- <div class="ts-space"></div> -->
</div>

<div class="ts-divider"></div>

<div class="ts-space is-big"></div>

<div class="ts-container is-very-narrow">
    <div class="ts-grid is-relaxed is-2-columns">
        <div class="column">
            <div class="ts-text is-label">使用者帳號</div>
            <div class="ts-space"></div>
            <div class="ts-input is-negative is-underlined is-fluid">
                <input type="text">
            </div>
            <div class="ts-space is-small"></div>
            <div class="ts-text is-description"><?=htmlentities($config['description']['username'])?></div>
        </div>
        <div class="column">
            <div class="ts-text is-label">暱稱</div>
            <div class="ts-space"></div>
            <div class="ts-input is-underlined is-fluid">
                <input type="text">
            </div>
            <div class="ts-space is-small"></div>
            <div class="ts-text is-description">替自己取一個獨一無二的暱稱，你可以在事後更改。</div>
        </div>
    </div>
    <div class="ts-space is-large"></div>
    <div class="ts-text is-label">電子郵件地址</div>
    <div class="ts-space"></div>
    <div class="ts-input is-underlined is-fluid">
        <input type="text">
    </div>
    <div class="ts-space is-large"></div>
    <div class="ts-text is-label">密碼</div>
    <div class="ts-space"></div>
    <div class="ts-input is-underlined is-fluid">
        <input type="password">
    </div>
    <div class="ts-space is-small"></div>
    <div class="ts-text is-description"><?=htmlentities($config['description']['password'])?></div>
    <div class="ts-space is-large"></div>
    <div class="ts-grid is-relaxed is-2-columns">
        <div class="column">
            <div class="ts-text is-label">生日</div>
            <div class="ts-space"></div>
            <div class="ts-grid is-3-columns">
                <div class="column">
                    <div class="ts-select is-underlined is-fluid">
                        <select>
                            <option value="2022">2022</option>
                        </select>
                    </div>
                </div>
                <div class="column">
                    <div class="ts-select is-underlined is-fluid">
                        <select>
                            <option value="01">01</option>
                        </select>
                    </div>
                </div>
                <div class="column">
                    <div class="ts-select is-underlined is-fluid">
                        <select>
                            <option value="01">01</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="ts-space is-small"></div>
            <div class="ts-text is-description">我們會依照你的年齡決定你是否能看見敏感性內容。</div>
        </div>
        <div class="column">
            <div class="ts-text is-label">性別</div>
            <div class="ts-space"></div>
            <div class="ts-wrap is-center-aligned">
                <label class="ts-radio">
                    <input name="gender" type="radio" checked="">
                    男性
                </label>
                <label class="ts-radio">
                    <input name="gender" type="radio">
                    女性
                </label>
                <label class="ts-radio">
                    <input name="gender" type="radio">
                    其它
                </label>
            </div>
        </div>
    </div>
    <div class="ts-space is-large"></div>
    <button class="ts-button is-fluid">下一步</button>
    <div class="ts-space is-small"></div>
    <div class="ts-text is-center-aligned is-description">按下「下一步」表示您也接受「伊繁星最高協議」、「個人隱私政策」、「使用者規範」。</div>
</div>


<?php
Inc::component('footer');