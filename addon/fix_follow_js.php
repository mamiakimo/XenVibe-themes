<?php

$newReplace = <<<'TEMPLATE'
<xf:macro id="overview_row" arg-data="!">
	<div class="xv-member-card" data-user-id="{$data.user.user_id}">
		<div class="xv-member-avatar">
			<xf:avatar user="$data.user" size="m" />
		</div>
		<h3 class="xv-member-name">
			<xf:username user="$data.user" rich="true" />
		</h3>
		<xf:if is="$data.user.custom_title">
			<span class="xv-member-badge">{$data.user.custom_title}</span>
		</xf:if>
		<div class="xv-member-stats">
			<div class="xv-stat">
				<span class="xv-stat-label">رسائل</span>
				<span class="xv-stat-value">{{ number_short($data.user.message_count) }}</span>
			</div>
			<div class="xv-stat">
				<span class="xv-stat-label">تفاعلات</span>
				<span class="xv-stat-value">{{ number_short($data.user.reaction_score) }}</span>
			</div>
			<div class="xv-stat">
				<span class="xv-stat-label">نقاط</span>
				<span class="xv-stat-value">{{ number_short($data.user.trophy_points) }}</span>
			</div>
		</div>
		<xf:if is="$xf.visitor.canFollowUser($data.user)">
			<a href="{{ link('members/follow', $data.user) }}"
				class="xv-member-follow-btn"
				data-xf-click="switch"
				data-sk-follow="<span class='material-symbols-outlined'>person_add</span> متابعة"
				data-sk-unfollow="<span class='material-symbols-outlined'>person_remove</span> إلغاء المتابعة">
				<span class="material-symbols-outlined">person_add</span>
				متابعة
			</a>
		<xf:else />
			<a href="{{ link('members', $data.user) }}" class="xv-member-follow-btn">
				<span class="material-symbols-outlined">person</span>
				عرض الملف
			</a>
		</xf:if>
	</div>
</xf:macro>
TEMPLATE;

require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

$result = $db->update('xf_template_modification', [
    'replace' => $newReplace
], 'modification_key = ?', 'xv_member_card_redesign');

echo $result ? "Card template updated!\n" : "No change to card\n";

// Now update the JS modification to include follow state check
$jsReplace = <<<'JSTEMPLATE'
<xf:css src="member.less" />
<xf:js>
// XenVibe Member Filters
(function() {
    var container = document.querySelector(".memberOverviewBlocks");
    if (!container) return;

    var filtersHtml = '<div class="xv-member-filters">' +
        '<button class="xv-filter-btn is-active" data-target="most_messages"><span class="material-symbols-outlined">trending_up</span> الأكثر مشاركة</button>' +
        '<button class="xv-filter-btn" data-target="highest_reaction_score"><span class="material-symbols-outlined">favorite</span> الأكثر تفاعلاً</button>' +
        '<button class="xv-filter-btn" data-target="most_solutions"><span class="material-symbols-outlined">check_circle</span> الأكثر إجابة</button>' +
        '<button class="xv-filter-btn" data-target="most_points"><span class="material-symbols-outlined">star</span> الأكثر نقاطاً</button>' +
        '<button class="xv-filter-btn" data-target="todays_birthdays"><span class="material-symbols-outlined">cake</span> أعياد ميلاد اليوم</button>' +
        '<button class="xv-filter-btn" data-target="staff_members"><span class="material-symbols-outlined">shield_person</span> طاقم الإدارة</button>' +
        '</div>';

    container.insertAdjacentHTML("beforebegin", filtersHtml);
    container.classList.add("js-filtered");

    var filters = document.querySelectorAll(".xv-filter-btn");
    var sections = document.querySelectorAll(".memberOverviewBlock");
    var keyMap = {
        "most_messages": "key=most_messages",
        "highest_reaction_score": "key=highest_reaction_score",
        "most_solutions": "key=most_solutions",
        "most_points": "key=most_points",
        "todays_birthdays": "key=todays_birthdays",
        "staff_members": "key=staff_members"
    };

    if (sections.length > 0) sections[0].classList.add("is-visible");

    filters.forEach(function(btn) {
        btn.addEventListener("click", function() {
            var target = this.getAttribute("data-target");
            var targetKey = keyMap[target];
            filters.forEach(function(f) { f.classList.remove("is-active"); });
            this.classList.add("is-active");
            sections.forEach(function(s) { s.classList.remove("is-visible"); });
            sections.forEach(function(section) {
                var link = section.querySelector(".block-textHeader a, .memberOverviewBlock-seeMore a");
                if (link && link.href && link.href.indexOf(targetKey) !== -1) {
                    section.classList.add("is-visible");
                }
            });
        });
    });

    // Check follow state for each button via AJAX
    if (typeof XF !== 'undefined' && XF.visitor && XF.visitor.user_id) {
        var followBtns = document.querySelectorAll(".xv-member-follow-btn[data-xf-click='switch']");
        followBtns.forEach(function(btn) {
            var href = btn.getAttribute("href");
            if (!href) return;

            // Make AJAX request to check follow state
            XF.ajax("GET", href, {}, function(data) {
                if (data.html && data.html.content) {
                    // Check if the response indicates already following
                    if (data.html.content.indexOf("stop_following") !== -1 ||
                        data.html.content.indexOf("إلغاء") !== -1 ||
                        data.html.content.indexOf("unfollow") !== -1) {
                        btn.innerHTML = "<span class='material-symbols-outlined'>person_remove</span> إلغاء المتابعة";
                        btn.classList.add("is-following");
                    }
                }
            }, { skipDefault: true, skipError: true, global: false });
        });
    }
})();
</xf:js>
JSTEMPLATE;

$result2 = $db->update('xf_template_modification', [
    'replace' => $jsReplace
], 'modification_key = ?', 'xv_member_filter_js');

echo $result2 ? "JS template updated!\n" : "No change to JS\n";

$template = \XF::finder('XF:Template')
    ->where('title', 'member_notable')
    ->where('type', 'public')
    ->fetchOne();

if ($template) {
    $template->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
    $template->save();
    echo "Template recompiled!\n";
}
