<?php
Inc::component('header');
Inc::component('navbar');

Inc::clas('db');
DB::connect() or die('Error - SQL Cannot Connect.');
$member = DB::query(
    'SELECT COUNT(DISTINCT `account`.`id`) AS `count`
    , COUNT(DISTINCT `register`.`uid`) as `registeredSince7Days`
    FROM `account`
    LEFT JOIN `account_event` AS `register` ON (`register`.`commit` = "register" AND `account`.`id` = `register`.`uid` AND UNIX_TIMESTAMP(`register`.`datetime`)>:datetimeSince7Days)
    WHERE `account`.`status` <> "removed" AND `account`.`status` <> "unverified"
;'
)::execute([
    ':datetimeSince7Days' => time() - 60*60*24*7,
])::fetch();
if(DB::error()){ die('Error - SQL Query.'); }
?>

<div class="ts-space is-large"></div>
    <div class="ts-container is-narrow">
        <div class="ts-header is-large is-heavy">About (本頁面僅模擬，資料並非事實)</div>
        <div class="ts-text is-secondary">關於我們的站點。</div>
        <div class="ts-space is-large"></div>
        <div class="ts-grid is-relaxed is-3-columns">
            <div class="column">
                <div class="ts-box">
                    <div class="ts-content">
                        <div class="ts-statistic">
                            <div class="value">42,689</div>
                            <div class="comparison is-increased">32</div>
                        </div>
                        本月拜訪次數
                    </div>
                    <div class="symbol">
                        <span class="ts-icon is-eye-icon"></span>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="ts-box">
                    <div class="ts-content">
                        <div class="ts-statistic">
                            <div class="value"><?=$member['count']?></div>
                            <div class="comparison is-increased"><?=$member['registeredSince7Days']?></div>
                        </div>
                        總會員數
                    </div>
                    <div class="symbol">
                        <span class="ts-icon is-users-icon"></span>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="ts-box">
                    <div class="ts-content">
                        <div class="ts-statistic">
                            <div class="value">3</div>
                            <div class="comparison is-decreased">14</div>
                        </div>
                        平均在線分鐘數
                    </div>
                    <div class="symbol">
                        <span class="ts-icon is-clock-icon"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
Inc::component('footer');
