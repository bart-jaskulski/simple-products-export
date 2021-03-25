#!/bin/bash

export WPDESK_PLUGIN_SLUG=simple-products-export
export WPDESK_PLUGIN_TITLE="Simple products export for WooCommerce"

export WOOTESTS_IP=${WOOTESTS_IP:wootests}

sh ./vendor/wpdesk/wp-codeception/scripts/common_bootstrap.sh
