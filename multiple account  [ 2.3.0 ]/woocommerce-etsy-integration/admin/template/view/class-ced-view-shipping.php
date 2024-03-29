<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
$header = new \Cedcommerce\Template\View\Ced_View_Header();
class Ced_Etsy_Shipping_Profile_Table extends WP_List_Table {

	public $all_shipping_profiles;

	/** Class constructor */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Etsy Shipping Profile', 'woocommerce-etsy-integration' ), // singular name of the listed records
				'plural'   => __( 'Etsy Shipping Profiles', 'woocommerce-etsy-integration' ), // plural name of the listed records
				'ajax'     => false, // does this table support ajax?
			)
		);
	}

	public function prepare_items() {
		global $wpdb;
		$shop_name = isset( $_GET['shop_name'] ) ? sanitize_text_field( wp_unslash( $_GET['shop_name'] ) ) : '';
		$per_page  = apply_filters( 'show_etsy_shipping_profile_per_page', 10 );
		$columns   = $this->get_columns();
		$hidden    = array();
		$sortable  = $this->get_sortable_columns();

		// Column headers
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$current_page          = $this->get_pagenum();
		if ( 1 < $current_page ) {
			$offset = $per_page * ( $current_page - 1 );
		} else {
			$offset = 0;
		}

		$count = self::get_count();
		// Set the pagination
		$this->set_pagination_args(
			array(
				'total_items' => $count,
				'per_page'    => $per_page,
				'total_pages' => ceil( $count / $per_page ),
			)
		);

		if ( ! $this->current_action() ) {
			$this->items = self::get_shipping_profiles( $per_page, $current_page );
			$this->renderHTML();
		} else {
			$this->process_bulk_action();
		}
	}

	public function get_shipping_profiles( $per_page = 10, $page_number = 1 ) {
		if ( ! empty( $this->all_shipping_profiles ) ) {
			$this->all_shipping_profiles = array_slice( $this->all_shipping_profiles, ( $page_number - 1 ) * $per_page, $per_page, true );
		}
		return $this->all_shipping_profiles;
	}

	/**
	 * Function to count number of responses in result
	 */
	public function get_count() {
		$shop_name             = isset( $_GET['shop_name'] ) ? sanitize_text_field( wp_unslash( $_GET['shop_name'] ) ) : '';
		$all_shipping_profiles = array();
		/*GET COUNTRIES LIST FOR SHIPPING TEMPLATE */
		$shop_id           = get_etsy_shop_id( $shop_name );
		$shippingTemplates = array();
		$action            = "application/shops/{$shop_id}/shipping-profiles";
		// Refresh token if isn't.
		do_action( 'ced_etsy_refresh_token', $shop_name );
		$shopShippingTemplates = etsy_request()->get( $action, $shop_name );
		/**
		 * Check if the shop has any shipping templates and that is less than Etsy's profiles.
		 *
		 * @var $shopShippingTemplates \Ced\Etsy_Shop\Helper\Data.
		 */

		$shipping_templates = array();
		if ( isset( $shopShippingTemplates['count'] ) && $shopShippingTemplates['count'] >= 1 ) {
			$shopShippingTemplates = $shopShippingTemplates['results'];
			foreach ( $shopShippingTemplates as $key => $value ) {
				$shippingTemplates['id']                        = $value['shipping_profile_id'];
				$shippingTemplates['name']                      = $value['title'];
				$all_shipping_profiles[]                        = $shippingTemplates;
				$shipping_templates[ $shippingTemplates['id'] ] = $shippingTemplates['name'];
			}
		}
		$this->all_shipping_profiles = $all_shipping_profiles;
		update_option( 'ced_etsy_shipping_templates_' . $shop_name, $shipping_templates );
		return count( $shipping_templates );
	}

	/** Text displayed when no customer data is available */
	public function no_items() {
		esc_html_e( 'No Profiles Created.', 'woocommerce-etsy-integration' );
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return '';
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	public function column_shipping_profile_name( $item ) {
		$shop_name         = isset( $_GET['shop_name'] ) ? sanitize_text_field( wp_unslash( $_GET['shop_name'] ) ) : '';
		$title             = '<strong>' . $item['name'] . '</strong>';
		$url               = admin_url( 'admin.php?page=ced_etsy&e_prof_id=' . $item['id'] . '&section=shipping-edit&panel=edit&shop_name=' . $shop_name );
		$actions['delete'] = '<a href="javascript:void(0)" class="Delete_shipping_profiles" data-e_profile_id="' . $item['id'] . '">Delete</a>';
		print_r( $title );
		return $this->row_actions( $actions );
	}

	public function column_shipping_profile_id( $item ) {
		$shop_name = isset( $_GET['shop_name'] ) ? sanitize_text_field( wp_unslash( $_GET['shop_name'] ) ) : '';
		$title     = $item['id'];
		print_r( $title );
	}
	public function column_shipping_profile_edit( $item ) {
		$shop_name = isset( $_GET['shop_name'] ) ? sanitize_text_field( wp_unslash( $_GET['shop_name'] ) ) : '';
		$url       = admin_url( 'admin.php?page=ced_etsy&e_prof_id=' . $item['id'] . '&section=shipping-edit&panel=edit&shop_name=' . $shop_name );
		$actions   = '<a href=' . $url . '>Edit</a>';
		print_r( $actions );
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'                    => '',
			'shipping_profile_name' => __( 'Name', 'woocommerce-etsy-integration' ),
			'shipping_profile_id'   => __( 'ID', 'woocommerce-etsy-integration' ),
			'shipping_profile_edit' => __( 'Edit', 'woocommerce-etsy-integration' ),
		);
		$columns = apply_filters( 'ced_etsy_alter_profiles_table_columns', $columns );
		return $columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array();
		return $actions;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array();
		return $sortable_columns;
	}

	/**
	 * Function to get changes in html
	 */
	public function renderHTML() {
		$shop_name = isset( $_GET['shop_name'] ) ? sanitize_text_field( wp_unslash( $_GET['shop_name'] ) ) : '';
		$url       = admin_url( 'admin.php?page=ced_etsy&section=profiles&panel=edit&shop_name=' . $shop_name );
		?>
		<div class="ced_etsy_heading">
			<?php echo esc_html_e( get_etsy_instuctions_html() ); ?>
			<div class="ced_etsy_child_element parent_default">
				<?php
				$activeShop = isset( $_GET['shop_name'] ) ? sanitize_text_field( $_GET['shop_name'] ) : '';

				$instructions = array(
					'Etsy orders will be displayed here.',
					'You can fetch the etsy orders manually by clicking the fetch order button or also you can enable the auto fetch order feature in Schedulers <a href="' . admin_url( 'admin.php?page=ced_etsy&section=ced-etsy-settings&shop_name=' . $activeShop ) . '">here</a>.',
					'Make sure you have the skus present in all your products/variations for order syncing.',
					'You can also submit the tracking details from woocommerce to etsy . You need to go in the order edit section using <a>Edit</a> option in the order table below.Once you go in order edit section you will find the section at the bottom where you can enter tracking info and update them on etsy.',
				);

				echo '<ul class="ced_etsy_instruction_list" type="disc">';
				foreach ( $instructions as $instruction ) {
					print_r( "<li>$instruction</li>" );
				}
				echo '</ul>';

				?>
			</div>
		</div>
		<div class="ced_etsy_wrap ced_etsy_wrap_extn">					
			<div>				
				<div id="post-body" class="metabox-holder columns-2">
					<div id="">
						<div class="meta-box-sortables ui-sortable">
							<td>
								<?php
								$url = admin_url( 'admin.php?page=ced_etsy&section=add-shipping-profile&shop_name=' . $activeShop );
								?>
								<a href="<?php echo esc_attr( $url ); ?>" class="button-primary ced_etsy_create_new_shipping_profile" >Create new Etsy shipping profile +</a>
							</td>
							<form method="post">
								<?php
								wp_nonce_field( 'etsy_profiles', 'etsy_profiles_actions' );
								$this->display();
								?>
							</form>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<br class="clear">
			</div>
		</div>
		<?php
	}

	public function current_action() {

		if ( isset( $_GET['panel'] ) ) {
			$action = isset( $_GET['panel'] ) ? sanitize_text_field( wp_unslash( $_GET['panel'] ) ) : '';
			return $action;
		} elseif ( isset( $_POST['action'] ) ) {
			if ( ! isset( $_POST['etsy_profiles_actions'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['etsy_profiles_actions'] ) ), 'etsy_profiles' ) ) {
				return;
			}
			$action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
			return $action;
		}
	}

	public function process_bulk_action() {

		if ( 'bulk-delete' === $this->current_action() || ( isset( $_GET['action'] ) && 'bulk-delete' === $_GET['action'] ) ) {

			if ( ! isset( $_POST['etsy_profiles_actions'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['etsy_profiles_actions'] ) ), 'etsy_profiles' ) ) {
				return;
			}
			$sanitized_array = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
			$profileIds      = isset( $sanitized_array['etsy_profile_ids'] ) ? $sanitized_array['etsy_profile_ids'] : array();
			if ( is_array( $profileIds ) && ! empty( $profileIds ) ) {

				global $wpdb;

				$tableName = $wpdb->prefix . 'ced_etsy_profiles';

				$shop_id = isset( $_GET['shop_name'] ) ? sanitize_text_field( wp_unslash( $_GET['shop_name'] ) ) : '';

				foreach ( $profileIds as $index => $pid ) {

					$product_ids_assigned = get_option( 'ced_etsy_product_ids_in_profile_' . $pid, array() );
					foreach ( $product_ids_assigned as $index => $ppid ) {
						delete_post_meta( $ppid, 'ced_etsy_profile_assigned' . $shop_id );
					}

					$term_id = $wpdb->get_results( $wpdb->prepare( "SELECT `woo_categories` FROM {$wpdb->prefix}ced_etsy_profiles WHERE `id` = %d", $pid ), 'ARRAY_A' );
					$term_id = json_decode( $term_id[0]['woo_categories'], true );
					foreach ( $term_id as $key => $value ) {
						delete_term_meta( $value, 'ced_etsy_profile_created_' . $shop_id );
						delete_term_meta( $value, 'ced_etsy_profile_id_' . $shop_id );
						delete_term_meta( $value, 'ced_etsy_mapped_category_' . $shop_id );
					}
				}
				foreach ( $profileIds as $id ) {
					$wpdb->delete( $tableName, array( 'id' => $id ) );
				}
				$redirectURL = get_admin_url() . 'admin.php?page=ced_etsy&section=profiles&shop_name=' . $shop_id;
				wp_redirect( $redirectURL );
			}
		} elseif ( isset( $_GET['panel'] ) && 'edit' == $_GET['panel'] ) {
			require_once CED_ETSY_DIRPATH . 'admin/template/view/class-ced-view-profile-edit.php';
		}
	}
}

$ced_etsy_profile_obj = new Ced_Etsy_Shipping_Profile_Table();
$ced_etsy_profile_obj->prepare_items();

