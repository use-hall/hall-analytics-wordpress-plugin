<?php
/**
 * WordPress settings functionality
 *
 * @package Hall AI Analytics for WordPress
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Registration

function hall_analytics_register_settings() {
    // Register access token setting with validation callback
    register_setting(
        HALL_ANALYTICS_SETTINGS_GROUP, 
        HALL_ANALYTICS_ACCESS_TOKEN, 
        array(
            'type' => 'string',
            'sanitize_callback' => 'hall_analytics_sanitize_access_token',
            'default' => '',
            'capability' => 'manage_options'
        )
    );
    
    // Register analytics enabled setting with validation callback
    register_setting(
        HALL_ANALYTICS_SETTINGS_GROUP, 
        HALL_ANALYTICS_ENABLED, 
        array(
            'type' => 'string',
            'sanitize_callback' => 'hall_analytics_sanitize_checkbox',
            'default' => '1',
            'capability' => 'manage_options'
        )
    );
}

/**
 * Sanitize access token input
 *
 * @param string $input The input to sanitize
 * @return string Sanitized access token
 */
function hall_analytics_sanitize_access_token($input) {
    // Remove any whitespace
    $input = trim($input);
    
    // Only allow alphanumeric characters, hyphens, and underscores
    $input = preg_replace('/[^a-zA-Z0-9_-]/', '', $input);
    
    // Limit length to 255 characters
    if (strlen($input) > 255) {
        $input = substr($input, 0, 255);
    }
    
    return $input;
}

/**
 * Sanitize checkbox input
 *
 * @param string $input The input to sanitize
 * @return string Sanitized checkbox value
 */
function hall_analytics_sanitize_checkbox($input) {
    return ($input === '1') ? '1' : '0';
}

add_action('admin_init', 'hall_analytics_register_settings');

// Menu Item

function hall_analytics_menu() {
    add_options_page(
        'AI Analytics',    // Page title
        'AI Analytics',    // Menu title  
        'manage_options',    // Capability
        'ai-analytics',    // Menu slug
        'hall_analytics_page' // Callback function
    );
}

add_action('admin_menu', 'hall_analytics_menu');

// Settings Page

