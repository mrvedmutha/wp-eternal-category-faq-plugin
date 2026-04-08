<?php
/**
 * Plugin Name: Eternal Product Category FAQ Questions
 * Plugin URI: https://eternal.com/
 * Description: Add FAQ questions and answers to WooCommerce product categories with a simple repeater field interface.
 * Version: 1.0.0
 * Author: Eternal
 * Author URI: https://eternal.com/
 * Text Domain: eternal-product-category-faq
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * @package Eternal_Product_Category_FAQ
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Plugin Class
 */
class Eternal_Product_Category_FAQ {

    /**
     * Constructor
     */
    public function __construct() {
        // Add FAQ fields to product category taxonomy
        add_action('product_cat_add_form_fields', array($this, 'add_faq_fields'));
        add_action('product_cat_edit_form_fields', array($this, 'edit_faq_fields'), 10, 2);

        // Save FAQ fields
        add_action('created_product_cat', array($this, 'save_faq_fields'));
        add_action('edited_product_cat', array($this, 'save_faq_fields'));

        // Enqueue scripts for repeater functionality
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // Output FAQs on frontend category archive
        add_action('woocommerce_archive_description', array($this, 'output_category_faqs'));
    }

    /**
     * Add FAQ fields to the "Add New" category form
     */
    public function add_faq_fields() {
        ?>
        <div class="form-field term-faq-questions-wrap">
            <label for="faq-questions"><?php _e('FAQ Questions', 'eternal-product-category-faq'); ?></label>
            <div id="faq-repeater-container">
                <div class="faq-row" style="margin-bottom: 15px; border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                    <div style="margin-bottom: 10px;">
                        <label style="font-weight: bold;"><?php _e('Question:', 'eternal-product-category-faq'); ?></label>
                        <textarea name="faq_questions[0][question]" class="faq-question" rows="2" style="width: 100%;"></textarea>
                    </div>
                    <div>
                        <label style="font-weight: bold;"><?php _e('Answer:', 'eternal-product-category-faq'); ?></label>
                        <textarea name="faq_questions[0][answer]" class="faq-answer" rows="3" style="width: 100%;"></textarea>
                    </div>
                </div>
            </div>
            <button type="button" class="button" id="add-faq-button" style="margin-top: 10px;">
                <?php _e('+ Add FAQ', 'eternal-product-category-faq'); ?>
            </button>
            <p class="description"><?php _e('Add frequently asked questions and answers for this category.', 'eternal-product-category-faq'); ?></p>
        </div>
        <?php
    }

