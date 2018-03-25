<?php
/**
 * Contains all the legacy includes (and hooks) for the plugin.  Eventually this will make its way into the new refactor
 * of the plugin. But while in transition this allows these legacy files to be included as necessary.
 */
require_once OS_MULTI_PATH . 'os-multi-setup.php';
//let's remove orgSeries core hooks/filter we're replacing
add_action('init', function(){
    remove_action('quick_edit_custom_box', 'inline_edit_series', 9);
    remove_action('manage_posts_custom_column', 'orgSeries_custom_column_action', 12);
    remove_action('admin_print_scripts-edit.php', 'inline_edit_series_js');
    remove_action('wp_ajax_add_series', 'admin_ajax_series');
    remove_action('admin_print_scripts-post.php', 'orgSeries_post_script');
    remove_action('admin_print_scripts-post-new.php', 'orgSeries_post_script');
    remove_action('delete_series', 'wp_delete_series', 10);
    remove_action('admin_init', 'orgseries_load_custom_column_actions', 10);
});
new osMulti;