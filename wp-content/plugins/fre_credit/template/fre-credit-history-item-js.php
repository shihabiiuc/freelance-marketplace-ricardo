<script type="text/template" id="fre-credit-history-loop">
    <div class="fre-table-col table-col-type"> {{= post_title }} <b>{{= amount_text }}</b></div>
    <div class="fre-table-col table-col-amount">{{= amount_text }}</div>
    <div class="fre-table-col table-col-action"> {{= info_changelog}}</div>
    <div class="fre-table-col table-col-payment">{{= payment_gateway}}</div>
    <div class="fre-table-col table-col-status">{{= history_status_text }} <span>on {{= history_time }}</span></div>
    <div class="fre-table-col table-col-time">{{= history_time }}</div>
</script>