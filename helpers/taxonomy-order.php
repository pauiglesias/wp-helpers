<?php

namespace Solutia\SHASAI\Helpers;

/**
 * TaxonomyOrder class
 *
 * Adds an order field to taxonomy terms for custom sorting.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class TaxonomyOrder {



	/**
	 * Taxonomy slug
	 *
	 * @var string
	 */
	private $taxonomy;



	/**
	 * Meta key for order field
	 *
	 * @var string
	 */
	private $metaKey;



	/**
	 * Default order value
	 *
	 * @var int
	 */
	private $defaultOrder;



	/**
	 * Labels for UI
	 *
	 * @var array
	 */
	private $labels;



	/**
	 * Constructor
	 *
	 * @param string $taxonomy     Taxonomy slug
	 * @param string $metaKey      Meta key for storing order value
	 * @param int    $defaultOrder Default order value for new terms
	 * @param array  $labels       Optional labels for UI elements
	 */
	public function __construct($taxonomy, $metaKey, $defaultOrder = 100, $labels = []) {
		$this->properties($taxonomy, $metaKey, $defaultOrder, $labels);
		$this->actions();
	}



	/**
	 * Set properties
	 *
	 * @param string $taxonomy     Taxonomy slug
	 * @param string $metaKey      Meta key for storing order value
	 * @param int    $defaultOrder Default order value for new terms
	 * @param array  $labels       Labels for UI elements
	 *
	 * @return void
	 */
	private function properties($taxonomy, $metaKey, $defaultOrder, $labels) {

		$this->taxonomy = $taxonomy;
		$this->metaKey = $metaKey;
		$this->defaultOrder = $defaultOrder;

		$this->labels = array_merge([
			'field_name'  => 'Order',
			'description' => 'Order number for sorting (lower = first)',
			'column_name' => 'Order',
		], $labels);
	}



	/**
	 * Register actions and filters
	 *
	 * @return void
	 */
	private function actions() {
		add_action($this->taxonomy . '_add_form_fields', [$this, 'addFormFields']);
		add_action($this->taxonomy . '_edit_form_fields', [$this, 'editFormFields'], 10, 2);
		add_action('created_' . $this->taxonomy, [$this, 'saveTerm']);
		add_action('edited_' . $this->taxonomy, [$this, 'saveTerm']);
		add_filter('manage_edit-' . $this->taxonomy . '_columns', [$this, 'addColumn']);
		add_filter('manage_' . $this->taxonomy . '_custom_column', [$this, 'columnContent'], 10, 3);
	}



	/**
	 * Get meta key
	 *
	 * @return string
	 */
	public function getMetaKey() {
		return $this->metaKey;
	}



	/**
	 * Add order field to add term form
	 *
	 * Callback for {$taxonomy}_add_form_fields action.
	 *
	 * @return void
	 */
	public function addFormFields() {

		?><div class="form-field">
			<label for="<?php echo esc_attr($this->metaKey); ?>"><?php echo esc_html($this->labels['field_name']); ?></label>
			<input type="number" name="<?php echo esc_attr($this->metaKey); ?>" id="<?php echo esc_attr($this->metaKey); ?>" value="<?php echo esc_attr($this->defaultOrder); ?>" min="0" step="1">
			<p><?php echo esc_html($this->labels['description']); ?></p>
		</div><?php

	}



	/**
	 * Add order field to edit term form
	 *
	 * Callback for {$taxonomy}_edit_form_fields action.
	 *
	 * @param WP_Term $term     Current term object
	 * @param string  $taxonomy Current taxonomy slug
	 *
	 * @return void
	 */
	public function editFormFields($term, $taxonomy) {

		$orden = get_term_meta($term->term_id, $this->metaKey, true);

		?><tr class="form-field">
			<th scope="row"><label for="<?php echo esc_attr($this->metaKey); ?>"><?php echo esc_html($this->labels['field_name']); ?></label></th>
			<td>
				<input type="number" name="<?php echo esc_attr($this->metaKey); ?>" id="<?php echo esc_attr($this->metaKey); ?>" value="<?php echo esc_attr($orden !== '' ? $orden : $this->defaultOrder); ?>" min="0" step="1">
				<p class="description"><?php echo esc_html($this->labels['description']); ?></p>
			</td>
		</tr><?php

	}



	/**
	 * Save order field
	 *
	 * Callback for created_{$taxonomy} and edited_{$taxonomy} actions.
	 *
	 * @param int $termId Term ID
	 *
	 * @return void
	 */
	public function saveTerm($termId) {

		if (!isset($_POST[$this->metaKey])) {
			return;
		}

		$orden = (int) $_POST[$this->metaKey];
		update_term_meta($termId, $this->metaKey, $orden);
	}



	/**
	 * Add order column to term list
	 *
	 * Callback for manage_edit-{$taxonomy}_columns filter.
	 *
	 * @param array $columns Existing columns
	 *
	 * @return array Modified columns with order column added after name
	 */
	public function addColumn($columns) {

		$newColumns = [];

		foreach ($columns as $key => $value) {
			$newColumns[$key] = $value;
			if ($key === 'name') {
				$newColumns[$this->metaKey] = $this->labels['column_name'];
			}
		}

		return $newColumns;
	}



	/**
	 * Display order column content
	 *
	 * Callback for manage_{$taxonomy}_custom_column filter.
	 *
	 * @param string $content    Column content
	 * @param string $columnName Column name
	 * @param int    $termId     Term ID
	 *
	 * @return string|int Column content or order value
	 */
	public function columnContent($content, $columnName, $termId) {

		if ($columnName === $this->metaKey) {
			$orden = get_term_meta($termId, $this->metaKey, true);
			return $orden !== '' ? $orden : $this->defaultOrder;
		}

		return $content;
	}



}
