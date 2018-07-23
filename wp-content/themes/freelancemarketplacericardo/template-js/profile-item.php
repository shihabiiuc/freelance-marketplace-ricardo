<script type="text/template" id="ae-profile-loop">

    <div class="profile-list-wrap">
        <a class="profile-list-avatar" href="{{= author_link }}">
            {{= et_avatar}}
        </a>
        <h2 class="profile-list-title">
            <a href="{{= author_link }}">{{= author_name }}</a>
        </h2>
        <p class="profile-list-subtitle">{{= et_professional_title }}</p>
        <div class="profile-list-info">
            <div class="profile-list-detail">
                <span class="rate-it" data-score="{{= rating_score }}"></span>
                <span>{{= experience }}</span>
                <span>{{= project_worked }}</span>
                <# if( hourly_rate_price ){  #>
                <span>{{= hourly_rate_price }}</span>
                <# } #>
                <span style="font-weight: normal;">{{= earned }}</span>
            </div>
            <div class="profile-list-desc">
                {{= excerpt }}
            </div>
        </div>
    </div>

</script>