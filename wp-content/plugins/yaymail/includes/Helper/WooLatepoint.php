<?php

namespace YayMail\Helper;

defined( 'ABSPATH' ) || exit;


class WooLatepoint {
	protected static $instance = null;
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	private function __construct() {}
	public static function wooLatepointBookingDetail( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/LatePoint/SampleTemplate/latepoint-booking-detail.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items = $order->get_items();
				ob_start();
				$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/LatePoint/latepoint-booking-detail.php';
				include $path;
				$html = ob_get_contents();
				ob_end_clean();
				return $html;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}

	public static function wooLatepointCalendarStartDate( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_calendar_start_date  = '<ul class="yaymail-woo-latepoint-list-calendar-start-date">';
			$list_calendar_start_date .= '<li>' . date_i18n( wc_date_format(), current_time( 'Y-m-d' ) ) . '</li>';
			$list_calendar_start_date .= '</ul>';
			return $list_calendar_start_date;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items              = $order->get_items();
				$list_calendar_start_date = '<ul class="yaymail-woo-latepoint-list-calendar-start-date">';
				foreach ( $order_items as $item ) {
					$data                = wc_get_order_item_meta( $item->get_id(), TECHXELA_WOOCOMMERCE_LATEPOINT_ORDER_ITEM_META_KEY );
					$calendar_start_date = isset( $data['restrictions']['calendar_start_date'] ) ? $data['restrictions']['calendar_start_date'] : null;
					if ( empty( $calendar_start_date ) ) {
						return null;
					}
					$list_calendar_start_date .= '<li>' . date_i18n( wc_date_format(), strtotime( $calendar_start_date ) ) . '</li>';
				}
				$list_calendar_start_date .= '</ul>';
				return $list_calendar_start_date;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}

	//WooCommerce for LatePoint
	public static function wooLatepointSelectedTotalAttendees( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-selected_total_attendies">';
			$list_attributes .= '<li>' . 5 . '</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'selected_total_attendies' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}

	public static function wooLatepointCaption( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-caption">';
			$list_attributes .= '<li>caption</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'caption' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}

	public static function wooLatepointBgColor( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-bg_color">';
			$list_attributes .= '<li>bg_color</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'bg_color' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointTextColor( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-text_color">';
			$list_attributes .= '<li>text_color</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'text_color' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointFontSize( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-font_size">';
			$list_attributes .= '<li>font_size</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'font_size' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointBorder( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-border">';
			$list_attributes .= '<li>border</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'border' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointBorderRadius( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-border_radius">';
			$list_attributes .= '<li>border_radius</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'border_radius' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointMargin( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-margin">';
			$list_attributes .= '<li>margin</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'margin' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointPaddings( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-padding">';
			$list_attributes .= '<li>padding</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'padding' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointCss( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-css">';
			$list_attributes .= '<li>css</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'css' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointShowLocations( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-show_locations">';
			$list_attributes .= '<li>show_locations</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'show_locations' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointShowAgents( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-show_agents">';
			$list_attributes .= '<li>show_agents</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'show_agents' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointShowServices( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-show_services">';
			$list_attributes .= '<li>show_services</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'show_services' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointShowServiceCategories( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-show_service_categories">';
			$list_attributes .= '<li>show_service_categories</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'show_service_categories' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointSelectedLocation( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-selected_location">';
			$list_attributes .= '<li>selected_location</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'selected_location' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointSelectedAgent( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-selected_agent">';
			$list_attributes .= '<li>selected_agent</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'selected_agent' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointSelectedService( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-selected_service">';
			$list_attributes .= '<li>' . 5 . '</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'selected_service' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointSelectedServiceCategory( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-selected_service_category">';
			$list_attributes .= '<li>' . 5 . '</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'selected_service_category' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointSelectedDuration( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-selected_duration">';
			$list_attributes .= '<li>' . 5 . '</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'selected_duration' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointHideSidePanel( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-hide_side_panel">';
			$list_attributes .= '<li>hide_side_panel</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'hide_side_panel' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	public static function wooLatepointHideSummary( $args, $order, $sent_to_admin ) {
		if ( null === $order ) {
			$list_attributes  = '<ul class="yaymail-woo-latepoint-hide_summary">';
			$list_attributes .= '<li>hide_summary</li>';
			$list_attributes .= '</ul>';
			return $list_attributes;
		} else {
			if ( method_exists( $order, 'get_items' ) ) {
				$order_items                 = $order->get_items();
				$valueRestrictionsAttributes = self::getRestrictionsAttributes( $order_items, 'hide_summary' );
				return $valueRestrictionsAttributes;
			} else {
				// Handle error: $order object doesn't have get_items() method
				return null;
			}
		}
	}
	/**
	 * It takes the order items and the value of the attribute you want to display, and returns a list of
	 * the attributes
	 *
	 * @param order_items This is the array of order items.
	 * @param value The name of the attribute you want to display.
	 *
	 * @return A list of attributes.
	 */
	public static function getRestrictionsAttributes( $order_items, $value ) {
		$list_attributes = '<ul class="yaymail-woo-latepoint-selected-' . $value . '">';
		$attributes      = $value;
		foreach ( $order_items as $item ) {
			$data            = wc_get_order_item_meta( $item->get_id(), TECHXELA_WOOCOMMERCE_LATEPOINT_ORDER_ITEM_META_KEY );
			$data_attributes = isset( $data['restrictions'][ $attributes ] ) ? $data['restrictions'][ $attributes ] : null;
			if ( empty( $data_attributes ) ) {
				return null;
			}
			$list_attributes .= '<li>' . $data_attributes . '</li>';
		}
		$list_attributes .= '</ul>';
		return $list_attributes;
	}
}
