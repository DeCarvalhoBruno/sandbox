<?php
return [
    'title' => [
        'login' => 'Log In',
        'blog_index' => 'Listing blog posts',
        'blog_add' => 'Managing a blog entry',
        'blog_category' => 'Managing blog categories',
        'group_add' => 'Adding a group',
        'group_edit' => 'Editing a group',
        'group_index' => 'Listing groups',
        'members' => 'Managing group members',
        'media_edit' => 'Editing a media',
        'settings' => 'Settings - General',
        'settings_password' => 'Settings - Change my password',
        'settings_profile' => 'Settings - Modify my profile',
        'user_edit' => 'Editing a user',
        'user_index' => 'Listing users',
        'dashboard' => 'Dashboard',
        'system_log' => 'System Log',
        'settings_social' => 'Social media settings'
    ],
    'breadcrumb' => [
        'admin-dashboard' => 'Home',
        'admin-users-index' => 'Users',
        'admin-groups-index' => 'Groups',
        'admin-users-edit' => 'Edit',
        'admin-groups-edit' => 'Edit',
        'admin-groups-add' => 'New Group',
        'admin-groups-members' => 'Edit Members',
        'admin-blog_posts-index' => 'Blog Posts',
        'admin-blog_posts-add' => 'Create',
        'admin-blog_posts-edit' => 'Edit',
        'admin-blog_posts-category' => 'Categories',
        'admin-media-edit' => 'Edit Image',
        'admin-user-password' => 'Your password',
        'admin-user-profile' => 'Your profile',
        'admin-user-general' => 'Your general settings',
        'admin-system-log' => 'System Log',
        'admin-settings-settings' => 'Website settings',
        'admin-settings-general' => 'General settings',
        'admin-settings-social' => 'Social media settings'
    ],
    'sidebar' => [
        'main_nav' => 'MAIN NAVIGATION',
        'dashboard' => 'Dashboard',
        'users' => 'Users',
        'groups' => 'Groups',
        'blog' => 'Blog',
        'list' => 'List',
        'add' => 'Add',
        'category' => 'Categories',
        'media' => 'Media'
    ],
    'general' => [
        'ok' => 'Ok',
        'cancel' => 'Cancel',
        'name' => 'Name',
        'home' => 'Home',
        'settings' => 'Settings',
        'profile' => 'Profile',
        'general' => 'General',
        'save' => 'Save',
        'save_changes' => 'Save Changes',
        'update' => 'Update',
        'create' => 'Create',
        'logout' => 'Logout',
        'login' => 'Log In',
        'password' => 'Password',
        'actions' => 'Actions',
        'email' => 'E-mail',
        'back' => 'Back',
        'next' => 'Next',
        'prev' => 'Previous',
        'first' => 'First',
        'last' => 'Last',
        'permission' => 'Permission|Permissions',
        'toggle' => 'Toggle On/Off',
        'select_all' => 'Select all',
        'apply' => 'Apply',
        'search' => 'Search',
        'reset_filters' => 'Reset Filters',
        'delete' => 'Delete',
        'reload' => 'Reload',
        'status' => 'Status',
        'expand_all' => 'Expand all',
        'collapse_all' => 'Collapse all',
        'uploaded_on' => 'Uploaded on',
        'media' => 'Media',
        'crop' => 'Crop',
        'close' => 'Close',
        'view_all' => 'View All',
        'choose_file' => 'Choose file',
    ],
    'db' => [
        'users' => 'User|Users',
        'groups' => 'Group|Groups',
        'blog_posts' => 'Blog Post|Blog Posts',
        'username' => 'Username',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'full_name' => 'Full name',
        'new_email' => 'New e-mail',
        'new_username' => 'New username',
        'user_created_at' => 'Registration date',
        'group_name' => 'Group name',
        'new_group_name' => 'New group name',
        'group_mask' => 'Group mask',
        'member_count' => 'Number of members',
        'blog_post_title' => 'Post title',
        'media_title' => 'Title',
        'media_alt' => 'Alt text',
        'media_description' => 'Description',
        'media_caption' => 'Caption',
        'medias' => 'Media'
    ],
    'db_raw' => [
        'full_name' => 'full_name',
        'username' => 'username',
        'email' => 'email',
        'group_name' => 'group_name',
        'created_at' => 'created_at',
        'blog_post_title' => 'blog_post_title'
    ],
    'db_raw_inv' => [
        'full_name' => 'full_name',
        'username' => 'username',
        'email' => 'email',
        'group_name' => 'group_name',
        'created_at' => 'created_at',
        'blog_post_title' => 'blog_post_title'
    ],
    'filters' => [
        'sortBy' => 'sortBy',
        'order' => 'order',
        'users_name' => 'name',
        'users_group' => 'group',
        'users_created' => 'created',
        'blog_posts_title' => 'title',
        'asc' => 'asc',
        'desc' => 'desc',
        'day' => 'day',
        'week' => 'week',
        'month' => 'month',
        'year' => 'year'
    ],
    'filter_labels' => [
        'users_group' => 'Group:',
        'users_name' => 'Full name:',
        'blog_posts_title' => 'Post title:',
        'users_created' => 'Registration period:',
        'created_today' => 'Registered today',
        'created_week' => 'Less than a week ago',
        'created_month' => 'Less than a month ago',
        'created_year' => 'Less than a year ago',
    ],
    'filters_inv' => [
        'registration' => 'createdAt',
        'blog_post_title' => 'title',
        'group' => 'group',
        'name' => 'fullName',
        'sortBy' => 'sortBy',
        'title' => 'title',
        'order' => 'order',
        'fullName' => 'name',
        'createdAt' => 'created',
    ],
    'constants' => [
        'BLOG_STATUS_DRAFT' => 'Draft',
        'BLOG_STATUS_REVIEW' => 'Under review',
        'BLOG_STATUS_PUBLISHED' => 'Published'
    ],
    'form' => [
        'description' => [
            'username' => 'The user\'s shorthand name.',
            'first_name' => 'The user\'s first (given) name.',
            'last_name' => 'The user\'s last (family) name.',
            'new_email' => '"{0}" is the current e-mail address.',
            'new_username' => '"{0}" is the current username.',
            'group_name' => 'The group name can only contain alphanumeric characters and underscores.',
            'new_group_name' => '"{0}" is the current group name. The group name can only contain alphanumeric characters and underscores.',
            'group_mask' => 'Determines the group\'s position in its hierarchy. The lower the mask, the higher the group status.',
            'media_title' => 'Title of the media',
            'media_alt' => 'A text alternative to the image for screen readers or when the image does not load.',
            'media_description' => 'For internal purposes, to help with tracking in searches.',
            'media_caption' => 'May be displayed below the image for commentary/description purposes.'
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
        ],
        'user_delete' => [
            'h' => 'Confirm user deletion',
            't' => 'Do you really want to delete user {name}?|Do you really want to delete those {number} users?'
        ],
        'group_delete' => [
            'h' => 'Confirm group deletion',
            't' => 'Do you really want to delete group {name}?'
        ],
        'blog_post_delete' => [
            'h' => 'Confirm blog deletion',
            't' => 'Do you really want to delete blog post {name}?|Do you really want to delete those {number} blog posts?'
        ],
        'user_profile_updated' => [
            'h' => 'About to log you out...',
            't' => 'We use your e-mail address to identify you, so we are going to log you out for safety reasons. Please log back in using your new e-mail address.',
            'b' => 'Got it!'
        ]
    ],
    'error' => [
        'page_not_found' => 'Page Not Found',
        'passwords_dont_match' => 'The passwords must be identical.',
        'form' => 'Errors were found on this form.',
        'add_category' => 'Creation of category "{cat}" failed. Please try again.'
    ],
    'message' => [
        'profile_updated' => 'Your profile has been updated.',
        'settings_updated' => 'Your general settings were updated.',
        'password_updated' => 'Your password has been updated.',
        'user_update_ok' => 'The user was updated.',
        'user_delete_ok' => 'User {name} was deleted.|The users were deleted.',
        'group_update_ok' => 'The group was updated.',
        'group_delete_ok' => 'Group {group} was deleted.',
        'blog_post_delete_ok' => 'Blog post {name} was deleted.',
        'group_create_ok' => 'The group was created.',
        'media_update_ok' => 'The media was updated.'
    ],
    'pages' => [
        'auth' => [
            'remember_me' => 'Remember Me',
            'forgot_password' => 'Forgot Your Password?',
            'send_password_reset_link' => 'Send Password Reset Link',
            'confirm_password' => 'Confirm Password',
            'reset_password' => 'Reset Password',
            'new_password' => 'New Password',
            'current_password' => 'Current Password',
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
            'filter_created_at' => 'Filter by registration date',
        ],
        'groups' => [
            'info1' => 'Permissions for all members of the group are defined here.',
            'info2' => 'Individual permissions can also be set at the user level,
            in which case user permissions will override permissions set here.'
        ],
        'user' => [
            'language' => 'Language',
            'notifications' => 'Choose the events for which you would like to receive live notifications:',
            'settings' => 'Your settings',

        ],
        'settings' => [
            'title' => 'Website settings',
            'site_description' => 'Website description',
            'site_title' => 'Website title',
            'site_keywords' => 'Website keywords',
            'allow_robots' => 'Allow search engine bots to crawl pages',
            'enable_open_graph' => 'Enable open graph support',
            'enable_twitter_cards' => 'Enable twitter cards support',
            'twitter_publisher' => 'Twitter publisher username',
            'facebook_app_id' => 'Facebook App ID',
            'facebook_publisher' => 'Facebook publisher url',
            'website_title' => 'Website title',
            'jsonld_on' => 'Enable structured data',
            'entity_type' => 'Represented entity',
            'website_type' => 'Website type',
            'entity_person' => 'Person',
            'entity_organization' => 'Organization',
            'entity_org_type' => 'Type',
            'person_name' => 'Full name',
            'person_url' => 'URL',
            'entity_logo' => 'Logo',
            'social' => 'Social',
            'social_url' => 'Social media page url',
            'entity_social_help' => 'Links to user/organization profiles can be added below. Google recognizes the following platforms: Facebook, Twitter, Google+, Instagram, YouTube, LinkedIn, Myspace, Pinterest, SoundCloud and Tumblr.'
        ],
        'blog' => [
            'categories' => 'Categories',
            'media' => 'Media',
            'tab_available' => 'Available media',
            'tab_upload' => 'Upload',
            'author' => 'Author',
            'filter_title' => 'Filter by title',
            'filter_name' => 'Filter by name',
            'delete_image' => 'Delete avatar',
            'click_featured' => 'Click on an image to make it the featured image for this post.',
            'image_uploaded' => 'Upload is complete.',
            'add_post' => 'Add post',
            'add_root_button' => 'Add root category',
            'add_tag_pholder' => 'Type enter to add tag, click to remove',
            'blog_post_excerpt' => 'Excerpt',
            'excerpt_label' => 'This user-defined summary of the post can be displayed on the front page.',
            'add_success' => 'The blog post was created.',
            'save_success' => 'The blog post was updated.',
            'edit_image' => 'Edit image',
            'published_at' => 'Publishing date:',
            'add_source_button' => 'Add source',
        ],
        'blog_categories' => [
            'add_child_node' => 'Add a child element to "{name}"',
            'edit_node' => 'Edit node "{name}"',
            'delete_node' => 'Delete node "{name}"',
        ],
        'system_log' => [
            'notifications' => 'No new notifications|1 new notification|{number} new notifications'
        ]
    ],
    'tables' => [
        'empty' => 'No data available.',
        'sort_asc' => 'Sort in ascending order',
        'sort_desc' => 'Sort in descending order',
        'select_item' => 'Select {name} for batch processing',
        'edit_item' => 'Edit {name}',
        'delete_item' => 'Delete {name}',
        'grouped_actions' => 'Grouped actions',
        'option_del_user' => 'Delete user',
        'option_del_blog' => 'Delete blog post',
        'btn_apply_title' => 'Apply action to all selected users'
    ],
    'locales' => [
        'en' => 'English',
        'fr' => 'French'
    ],
    'go_home' => 'Go Home',
    'toggle_navigation' => 'Toggle navigation',
];
