<?php

use FpDbTest\Database;
use FpDbTest\DatabaseTest;
use FpDbTest\ORM\ConditionalBlockService;
use FpDbTest\ORM\ConvertHelper\ArgumentConverter;
use FpDbTest\ORM\ConvertHelper\ConvertArrayHelper;
use FpDbTest\ORM\SpecifiersService;

spl_autoload_register(function ($class) {
    $a = array_slice(explode('\\', $class), 1);
    if (!$a) {
        throw new Exception();
    }
    $filename = implode('/', [__DIR__, ...$a]) . '.php';
    require_once $filename;
});
// TODO read from dotenv
$mysqli = @new mysqli('db', 'fp-user', 'fp-secret', 'fp', 3306);
if ($mysqli->connect_errno) {
    throw new Exception($mysqli->connect_error);
}

$test = new DatabaseTest(
    new Database(
        $mysqli,
        new SpecifiersService(),
        new ArgumentConverter(
            new ConvertArrayHelper()
        ),
        new ConditionalBlockService()
    )
);
$test->testBuildQuery();

exit("OK\n");
