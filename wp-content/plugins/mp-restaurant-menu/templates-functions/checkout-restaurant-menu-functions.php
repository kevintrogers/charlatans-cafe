<?php
use mp_restaurant_menu\classes\models;
use mp_restaurant_menu\classes\View;


/**
 * @return mixed|void
 */
function mprm_is_checkout() {
	return models\Checkout::get_instance()->is_checkout();
}

/**
 * @return mixed|void
 */
function mprm_get_checkout_uri() {
	return models\Checkout::get_instance()->get_checkout_uri();
}

function mprm_get_checkout_cart_template() {
	$data = array();
	$data['is_ajax_disabled'] = models\Settings::get_instance()->is_ajax_disabled();
	$data['cart_items'] = models\Cart::get_instance()->get_cart_contents();
	mprm_get_template('shop/checkout-cart', $data);
}

/**
 * @return mixed|void
 */
function mprm_checkout_button_next() {
	$color = mprm_get_option('checkout_color', 'blue');
	$color = ($color == 'inherit') ? '' : $color;
	$padding = mprm_get_option('checkout_padding', 'mprm-inherit');
	$style = mprm_get_option('button_style', 'button');
	$purchase_page = mprm_get_option('purchase_page', '0');
	ob_start();
	?>
	<input type="hidden" name="mprm_action" value="gateway_select"/>
	<input type="hidden" name="page_id" value="<?php echo absint($purchase_page); ?>"/>
	<input type="submit" name="gateway_submit" id="mprm_next_button" class="mprm-submit <?php echo $color; ?> <?php echo $padding; ?> <?php echo $style; ?>" value="<?php _e('Next', 'mp-restaurant-menu'); ?>"/>
	<?php
	return apply_filters('mprm_checkout_button_next', ob_get_clean());
}

/**
 * @return mixed|void
 */
function mprm_checkout_button_purchase() {
	$color = mprm_get_option('checkout_color', 'blue');
	$color = ($color == 'inherit') ? '' : $color;
	$style = mprm_get_option('button_style', 'button');
	$label = mprm_get_option('checkout_label', '');
	$padding = mprm_get_option('checkout_padding', 'mprm-inherit');
	if (mprm_get_cart_total()) {
		$complete_purchase = !empty($label) ? $label : __('Purchase', 'mp-restaurant-menu');
	} else {
		$complete_purchase = !empty($label) ? $label : __('Free Menu item', 'mp-restaurant-menu');
	}
	ob_start();
	?>
	<input type="submit" class="mprm-submit <?php echo $color; ?> <?php echo $padding; ?> <?php echo $style; ?>" id="mprm-purchase-button" name="mprm-purchase" value="<?php echo $complete_purchase; ?>"/>
	<?php
	return apply_filters('mprm_checkout_button_purchase', ob_get_clean());
}

/**
 * @return bool
 */
function mprm_is_no_guest_checkout() {
	return models\Misc::get_instance()->no_guest_checkout();
}

function mprm_checkout_tax_fields() {
	if (models\Taxes::get_instance()->cart_needs_tax_address_fields() && mprm_get_cart_total()) {
		mprm_default_cc_address_fields();
	}
}

function mprm_checkout_submit() { ?>
	<fieldset id="mprm_purchase_submit">
		<?php do_action('mprm_purchase_form_before_submit'); ?>
		<?php mprm_checkout_hidden_fields(); ?>
		<?php echo mprm_checkout_button_purchase(); ?>
		<?php do_action('mprm_purchase_form_after_submit'); ?>
	</fieldset>
	<?php
}

function mprm_checkout_additional_information() {
	View::get_instance()->render_html('/shop/checkout-additional-information');
}

function mprm_checkout_final_total() {
	?>
	<p id="mprm_final_total_wrap">
		<strong><?php _e('Purchase Total:', 'mp-restaurant-menu'); ?></strong>
		<span class="mprm_cart_amount" data-subtotal="<?php echo mprm_get_cart_subtotal(); ?>" data-total="<?php echo mprm_get_cart_subtotal(); ?>"><?php echo mprm_currency_filter(mprm_format_amount(mprm_get_cart_total())); ?></span>
	</p>
	<?php
}

function mprm_checkout_hidden_fields() {
	?>
	<?php if (is_user_logged_in()) { ?>
		<input type="hidden" name="mprm-user-id" value="<?php echo get_current_user_id(); ?>"/>
	<?php } ?>
	<input type="hidden" name="mprm_action" value="purchase"/>
	<input type="hidden" name="controller" value="cart"/>
	<input type="hidden" name="mprm-gateway" value="<?php echo models\Gateways::get_instance()->get_chosen_gateway(); ?>"/>
	<?php
}