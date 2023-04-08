<?php

// Limit activity posts by day and user role
function limit_activity_posts($user_id)
{
    // get the current user role
    $user_role = get_userdata($user_id)->roles[0];

    // set the maximum number of posts based on user role
    switch ($user_role) {
        case "subscriber":
            $max_posts = 3;
            break;
        case "contributor":
            $max_posts = 5;
            break;
        case "editor":
            $max_posts = 10;
            break;
        case "administrator":
            $max_posts = -1;
            break;
        default:
            $max_posts = 0;
    }

    // check the number of posts for the current user
    $today = strtotime("today midnight");
    $args = [
        "user_id" => $user_id,
        "date_query" => [
            [
                "after" => $today,
                "inclusive" => true,
            ],
        ],
    ];
    $count = bp_activity_get($args, "total");

    // if the maximum number of posts is reached, prevent new post submission
    if ($max_posts >= 0 && $count >= $max_posts) {
        bp_core_add_message(
            sprintf(
                __(
                    "You have reached the maximum number of posts allowed for today (%d).",
                    "buddypress"
                ),
                $max_posts
            ),
            "error"
        );
        bp_core_redirect(bp_get_activity_directory_permalink());
    }
}
add_action("bp
