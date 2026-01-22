<?php
/**
 * Insert template modifications for XenVibe/Core
 * Run this on the server: php insert_mods.php
 */

require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

// JS Filter modification - find and replace
$jsFind = '<xf:css src="member.less" />';

$jsReplace = '<xf:css src="member.less" />
<xf:js>
// XenVibe Member Filters
(function() {
    var container = document.querySelector(".memberOverviewBlocks");
    if (!container) return;

    var filtersHtml = \'<div class="xv-member-filters">\' +
        \'<button class="xv-filter-btn is-active" data-target="most_messages"><span class="material-symbols-outlined">trending_up</span> الأكثر مشاركة</button>\' +
        \'<button class="xv-filter-btn" data-target="highest_reaction_score"><span class="material-symbols-outlined">favorite</span> الأكثر تفاعلاً</button>\' +
        \'<button class="xv-filter-btn" data-target="most_solutions"><span class="material-symbols-outlined">check_circle</span> الأكثر إجابة</button>\' +
        \'<button class="xv-filter-btn" data-target="most_points"><span class="material-symbols-outlined">star</span> الأكثر نقاطاً</button>\' +
        \'<button class="xv-filter-btn" data-target="todays_birthdays"><span class="material-symbols-outlined">cake</span> أعياد ميلاد اليوم</button>\' +
        \'<button class="xv-filter-btn" data-target="staff_members"><span class="material-symbols-outlined">shield_person</span> طاقم الإدارة</button>\' +
        \'</div>\';

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

    // Toggle is-following class on follow buttons
    document.querySelectorAll(".xv-member-follow-btn[data-xf-click=switch]").forEach(function(btn) {
        btn.addEventListener("click", function() {
            this.classList.toggle("is-following");
        });
    });
})();
</xf:js>';

// Card redesign modification
$cardFind = '<xf:macro id="overview_row" arg-data="!">
	<div class="contentRow contentRow--alignMiddle">
		<div class="contentRow-figure">
			<xf:avatar user="$data.user" size="xs" />
		</div>
		<div class="contentRow-main">
			<xf:if is="$data.value">
				<div class="contentRow-extra contentRow-extra--large">{$data.value}</div>
			</xf:if>
			<h3 class="contentRow-title"><xf:username user="$data.user" rich="true" /></h3>
		</div>
	</div>
</xf:macro>';

$cardReplace = '<xf:macro id="overview_row" arg-data="!">
	<div class="xv-member-card">
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
			<a href="{{ link(\'members/follow\', $data.user) }}"
				class="xv-member-follow-btn{{ $xf.visitor.isFollowing($data.user) ? \' is-following\' : \'\' }}"
				data-xf-click="switch"
				data-sk-follow="متابعة"
				data-sk-unfollow="إلغاء المتابعة">{{ $xf.visitor.isFollowing($data.user) ? \'إلغاء المتابعة\' : \'متابعة\' }}</a>
		<xf:else />
			<a href="{{ link(\'members\', $data.user) }}" class="xv-member-follow-btn xv-view-profile">عرض الملف</a>
		</xf:if>
	</div>
</xf:macro>';

// Delete existing modifications first
$deleted = $db->delete('xf_template_modification', "modification_key IN ('xv_member_filter_js', 'xv_member_card_redesign')");
echo "Deleted $deleted existing mods\n";

// Insert JS modification
$db->insert('xf_template_modification', [
    'type' => 'public',
    'template' => 'member_notable',
    'modification_key' => 'xv_member_filter_js',
    'description' => 'JavaScript filter buttons',
    'execution_order' => 20,
    'enabled' => 1,
    'addon_id' => 'XenVibe/Core',
    'action' => 'str_replace',
    'find' => $jsFind,
    'replace' => $jsReplace
]);
echo "Inserted xv_member_filter_js\n";

// Insert card modification
$db->insert('xf_template_modification', [
    'type' => 'public',
    'template' => 'member_notable',
    'modification_key' => 'xv_member_card_redesign',
    'description' => 'XenVibe Stitch-style member cards',
    'execution_order' => 30,
    'enabled' => 1,
    'addon_id' => 'XenVibe/Core',
    'action' => 'str_replace',
    'find' => $cardFind,
    'replace' => $cardReplace
]);
echo "Inserted xv_member_card_redesign\n";

// Recompile the template
$template = \XF::finder('XF:Template')
    ->where('title', 'member_notable')
    ->where('type', 'public')
    ->fetchOne();

if ($template) {
    $template->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
    $template->save();
    echo "Template recompiled!\n";
}

// Verify
$mods = $db->fetchAll("SELECT modification_key FROM xf_template_modification WHERE modification_key LIKE 'xv_%'");
echo "\nNow have " . count($mods) . " xv_ modifications:\n";
foreach ($mods as $m) {
    echo "- " . $m['modification_key'] . "\n";
}
