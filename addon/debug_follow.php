<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

// Simulate a logged in user - get user ID 16568 (your test user)
$visitor = \XF::em()->find('XF:User', 16568);
if (!$visitor) {
    die("User not found\n");
}

// Set as visitor
\XF::setVisitor($visitor);

echo "=== Visitor Info ===\n";
echo "User ID: " . $visitor->user_id . "\n";
echo "Username: " . $visitor->username . "\n\n";

// Check Profile relation
echo "=== Profile Following ===\n";
$profile = $visitor->Profile;
if ($profile) {
    echo "Profile exists\n";
    echo "Profile following (raw): " . print_r($profile->following, true) . "\n";
}

// Test isFollowing with a specific user
$targetUser = \XF::em()->find('XF:User', 111894);
if ($targetUser) {
    echo "\n=== Testing isFollowing ===\n";
    echo "Target user: " . $targetUser->username . " (ID: " . $targetUser->user_id . ")\n";

    try {
        $result = $visitor->isFollowing($targetUser);
        echo "isFollowing result: " . ($result ? 'TRUE - Following' : 'FALSE - Not following') . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Check the User entity's isFollowing method source
echo "\n=== Checking User Entity ===\n";
$reflection = new ReflectionMethod($visitor, 'isFollowing');
echo "Method file: " . $reflection->getFileName() . "\n";
echo "Method line: " . $reflection->getStartLine() . "\n";

// Check what's in xf_user_follow table
echo "\n=== Database Check ===\n";
$db = \XF::db();
$following = $db->fetchAll("SELECT * FROM xf_user_follow WHERE user_id = ?", [$visitor->user_id]);
echo "Following count from DB: " . count($following) . "\n";
foreach ($following as $f) {
    echo "  - Following user_id: " . $f['follow_user_id'] . "\n";
}