function hall_analytics_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'hall-analytics'));
    }
    
    // Handle form submission
    if (isset($_POST['submit'])) {
        // Verify nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'hall_analytics_settings_nonce')) {
            wp_die(esc_html__('Security check failed. Please try again.', 'hall-analytics'));
        }
        
        // Check user capabilities again
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to save settings.', 'hall-analytics'));
        }
        
        // Sanitize and save access token
        if (isset($_POST[HALL_ANALYTICS_ACCESS_TOKEN])) {
            $access_token = hall_analytics_sanitize_access_token($_POST[HALL_ANALYTICS_ACCESS_TOKEN]);
            // Only update if a new token is provided (not empty)
            if (!empty($access_token)) {
                update_option(HALL_ANALYTICS_ACCESS_TOKEN, $access_token);
            }
        }
        
        // Sanitize and save analytics enabled setting
        $analytics_enabled = isset($_POST[HALL_ANALYTICS_ENABLED]) ? '1' : '0';
        update_option(HALL_ANALYTICS_ENABLED, $analytics_enabled);
        
        // Show success message
        add_settings_error(
            'hall_analytics_messages',
            'hall_analytics_message',
            esc_html__('Settings saved successfully.', 'hall-analytics'),
            'updated'
        );
    }
    
    // Show any settings errors
    settings_errors('hall_analytics_messages');
    
    ?>
    <style>
        .settings_page_ai-analytics #wpcontent,
        .settings_page_ai-analytics.auto-fold #wpcontent {
            padding-left: 0;
        }
        .header {
            background-color: #fff;
            padding: 40px 40px 0 60px;
            border-bottom: 1px solid #dcdcde;
            display: flex;
            flex-direction: row;
            height: 400px;
            overflow: hidden;
            flex-shrink: 1;
        }
        .header .text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex-shrink: 1;
            padding-right: 50px;
            gap: 8px;
        }
        .header .logo {
            width: 84px;
            height: 28px;
            display: block;
        }
        .header .headline {
            margin-top: 20px;
            font-size: 32px;
            line-height: 1.1em;
            font-weight: 500;
            color: #1d2327;
        }
        .header .lead {
            font-size: 16px;
            color: #666;
        }
        .header .screenshot {
            max-width: 600px;
            width: auto;
            height: auto;
            margin-bottom: -20px;
            flex-shrink: 0;
            object-fit: contain;
        }
        .header .button-primary, .header .button {
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
            width: fit-content;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        @media (max-width: 1200px) {
            .header {
                padding: 40px;
                height: auto;
            }
            .header .screenshot {
                max-width: 400px;
                width: auto;
                height: auto;
                margin-bottom: -80px;
            }
            .header .headline {
                font-size: 24px;
            }
            .header .lead {
                font-size: 14px;
            }
        } 
        @media (max-width: 768px) {

            .header .screenshot {
                display: none;
            }
        }
        .page-heading {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-top: 40px;
        }
        .page-heading h1 {
            margin: 0;
            padding: 0;
        }
        .page-heading .button {
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }
        .step-section {
            padding: 5px 0;
        }
        .password-input-container {
            width: 100%;
            display: flex;
            align-items: center;
        }
        .password-input-container input {
            flex: 1;
            margin-right: 8px;
        }
        .password-input-container .button {
            margin-left: 8px;
            flex-shrink: 1;
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            margin: 0;
            gap: 8px;
        }
        .checkbox-input-container {
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
            padding-top: 5px;
        }
        .checkbox-input-container input {
            margin: 0;
        }
    </style>
    <div class="header">
        <div class="text">
            <a href="https://usehall.com/?utm_source=wordpress_plugin" target="_blank" rel="noopener noreferrer" style="width: fit-content;">
                <img src="<?php echo plugin_dir_url(__FILE__) . 'hall-logo.svg'; ?>" alt="<?php echo esc_attr__('Hall logo', 'hall-analytics'); ?>" class="logo" width="84" height="28" />
            </a>
            <div class="headline"><?php echo esc_html__('Track agent activity and referrals from AI', 'hall-analytics'); ?></div>
            <p class="lead"><?php echo esc_html__('Measure and understand how AI agents and assistants are accessing your WordPress site. Track referrals and clicks from conversational AI platforms like ChatGPT.', 'hall-analytics'); ?></p>
            <div style="display: flex; flex-direction: row; align-items: center; gap: 16px;">
                <a href="https://usehall.com/ai-agent-analytics?utm_source=wordpress_plugin" target="_blank" rel="noopener noreferrer" class="button button-primary">
                   <?php echo esc_html__('See how it works', 'hall-analytics'); ?>
                </a>
                <a href="https://app.usehall.com/" target="_blank" rel="noopener noreferrer" class="button">
                    <span><?php echo esc_html__('Log in to analytics dashboard', 'hall-analytics'); ?></span>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>
        </div>
        <img src="<?php echo plugin_dir_url(__FILE__) . 'plugin-ai-analytics.png'; ?>" alt="<?php echo esc_attr__('AI analytics screenshot', 'hall-analytics'); ?>" class="screenshot" />
    </div>
    <div class="container wrap">
        <div class="page-heading">
            <h1><?php echo esc_html__('AI Analytics Settings', 'hall-analytics'); ?></h1>
            <a href="https://docs.usehall.com/?utm_source=wordpress_plugin" target="_blank" rel="noopener noreferrer" class="button">
                <span><?php echo esc_html__('View the documentation', 'hall-analytics'); ?></span>
                <span class="dashicons dashicons-external"></span>
            </a>
        </div>
        <form method="post" action="" class="hall-analytics-form">
            <?php wp_nonce_field('hall_analytics_settings_nonce'); ?>
            <div class="step-section">
                <h2><?php echo esc_html__('Step 1: Create an account and set up your domain', 'hall-analytics'); ?></h2>
                <p><a href="https://usehall.com/ai-agent-analytics?utm_source=wordpress_plugin" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('Sign up for a free account', 'hall-analytics'); ?></a> <?php echo esc_html__('to get started.', 'hall-analytics'); ?>
                <?php echo esc_html__('Then, navigate to your domain or click', 'hall-analytics'); ?> <strong><?php echo esc_html__('New domain', 'hall-analytics'); ?></strong> <?php echo esc_html__('in the navigation sidebar and add your domain.', 'hall-analytics'); ?></p> 
            </div>
            <div class="step-section">
                <h2><?php echo esc_html__('Step 2: Create your API key', 'hall-analytics'); ?></h2>
                <p><?php echo esc_html__('Then click', 'hall-analytics'); ?> <strong><?php echo esc_html__('Domain settings', 'hall-analytics'); ?></strong> <?php echo esc_html__('and follow the set up instructions to create an API key for your domain.', 'hall-analytics'); ?></p>
                <p><?php echo esc_html__('Copy and paste your API key for your domain below.', 'hall-analytics'); ?></p>
                <div class="password-input-container">
                    <?php 
                    $existing_token = get_option(HALL_ANALYTICS_ACCESS_TOKEN, '');
                    $has_existing_token = !empty($existing_token);
                    ?>
                    <input type="password"
                        placeholder="<?php echo $has_existing_token ? esc_attr__('API key is set (leave blank to keep current)', 'hall-analytics') : esc_attr__('Paste your API key here', 'hall-analytics'); ?>"
                        id="<?php echo esc_attr(HALL_ANALYTICS_ACCESS_TOKEN); ?>" 
                        name="<?php echo esc_attr(HALL_ANALYTICS_ACCESS_TOKEN); ?>" 
                        value=""
                        maxlength="255"
                        pattern="[a-zA-Z0-9_-]*"
                        title="<?php echo esc_attr__('Only letters, numbers, hyphens, and underscores are allowed', 'hall-analytics'); ?>"
                    />
                    <button type="button" class="button" onclick="togglePassword()">
                        <span class="dashicons dashicons-visibility"></span>
                        <span id="toggle-text">Show</span>
                    </button>
                </div>
            </div>
            <div class="step-section">
                <h2><?php echo esc_html__('Step 3: Enable analytics', 'hall-analytics'); ?></h2>
                <p><?php echo esc_html__('Enable tracking for your WordPress site. Data from this plugin will start to appear in your', 'hall-analytics'); ?> <a href="https://app.usehall.com/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('domain dashboard', 'hall-analytics'); ?></a> <?php echo esc_html__('within your Hall account after enabling.', 'hall-analytics'); ?></p>
                    
                    
                    
                <div class="checkbox-input-container">
                    <input
                        type="checkbox"
                        id="<?php echo esc_attr(HALL_ANALYTICS_ENABLED); ?>"
                        name="<?php echo esc_attr(HALL_ANALYTICS_ENABLED); ?>"
                        <?php checked(get_option(HALL_ANALYTICS_ENABLED, '1') == '1'); ?>
                        value="1"
                    />
                    <label for="<?php echo esc_attr(HALL_ANALYTICS_ENABLED); ?>"><?php echo esc_html__('Enable Analytics', 'hall-analytics'); ?></label><br>
                </div>
            </div>
            <?php submit_button(esc_html__('Save Changes', 'hall-analytics')); ?>
        </form>
    </div>
    <script>
        function togglePassword() {
            const input = document.getElementById('<?php echo esc_attr(HALL_ANALYTICS_ACCESS_TOKEN); ?>');
            const toggleText = document.getElementById('toggle-text');
            const toggleIcon = document.getElementById('toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                toggleText.textContent = 'Hide';
                toggleIcon.className = 'dashicons dashicons-hidden';
            } else {
                input.type = 'password';
                toggleText.textContent = 'Show';
                toggleIcon.className = 'dashicons dashicons-visibility';
            }
        }
    </script>   
    <?php
}
