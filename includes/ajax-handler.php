<?php
// Handle Form Submission via AJAX
add_action('wp_ajax_sproduct_submit_form', 'sproduct_submit_form');
add_action('wp_ajax_nopriv_sproduct_submit_form', 'sproduct_submit_form');

function sproduct_submit_form() {
    global $wpdb;
    $form_data = $_POST['form_data'];
    $product_id = $_POST['product_id'];
    $user_id = get_current_user_id();
    $end_date = date('Y-m-d', strtotime('+1 month'));

    $wpdb->insert(
        "{$wpdb->prefix}s_subscriptions",
        [
            'sproduct_id' => $product_id,
            'user_id' => $user_id,
            'end_date' => $end_date,
            'plan' => $_POST['plan']
        ]
    );

    wp_send_json_success(['message' => 'Subscription Created']);
}


function sproduct_handle_form_submission() {
    check_ajax_referer('sproduct_form_nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $form_data = wp_unslash($_POST['form_data']);
    parse_str($form_data, $parsed_data);

    $selected_plan = sanitize_text_field($parsed_data['selected_plan'] ?? '');
    if (!$selected_plan) {
        wp_send_json_error(['message' => 'Please select a subscription plan.']);
    }
    global $wpdb;
    $wpdb->insert(
        "{$wpdb->prefix}s_subscriptions",
        [
            'sproduct_id' => $post_id,
            'user_id' => get_current_user_id(),
            'end_date' => date('Y-m-d', strtotime('+1 month')),
            'plan' => $selected_plan
        ]
    );

    wp_send_json_success();
}

add_action('wp_ajax_sproduct_submit_form', 'sproduct_handle_form_submission');
add_action('wp_ajax_nopriv_sproduct_submit_form', 'sproduct_handle_form_submission');