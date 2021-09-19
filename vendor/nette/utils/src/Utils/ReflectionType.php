<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Utils;


/**
 * PHP type reflection.
 */
final class ReflectionType
{
	/** @var array */
	private $types;

	/** @var bool */
	private $single;

	/** @var ?string  |, & */
	private $kind = '|';


	/** @internal */
	public function __construct(string $type)
	{
		if ($type === '') {
			throw new Nette\InvalidArgumentException("Invalid type '$type'.");
		}
		if ($type[0] === '?') {
			$this->types = [substr($type, 1), 'null'];
			$this->single = true;
			return;
		}

		$this->types = explode('&', $type);
		if (count($this->types) > 1) {
			$this->kind = '&';
			$this->single = false;
			return;
		}

		$this->types = explode('|', $type);
		$t = array_diff($this->types, ['null']);
		$this->single = count($t) < 2;
		if (count($this->types) < 2) {
			$this->kind = null;
		} elseif ($this->single) {
			$this->types = [reset($t), 'null'];
		}
	}


	public function __toString(): string
	{
		return $this->single
			? (count($this->types) > 1 ? '?' : '') . $this->types[0]
			: implode($this->kind, $this->types);
	}


	/** @return string[] */
	public function getNames(): array
	{
		return $this->types;
	}


	/** @return self[] */
	public function getTypes(): array
	{
		return array_map(fn($name) => new self($name), $this->types);
	}


	public function allows(string $type): bool
	{
		if ($this->types === ['mixed']) {
			return true;
		}

		$type = new self($type);

		if ($this->isIntersection()) {
			if (!$type->isIntersection()) {
				return false;
			}
			return Arrays::every($this->types, function ($currentType) use ($type) {
				$builtin = Reflection::isBuiltinType($currentType);
				foreach ($type->types as $testedType) {
					if ($builtin ? strcasecmp($currentType, $testedType) === 0 : is_a($testedType, $currentType, true)) {
						return true;
					}
				}
			});
		}

		$method = $type->isIntersection() ? 'some' : 'every';
		return Arrays::$method($type->types, function ($testedType) {
			$builtin = Reflection::isBuiltinType($testedType);
			foreach ($this->types as $currentType) {
				if ($builtin ? strcasecmp($currentType, $testedType) === 0 : is_a($testedType, $currentType, true)) {
					return true;
				}
			}
		});
	}


	public function isUnion(): bool
	{
		return $this->kind === '|';
	}


	public function isIntersection(): bool
	{
		return $this->kind === '&';
	}


	public function isSingle(): bool
	{
		return $this->single;
	}


	public function isBuiltin(): bool
	{
		return $this->single && Reflection::isBuiltinType($this->types[0]);
	}


	public function getSingleName(): ?string
	{
		return $this->single
			? $this->types[0]
			: null;
	}


	public function getSingleClassName(): ?string
	{
		return $this->single && !Reflection::isBuiltinType($this->types[0])
			? $this->types[0]
			: null;
	}
}
