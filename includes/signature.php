<?php
/**
 * Pure webhook signature helpers. Safe to include without WordPress.
 */

function argon_astro_sign($secret, $timestamp, $nonce, $body) {
    $payload = $timestamp . '.' . $nonce . '.' . $body;
    return hash_hmac('sha256', $payload, $secret);
}

function argon_astro_retry_delay($now, $last, $interval = 60) {
    $elapsed = (int) $now - (int) $last;
    if ($elapsed < (int) $interval) {
        return (int) $interval - $elapsed;
    }
    return 0;
}

function argon_astro_timestamp_fresh($timestamp, $now, $window = 300) {
    if (!preg_match('/^[0-9]{10,}$/', (string) $timestamp)) {
        return false;
    }
    return abs((int) $now - (int) $timestamp) <= (int) $window;
}
