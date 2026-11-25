<?php
require dirname(__DIR__) . '/includes/signature.php';

$expected = '2fc545085a5521247b67cb18ae91d77495f2f1db8c2da6e9f98856bf1a265988';
$actual = argon_astro_sign('test-secret', '1700000000', 'abc', '{}');
if (!hash_equals($expected, $actual)) {
    fwrite(STDERR, "signature mismatch: $actual\n");
    exit(1);
}
if (!argon_astro_timestamp_fresh('1700000000', 1700000100, 300)) {
    fwrite(STDERR, "fresh timestamp rejected\n");
    exit(1);
}
if (argon_astro_timestamp_fresh('1700000000', 1700000401, 300)) {
    fwrite(STDERR, "stale timestamp accepted\n");
    exit(1);
}
if (argon_astro_retry_delay(100, 80, 60) !== 40) {
    fwrite(STDERR, "rate limit delay mismatch\n");
    exit(1);
}
if (argon_astro_retry_delay(200, 80, 60) !== 0) {
    fwrite(STDERR, "rate limit should allow dispatch\n");
    exit(1);
}
echo "signature ok\n";
