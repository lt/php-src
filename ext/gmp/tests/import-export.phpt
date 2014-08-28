--TEST--
Check gmp_import and gmp_export behave as intended
--SKIPIF--
<?php if (!extension_loaded("gmp")) print "skip"; ?>
--FILE--
<?php

// Tests taken from GMPs own test suite.

$import = [
    ['0',0,1,1,1,0,''],
    ['0',1,1,0,1,0,''],
    ['12345678',4,1,1,1,0,'12345678'],
    ['12345678',1,1,4,1,0,'12345678'],
    ['12345678',1,-1,4,1,0,'12345678'],
    ['12345678',4,-1,1,-1,0,'78563412'],
    ['12345678',1,1,4,-1,0,'78563412'],
    ['12345678',1,-1,4,-1,0,'78563412'],
    ['0',5,1,1,1,7,'fefefefefe'],
    ['0',5,-1,1,1,7,'fefefefefe'],
    ['15',5,1,1,1,7,'fffefffeff'],
    ['0',3,1,2,1,1,'800080008000'],
    ['0',3,1,2,-1,1,'008000800080'],
    ['0',3,1,2,1,15,'fffefffefffe'],
    ['2a',3,1,2,1,14,'fffefffefffe'],
    ['6',3,1,2,1,14,'fffcfffdfffe'],
    ['24',3,-1,2,1,14,'fffcfffdfffe'],
    ['123456789abc',3,1,2,1,0,'123456789abc'],
    ['123456789abc',3,-1,2,1,0,'9abc56781234'],
    ['123456789abc',3,1,2,-1,0,'34127856bc9a'],
    ['123456789abc',3,-1,2,-1,0,'bc9a78563412'],
    ['112233445566778899aabbcc',3,1,4,1,0,'112233445566778899aabbcc'],
    ['112233445566778899aabbcc',3,-1,4,1,0,'99aabbcc5566778811223344'],
    ['112233445566778899aabbcc',3,1,4,-1,0,'4433221188776655ccbbaa99'],
    ['112233445566778899aabbcc',3,-1,4,-1,0,'ccbbaa998877665544332211'],
    ['100120023003400450056006700780089009a00ab00bc00c',3,1,8,1,0,'100120023003400450056006700780089009a00ab00bc00c'],
    ['100120023003400450056006700780089009a00ab00bc00c',3,-1,8,1,0,'9009a00ab00bc00c50056006700780081001200230034004'],
    ['100120023003400450056006700780089009a00ab00bc00c',3,1,8,-1,0,'044003300220011008800770066005500cc00bb00aa00990'],
    ['100120023003400450056006700780089009a00ab00bc00c',3,-1,8,-1,0,'0cc00bb00aa0099008800770066005500440033002200110'],
    ['155555555555555555555555',3,1,4,1,1,'d5555555aaaaaaaad5555555'],
    ['155555555555555555555555',3,-1,4,1,1,'d5555555aaaaaaaad5555555'],
    ['155555555555555555555555',3,1,4,-1,1,'555555d5aaaaaaaa555555d5'],
    ['155555555555555555555555',3,-1,4,-1,1,'555555d5aaaaaaaa555555d5']
];

$export = [
    ['0',0,1,1,1,0,''],
    ['0',0,1,2,1,0,''],
    ['0',0,1,3,1,0,''],
    ['12345678',4,1,1,1,0,'12345678'],
    ['12345678',1,1,4,1,0,'12345678'],
    ['12345678',1,-1,4,1,0,'12345678'],
    ['12345678',4,-1,1,-1,0,'78563412'],
    ['12345678',1,1,4,-1,0,'78563412'],
    ['12345678',1,-1,4,-1,0,'78563412'],
    ['15',5,1,1,1,7,'0100010001'],
    ['1FFFFFFFFFFF',3,1,2,1,1,'7fff7fff7fff'],
    ['1FFFFFFFFFFF',3,1,2,-1,1,'ff7fff7fff7f'],
    ['7',3,1,2,1,15,'000100010001'],
    ['7',3,1,2,-1,15,'010001000100'],
    ['24',3,1,2,1,14,'000200010000'],
    ['24',3,1,2,-1,14,'020001000000'],
    ['24',3,-1,2,-1,14,'000001000200'],
    ['24',3,-1,2,1,14,'000000010002'],
    ['123456789ABC',3,1,2,1,0,'123456789abc'],
    ['123456789ABC',3,-1,2,1,0,'9abc56781234'],
    ['123456789ABC',3,1,2,-1,0,'34127856bc9a'],
    ['123456789ABC',3,-1,2,-1,0,'bc9a78563412'],
    ['112233445566778899AABBCC',3,1,4,1,0,'112233445566778899aabbcc'],
    ['112233445566778899AABBCC',3,-1,4,1,0,'99aabbcc5566778811223344'],
    ['112233445566778899AABBCC',3,1,4,-1,0,'4433221188776655ccbbaa99'],
    ['112233445566778899AABBCC',3,-1,4,-1,0,'ccbbaa998877665544332211'],
    ['100120023003400450056006700780089009A00AB00BC00C',3,1,8,1,0,'100120023003400450056006700780089009a00ab00bc00c'],
    ['100120023003400450056006700780089009A00AB00BC00C',3,-1,8,1,0,'9009a00ab00bc00c50056006700780081001200230034004'],
    ['100120023003400450056006700780089009A00AB00BC00C',3,1,8,-1,0,'044003300220011008800770066005500cc00bb00aa00990'],
    ['100120023003400450056006700780089009A00AB00BC00C',3,-1,8,-1,0,'0cc00bb00aa0099008800770066005500440033002200110'],
    ['155555555555555555555555',3,1,4,1,1,'555555552aaaaaaa55555555'],
    ['155555555555555555555555',3,-1,4,1,1,'555555552aaaaaaa55555555'],
    ['155555555555555555555555',3,1,4,-1,1,'55555555aaaaaa2a55555555'],
    ['155555555555555555555555',3,-1,4,-1,1,'55555555aaaaaa2a55555555']
];

print "Import:\n";
$passed = true;
foreach ($import as $k => $test) {
    // count * size
    $data = substr(hex2bin($test[6]), 0, $test[1] * $test[3]);
    // data, order, size, endian, nails
    $gmp = gmp_import($data, $test[2], $test[3], $test[4], $test[5]);
    $result = gmp_strval($gmp, 16);
    if ($result !== $test[0]) {
        print "$k: '$result' !== '{$test[0]}'\n";
        $passed = false;
    }
}

var_dump($passed);

print "Export:\n";
$passed = true;
foreach ($export as $k => $test) {
    $gmp = gmp_init($test[0], 16);
    // gmpumber, order, size, endian, nails
    $str = gmp_export($gmp, $test[2], $test[3], $test[4], $test[5]);
    // count * size
    $result = bin2hex(substr($str, 0, $test[1] * $test[3]));
    if ($result !== $test[6]) {
        print "$k: '$result' !== '{$test[6]}'\n";
        $passed = false;
    }
}

var_dump($passed);
--EXPECTF--
Import:

Warning: gmp_import(): Bad word size: 0 (should be at least 1 byte) in %s on line %d
bool(true)
Export:
bool(true)
