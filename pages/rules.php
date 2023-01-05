<?php
Inc::component('header');
Inc::component('navbar');
?>

<div class="ts-space is-large"></div>
<div id="Ruels" class="ts-container is-narrow">
    <div class="ts-header is-large is-heavy">Rules</div>
    <div class="ts-text is-secondary" style="white-space: pre-line">{{ description }}</div>
    <div class="ts-space is-large"></div>

    <template v-for="rule in rules">
        <div class="ts-segment is-dense">
            <div class="ts-quote is-small" style="white-space: pre-line">
                <p v-html="rule.content"></p>
                <p v-if="rule.aftermath" class="ts-text is-secondary" v-html="rule.aftermath"></p>
                <p v-if="rule.punish" class="ts-text is-negative" v-html="rule.punish"></p>
            </div>
        </div>
    </template>

</div>

<script type="module">
    import { createApp } from '<?=Uri::js('vue')?>';

    const Rules = createApp({setup(){
        let name = '<?=NAME?>';
        let title = '';
        let description = '在 <?=NAME?> 社群網站上，我們希望所有用戶都能夠愉快地使用我們的服務。\n為了確保我們的社群保持和諧，我們列出了以下規章，希望用戶們能夠遵守。';
        // 
        let aftermaths = {
            legalLiability: () => { return '自行負責其相關法律責任'; },
        };
        let punishs = {
            free: () => { return '不限範圍的懲處'; },
            deleteAccount: () => { return '永久刪除帳戶'; },
            banByDay: (day, toDay=false) => { return `暫停帳號使用權 ${day} ` + (toDay!==false ? `至 ${toDay} ` : '') + `天`; },
        };
        // 
        let rules = [
            {
                content: '發佈或使用任何 威脅、騷擾、暴力、血腥 或 淫穢 內容',
                aftermath: '刪除您的文章，並提出警告',
                punish: `視情況可能${punishs.banByDay(1,3)}，甚至${punishs.deleteAccount()}`,
            },
            {
                content: '侵犯任何人的版權或知識產權',
                aftermath: `${aftermaths.legalLiability()}`,
                punish: `若侵權事件影響本服務，則視情況進行${punishs.free()}`
            },
            {
                content: '濫用系統功能，包括惡意的進行網路攻擊行為，或是嘗試入侵他人帳戶，侵犯他人權益等\n(詳情請見 <a href="https://law.moj.gov.tw/LawClass/LawParaDeatil.aspx?pcode=C0000001&bp=53">中華民國刑法 第 三十六 章 妨害電腦使用罪 第 358 至 363 條)</a>',
                punish: `視情況可能${punishs.banByDay(1,7)}，甚至${punishs.deleteAccount()}\n視情況將可能採取相關法律行動`,
            },
            {
                content: '禁止冒充他人，及其他各種誤導行為也將不被允許',
                aftermath: `${aftermaths.legalLiability()}`,
                punish: `視情況可能${punishs.banByDay(1,7)}，甚至${punishs.deleteAccount()}`,
            },
            {
                content: '禁止垃圾訊息、未經授權的廣告、未經授權的商業活動',
                punish: `視情況可能${punishs.banByDay(1,7)}，甚至${punishs.deleteAccount()}`,
            },
            {
                content: '禁止使用程式自動化工具，包括未經授權調用 API 等',
                punish: `視情況可能${punishs.banByDay(1,7)}，甚至${punishs.deleteAccount()}`
            }
        ];
        // 
        return { name, title, description, rules };
    }}).mount('#Ruels');
</script>

<?php
Inc::component('footer');