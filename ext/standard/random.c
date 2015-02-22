/*
   +----------------------------------------------------------------------+
   | PHP Version 7                                                        |
   +----------------------------------------------------------------------+
   | Copyright (c) 1997-2015 The PHP Group                                |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Authors: Sammy Kaye Powers <me@sammyk.me>                            |
   +----------------------------------------------------------------------+
*/

/* $Id$ */

#include <stdlib.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <math.h>

#include "php.h"

#if PHP_WIN32
# include "win32/winutil.h"
#endif

static int php_random_bytes(void *bytes, size_t size)
{
	int n = 0;

#if PHP_WIN32
	/* Defer to CryptGenRandom on Windows */
	if (php_win32_get_random_bytes(bytes, size) == FAILURE) {
		php_error_docref(NULL, E_WARNING, "Could not gather sufficient random data");
		return FAILURE;
	}
#else
	int    fd = -1;
	size_t read_bytes = 0;
#if HAVE_DEV_ARANDOM
	fd = open("/dev/arandom", O_RDONLY);
#else
#if HAVE_DEV_URANDOM
	fd = open("/dev/urandom", O_RDONLY);
#endif
#endif
	if (fd < 0) {
		php_error_docref(NULL, E_WARNING, "Cannot open source device");
		return FAILURE;
	}

	while (read_bytes < size) {
		n = read(fd, bytes + read_bytes, size - read_bytes);
		if (n < 0) {
			break;
		}
		read_bytes += n;
	}

	close(fd);
	if (read_bytes < size) {
		php_error_docref(NULL, E_WARNING, "Could not gather sufficient random data");
		return FAILURE;
	}
#endif

	return SUCCESS;
}

/* {{{ proto string random_bytes(int length)
Return an arbitrary length of pseudo-random bytes as binary string */
PHP_FUNCTION(random_bytes)
{
	zend_long size;
	zend_string *bytes;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "l", &size) == FAILURE) {
		return;
	}

	if (size < 1) {
		php_error_docref(NULL, E_WARNING, "Length must be greater than 0");
		RETURN_FALSE;
	}

	bytes = zend_string_alloc(size, 0);

	if (php_random_bytes(bytes->val, size) == FAILURE) {
		zend_string_release(bytes);
		return;
	}

	bytes->val[size] = '\0';

	RETURN_STR(bytes);
}
/* }}} */

/* {{{ proto int random_int(int max)
Return an arbitrary pseudo-random integer */
PHP_FUNCTION(random_int)
{
	zend_long max = ZEND_LONG_MAX;
	zend_long limit;
	zend_long result;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "|l", &max) == FAILURE) {
		return;
	}

	if (max <= 0 || max > ZEND_LONG_MAX) {
		php_error_docref(NULL, E_WARNING, "Cannot use maximum less than 1 or greater than %d", ZEND_LONG_MAX);
		RETURN_FALSE;
	}

	// Special case so we can return a range inclusive of the upper bound
	if (max == ZEND_LONG_MAX) {
		if (php_random_bytes(&result, sizeof(result)) == FAILURE) {
			return;
		}
		RETURN_LONG(result & ZEND_LONG_MAX);
	}

	// Increment the max so the range is inclusive of max
	max++;

	// Ceiling under which ZEND_LONG_MAX % max == 0
	limit = ZEND_LONG_MAX - (ZEND_LONG_MAX % max) - 1;

	// Discard numbers over the limit to avoid modulo bias
	do {
		if (php_random_bytes(&result, sizeof(result)) == FAILURE) {
			return;
		}
		result &= ZEND_LONG_MAX;
	} while (result > limit);

	RETURN_LONG(result % max);
}
/* }}} */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: sw=4 ts=4 fdm=marker
 * vim<600: sw=4 ts=4
 */
