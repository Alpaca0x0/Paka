<?php
Inc::component('header');
Inc::component('navbar');
?>

<div class="ts-space is-large"></div>
    <div class="ts-container is-narrow">
        <div class="ts-header is-large is-heavy">About</div>
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
                            <div class="value">8,652</div>
                            <div class="comparison is-increased">351</div>
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