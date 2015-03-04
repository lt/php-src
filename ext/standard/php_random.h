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

#ifndef PHP_RANDOM_H
#define PHP_RANDOM_H

PHP_FUNCTION(random_bytes);
PHP_FUNCTION(random_int);

PHP_MINIT_FUNCTION(lcg);

ZEND_BEGIN_MODULE_GLOBALS(random)
	int fd;
ZEND_END_MODULE_GLOBALS(random)

#ifdef ZTS
# define RANDOM_G(v) TSRMG(random_globals_id, zend_random_globals *, v);
#else
# define RANDOM_G(v) random_globals.v
#endif

#endif

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
