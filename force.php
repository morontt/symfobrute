<?php
/**
 * Created by PhpStorm.
 *
 * Date: 10.12.15
 * Time: 22:58
 */

ini_set('display_errors', 0);
require_once __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

/**
 * Password source - https://stricture-group.com/files/adobe-top100.txt
 */
$passwords = [];
$f = fopen(__DIR__ . '/adobe-top100.txt', 'r');
while (($buffer = fgets($f, 1024)) !== false) {
    $matches = [];
    if (preg_match('/^\d+\.\s+\d+\s+\S+\s+(\w+)$/', $buffer, $matches)) {
        $passwords[] = $matches[1];
    }
}
fclose($f);

try {
    $db = new \PDO('mysql:host=localhost;dbname=' . $config['dbname'], $config['user'], $config['password']);
} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

$encoder = new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder();

$userTable = $config['table'];

$upd = $db->prepare("UPDATE `{$userTable}` SET `checked` = 1 WHERE `id` = :id");
$success = $db->prepare("UPDATE `{$userTable}` SET `checked` = 1, `plain_password` = :password WHERE `id` = :id");

do {
    $sth = $db->query("SELECT * FROM `{$userTable}` WHERE `checked` = 0 AND `plain_password` IS NULL LIMIT 1", \PDO::FETCH_ASSOC);
    $count = $sth->rowCount();

    $item = $sth->fetch();
    $id = (int)$item['id'];
    foreach ($passwords as $password) {
        $hash = $encoder->encodePassword($password, $item['salt']);
        if ($hash == $item['password']) {
            echo sprintf('%s - %s', $item['username'], $password) . PHP_EOL;
            $success->execute([':id' => $id, ':password' => $password]);
            break;
        }
    }

    $upd->execute([':id' => $id]);
} while ($count == 1);
