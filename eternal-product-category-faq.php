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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Plugin Class
 */
class Eternal_Product_Category_FAQ {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add FAQ fields to product category taxonomy.
		add_action( 'product_cat_add_form_fields', array( $this, 'add_faq_fields' ) );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_faq_fields' ), 10, 2 );

		// Save FAQ fields.
		add_action( 'created_product_cat', array( $this, 'save_faq_fields' ) );
		add_action( 'edited_product_cat', array( $this, 'save_faq_fields' ) );

		// Enqueue scripts for repeater functionality.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Output FAQs on frontend category archive.
		add_action( 'woocommerce_archive_description', array( $this, 'output_category_faqs' ) );
	}

	/**
	 * Add FAQ fields to the "Add New" category form.
	 */
	public function add_faq_fields() {
		wp_nonce_field( 'eternal_faq_save', 'eternal_faq_nonce' );
		?>
		<div class="form-field term-faq-questions-wrap">
			<label for="faq-questions"><?php esc_html_e( 'FAQ Questions', 'eternal-product-category-faq' ); ?></label>
			<div id="faq-repeater-container">
				<div class="faq-row" style="margin-bottom: 15px; border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
					<div style="margin-bottom: 10px;">
						<label style="font-weight: bold; color: #0073aa;"><?php esc_html_e( 'Question:', 'eternal-product-category-faq' ); ?></label>
						<textarea name="faq_questions[0][question]" class="faq-question" rows="2" style="width: 100%;" placeholder="<?php esc_attr_e( 'Enter your question here (e.g., What are the benefits of this product?)', 'eternal-product-category-faq' ); ?>"></textarea>
					</div>
					<div>
						<label style="font-weight: bold; color: #0073aa;"><?php esc_html_e( 'Answer:', 'eternal-product-category-faq' ); ?></label>
						<textarea name="faq_questions[0][answer]" class="faq-answer" rows="3" style="width: 100%;" placeholder="<?php esc_attr_e( 'Enter the answer to the question above', 'eternal-product-category-faq' ); ?>"></textarea>
					</div>
				</div>
			</div>
			<button type="button" class="button" id="add-faq-button" style="margin-top: 10px;">
				<?php esc_html_e( '+ Add FAQ', 'eternal-product-category-faq' ); ?>
			</button>
			<p class="description"><?php esc_html_e( 'Add frequently asked questions and answers for this category.', 'eternal-product-category-faq' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Add FAQ fields to the "Edit" category form.
	 *
	 * @param WP_Term $term     Term object.
	 * @param string  $taxonomy Taxonomy slug. Unused but required by hook.
	 */
	public function edit_faq_fields( $term, $taxonomy ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// Get existing FAQs.
		$faq_questions = get_term_meta( $term->term_id, 'faq_questions', true );
		if ( empty( $faq_questions ) ) {
			$faq_questions = array();
		}
		?>
		<tr class="form-field term-faq-questions-wrap">
			<th scope="row"><label for="faq-questions"><?php esc_html_e( 'FAQ Questions', 'eternal-product-category-faq' ); ?></label></th>
			<td>
				<?php wp_nonce_field( 'eternal_faq_save', 'eternal_faq_nonce' ); ?>
				<div id="faq-repeater-container">
					<?php if ( ! empty( $faq_questions ) ) : ?>
						<?php foreach ( $faq_questions as $index => $faq ) : ?>
							<div class="faq-row" style="margin-bottom: 15px; border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
								<div style="margin-bottom: 10px;">
									<label style="font-weight: bold; color: #0073aa;"><?php esc_html_e( 'Question:', 'eternal-product-category-faq' ); ?></label>
									<textarea name="faq_questions[<?php echo esc_attr( $index ); ?>][question]" class="faq-question" rows="2" style="width: 100%;" placeholder="<?php esc_attr_e( 'Enter your question here (e.g., What are the benefits of this product?)', 'eternal-product-category-faq' ); ?>"><?php echo esc_textarea( $faq['question'] ); ?></textarea>
								</div>
								<div>
									<label style="font-weight: bold; color: #0073aa;"><?php esc_html_e( 'Answer:', 'eternal-product-category-faq' ); ?></label>
									<textarea name="faq_questions[<?php echo esc_attr( $index ); ?>][answer]" class="faq-answer" rows="3" style="width: 100%;" placeholder="<?php esc_attr_e( 'Enter the answer to the question above', 'eternal-product-category-faq' ); ?>"><?php echo esc_textarea( $faq['answer'] ); ?></textarea>
								</div>
								<button type="button" class="button remove-faq-button" style="margin-top: 10px; color: #a00;">
									<?php esc_html_e( 'Remove FAQ', 'eternal-product-category-faq' ); ?>
								</button>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="faq-row" style="margin-bottom: 15px; border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
							<div style="margin-bottom: 10px;">
								<label style="font-weight: bold; color: #0073aa;"><?php esc_html_e( 'Question:', 'eternal-product-category-faq' ); ?></label>
								<textarea name="faq_questions[0][question]" class="faq-question" rows="2" style="width: 100%;" placeholder="<?php esc_attr_e( 'Enter your question here (e.g., What are the benefits of this product?)', 'eternal-product-category-faq' ); ?>"></textarea>
							</div>
							<div>
								<label style="font-weight: bold; color: #0073aa;"><?php esc_html_e( 'Answer:', 'eternal-product-category-faq' ); ?></label>
								<textarea name="faq_questions[0][answer]" class="faq-answer" rows="3" style="width: 100%;" placeholder="<?php esc_attr_e( 'Enter the answer to the question above', 'eternal-product-category-faq' ); ?>"></textarea>
							</div>
							<button type="button" class="button remove-faq-button" style="margin-top: 10px; color: #a00;">
								<?php esc_html_e( 'Remove FAQ', 'eternal-product-category-faq' ); ?>
							</button>
						</div>
					<?php endif; ?>
				</div>
				<button type="button" class="button" id="add-faq-button" style="margin-top: 10px;">
					<?php esc_html_e( '+ Add FAQ', 'eternal-product-category-faq' ); ?>
				</button>
				<p class="description"><?php esc_html_e( 'Add frequently asked questions and answers for this category.', 'eternal-product-category-faq' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save FAQ fields when category is saved.
	 *
	 * @param int $term_id Term ID.
	 */
	public function save_faq_fields( $term_id ) {
		// Verify nonce for security.
		if ( ! isset( $_POST['eternal_faq_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['eternal_faq_nonce'] ) ), 'eternal_faq_save' ) ) {
			return;
		}

		// Check if our nonce is set (we'll add this in the JS).
		if ( isset( $_POST['faq_questions'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized below.
			$faq_questions = array();

			// Sanitize and organize FAQ data.
			$faq_data = wp_unslash( $_POST['faq_questions'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Items sanitized individually below.
			if ( is_array( $faq_data ) ) {
				foreach ( $faq_data as $faq ) {
					if ( ! empty( $faq['question'] ) || ! empty( $faq['answer'] ) ) {
						$faq_questions[] = array(
							'question' => sanitize_textarea_field( $faq['question'] ),
							'answer'   => sanitize_textarea_field( $faq['answer'] ),
						);
					}
				}
			}

			// Save FAQ data.
			update_term_meta( $term_id, 'faq_questions', $faq_questions );
		} else {
			// If no FAQs submitted, delete existing data.
			delete_term_meta( $term_id, 'faq_questions' );
		}
	}

	/**
	 * Enqueue admin scripts for repeater functionality.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_scripts( $hook ) {
		// Only load on taxonomy edit screens.
		if ( 'edit-tags.php' !== $hook && 'term.php' !== $hook ) {
			return;
		}

		// Get current taxonomy.
		$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only reading taxonomy name for screen check.
		if ( 'product_cat' !== $taxonomy ) {
			return;
		}

		wp_enqueue_script(
			'eternal-faq-repeater',
			plugin_dir_url( __FILE__ ) . 'assets/js/faq-repeater.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);

		wp_localize_script(
			'eternal-faq-repeater',
			'eternalFaqData',
			array(
				'removeText'          => esc_html__( 'Remove FAQ', 'eternal-product-category-faq' ),
				'questionLabel'       => esc_html__( 'Question:', 'eternal-product-category-faq' ),
				'answerLabel'         => esc_html__( 'Answer:', 'eternal-product-category-faq' ),
				'questionPlaceholder' => esc_attr__( 'Enter your question here (e.g., What are the benefits of this product?)', 'eternal-product-category-faq' ),
				'answerPlaceholder'   => esc_attr__( 'Enter the answer to the question above', 'eternal-product-category-faq' ),
			)
		);
	}

	/**
	 * Output FAQs on frontend category archive.
	 */
	public function output_category_faqs() {
		// Only run on product category taxonomy pages.
		if ( ! is_product_category() ) {
			return;
		}

		$term = get_queried_object();

		if ( empty( $term ) || ! isset( $term->term_id ) ) {
			return;
		}

		// Get FAQs for this category.
		$faq_questions = get_term_meta( $term->term_id, 'faq_questions', true );

		// TODO: Remove console.log before production. This is for testing/debugging only.
		// Output FAQs to console for testing.
		?>
		<script>
			console.log('Eternal Product Category FAQ - Category ID: <?php echo esc_js( $term->term_id ); ?>');
			console.log('Eternal Product Category FAQ - Category Name: <?php echo esc_js( $term->name ); ?>');
			console.log('Eternal Product Category FAQ - FAQ Data:', <?php echo wp_json_encode( $faq_questions ); ?>);
		</script>
		<?php
		// END TODO: Remove console.log before production.

		// TODO: Add actual FAQ display logic here.
		// The FAQ data is available in $faq_questions array.
		// Structure: array( array( 'question' => '...', 'answer' => '...' ), ... ).
		// Example for future implementation.

		/* phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- Intentional example code.
		 * Example FAQ display code.
		 *
		 * if ( ! empty( $faq_questions ) ) {
		 *     echo '<div class="eternal-category-faqs">';
		 *     echo '<h3>' . esc_html__( 'Frequently Asked Questions', 'eternal-product-category-faq' ) . '</h3>';
		 *     echo '<div class="faq-list">';
		 *     foreach ( $faq_questions as $faq ) {
		 *         echo '<div class="faq-item">';
		 *         echo '<h4 class="faq-question">' . esc_html( $faq['question'] ) . '</h4>';
		 *         echo '<div class="faq-answer">' . wp_kses_post( $faq['answer'] ) . '</div>';
		 *         echo '</div>';
		 *     }
		 *     echo '</div>';
		 *     echo '</div>';
		 * }
		 */
		// END TODO: Add actual FAQ display logic here.
	}
}

// Initialize the plugin.
new Eternal_Product_Category_FAQ();
