<?php

if (!function_exists('add_action')) {
    die();
}

function Hci_init($HeyCheckIt)
{
    global $HeyCheckIt;
    $options = get_option('hci_options', $HeyCheckIt->default_options());
    add_action('wp_footer', 'hci_footer');
}

function hci_footer()
{
    extract(hci_options());

    if (empty($tracking_id)) {
        return;
    }

    // Insert Header code
    if (is_user_logged_in()) {
        if (!empty($tracking_id)) {
            $user = wp_get_current_user();
            $roles = (array) $user->roles;

            if (!in_array('administrator', $roles)) {
                return;
            }
            ?>
                <script src="https://app.heycheckit.com/app/<?php echo $tracking_id; ?>" async defer></script>
            <?php
        }
    }
    ?>
    <?php
}

function hci_options()
{
    global $HeyCheckIt;
    $options = get_option('hci_options', $HeyCheckIt->default_options());
    $tracking_id = (isset($options['hci_id']) && !empty($options['hci_id'])) ? $options['hci_id'] : '';
    return [
        'options'           => $options,
        'tracking_id'       => $tracking_id,
    ];
}
