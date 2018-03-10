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
        'back' => 'Back',
        'permission' => 'Permission|Permissions',
        'toggle' => 'Toggle On/Off',
        'select_all' => 'Select all',
        'apply' => 'Apply',
        'search' => 'Search',
        'reset_filters'=>'Reset Filters'
    ],
    'db' => [
        'user' => 'User|Users',
        'group' => 'Group|Groups',
        'username' => 'User name',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'full_name' => 'Full name',
        'new_email' => 'New e-mail',
        'new_username' => 'New username',
        'user_created_at' => 'Registration date',
        'group_name' => 'Group name',
        'new_group_name' => 'New group name',
        'group_mask' => 'Group mask',
        'member_count' => 'Number of members'
    ],
    'db_raw' => [
        'full_name' => 'full_name',
        'email' => 'email',
        'group_name' => 'group_name',
        'created_at' => 'created_at'
    ],
    'db_raw_reverse' => [
        'full_name' => 'full_name',
        'email' => 'email',
        'group_name' => 'group_name',
        'created_at' => 'created_at',
    ],
    'form' => [
        'description' => [
            'username' => 'The user\'s shorthand name.',
            'first_name' => 'The user\'s first (given) name.',
            'last_name' => 'The user\'s last (family) name.',
            'new_email' => '"{0}" is the current e-mail address.',
            'new_username' => '"{0}" is the current username.',
            'group_name' => '"{0}" is the current group name. The group name can only contain alphanumeric characters and underscores.',
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
        'user_update_ok' => 'The user was updated. It may take a few seconds for permissions to update.',
        'user_delete_ok' => 'The user was deleted.|The users were deleted.',
        'group_update_ok' => 'The group was updated. It may take a few seconds for permissions to update.'
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
        'members' => [
            'member_search' => 'Type user full name here, i.e \'Jane Doe\'',
            'group_name' => 'Group:',
            'edit_preview' => 'Preview',
            'no_changes' => 'No changes so far.',
            'add_members' => 'Add members',
            'remove_members' => 'Remove members',
            'user_add_tag' => 'The following users will be added:',
            'user_no_add' => 'No added members.',
            'user_remove_tag' => 'The following users will be removed:',
            'user_no_remove' => 'No removed members.',
            'user_none' => 'There are no members in this group.',
            'current_members' => 'The following users are members of this group:',
        ],
        'users' => [
            'warning1' => 'Setting individual permissions for this user 
            will override permissions set on groups of which the user is a member.',
            'warning2' => 'We recommend setting permissions on groups instead, 
            and use individual user permissions to handle exceptions.',
            'filter_full_name' => 'Filter by full name',
            'filter_group' => 'Filter by group',
        ],
        'groups' => [
            'info1' => 'Permissions for all members of the group are defined here.',
            'info2' => 'Individual permissions can also be set at the user level,
            in which case user permissions will override permissions set here.'
        ]
    ],
    'tables' => [
        'empty' => 'There is currently no data available.',
        'sort_ascending' => 'Sort in ascending order',
        'sort_descending' => 'Sort in descending order',
        'select_item' => 'Select {name}',
        'edit_item' => 'Edit {name}',
        'delete_item' => 'Delete {name}',
        'grouped_actions' => 'Grouped actions',
        'option_del_user' => 'Delete user',
        'btn_apply_title' => 'Apply action to all selected users'
    ],
    'filters' => [
        'sortBy' => 'sortBy',
        'order' => 'order',
        'name' => 'username',
        'group'=>'group',
        'registration'=>'created_at',
        'asc' => 'ascending',
        'desc' => 'descending',
        'ascending' => 'asc',
        'descending' => 'desc'
    ],
    'go_home' => 'Go Home',
    'toggle_navigation' => 'Toggle navigation',
    'your_info' => 'Your Info',
];
