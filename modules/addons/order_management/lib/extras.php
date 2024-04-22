<?php
if (!defined('WHMCS')) {
	die('This file cannot be accessed directly.');
}

use WHMCS\Database\Capsule;

/**
 * Get number of days after which to cancel an order and/or invoice that is still unpaid. Default is 14 days.
 * @return string
 */
function cancelAfterDays() {
	$cancelAfterDays = '14';

	try {
		$query = Capsule::table('tbladdonmodules')
			->select('value')
			->where('module', 'order_management')
			->where('setting', 'cancelAfter')
			->first();

		$cancelAfterResult = trim($query->value);

		if ($cancelAfterResult != '') {
			$cancelAfterDays = $cancelAfterResult;
		}
	} catch (\Exception $e) {
		logActivity('[Order Management] ' . $e);
	}

	return $cancelAfterDays;
}

function cancelAgedOrdersIsEnabled() {
	return isSettingOn('enableCancelAgedOrders');
}

function cancelAgedInvoicesIsEnabled() {
	return isSettingOn('enableCancelAgedInvoices');
}

function acceptPaidPendingOrdersIsEnabled() {
	return isSettingOn('enableAcceptPaidPendingOrders');
}

/**
 * Unless there is an entry in the database for the setting with a blank value, will return true
 * @param $setting
 * @return bool
 */
function isSettingOn($setting) {
    $isEnabled = true;

    try {
        $query = Capsule::table('tbladdonmodules')
                        ->select('value')
                        ->where('module', 'order_management')
                        ->where('setting', $setting)
                        ->first();

        if ($query) {
            $result = trim($query->value);
            // Check if the result is an empty string which means setting is "off"
            if ($result === '') {
                $isEnabled = false;
            }
        } else {
            // No setting found in the database, default to true as specified
            $isEnabled = true;
        }
    } catch (\Exception $e) {
        logActivity('[Order Management] ' . $e);
    }

    return $isEnabled;
}

