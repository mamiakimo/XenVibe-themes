<?php

$newReplace = <<<'TEMPLATE'
<xf:macro id="overview_row" arg-data="!">
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
			<xf:set var="$followingArray" value="{{ $xf.visitor.Profile.following }}" />
			<xf:set var="$isFollowingCheck" value="{{ in_array($data.user.user_id, $followingArray) }}" />
			<a href="{{ link('members/follow', $data.user) }}"
				class="xv-member-follow-btn {{ $isFollowingCheck ? 'is-following' : '' }}"
				data-xf-click="switch"
				data-sk-follow="<span class='material-symbols-outlined'>person_add</span> متابعة"
				data-sk-unfollow="<span class='material-symbols-outlined'>person_remove</span> إلغاء المتابعة">
				<xf:if is="$isFollowingCheck">
					<span class="material-symbols-outlined">person_remove</span>
					إلغاء المتابعة
				<xf:else />
					<span class="material-symbols-outlined">person_add</span>
					متابعة
				</xf:if>
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

echo $result ? "Updated!\n" : "No change\n";

$template = \XF::finder('XF:Template')
    ->where('title', 'member_notable')
    ->where('type', 'public')
    ->fetchOne();

if ($template) {
    $template->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
    $template->save();
    echo "Template recompiled!\n";
}
