<?php
namespace mp_restaurant_menu\classes\models;

use mp_restaurant_menu\classes\Model;
use mp_restaurant_menu\classes\View as View;

/**
 * Class Errors
 * @package mp_restaurant_menu\classes\models
 */
class Errors extends Model {
	protected static $instance;

	/**
	 * @return Errors
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function print_errors() {
		$errors = $this->get_errors();
		if ($errors) {
			$classes = apply_filters('mprm_error_class', array(
				'mprm-errors', 'mprm-alert', 'mprm-alert-error'
			));
			echo '<div class="' . implode(' ', $classes) . '">';
			// Loop error codes and display errors
			foreach ($errors as $error_id => $error) {
				echo '<p class="mprm_error" id="mprm-error_' . $error_id . '"><strong>' . __('Error', 'mp-restaurant-menu') . '</strong>: ' . $error . '</p>';
			}
			echo '</div>';
			$this->clear_errors();
		}
	}

	/**
	 * @return bool|mixed
	 */
	public function get_error_html() {
		$errors = $this->get_errors();
		if ($errors) {

			$classes = apply_filters('mprm_error_class', array(
				'mprm-errors', 'mprm-alert', 'mprm-alert-error'
			));

			$error_html = View::get_instance()->render_html('shop/errors', array('errors' => $errors, 'classes' => $classes), false);
			$this->clear_errors();
			return $error_html;
		}
		return false;
	}

	/**
	 * Get Errors
	 *
	 * Retrieves all error messages stored during the checkout process.
	 * If errors exist, they are returned.
	 *
	 * @since 1.0
	 * @return mixed array if errors are present, false if none found
	 */
	public function get_errors() {
		return $this->get('session')->get_session_by_key('mprm_errors');
	}

	/**
	 * Set Error
	 *
	 * Stores an error in a session var.
	 *
	 * @since 1.0
	 *
	 * @param int $error_id ID of the error being set
	 * @param string $error_message Message to store with the error
	 *
	 * @return void
	 */
	public function set_error($error_id, $error_message) {
		$errors = $this->get_errors();
		if (!$errors) {
			$errors = array();
		}
		$errors[$error_id] = $error_message;
		$this->get('session')->set('mprm_errors', $errors);
	}

	/**
	 * Clears all stored errors.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function clear_errors() {
		$this->get('session')->set('mprm_errors', null);
	}

	/**
	 * Removes (unsets) a stored error
	 *
	 * @since 1.3.4
	 *
	 * @param int $error_id ID of the error being set
	 *
	 * @return string
	 */
	public function unset_error($error_id) {
		$errors = $this->get_errors();
		if ($errors) {
			unset($errors[$error_id]);
			$this->get('session')->set('mprm_errors', $errors);
		}
	}

}