<?php
return [
    'breadcrumb' => [
        'admin-dashboard' => 'Home',
        'admin-users-index' => 'User',
        'admin-groups-index' => 'Group',
        'admin-users-edit' => 'Edit',
        'admin-groups-edit' => 'Edit',
        'admin-groups-members' => 'Edit Members',
    ],
    'general' => [
        'ok' => 'Ok',
        'cancel' => 'Cancel',
        'name' => 'Name',
        'home' => 'Home',
        'settings' => 'Settings',
        'profile' => 'Profile',
        'save' => 'Save',
        'save_changes' => 'Save Changes',
        'update' => 'Update',
        'logout' => 'Logout',
        'login' => 'Log In',
        'register' => 'Register',
        'success' => 'Success!',
        'password' => 'Password',
        'actions' => 'Actions',
        'email' => 'E-mail',
    ],
    'db' => [
        'username' => 'User name',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'full_name' => 'Full name',
        'new_email' => 'New e-mail',
        'new_username' => 'New username',
        'user_created_at' => 'Registration date',
        'group_name' => 'Group name',
        'group_mask' => 'Group mask',
        'member_count' => 'Number of members'
    ],
    'form' => [
        'description' => [
            'username' => 'The user\'s shorthand name.',
            'first_name' => 'The user\'s first (given) name.',
            'last_name' => 'The user\'s last (family) name.',
            'new_email' => '"{0}" is the current e-mail address.',
            'new_username' => '"{0}" is the current username.',
            'group_name' => 'The group name can only contain alphanumeric characters and underscores.',
            'group_mask' => 'Determines the group\'s position in its hierarchy. The lower the mask, the higher the group status.'
        ],
    ],
    'modal' => [
        'error' => [
            'h' => 'Oops...',
            't' => 'Something went wrong! Please try again.'
        ],
        'token_expired' => [
            'h' => 'Session Expired!',
            't' => 'Please log in again to continue.',
        ],
        'unauthorized' => [
            'h' => 'Access Denied',
            't' => 'You are not authorized to view this page.',
        ]
    ],
    'error' => [
        'page_not_found' => 'Page Not Found',
        'passwords_dont_match' => 'The passwords must be identical.',
        'form' => 'Errors were found on this form.'

    ],
    'message' => [
        'info_updated' => 'Your info has been updated!',
        'password_updated' => 'Your password has been updated!',
        'user_update_ok' => 'The user was updated successfully.',
        'group_update_ok' => 'The group was updated successfully.'
    ],
    'pages' => [
        'auth' => [
            'remember_me' => 'Remember Me',
            'forgot_password' => 'Forgot Your Password?',
            'send_password_reset_link' => 'Send Password Reset Link',
            'confirm_password' => 'Confirm Password',
            'reset_password' => 'Reset Password',
            'your_password' => 'Your Password',
            'new_password' => 'New Password',
        ],
        'members'=>[
            'member_search'=>'Type user full name here, i.e \'Jane Doe\'',
            'group_name'=>'Group:',
            'edit_preview'=>'Preview',
            'no_changes'=>'No changes so far.',
            'add_members'=>'Add members',
            'remove_members'=>'Remove members',
            'user_add_tag'=>'The following users will be added:',
            'user_no_add'=>'No added members.',
            'user_remove_tag'=>'The following users will be removed:',
            'user_no_remove'=>'No removed members.',
            'user_none'=>'There are no members in this group.',
            'current_members'=>'The following users are members of this group:',
        ]
    ],
    'tables' => [
        'empty' => 'There is currently no data available.',
        'sort_asc' => 'Sort in ascending order',
        'sort_desc' => 'Sort in descending order',

    ],
    'go_home' => 'Go Home',
    'toggle_navigation' => 'Toggle navigation',
    'your_info' => 'Your Info',
];
