<?php
/**
 * Sets session hashing to something a bit more... usable.
 *
 */

/**
 * Using WhirlPool as a hash function. It is superior to both MD5 AND SHA1
 * @see http://md5-sha-whirlpool.reviews.r-tt.com/ Whirlpool 512-bit digest
 */
ini_set('session.hash_function', 'whirlpool');

ini_set('session.hash_bits_per_character', SESSION_HASH_BITS_PER_CHARACTER);
session_set_cookie_params(SESSION_LIFETIME, '/', SESSION_HOSTNAME, false, true);
session_name(SESSION_NAME);
session_start();
session_regenerate_id(SESSION_REGENERATE_ID);
