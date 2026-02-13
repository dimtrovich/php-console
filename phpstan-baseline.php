<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Call to function is_callable\\(\\) with callable\\(\\)\\: mixed will always evaluate to true\\.$#',
	'identifier' => 'function.alreadyNarrowedType',
	'count' => 1,
	'path' => __DIR__ . '/src/Command.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function is_string\\(\\) with string will always evaluate to true\\.$#',
	'identifier' => 'function.alreadyNarrowedType',
	'count' => 2,
	'path' => __DIR__ . '/src/Command.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Dimtrovich\\\\Console\\\\Command\\:\\:\\$parameters \\(array\\{arguments\\: array\\<string, mixed\\>, options\\: array\\<string, mixed\\>\\}\\) does not accept default value of type array\\{\\}\\.$#',
	'identifier' => 'property.defaultValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Command.php',
];
$ignoreErrors[] = [
	'message' => '#^Result of && is always false\\.$#',
	'identifier' => 'booleanAnd.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/src/Command.php',
];
$ignoreErrors[] = [
	'message' => '#^Static property Dimtrovich\\\\Console\\\\Components\\\\Alert\\:\\:\\$instance \\(static\\(Dimtrovich\\\\Console\\\\Components\\\\Alert\\)\\|null\\) does not accept Dimtrovich\\\\Console\\\\Components\\\\Alert\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Components/Alert.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\=\\=\\= between null and null will always evaluate to true\\.$#',
	'identifier' => 'identical.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Components/Alert.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Ahc\\\\Cli\\\\Output\\\\Writer\\:\\:boldWhiteBgGray\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Components/Badge.php',
];
$ignoreErrors[] = [
	'message' => '#^Static property Dimtrovich\\\\Console\\\\Components\\\\Badge\\:\\:\\$instance \\(static\\(Dimtrovich\\\\Console\\\\Components\\\\Badge\\)\\|null\\) does not accept Dimtrovich\\\\Console\\\\Components\\\\Badge\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Components/Badge.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\=\\=\\= between null and null will always evaluate to true\\.$#',
	'identifier' => 'identical.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Components/Badge.php',
];
$ignoreErrors[] = [
	'message' => '#^Static property Dimtrovich\\\\Console\\\\Components\\\\Logger\\:\\:\\$instance \\(static\\(Dimtrovich\\\\Console\\\\Components\\\\Logger\\)\\|null\\) does not accept Dimtrovich\\\\Console\\\\Components\\\\Logger\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 2,
	'path' => __DIR__ . '/src/Components/Logger.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\=\\=\\= between null and null will always evaluate to true\\.$#',
	'identifier' => 'identical.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Components/Logger.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function is_callable\\(\\) with callable\\(\\)\\: mixed will always evaluate to true\\.$#',
	'identifier' => 'function.alreadyNarrowedType',
	'count' => 1,
	'path' => __DIR__ . '/src/Console.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var has invalid value \\(array\\<string, array\\{
     \'action\' \\=\\> callable,\\}\\>
     \'name\'   \\=\\> string,
     \'alias\'  \\=\\> string,
\\}\\>\\)\\: Unexpected token "\\=\\>", expected \'\\}\' at offset 93 on line 5$#',
	'identifier' => 'phpDoc.parseError',
	'count' => 1,
	'path' => __DIR__ . '/src/Console.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Ahc\\\\Cli\\\\IO\\\\Interactor\\:\\:help_usage\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Overrides/Command.php',
];
$ignoreErrors[] = [
	'message' => '#^Trait Dimtrovich\\\\Console\\\\Traits\\\\AsciiArt is used zero times and is not analysed\\.$#',
	'identifier' => 'trait.unused',
	'count' => 1,
	'path' => __DIR__ . '/src/Traits/AsciiArt.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
