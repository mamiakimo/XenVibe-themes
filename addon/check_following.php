<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

// Get visitor (simulate logged in user)
$visitor = \XF::visitor();

echo "Visitor ID: " . $visitor->user_id . "\n";
echo "Visitor username: " . $visitor->username . "\n\n";

// Check what methods/properties are available for following
echo "=== Checking following methods ===\n";

// Check if Profile relation exists
if ($visitor->Profile) {
    echo "Profile exists\n";
    $profile = $visitor->Profile;

    // Check following
    if (isset($profile->following)) {
        echo "Profile->following: " . print_r($profile->following, true) . "\n";
    }
}

// Check isFollowing method
if (method_exists($visitor, 'isFollowing')) {
    echo "isFollowing method exists\n";

    // Try calling it with a user_id
    try {
        $result = $visitor->isFollowing(111894);
        echo "isFollowing(111894): " . ($result ? 'true' : 'false') . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Check the following relation
echo "\n=== Checking Following relation ===\n";
$following = $visitor->Following;
if ($following) {
    echo "Following count: " . $following->count() . "\n";
    foreach ($following as $user) {
        echo "- " . $user->username . " (ID: " . $user->user_id . ")\n";
    }
}
