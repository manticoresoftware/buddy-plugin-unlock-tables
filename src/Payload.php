<?php declare(strict_types=1);

/*
  Copyright (c) 2023, Manticore Software LTD (https://manticoresearch.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License version 2 or any later
  version. You should have received a copy of the GPL license along with this
  program; if you did not, you can find it at http://www.gnu.org/
*/
namespace Manticoresearch\Buddy\Plugin\UnlockTables;

use Manticoresearch\Buddy\Core\Network\Request;
use Manticoresearch\Buddy\Core\Plugin\BasePayload;

final class Payload extends BasePayload {
	public string $path;

	/**
	 * Initialize request with tables to lock
	 * @param array<string> $tables
	 * @return void
	 */
	public function __construct(public array $tables = []) {
	}


  /**
	 * @param Request $request
	 * @return static
	 */
	public static function fromRequest(Request $request): static {
		// Cut lock tables prefix
		$query = trim(substr($request->payload, 13));
		preg_match_all('/(ALL|([a-z_]+)(\s*,\s*[a-z_]+)*)/i', $query, $matches, PREG_SET_ORDER);

		// We parse lock type and alias but actually do not use now
		if ($matches && strtoupper($matches[0][0]) !== 'ALL') {
			$tables = explode(',', $matches[0][0]);
			$tables = array_map('trim', $tables);
		} else {
			$tables = [];
		}

		$self = new static($tables);
		$self->path = $request->path;
		return $self;
	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	public static function hasMatch(Request $request): bool {
		return stripos($request->payload, 'unlock tables') === 0;
	}
}
