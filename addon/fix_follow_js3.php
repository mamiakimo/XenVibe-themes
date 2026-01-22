<?php

// Use inline script instead of xf:js for the variable
$jsReplace = <<<'JSTEMPLATE'
<xf:css src="member.less" />
<script>
window.xvFollowing = {{ json_encode($xf.visitor.Profile.following ?: []) }};
</script>
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

    // Update follow buttons based on xvFollowing data
    if (window.xvFollowing && Array.isArray(window.xvFollowing)) {
        var followBtns = document.querySelectorAll(".js-followBtn");
        followBtns.forEach(function(btn) {
            var userId = parseInt(btn.getAttribute("data-user-id"), 10);
            if (window.xvFollowing.indexOf(userId) !== -1) {
                btn.innerHTML = "<span class='material-symbols-outlined'>person_remove</span> إلغاء المتابعة";
                btn.classList.add("is-following");
            }
        });
    }
})();
</xf:js>
JSTEMPLATE;

require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

$result = $db->update('xf_template_modification', [
    'replace' => $jsReplace
], 'modification_key = ?', 'xv_member_filter_js');

echo $result ? "JS template updated!\n" : "No change to JS\n";

$template = \XF::finder('XF:Template')
    ->where('title', 'member_notable')
    ->where('type', 'public')
    ->fetchOne();

if ($template) {
    $template->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
    $template->save();
    echo "Template recompiled!\n";
}
