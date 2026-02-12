<?php

use function Kahlan\beforeAll;

function callClosure(?Closure $closure, array $args = [])
{
	if ($closure) {
		$closure(...$args);
	}
}


function fileHook(string|array $file, ?Closure $beforeAll = null, ?Closure $afterAll = null, ?Closure $beforeEach = null, ?Closure $afterEach = null)
{
	$files = (array) $file;
	foreach ($files as &$file) {
		$file = __DIR__ . '/' . trim($file, '/');

		if (! is_dir($dirname = pathinfo($file, PATHINFO_DIRNAME))) {
			mkdir($dirname);
		}
		file_put_contents($file, '', LOCK_EX);
	}

	beforeAll(function() use($files, $beforeAll) {
		callClosure($beforeAll, [$files, ...func_get_args()]);
	});

	beforeEach(function () use($files, $beforeEach) {
		callClosure($beforeEach, [$files, ...func_get_args()]);
	});

	afterEach(function() use($files, $afterEach) {
		foreach ($files as $file) {
			file_put_contents($file, '', LOCK_EX);
		}

		callClosure($afterEach, [$files, ...func_get_args()]);
	});

	afterAll(function() use($files, $afterAll) {
		foreach ($files as $file) {
			if (file_exists($file)) {
				unlink($file);
			}
		}
		callClosure($afterAll, [$files, ...func_get_args()]);
	});
}