    /**
     * Add FAQ fields to the "Edit" category form
     */
    public function edit_faq_fields($term, $taxonomy) {
        // Get existing FAQs
        $faq_questions = get_term_meta($term->term_id, 'faq_questions', true);
        if (empty($faq_questions)) {
            $faq_questions = array();
        }
        ?>
        <tr class="form-field term-faq-questions-wrap">
            <th scope="row"><label for="faq-questions"><?php _e('FAQ Questions', 'eternal-product-category-faq'); ?></label></th>
            <td>
                <div id="faq-repeater-container">
                    <?php if (!empty($faq_questions)) : ?>
                        <?php foreach ($faq_questions as $index => $faq) : ?>
                            <div class="faq-row" style="margin-bottom: 15px; border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                                <div style="margin-bottom: 10px;">
                                    <label style="font-weight: bold;"><?php _e('Question:', 'eternal-product-category-faq'); ?></label>
                                    <textarea name="faq_questions[<?php echo esc_attr($index); ?>][question]" class="faq-question" rows="2" style="width: 100%;"><?php echo esc_textarea($faq['question']); ?></textarea>
                                </div>
                                <div>
                                    <label style="font-weight: bold;"><?php _e('Answer:', 'eternal-product-category-faq'); ?></label>
                                    <textarea name="faq_questions[<?php echo esc_attr($index); ?>][answer]" class="faq-answer" rows="3" style="width: 100%;"><?php echo esc_textarea($faq['answer']); ?></textarea>
                                </div>
                                <button type="button" class="button remove-faq-button" style="margin-top: 10px; color: #a00;">
                                    <?php _e('Remove FAQ', 'eternal-product-category-faq'); ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="faq-row" style="margin-bottom: 15px; border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                            <div style="margin-bottom: 10px;">
                                <label style="font-weight: bold;"><?php _e('Question:', 'eternal-product-category-faq'); ?></label>
                                <textarea name="faq_questions[0][question]" class="faq-question" rows="2" style="width: 100%;"></textarea>
                            </div>
                            <div>
                                <label style="font-weight: bold;"><?php _e('Answer:', 'eternal-product-category-faq'); ?></label>
                                <textarea name="faq_questions[0][answer]" class="faq-answer" rows="3" style="width: 100%;"></textarea>
                            </div>
                            <button type="button" class="button remove-faq-button" style="margin-top: 10px; color: #a00;">
                                <?php _e('Remove FAQ', 'eternal-product-category-faq'); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" class="button" id="add-faq-button" style="margin-top: 10px;">
                    <?php _e('+ Add FAQ', 'eternal-product-category-faq'); ?>
                </button>
                <p class="description"><?php _e('Add frequently asked questions and answers for this category.', 'eternal-product-category-faq'); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save FAQ fields when category is saved
     */
    public function save_faq_fields($term_id) {
        // Check if our nonce is set (we'll add this in the JS)
        if (isset($_POST['faq_questions'])) {
            $faq_questions = array();

            // Sanitize and organize FAQ data
            if (is_array($_POST['faq_questions'])) {
                foreach ($_POST['faq_questions'] as $faq) {
                    if (!empty($faq['question']) || !empty($faq['answer'])) {
                        $faq_questions[] = array(
                            'question' => sanitize_textarea_field($faq['question']),
                            'answer'   => sanitize_textarea_field($faq['answer']),
                        );
                    }
                }
            }

            // Save FAQ data
            update_term_meta($term_id, 'faq_questions', $faq_questions);
        } else {
            // If no FAQs submitted, delete existing data
            delete_term_meta($term_id, 'faq_questions');
        }
    }

    /**
     * Enqueue admin scripts for repeater functionality
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on taxonomy edit screens
        if ('edit-tags.php' !== $hook && 'term.php' !== $hook) {
            return;
        }

        // Get current taxonomy
        $taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : '';
        if ($taxonomy !== 'product_cat') {
            return;
        }

        wp_enqueue_script(
            'eternal-faq-repeater',
            plugin_dir_url(__FILE__) . 'assets/js/faq-repeater.js',
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('eternal-faq-repeater', 'eternalFaqData', array(
            'removeText' => __('Remove FAQ', 'eternal-product-category-faq'),
            'questionLabel' => __('Question:', 'eternal-product-category-faq'),
            'answerLabel' => __('Answer:', 'eternal-product-category-faq'),
        ));
    }

    /**
     * Output FAQs on frontend category archive
     */
    public function output_category_faqs() {
        // Only run on product category taxonomy pages
        if (!is_product_category()) {
            return;
        }

        $term = get_queried_object();

        if (empty($term) || !isset($term->term_id)) {
            return;
        }

        // Get FAQs for this category
        $faq_questions = get_term_meta($term->term_id, 'faq_questions', true);

        // TODO: Remove console.log before production - This is for testing/debugging only
        // Output FAQs to console for testing
        ?>
        <script>
            console.log('Eternal Product Category FAQ - Category ID: <?php echo esc_js($term->term_id); ?>');
            console.log('Eternal Product Category FAQ - Category Name: <?php echo esc_js($term->name); ?>');
            console.log('Eternal Product Category FAQ - FAQ Data:', <?php echo json_encode($faq_questions); ?>);
        </script>
        <?php
        // END TODO: Remove console.log before production

        // TODO: Add actual FAQ display logic here
        // The FAQ data is available in $faq_questions array
        // Structure: array( array( 'question' => '...', 'answer' => '...' ), ... )
        // Example for future implementation:
        /*
        if (!empty($faq_questions)) {
            echo '<div class="eternal-category-faqs">';
            echo '<h3>' . esc_html__('Frequently Asked Questions', 'eternal-product-category-faq') . '</h3>';
            echo '<div class="faq-list">';
            foreach ($faq_questions as $faq) {
                echo '<div class="faq-item">';
                echo '<h4 class="faq-question">' . esc_html($faq['question']) . '</h4>';
                echo '<div class="faq-answer">' . wp_kses_post($faq['answer']) . '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
        }
        */
        // END TODO: Add actual FAQ display logic here
    }
}

// Initialize the plugin
new Eternal_Product_Category_FAQ();
