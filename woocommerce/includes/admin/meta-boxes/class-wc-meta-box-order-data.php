<?php
/**
 * Order Data
 *
 * Functions for displaying the order data meta box.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Meta Boxes
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Meta_Box_Order_Data Class.
 */
class WC_Meta_Box_Order_Data {

	/**
	 * Billing fields.
	 *
	 * @var array
	 */
	protected static $billing_fields = array();

	/**
	 * Shipping fields.
	 *
	 * @var array
	 */
	protected static $shipping_fields = array();

	/**
	 * Init billing and shipping fields we display + save.
	 */
	public static function init_address_fields() {

		self::$billing_fields = apply_filters( 'woocommerce_admin_billing_fields', array(
			'first_name' => array(
				'label' => __( 'First name', 'woocommerce' ),
				'show'  => false,
			),
			'last_name' => array(
				'label' => __( 'Last name', 'woocommerce' ),
				'show'  => false,
			),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false,
			),
			'address_1' => array(
				'label' => __( 'Address line 1', 'woocommerce' ),
				'show'  => false,
			),
			'address_2' => array(
				'label' => __( 'Address line 2', 'woocommerce' ),
				'show'  => false,
			),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false,
			),
			'postcode' => array(
				'label' => __( 'Postcode / ZIP', 'woocommerce' ),
				'show'  => false,
			),
			'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'class'   => 'js_field-country select short',
				'type'    => 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries(),
			),
			'state' => array(
				'label' => __( 'State / County', 'woocommerce' ),
				'class'   => 'js_field-state select short',
				'show'  => false,
			),
			'email' => array(
				'label' => __( 'Email address', 'woocommerce' ),
			),
			'phone' => array(
				'label' => __( 'Phone', 'woocommerce' ),
			),
		) );

		self::$shipping_fields = apply_filters( 'woocommerce_admin_shipping_fields', array(
			'first_name' => array(
				'label' => __( 'First name', 'woocommerce' ),
				'show'  => false,
			),
			'last_name' => array(
				'label' => __( 'Last name', 'woocommerce' ),
				'show'  => false,
			),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false,
			),
			'address_1' => array(
				'label' => __( 'Address line 1', 'woocommerce' ),
				'show'  => false,
			),
			'address_2' => array(
				'label' => __( 'Address line 2', 'woocommerce' ),
				'show'  => false,
			),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false,
			),
			'postcode' => array(
				'label' => __( 'Postcode / ZIP', 'woocommerce' ),
				'show'  => false,
			),
			'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'type'    => 'select',
				'class'   => 'js_field-country select short',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_shipping_countries(),
			),
			'state' => array(
				'label' => __( 'State / County', 'woocommerce' ),
				'class'   => 'js_field-state select short',
				'show'  => false,
			),
		) );
	}

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		global $theorder;

		if ( ! is_object( $theorder ) ) {
			$theorder = wc_get_order( $post->ID );
		}

		$order = $theorder;

		self::init_address_fields();

		if ( WC()->payment_gateways() ) {
			$payment_gateways = WC()->payment_gateways->payment_gateways();
		} else {
			$payment_gateways = array();
		}

		$payment_method = $order->get_payment_method();

		$order_type_object = get_post_type_object( $post->post_type );
		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );
		?>
		<style type="text/css">
			#post-body-content, #titlediv { display:none }
		</style>
		<div class="panel-wrap woocommerce">
			<input name="post_title" type="hidden" value="<?php echo empty( $post->post_title ) ? __( 'Order', 'woocommerce' ) : esc_attr( $post->post_title ); ?>" />
			<input name="post_status" type="hidden" value="<?php echo esc_attr( $post->post_status ); ?>" />
			<div id="order_data" class="panel">

				<h2><?php
					/* translators: 1: order type 2: order number */
					printf(
						esc_html__( '%1$s #%2$s details', 'woocommerce' ),
						$order_type_object->labels->singular_name,
						$order->get_order_number()
					);
				?></h2>
				<p class="order_number"><?php

					if ( $payment_method ) {
						/* translators: %s: payment method */
						printf(
							__( 'Payment via %s', 'woocommerce' ),
							( isset( $payment_gateways[ $payment_method ] ) ? esc_html( $payment_gateways[ $payment_method ]->get_title() ) : esc_html( $payment_method ) )
						);

						if ( $transaction_id = $order->get_transaction_id() ) {
								if ( isset( $payment_gateways[ $payment_method ] ) && ( $url = $payment_gateways[ $payment_method ]->get_transaction_url( $order ) ) ) {
								echo ' (<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $transaction_id ) . '</a>)';
							} else {
								echo ' (' . esc_html( $transaction_id ) . ')';
							}
						}

						if ( $order->get_date_paid() ) {
							/* translators: 1: date 2: time */
							printf( ' ' . __( 'on %1$s @ %2$s', 'woocommerce' ), wc_format_datetime( $order->get_date_paid() ), wc_format_datetime( $order->get_date_paid(), get_option( 'time_format' ) ) );
						}

						echo '. ';
					}

					if ( $ip_address = $order->get_customer_ip_address() ) {
						/* translators: %s: IP address */
						printf(
							__( 'Customer IP: %s', 'woocommerce' ),
							'<span class="woocommerce-Order-customerIP">' . esc_html( $ip_address ) . '</span>'
						);
					}
				?></p>

				<div class="order_data_column_container">
					<div class="order_data_column">
						<h3><?php _e( 'General Details', 'woocommerce' ); ?></h3>

						<p class="form-field form-field-wide"><label for="order_date"><?php _e( 'Order date:', 'woocommerce' ) ?></label>
							<input type="text" class="date-picker" name="order_date" id="order_date" maxlength="10" value="<?php echo date_i18n( 'Y-m-d', strtotime( $post->post_date ) ); ?>" pattern="<?php echo esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ); ?>" />@&lrm;<input type="number" class="hour" placeholder="<?php esc_attr_e( 'h', 'woocommerce' ) ?>" name="order_date_hour" id="order_date_hour" min="0" max="23" step="1" value="<?php echo date_i18n( 'H', strtotime( $post->post_date ) ); ?>" pattern="([01]?[0-9]{1}|2[0-3]{1})" />:<input type="number" class="minute" placeholder="<?php esc_attr_e( 'm', 'woocommerce' ) ?>" name="order_date_minute" id="order_date_minute" min="0" max="59" step="1" value="<?php echo date_i18n( 'i', strtotime( $post->post_date ) ); ?>" pattern="[0-5]{1}[0-9]{1}" />&lrm;
						</p>

						<p class="form-field form-field-wide wc-order-status"><label for="order_status"><?php _e( 'Order status:', 'woocommerce' ) ?> <?php
							if ( $order->needs_payment() ) {
								printf( '<a href="%s">%s</a>',
									esc_url( $order->get_checkout_payment_url() ),
									__( 'Customer payment page &rarr;', 'woocommerce' )
								);
							}
						?></label>
						<select id="order_status" name="order_status" class="wc-enhanced-select">
							<?php
								$statuses = wc_get_order_statuses();
								foreach ( $statuses as $status => $status_name ) {
									echo '<option value="' . esc_attr( $status ) . '" ' . selected( $status, 'wc-' . $order->get_status( 'edit' ), false ) . '>' . esc_html( $status_name ) . '</option>';
								}
							?>
						</select></p>

						<p class="form-field form-field-wide wc-customer-user">
							<!--email_off--> <!-- Disable CloudFlare email obfuscation -->
							<label for="customer_user"><?php _e( 'Customer:', 'woocommerce' ) ?> <?php
								if ( $order->get_user_id( 'edit' ) ) {
									$args = array(
										'post_status'    => 'all',
										'post_type'      => 'shop_order',
										'_customer_user' => $order->get_user_id( 'edit' ),
									);
									printf( '<a href="%s">%s</a>',
										esc_url( add_query_arg( $args, admin_url( 'edit.php' ) ) ),
										__( 'View other orders &rarr;', 'woocommerce' )
									);
								}
							?></label>
							<?php
							$user_string = '';
							$user_id     = '';
							if ( $order->get_user_id() ) {
								$user_id     = absint( $order->get_user_id() );
								$user        = get_user_by( 'id', $user_id );
								/* translators: 1: user display name 2: user ID 3: user email */
								$user_string = sprintf(
									esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce' ),
									$user->display_name,
									absint( $user->ID ),
									$user->user_email
								);
							}
							?>
							<select class="wc-customer-search" id="customer_user" name="customer_user" data-placeholder="<?php esc_attr_e( 'Guest', 'woocommerce' ); ?>" data-allow_clear="true">
								<option value="<?php echo esc_attr( $user_id ); ?>" selected="selected"><?php echo htmlspecialchars( $user_string ); ?></option>
							</select>
							<!--/email_off-->
						</p>
						<?php do_action( 'woocommerce_admin_order_data_after_order_details', $order ); ?>
					</div>
					<div class="order_data_column">
						<h3>
							<?php _e( 'Billing details', 'woocommerce' ); ?>
							<a href="#" class="edit_address"><?php _e( 'Edit', 'woocommerce' ); ?></a>
							<span>
								<a href="#" class="load_customer_billing" style="display:none;"><?php _e( 'Load billing address', 'woocommerce' ); ?></a>
							</span>
						</h3>
						<?php
							// Display values
							echo '<div class="address">';

								if ( $order->get_formatted_billing_address() ) {
									echo '<p><strong>' . __( 'Address:', 'woocommerce' ) . '</strong>' . wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
								} else {
									echo '<p class="none_set"><strong>' . __( 'Address:', 'woocommerce' ) . '</strong> ' . __( 'No billing address set.', 'woocommerce' ) . '</p>';
								}

								foreach ( self::$billing_fields as $key => $field ) {
									if ( isset( $field['show'] ) && false === $field['show'] ) {
										continue;
									}

									$field_name = 'billing_' . $key;

									if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
										$field_value = $order->{"get_$field_name"}( 'edit' );
									} else {
										$field_value = $order->get_meta( '_' . $field_name );
									}

									if ( 'billing_phone' === $field_name ) {
										$field_value = wc_make_phone_clickable( $field_value );
									} else {
										$field_value = make_clickable( esc_html( $field_value ) );
									}

									echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . wp_kses_post( $field_value ) . '</p>';
								}

							echo '</div>';

							// Display form
							echo '<div class="edit_address">';

							foreach ( self::$billing_fields as $key => $field ) {
								if ( ! isset( $field['type'] ) ) {
									$field['type'] = 'text';
								}
								if ( ! isset( $field['id'] ) ) {
									$field['id'] = '_billing_' . $key;
								}
								switch ( $field['type'] ) {
									case 'select' :
										woocommerce_wp_select( $field );
									break;
									default :
										woocommerce_wp_text_input( $field );
									break;
								}
							}
							?>
							<p class="form-field form-field-wide">
							
								<label><?php _e( 'Payment method:', 'woocommerce' ); ?></label>
								<select name="_payment_method" id="_payment_method" class="first">
									<option value=""><?php _e( 'N/A', 'woocommerce' ); ?></option>
									<?php
										$found_method 	= false;

										foreach ( $payment_gateways as $gateway ) {
											if ( 'yes' === $gateway->enabled ) {
												echo '<option value="' . esc_attr( $gateway->id ) . '" ' . selected( $payment_method, $gateway->id, false ) . '>' . esc_html( $gateway->get_title() ) . '</option>';
												if ( $payment_method == $gateway->id ) {
													$found_method = true;
												}
											}
										}

										if ( ! $found_method && ! empty( $payment_method ) ) {
											echo '<option value="' . esc_attr( $payment_method ) . '" selected="selected">' . __( 'Other', 'woocommerce' ) . '</option>';
										} else {
											echo '<option value="other">' . __( 'Other', 'woocommerce' ) . '</option>';
										}
									?>
								</select>
							</p>
							<?php

							woocommerce_wp_text_input( array( 'id' => '_transaction_id', 'label' => __( 'Transaction ID', 'woocommerce' ) ) );

							echo '</div>';

							do_action( 'woocommerce_admin_order_data_after_billing_address', $order );
						?>
					</div>
					<div class="order_data_column">

						<h3>
							<?php _e( 'Shipping details', 'woocommerce' ); ?>
							<a href="#" class="edit_address"><?php _e( 'Edit', 'woocommerce' ); ?></a>
							<span>
								<a href="#" class="load_customer_shipping" style="display:none;"><?php _e( 'Load shipping address', 'woocommerce' ); ?></a>
								<a href="#" class="billing-same-as-shipping" style="display:none;"><?php _e( 'Copy billing address', 'woocommerce' ); ?></a>
							</span>
						</h3>
						<?php
							// Display values
							echo '<div class="address">';

								if ( $order->get_formatted_shipping_address() ) {
									echo '<p><strong>' . __( 'Address:', 'woocommerce' ) . '</strong>' . wp_kses( $order->get_formatted_shipping_address(), array( 'br' => array() ) ) . '</p>';
								} else {
									echo '<p class="none_set"><strong>' . __( 'Address:', 'woocommerce' ) . '</strong> ' . __( 'No shipping address set.', 'woocommerce' ) . '</p>';
								}

								if ( ! empty( self::$shipping_fields ) ) {
									foreach ( self::$shipping_fields as $key => $field ) {
										if ( isset( $field['show'] ) && false === $field['show'] ) {
											continue;
										}

										$field_name = 'shipping_' . $key;

										if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
											$field_value = $order->{"get_$field_name"}( 'edit' );
										} else {
											$field_value = $order->get_meta( '_' . $field_name );
										}

										echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . make_clickable( esc_html( $field_value ) ) . '</p>';
									}
								}

								if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' == get_option( 'woocommerce_enable_order_comments', 'yes' ) ) && $post->post_excerpt ) {
									echo '<p><strong>' . __( 'Customer provided note:', 'woocommerce' ) . '</strong> ' . nl2br( esc_html( $post->post_excerpt ) ) . '</p>';
								}

							echo '</div>';

							// Display form
							echo '<div class="edit_address">';

							if ( ! empty( self::$shipping_fields ) ) {
								foreach ( self::$shipping_fields as $key => $field ) {
									if ( ! isset( $field['type'] ) ) {
										$field['type'] = 'text';
									}
									if ( ! isset( $field['id'] ) ) {
										$field['id'] = '_shipping_' . $key;
									}

									switch ( $field['type'] ) {
										case 'select' :
											woocommerce_wp_select( $field );
										break;
										default :
											woocommerce_wp_text_input( $field );
										break;
									}
								}
							}

							if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' == get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) {
								?>
								<p class="form-field form-field-wide"><label for="excerpt"><?php _e( 'Customer provided note', 'woocommerce' ) ?>:</label>
								<textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt" placeholder="<?php esc_attr_e( 'Customer notes about the order', 'woocommerce' ); ?>"><?php echo wp_kses_post( $post->post_excerpt ); ?></textarea></p>
								<?php
							}

							echo '</div>';

							do_action( 'woocommerce_admin_order_data_after_shipping_address', $order );
						?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int $order_id Order ID.
	 */
	public static function save( $order_id ) {
		self::init_address_fields();

		// Ensure gateways are loaded in case they need to insert data into the emails.
		WC()->payment_gateways();
		WC()->shipping();

		// Get order object.
		$order = wc_get_order( $order_id );
		$props = array();

		// Create order key.
		if ( ! $order->get_order_key() ) {
			$props['order_key'] = 'wc_' . apply_filters( 'woocommerce_generate_order_key', uniqid( 'order_' ) );
		}

		// Update customer.
		$customer_id = isset( $_POST['customer_user'] ) ? absint( $_POST['customer_user'] ) : 0;
		if ( $customer_id !== $order->get_customer_id() ) {
			$props['customer_id'] = $customer_id;
		}

		// Update billing fields.
		if ( ! empty( self::$billing_fields ) ) {
			foreach ( self::$billing_fields as $key => $field ) {
				if ( ! isset( $field['id'] ) ) {
					$field['id'] = '_billing_' . $key;
				}

				if ( ! isset( $_POST[ $field['id'] ] ) ) {
					continue;
				}

				if ( is_callable( array( $order, 'set_billing_' . $key ) ) ) {
					$props[ 'billing_' . $key ] = wc_clean( $_POST[ $field['id'] ] );
				} else {
					$order->update_meta_data( $field['id'], wc_clean( $_POST[ $field['id'] ] ) );
				}
			}
		}

		// Update shipping fields.
		if ( ! empty( self::$shipping_fields ) ) {
			foreach ( self::$shipping_fields as $key => $field ) {
				if ( ! isset( $field['id'] ) ) {
					$field['id'] = '_shipping_' . $key;
				}

				if ( ! isset( $_POST[ $field['id'] ] ) ) {
					continue;
				}

				if ( is_callable( array( $order, 'set_shipping_' . $key ) ) ) {
					$props[ 'shipping_' . $key ] = wc_clean( $_POST[ $field['id'] ] );
				} else {
					$order->update_meta_data( $field['id'], wc_clean( $_POST[ $field['id'] ] ) );
				}
			}
		}

		if ( isset( $_POST['_transaction_id'] ) ) {
			$props['transaction_id'] = wc_clean( $_POST['_transaction_id'] );
		}

		// Payment method handling.
		if ( $order->get_payment_method() !== wp_unslash( $_POST['_payment_method'] ) ) {
			$methods              = WC()->payment_gateways->payment_gateways();
			$payment_method       = wc_clean( $_POST['_payment_method'] );
			$payment_method_title = $payment_method;

			if ( isset( $methods ) && isset( $methods[ $payment_method ] ) ) {
				$payment_method_title = $methods[ $payment_method ]->get_title();
			}

			$props['payment_method'] = $payment_method;
			$props['payment_method_title'] = $payment_method_title;
		}

		// Update date.
		if ( empty( $_POST['order_date'] ) ) {
			$date = current_time( 'timestamp', true );
		} else {
			$date = gmdate( 'Y-m-d H:i:s', strtotime( $_POST['order_date'] . ' ' . (int) $_POST['order_date_hour'] . ':' . (int) $_POST['order_date_minute'] . ':00' ) );
		}

		$props['date_created'] = $date;

		// Save order data.
		$order->set_props( $props );
		$order->set_status( wc_clean( $_POST['order_status'] ), '', true );
		$order->save();
	}
}