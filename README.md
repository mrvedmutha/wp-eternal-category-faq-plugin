# Eternal Product Category FAQ Questions

A WordPress plugin that adds FAQ (Frequently Asked Questions) functionality to WooCommerce product categories.

## Features

- Add unlimited FAQ questions and answers to WooCommerce product categories
- Simple repeater field interface in the WordPress admin
- Text area fields for both questions and answers (supports longer content)
- Frontend output with console logging for testing
- Easy integration with custom themes

## Installation

1. Upload the `eternal-product-category-faq` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Products > Categories** to add FAQs to any product category

## Usage

### Adding FAQs in Admin

1. Navigate to **Products > Categories**
2. Edit an existing category or add a new one
3. Find the **FAQ Questions** section
4. Enter your question and answer in the text areas
5. Click **+ Add FAQ** to add more questions
6. Click **Remove FAQ** to delete a question
7. Save the category

### Frontend Output

The plugin outputs FAQ data to the browser console on product category archive pages (`/product-category/[category-name]`).

**To view the FAQ data:**
1. Visit a product category page on your site
2. Open your browser's developer tools (F12)
3. Go to the Console tab
4. You'll see:
   - Category ID
   - Category Name
   - FAQ Data array with all questions and answers

### TODO: Production Implementation

Before going to production, you should:

1. **Remove console.log statements** - Search for `TODO: Remove console.log before production` in the main plugin file
2. **Implement actual frontend display** - Uncomment and customize the FAQ display HTML in the `output_category_faqs()` method
3. **Add CSS styling** - Create styles for `.eternal-category-faqs`, `.faq-list`, `.faq-item`, etc.

## Example FAQ Data Structure

```javascript
[
  {
    "question": "What sizes do you offer?",
    "answer": "We offer sizes from XS to 3XL."
  },
  {
    "question": "How do I care for this product?",
    "answer": "Machine wash cold, tumble dry low."
  }
]
```

## Theme Integration

To display FAQs in your theme, you can use:

```php
$term_id = get_queried_object_id();
$faq_questions = get_term_meta($term_id, 'faq_questions', true);

if (!empty($faq_questions)) {
    foreach ($faq_questions as $faq) {
        echo '<div class="faq-item">';
        echo '<h4>' . esc_html($faq['question']) . '</h4>';
        echo '<p>' . esc_html($faq['answer']) . '</p>';
        echo '</div>';
    }
}
```

## Changelog

### 1.0.0
- Initial release
- Add FAQ fields to WooCommerce product categories
- Repeater interface for unlimited Q&A pairs
- Console log output for testing

## Support

For issues or questions, please contact Eternal support.
